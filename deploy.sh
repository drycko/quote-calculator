#!/usr/bin/env bash
set -euo pipefail

# Usage: ./deploy.sh [--deploy]
#   --deploy   After building the zip, upload it to the server and run server-deploy.sh via SSH.
#              Requires the SSH alias 'youralias' to be configured in ~/.ssh/config,
#              and ~/server-deploy.sh to exist on the server.

# Parse flags
DEPLOY_TO_SERVER=false
for arg in "$@"; do
    [[ "$arg" == "--deploy" ]] && DEPLOY_TO_SERVER=true
done

SSH_HOST="${SSH_ALIAS:-youralias}"

# Get the project root directory
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"
DEPLOYMENT_DIR="${PROJECT_ROOT}/deployment"

# Create deployment directory if it doesn't exist
mkdir -p "$DEPLOYMENT_DIR"

# Generate timestamp and zip name
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
ZIP_NAME="deploy_${TIMESTAMP}.zip"
ZIP_PATH="${DEPLOYMENT_DIR}/${ZIP_NAME}"

echo "🔨 Building production assets..."
cd "$PROJECT_ROOT"

# Normalize line endings to LF (Unix style)
echo "🔧 Normalizing line endings..."
find . -type f -name "*.php" -exec dos2unix {} \; 2>/dev/null || true
find . -type f -name "*.blade.php" -exec dos2unix {} \; 2>/dev/null || true
find . -type f -name "*.js" -exec dos2unix {} \; 2>/dev/null || true
find . -type f -name "*.css" -exec dos2unix {} \; 2>/dev/null || true
find . -type f -name "*.sh" -exec dos2unix {} \; 2>/dev/null || true

# Install production dependencies (dev deps stripped for the zip)
composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
npm ci --include=dev
npm run build

echo "📦 Creating deployment package: $ZIP_NAME"

# Create zip from project root, save to deployment directory
zip -r "$ZIP_PATH" . \
  -x ".git/*" \
     ".github/*" \
     "node_modules/*" \
     "tests/*" \
     "storage/app/public/*" \
     "storage/debugbar/*" \
     "storage/logs/*" \
     ".env" \
     ".env.backup" \
     ".env.testing" \
     ".gitignore" \
     "deployment/*" \
     "compose.yaml" \
     "docker-compose*" \
     "docker/*" \
     "Dockerfile" \
     "*.sh" \
     "*.md" \
     "phpunit.xml" \
     "vite.config.js" \
     "package*.json"

# Restore dev dependencies so local Sail environment keeps working
echo "🔄 Restoring dev dependencies for local development..."
composer install --optimize-autoloader --no-interaction

echo "✅ Package created: $ZIP_NAME"
echo "📦 Size: $(du -h "$ZIP_PATH" | cut -f1)"
echo "📍 Location: $ZIP_PATH"

# ── Optional: deploy to server ────────────────────────────────────────────────
if $DEPLOY_TO_SERVER; then
    echo ""
    echo "🚀 Uploading $ZIP_NAME to $SSH_HOST..."
    scp "$ZIP_PATH" "${SSH_HOST}:~/"
    echo "⚙️  Running server-deploy.sh on server..."
    ssh "$SSH_HOST" "bash ~/server-deploy.sh ~/${ZIP_NAME}"
else
    echo ""
    echo "💡 To deploy to production:"
    echo "   ./deploy.sh --deploy"
    echo ""
    echo "   Or manually:"
    echo "   scp $ZIP_PATH ${SSH_HOST}:~/"
    echo "   ssh ${SSH_HOST} 'bash ~/server-deploy.sh ~/${ZIP_NAME}'"
fi
