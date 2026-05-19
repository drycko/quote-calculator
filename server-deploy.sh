#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# server-deploy.sh — Laravel cPanel production deployment
#
# Upload this script to the server ONCE, then run it for every new deployment:
#   scp server-deploy.sh myapp:~/
#   ssh myapp 'bash ~/server-deploy.sh ~/deploy_20260518_180835.zip'
#
# Prerequisites (one-time):
#   1. Place a .env.production file inside the zip (alongside artisan) with your production environment variables.
#   2. Upload this script to ~/server-deploy.sh on the server.
#
# Persistent storage (uploaded files) lives in ~/persistent_storage/app/public/
# and is NEVER touched by deployments — each deploy folder symlinks into it.
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail

# ── Config ────────────────────────────────────────────────────────────────────
# Override PHP binary if needed: PHP_BIN=/usr/local/php82/bin/php bash server-deploy.sh ...
PHP="${PHP_BIN:-php}"
HOME_DIR="$HOME"
PUBLIC_HTML="$HOME_DIR/public_html"
PERSISTENT="$HOME_DIR/persistent_storage"
DEPLOY_FOLDER_PLACEHOLDER="laravel_deploy"   # must match index.production.php
LOCK_FILE="$PERSISTENT/installed"              # written by install.php after first setup
# ─────────────────────────────────────────────────────────────────────────────

ZIP_PATH="${1:?Usage: bash server-deploy.sh <path-to-zip>}" #
ZIP_PATH="$(realpath "$ZIP_PATH")"

if [[ ! -f "$ZIP_PATH" ]]; then
    echo "❌ File not found: $ZIP_PATH"
    exit 1
fi

ZIP_BASENAME="$(basename "$ZIP_PATH" .zip)"
DEPLOY_DIR="$HOME_DIR/$ZIP_BASENAME"
ENV_FILE="$DEPLOY_DIR/.env.production"

echo ""
echo "════════════════════════════════════════════════"
echo "  Laravel — Production Deployment"
echo "  Package : $ZIP_BASENAME"
echo "  Target  : $DEPLOY_DIR"
echo "════════════════════════════════════════════════"
echo ""

# ── 1. Extract ────────────────────────────────────────────────────────────────
echo "📦  Extracting package..."
mkdir -p "$DEPLOY_DIR"
unzip -q "$ZIP_PATH" -d "$DEPLOY_DIR"
echo "✅  Extracted → $DEPLOY_DIR"

# ── 2. Copy .env ──────────────────────────────────────────────────────────────
if [[ -f "$ENV_FILE" ]]; then
    cp "$ENV_FILE" "$DEPLOY_DIR/.env"
    echo "✅  .env copied from $ENV_FILE"
else
    echo ""
    echo "⚠️   .env.production not found in deploy package."
    echo "    Add a .env.production file to the root of your zip,"
    echo "    or copy your .env to $DEPLOY_DIR/.env manually,"
    echo "    then run artisan commands (step 5) yourself."
    echo ""
fi

# ── 3. Storage directories + permissions ─────────────────────────────────────
echo "📁  Setting up storage directories..."
mkdir -p \
    "$DEPLOY_DIR/storage/framework/sessions" \
    "$DEPLOY_DIR/storage/framework/views" \
    "$DEPLOY_DIR/storage/framework/cache" \
    "$DEPLOY_DIR/storage/logs" \
    "$DEPLOY_DIR/bootstrap/cache"
chmod -R 775 "$DEPLOY_DIR/storage" "$DEPLOY_DIR/bootstrap/cache"

# ── 4. Persistent storage ─────────────────────────────────────────────────────
echo "🗄️   Setting up persistent storage..."
mkdir -p "$PERSISTENT/app/public"

# If the deploy has a real storage/app/public dir (first-ever deploy), migrate its
# contents into persistent_storage before replacing it with the symlink.
if [[ -d "$DEPLOY_DIR/storage/app/public" && ! -L "$DEPLOY_DIR/storage/app/public" ]]; then
    cp -rn "$DEPLOY_DIR/storage/app/public/." "$PERSISTENT/app/public/" 2>/dev/null || true
    rm -rf "$DEPLOY_DIR/storage/app/public"
fi

# Symlink this deploy's storage/app/public → persistent storage
ln -sfn "$PERSISTENT/app/public" "$DEPLOY_DIR/storage/app/public"
echo "✅  $DEPLOY_DIR/storage/app/public → $PERSISTENT/app/public"

# Symlink public_html/storage → persistent storage
rm -f "$PUBLIC_HTML/storage"
ln -s "$PERSISTENT/app/public" "$PUBLIC_HTML/storage"
echo "✅  $PUBLIC_HTML/storage → $PERSISTENT/app/public"

# ── 5. Artisan commands — only on subsequent deploys (installer handles first run) ──
if [[ -f "$LOCK_FILE" ]]; then
    echo "⚙️   Existing installation detected — running artisan commands..."
    # Copy the saved .env from persistent storage into the new deploy folder
    if [[ -f "$PERSISTENT/.env.production" ]]; then
        cp "$PERSISTENT/.env.production" "$DEPLOY_DIR/.env"
        echo "✅  .env restored from persistent storage"
    fi
    cd "$DEPLOY_DIR"
    $PHP artisan migrate --force
    $PHP artisan config:cache
    $PHP artisan route:cache
    $PHP artisan view:cache
    echo "✅  Artisan done"
else
    echo "ℹ️   Fresh installation — artisan commands will run via the web installer."
fi

# ── 6. Publish public assets to public_html ───────────────────────────────────
echo "🌐  Syncing public assets to public_html..."

# Back up current public_html contents before overwriting (skip the storage symlink)
BACKUP_DIR="$HOME_DIR/public_backups/$ZIP_BASENAME"
mkdir -p "$BACKUP_DIR"
find "$PUBLIC_HTML" -mindepth 1 -maxdepth 1 ! -name 'storage' -print0 \
    | xargs -0 -I{} cp -r {} "$BACKUP_DIR/" 2>/dev/null || true
echo "📁  Current public_html backed up → $BACKUP_DIR"

# Copy everything from public/ except index files and install.php (handled separately)
find "$DEPLOY_DIR/public" -mindepth 1 -maxdepth 1 \
    ! -name 'index.php' \
    ! -name 'index.production.php' \
    ! -name 'install.php' \
    -exec cp -r {} "$PUBLIC_HTML/" \;

# Write public_html/index.php from the template, substituting the deploy folder name
cp "$DEPLOY_DIR/public/index.production.php" "$PUBLIC_HTML/index.php"
sed -i "s|$DEPLOY_FOLDER_PLACEHOLDER|$ZIP_BASENAME|g" "$PUBLIC_HTML/index.php"
echo "✅  public_html/index.php updated → references $ZIP_BASENAME"

# Copy install.php only on fresh install (not yet installed)
if [[ ! -f "$LOCK_FILE" ]]; then
    sed "s|__APP_ROOT__|$DEPLOY_DIR|g; s|__PERSISTENT_DIR__|$PERSISTENT|g" \
        "$DEPLOY_DIR/public/install.php" > "$PUBLIC_HTML/install.php"
    echo "✅  install.php deployed → $PUBLIC_HTML/install.php"
else
    # Remove any leftover installer from previous deploys
    rm -f "$PUBLIC_HTML/install.php"
fi

# ── 7. Update stable 'current' symlink ───────────────────────────────────────
ln -sfn "$DEPLOY_DIR" "$HOME_DIR/current"
echo "✅  ~/current → $DEPLOY_DIR"

# ── 8. Clean up zip ───────────────────────────────────────────────────────────
rm -f "$ZIP_PATH"
echo "🗑️   Zip removed"

echo ""
echo "════════════════════════════════════════════════"
echo "✅  Deployment complete!"
echo "    Live from : $DEPLOY_DIR"
echo "    Uploads   : $PERSISTENT/app/public"
echo "    Backup    : $BACKUP_DIR"
if [[ ! -f "$LOCK_FILE" ]]; then
    echo ""
    echo "  ➡  NEXT STEP: Open the installer in your browser:"
    echo "     https://public_html/install.php"
fi
echo "════════════════════════════════════════════════"
echo ""

