<?php
/**
 * ─────────────────────────────────────────────────────────────────────────────
 * Swift Steel — Web Installer
 *
 * Access this file at: https://yourdomain.com/install.php
 *
 * Placeholders below are replaced by server-deploy.sh at deploy time.
 * Do NOT edit these constants manually.
 * ─────────────────────────────────────────────────────────────────────────────
 */
define('APP_ROOT',       '__APP_ROOT__');
define('PERSISTENT_DIR', '__PERSISTENT_DIR__');
define('LOCK_FILE',      PERSISTENT_DIR . '/installed');
define('ENV_FILE',       APP_ROOT . '/.env');
define('ENV_EXAMPLE',    APP_ROOT . '/.env.example');

// ── Already installed? Block access. ─────────────────────────────────────────
if (file_exists(LOCK_FILE)) {
    http_response_code(404);
    exit;
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────
ini_set('display_errors', '0');
error_reporting(E_ALL);
session_start();

if (empty($_SESSION['_ins_csrf'])) {
    $_SESSION['_ins_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['_ins_csrf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['_token'] ?? '') !== $csrf) {
        http_response_code(403);
        exit('Forbidden.');
    }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function artisan(string $cmd): array {
    $php = escapeshellarg(PHP_BINARY);
    $art = escapeshellarg(APP_ROOT . '/artisan');
    exec("{$php} {$art} {$cmd} 2>&1", $out, $code);
    return ['ok' => $code === 0, 'out' => implode("\n", $out)];
}

function writeEnv(array $vals): bool {
    // Use .env.production as the base template if present, otherwise fall back to .env.example
    $envProd    = APP_ROOT . '/.env.production';
    $envExample = APP_ROOT . '/.env.example';
    if (file_exists($envProd)) {
        $content = file_get_contents($envProd);
    } elseif (file_exists($envExample)) {
        $content = file_get_contents($envExample);
    } else {
        $content = '';
    }

    foreach ($vals as $key => $value) {
        $value  = (string) $value;
        $quoted = (preg_match('/[\s#"\'\\\\$]/', $value) || $value === '')
            ? '"' . addcslashes($value, '"\\') . '"'
            : $value;
        $pattern     = '/^(' . preg_quote($key, '/') . '\s*=).*/m';
        $replacement = "$1{$quoted}";
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
        } else {
            $content .= "\n{$key}={$quoted}";
        }
    }

    // Remove a leading blank line if present
    $content = ltrim($content, "\r\n");

    return file_put_contents(ENV_FILE, $content) !== false;
}

function parseEnvFile(string $path): array {
    $vals = [];
    if (!file_exists($path)) return $vals;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        if (strlen($val) >= 2 && (($val[0] === '"' && $val[-1] === '"') || ($val[0] === "'" && $val[-1] === "'"))) {
            $val = stripslashes(substr($val, 1, -1));
        }
        $vals[$key] = $val;
    }
    return $vals;
}

function testDb(string $host, int $port, string $db, string $user, string $pass): ?string {
    try {
        new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        return null;
    } catch (Throwable $e) {
        return $e->getMessage();
    }
}

function requirements(): array {
    $checks = [];
    $checks[] = ['PHP &ge; 8.2',             version_compare(PHP_VERSION, '8.2', '>='), PHP_VERSION];
    foreach (['pdo_mysql', 'mbstring', 'openssl', 'fileinfo', 'xml', 'curl'] as $ext) {
        $checks[] = ["ext-{$ext}", extension_loaded($ext), extension_loaded($ext) ? 'Loaded' : 'Missing'];
    }
    $execOk = function_exists('exec') && !str_contains((string) ini_get('disable_functions'), 'exec');
    $checks[] = ['exec() enabled',         $execOk,                                           $execOk ? 'Yes' : 'Disabled'];
    $checks[] = ['storage/ writable',      is_writable(APP_ROOT . '/storage'),               is_writable(APP_ROOT . '/storage') ? 'Writable' : 'Not writable'];
    $checks[] = ['bootstrap/cache/ writable', is_writable(APP_ROOT . '/bootstrap/cache'),   is_writable(APP_ROOT . '/bootstrap/cache') ? 'Writable' : 'Not writable'];
    return $checks;
}

// ── AJAX: test DB ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'test_db') {
    header('Content-Type: application/json');
    if (($_POST['_token'] ?? '') !== $csrf) { echo json_encode(['ok' => false, 'msg' => 'Forbidden']); exit; }
    $err = testDb(
        $_POST['db_host']     ?? 'localhost',
        (int) ($_POST['db_port'] ?? 3306),
        $_POST['db_database'] ?? '',
        $_POST['db_username'] ?? '',
        $_POST['db_password'] ?? ''
    );
    echo json_encode(['ok' => $err === null, 'msg' => $err ?? 'Connection successful']);
    exit;
}

// ── Step machine ──────────────────────────────────────────────────────────────
$step   = max(1, min(5, (int) ($_POST['step'] ?? $_GET['step'] ?? 1)));
$errors = [];
if (!isset($_SESSION['_ins'])) $_SESSION['_ins'] = [];
$d = &$_SESSION['_ins'];

// Pre-populate fields from a persisted .env.production, bundled .env.production, or .env.example
if (empty($d)) {
    if (file_exists(PERSISTENT_DIR . '/.env.production')) {
        $envSrc = PERSISTENT_DIR . '/.env.production';
    } elseif (file_exists(APP_ROOT . '/.env.production')) {
        $envSrc = APP_ROOT . '/.env.production';
    } else {
        $envSrc = APP_ROOT . '/.env.example';
    }
    if (file_exists($envSrc)) {
        $e = parseEnvFile($envSrc);
        foreach ([
            'db_host'           => 'DB_HOST',
            'db_port'           => 'DB_PORT',
            'db_database'       => 'DB_DATABASE',
            'db_username'       => 'DB_USERNAME',
            'db_password'       => 'DB_PASSWORD',
            'app_name'          => 'APP_NAME',
            'app_url'           => 'APP_URL',
            'app_timezone'      => 'APP_TIMEZONE',
            'mail_mailer'       => 'MAIL_MAILER',
            'mail_host'         => 'MAIL_HOST',
            'mail_port'         => 'MAIL_PORT',
            'mail_username'     => 'MAIL_USERNAME',
            'mail_password'     => 'MAIL_PASSWORD',
            'mail_encryption'   => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name'    => 'MAIL_FROM_NAME',
        ] as $field => $envKey) {
            if (isset($e[$envKey])) $d[$field] = $e[$envKey];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $d['db_host']      = trim($_POST['db_host']      ?? 'localhost');
        $d['db_port']      = (int) ($_POST['db_port']    ?? 3306);
        $d['db_database']  = trim($_POST['db_database']  ?? '');
        $d['db_username']  = trim($_POST['db_username']  ?? '');
        $d['db_password']  = $_POST['db_password']       ?? '';
        $d['app_name']     = trim($_POST['app_name']     ?? 'Swift Steel');
        $d['app_url']      = rtrim(trim($_POST['app_url'] ?? ''), '/');
        $d['app_timezone'] = $_POST['app_timezone']      ?? 'Africa/Johannesburg';

        if (!$d['db_database'])                                              $errors[] = 'Database name is required.';
        if (!$d['db_username'])                                              $errors[] = 'Database username is required.';
        if (!$d['app_url'] || !filter_var($d['app_url'], FILTER_VALIDATE_URL)) $errors[] = 'A valid Application URL is required (include https://).';

        if (empty($errors)) {
            $connErr = testDb($d['db_host'], $d['db_port'], $d['db_database'], $d['db_username'], $d['db_password']);
            if ($connErr) $errors[] = "Database connection failed: {$connErr}";
        }
        if (empty($errors)) $step = 3;
    } elseif ($step === 3) {
        $d['mail_mailer']       = $_POST['mail_mailer']       ?? 'log';
        $d['mail_host']         = trim($_POST['mail_host']    ?? '');
        $d['mail_port']         = (int) ($_POST['mail_port']  ?? 587);
        $d['mail_username']     = trim($_POST['mail_username'] ?? '');
        $d['mail_password']     = $_POST['mail_password']     ?? '';
        $d['mail_encryption']   = $_POST['mail_encryption']   ?? 'tls';
        $d['mail_from_address'] = trim($_POST['mail_from_address'] ?? '');
        $d['mail_from_name']    = trim($_POST['mail_from_name']    ?? ($d['app_name'] ?? ''));
        $step = 4;
    } elseif ($step === 4) {
        $d['admin_name']     = trim($_POST['admin_name']     ?? '');
        $d['admin_email']    = trim($_POST['admin_email']    ?? '');
        $d['admin_password'] = $_POST['admin_password']      ?? '';
        $confirm             = $_POST['admin_confirm']       ?? '';

        if (!$d['admin_name'])                                                 $errors[] = 'Name is required.';
        if (!filter_var($d['admin_email'], FILTER_VALIDATE_EMAIL))             $errors[] = 'A valid email is required.';
        if (strlen($d['admin_password']) < 8)                                  $errors[] = 'Password must be at least 8 characters.';
        if ($d['admin_password'] !== $confirm)                                 $errors[] = 'Passwords do not match.';
        if (empty($errors)) $step = 5;
    }
}

// ── Run installation ──────────────────────────────────────────────────────────
$log     = [];
$success = false;

if ($step === 5 && !empty($d['db_host'])) {
    $wrote = writeEnv([
        'APP_NAME'          => $d['app_name'],
        'APP_ENV'           => 'production',
        'APP_DEBUG'         => 'false',
        'APP_URL'           => $d['app_url'],
        'APP_TIMEZONE'      => $d['app_timezone'],
        'DB_CONNECTION'     => 'mysql',
        'DB_HOST'           => $d['db_host'],
        'DB_PORT'           => $d['db_port'],
        'DB_DATABASE'       => $d['db_database'],
        'DB_USERNAME'       => $d['db_username'],
        'DB_PASSWORD'       => $d['db_password'],
        'CACHE_STORE'       => 'file',
        'SESSION_DRIVER'    => 'file',
        'QUEUE_CONNECTION'  => 'sync',
        'MAIL_MAILER'       => $d['mail_mailer'],
        'MAIL_HOST'         => $d['mail_host'],
        'MAIL_PORT'         => $d['mail_port'],
        'MAIL_USERNAME'     => $d['mail_username'],
        'MAIL_PASSWORD'     => $d['mail_password'],
        'MAIL_ENCRYPTION'   => $d['mail_encryption'],
        'MAIL_FROM_ADDRESS' => $d['mail_from_address'],
        'MAIL_FROM_NAME'    => $d['mail_from_name'],
        // App-specific
        'APP_CURRENCY'           => 'ZAR',
        'DEFAULT_CONVERSION_RATE'=> '18.50',
        'QUOTE_VAT_RATE'         => '0.15',
        'QUOTE_PLUGIN_MARKUP'    => '10',
    ]);
    $log[] = [$wrote, '.env file written'];

    if ($wrote) {
        // Only generate a key if one isn't already set in the written .env
        $existingEnv = parseEnvFile(ENV_FILE);
        $hasKey = !empty($existingEnv['APP_KEY']) && str_starts_with($existingEnv['APP_KEY'], 'base64:');
        if (!$hasKey) {
            $r = artisan('key:generate --force');
            $log[] = [$r['ok'], 'Application key generated' . ($r['ok'] ? '' : ': ' . $r['out'])];
            if (!$r['ok']) $step = -1;
        } else {
            $log[] = [true, 'Application key already present — skipped'];
        }

        foreach ($step !== -1 ? [
            ['migrate --force',                              'Database migrated'],
            ['db:seed --class=LineItemTemplateSeeder --force', 'Templates seeded'],
        ] : [] as [$cmd, $label]) {
            $r = artisan($cmd);
            $log[] = [$r['ok'], $label . ($r['ok'] ? '' : ': ' . $r['out'])];
            if (!$r['ok']) { $step = -1; break; }
        }

        if ($step !== -1) {
            $name  = escapeshellarg($d['admin_name']);
            $email = escapeshellarg($d['admin_email']);
            $pass  = escapeshellarg($d['admin_password']);
            $r = artisan("app:install --name={$name} --email={$email} --password={$pass}");
            $log[] = [$r['ok'], 'Admin user created' . ($r['ok'] ? '' : ': ' . $r['out'])];

            if ($r['ok']) {
                artisan('config:cache');
                artisan('route:cache');
                artisan('view:cache');
                $log[] = [true, 'Caches optimised'];

                file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'));
                // Persist the .env so server-deploy.sh can restore it on future deploys
                @copy(ENV_FILE, PERSISTENT_DIR . '/.env.production');
                $log[] = [true, 'Installation locked'];

                unset($_SESSION['_ins']);
                $success = true;
                register_shutdown_function(fn() => @unlink(__FILE__));
            } else {
                $step = -1;
            }
        }
    } else {
        $step = -1;
    }
}

// ── Computed view data ────────────────────────────────────────────────────────
$reqs    = ($step === 1) ? requirements() : [];
$allPass = !empty($reqs) && !in_array(false, array_column($reqs, 1), true);
$tzList  = DateTimeZone::listIdentifiers();
$appUrl  = ($d['app_url'] ?? '') ?: (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '');

$stepLabels = [1 => 'Requirements', 2 => 'Database', 3 => 'Mail', 4 => 'Admin Account', 5 => 'Install'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Swift Steel — Installer</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --orange: #e8610a;
  --orange-d: #c44f00;
  --bg: #f5f5f5;
  --card: #fff;
  --border: #e0e0e0;
  --muted: #6b7280;
  --ok: #16a34a;
  --fail: #dc2626;
  --radius: 10px;
}
body { font-family: system-ui, -apple-system, sans-serif; background: var(--bg); color: #1f2937; font-size: 15px; line-height: 1.6; }
a { color: var(--orange); }

/* Layout */
.wrap { display: flex; min-height: 100vh; }
.sidebar {
  width: 240px; flex-shrink: 0;
  background: #1e1e2e; color: #d1d5db;
  padding: 40px 0;
  display: flex; flex-direction: column;
}
.sidebar .brand {
  padding: 0 28px 32px;
  font-size: 18px; font-weight: 700; color: #fff;
  border-bottom: 1px solid #2d2d3e;
}
.sidebar .brand span { color: var(--orange); }
.sidebar nav { padding: 24px 0; flex: 1; }
.sidebar nav a {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 28px;
  color: #9ca3af; text-decoration: none; font-size: 14px;
  transition: color .15s, background .15s;
}
.sidebar nav a.active { color: #fff; background: rgba(255,255,255,.07); }
.sidebar nav a.done  { color: #4ade80; }
.sidebar nav a .num {
  width: 22px; height: 22px; border-radius: 50%;
  background: #2d2d3e; display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.sidebar nav a.active .num { background: var(--orange); color: #fff; }
.sidebar nav a.done  .num  { background: var(--ok);    color: #fff; }
.main { flex: 1; padding: 48px; max-width: 700px; }
.main h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.main .sub { color: var(--muted); margin-bottom: 32px; font-size: 14px; }

/* Card */
.card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }

/* Form */
.field { margin-bottom: 18px; }
.field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #374151; }
.field input, .field select {
  width: 100%; padding: 9px 13px;
  border: 1px solid var(--border); border-radius: 6px;
  font-size: 14px; background: #fafafa; color: #1f2937;
  transition: border-color .15s;
}
.field input:focus, .field select:focus { outline: none; border-color: var(--orange); background: #fff; }
.field small { display: block; margin-top: 4px; color: var(--muted); font-size: 12px; }
.row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* Buttons */
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; border-radius: 6px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: background .15s; }
.btn-primary { background: var(--orange); color: #fff; }
.btn-primary:hover { background: var(--orange-d); }
.btn-outline { background: transparent; border: 1px solid var(--border); color: #374151; }
.btn-outline:hover { background: #f3f4f6; }
.btn-sm { padding: 6px 14px; font-size: 13px; }
.btn:disabled { opacity: .5; cursor: not-allowed; }
.btns { display: flex; gap: 12px; margin-top: 24px; align-items: center; }

/* Alerts */
.alert { padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; }
.alert-danger  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #14532d; }
.alert ul { margin: 6px 0 0 18px; }

/* Requirements table */
.req-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.req-table td { padding: 8px 4px; border-bottom: 1px solid var(--border); }
.req-table td:last-child { text-align: right; font-weight: 600; }
.ok   { color: var(--ok); }
.fail { color: var(--fail); }

/* Log */
.log { list-style: none; font-size: 14px; }
.log li { padding: 8px 0; border-bottom: 1px solid var(--border); display: flex; align-items: flex-start; gap: 10px; }
.log li:last-child { border-bottom: none; }
.log .icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }

/* DB test */
#db-test-result { font-size: 13px; margin-left: 8px; font-weight: 600; }
#db-test-result.ok   { color: var(--ok); }
#db-test-result.fail { color: var(--fail); }

/* Success */
.success-box { text-align: center; padding: 40px 0; }
.success-box .checkmark { font-size: 64px; margin-bottom: 16px; }
.success-box h2 { font-size: 24px; margin-bottom: 8px; }
.success-box p { color: var(--muted); margin-bottom: 28px; }
</style>
</head>
<body>
<div class="wrap">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand">Swift <span>Steel</span></div>
    <nav>
      <?php foreach ($stepLabels as $n => $label): ?>
        <?php
          $cls  = $n === $step ? 'active' : ($n < $step ? 'done' : '');
          $icon = $n < $step ? '✓' : $n;
        ?>
        <a href="#" class="<?= $cls ?>">
          <span class="num"><?= $icon ?></span>
          <?= $label ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main">

    <?php if ($step === 1): ?>
    <!-- ── Step 1: Requirements ─────────────────────────────────────────── -->
    <h1>System Requirements</h1>
    <p class="sub">Checking your server environment before we begin.</p>

    <div class="card">
      <table class="req-table">
        <?php foreach ($reqs as [$label, $pass, $value]): ?>
        <tr>
          <td><?= htmlspecialchars($label) ?></td>
          <td class="<?= $pass ? 'ok' : 'fail' ?>"><?= htmlspecialchars($value) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <div class="btns">
        <?php if ($allPass): ?>
          <a href="?step=2" class="btn btn-primary">Continue &rarr;</a>
        <?php else: ?>
          <button class="btn btn-primary" disabled>Fix issues above to continue</button>
          <a href="?step=1" class="btn btn-outline btn-sm">Re-check</a>
        <?php endif; ?>
      </div>
    </div>

    <?php elseif ($step === 2): ?>
    <!-- ── Step 2: Database + App ───────────────────────────────────────── -->
    <h1>Database & Application</h1>
    <p class="sub">Configure the database connection and basic application settings.</p>

    <?php if ($errors): ?>
    <div class="alert alert-danger"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST">
        <input type="hidden" name="_token" value="<?= $csrf ?>">
        <input type="hidden" name="step"   value="2">

        <p style="font-weight:600;margin-bottom:14px;font-size:13px;text-transform:uppercase;color:var(--muted);letter-spacing:.05em;">Database</p>

        <div class="row2">
          <div class="field">
            <label>Database Host</label>
            <input type="text" name="db_host" value="<?= htmlspecialchars($d['db_host'] ?? 'localhost') ?>">
          </div>
          <div class="field">
            <label>Port</label>
            <input type="number" name="db_port" value="<?= htmlspecialchars($d['db_port'] ?? '3306') ?>">
          </div>
        </div>
        <div class="field">
          <label>Database Name</label>
          <input type="text" name="db_database" value="<?= htmlspecialchars($d['db_database'] ?? '') ?>">
        </div>
        <div class="row2">
          <div class="field">
            <label>Username</label>
            <input type="text" name="db_username" value="<?= htmlspecialchars($d['db_username'] ?? '') ?>">
          </div>
          <div class="field">
            <label>Password</label>
            <input type="password" name="db_password" value="<?= htmlspecialchars($d['db_password'] ?? '') ?>">
          </div>
        </div>

        <div class="btns" style="margin-top:4px;margin-bottom:24px;">
          <button type="button" class="btn btn-outline btn-sm" id="btn-test-db">Test Connection</button>
          <span id="db-test-result"></span>
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:20px 0;">
        <p style="font-weight:600;margin-bottom:14px;font-size:13px;text-transform:uppercase;color:var(--muted);letter-spacing:.05em;">Application</p>

        <div class="field">
          <label>Application Name</label>
          <input type="text" name="app_name" value="<?= htmlspecialchars($d['app_name'] ?? 'Swift Steel') ?>">
        </div>
        <div class="field">
          <label>Application URL</label>
          <input type="url" name="app_url" value="<?= htmlspecialchars($d['app_url'] ?? $appUrl) ?>" placeholder="https://yourdomain.com">
          <small>Include https:// — no trailing slash.</small>
        </div>
        <div class="field">
          <label>Timezone</label>
          <select name="app_timezone">
            <?php foreach ($tzList as $tz): ?>
            <option value="<?= $tz ?>" <?= ($d['app_timezone'] ?? 'Africa/Johannesburg') === $tz ? 'selected' : '' ?>><?= $tz ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="btns">
          <a href="?step=1" class="btn btn-outline">&larr; Back</a>
          <button type="submit" class="btn btn-primary">Save &amp; Continue &rarr;</button>
        </div>
      </form>
    </div>

    <script>
    document.getElementById('btn-test-db').addEventListener('click', function() {
        const btn = this, res = document.getElementById('db-test-result');
        btn.disabled = true; res.textContent = 'Testing…'; res.className = '';
        const form = btn.closest('form');
        const data = new FormData();
        data.append('_token',      '<?= $csrf ?>');
        data.append('_action',     'test_db');
        data.append('db_host',     form.db_host.value);
        data.append('db_port',     form.db_port.value);
        data.append('db_database', form.db_database.value);
        data.append('db_username', form.db_username.value);
        data.append('db_password', form.db_password.value);
        fetch('', {method:'POST', body: data})
            .then(r => r.json())
            .then(j => { res.textContent = j.msg; res.className = j.ok ? 'ok' : 'fail'; })
            .catch(() => { res.textContent = 'Request failed'; res.className = 'fail'; })
            .finally(() => btn.disabled = false);
    });
    </script>

    <?php elseif ($step === 3): ?>
    <!-- ── Step 3: Mail ─────────────────────────────────────────────────── -->
    <h1>Mail Configuration</h1>
    <p class="sub">Set up outgoing email for order notifications, invoices, and password resets.</p>

    <div class="card">
      <form method="POST">
        <input type="hidden" name="_token" value="<?= $csrf ?>">
        <input type="hidden" name="step"   value="3">

        <div class="field">
          <label>Mail Driver</label>
          <select name="mail_mailer" id="mailer-select">
            <?php foreach (['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'log' => 'Log (testing only)', 'null' => 'Null (disable email)'] as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($d['mail_mailer'] ?? 'log') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div id="smtp-fields">
          <div class="row2">
            <div class="field">
              <label>SMTP Host</label>
              <input type="text" name="mail_host" value="<?= htmlspecialchars($d['mail_host'] ?? '') ?>" placeholder="smtp.yourdomain.com">
            </div>
            <div class="field">
              <label>Port</label>
              <input type="number" name="mail_port" value="<?= htmlspecialchars($d['mail_port'] ?? '587') ?>">
            </div>
          </div>
          <div class="row2">
            <div class="field">
              <label>Username</label>
              <input type="text" name="mail_username" value="<?= htmlspecialchars($d['mail_username'] ?? '') ?>">
            </div>
            <div class="field">
              <label>Password</label>
              <input type="password" name="mail_password" value="<?= htmlspecialchars($d['mail_password'] ?? '') ?>">
            </div>
          </div>
          <div class="field">
            <label>Encryption</label>
            <select name="mail_encryption">
              <?php foreach (['tls' => 'TLS (recommended)', 'ssl' => 'SSL', '' => 'None'] as $val => $lbl): ?>
              <option value="<?= $val ?>" <?= ($d['mail_encryption'] ?? 'tls') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="row2">
          <div class="field">
            <label>From Address</label>
            <input type="email" name="mail_from_address" value="<?= htmlspecialchars($d['mail_from_address'] ?? '') ?>" placeholder="noreply@yourdomain.com">
          </div>
          <div class="field">
            <label>From Name</label>
            <input type="text" name="mail_from_name" value="<?= htmlspecialchars($d['mail_from_name'] ?? ($d['app_name'] ?? '')) ?>">
          </div>
        </div>

        <div class="btns">
          <a href="?step=2" class="btn btn-outline">&larr; Back</a>
          <button type="submit" class="btn btn-primary">Save &amp; Continue &rarr;</button>
        </div>
      </form>
    </div>

    <script>
    (function(){
        var sel = document.getElementById('mailer-select');
        var smtp = document.getElementById('smtp-fields');
        function toggle() { smtp.style.display = ['smtp'].includes(sel.value) ? '' : 'none'; }
        sel.addEventListener('change', toggle);
        toggle();
    })();
    </script>

    <?php elseif ($step === 4): ?>
    <!-- ── Step 4: Admin Account ────────────────────────────────────────── -->
    <h1>Admin Account</h1>
    <p class="sub">Create the first administrator account for the back office.</p>

    <?php if ($errors): ?>
    <div class="alert alert-danger"><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST">
        <input type="hidden" name="_token" value="<?= $csrf ?>">
        <input type="hidden" name="step"   value="4">

        <div class="field">
          <label>Full Name</label>
          <input type="text" name="admin_name" value="<?= htmlspecialchars($d['admin_name'] ?? '') ?>" autocomplete="name">
        </div>
        <div class="field">
          <label>Email Address</label>
          <input type="email" name="admin_email" value="<?= htmlspecialchars($d['admin_email'] ?? '') ?>" autocomplete="email">
        </div>
        <div class="row2">
          <div class="field">
            <label>Password</label>
            <input type="password" name="admin_password" autocomplete="new-password">
            <small>Minimum 8 characters.</small>
          </div>
          <div class="field">
            <label>Confirm Password</label>
            <input type="password" name="admin_confirm" autocomplete="new-password">
          </div>
        </div>

        <div class="btns">
          <a href="?step=3" class="btn btn-outline">&larr; Back</a>
          <button type="submit" class="btn btn-primary">Install Now &rarr;</button>
        </div>
      </form>
    </div>

    <?php elseif ($step === 5 || $step === -1): ?>
    <!-- ── Step 5: Install ──────────────────────────────────────────────── -->
    <h1>Installing</h1>
    <p class="sub">Running migrations, creating your account, and optimising the application.</p>

    <div class="card">
      <?php if ($success): ?>
      <div class="success-box">
        <div class="checkmark">✅</div>
        <h2>Installation Complete</h2>
        <p>Swift Steel has been installed successfully.<br>This installer file has been removed.</p>
        <a href="<?= htmlspecialchars($d['app_url'] ?? '/') ?>/admin" class="btn btn-primary" style="font-size:15px;padding:12px 28px;">
          Go to Admin Panel &rarr;
        </a>
      </div>
      <?php else: ?>
      <?php if ($step === -1): ?>
      <div class="alert alert-danger">Installation failed. Review the log below and try again.</div>
      <?php endif; ?>
      <ul class="log">
        <?php foreach ($log as [$ok, $msg]): ?>
        <li>
          <span class="icon <?= $ok ? 'ok' : 'fail' ?>"><?= $ok ? '✓' : '✗' ?></span>
          <span><?= htmlspecialchars($msg) ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php if ($step === -1): ?>
      <div class="btns"><a href="?step=4" class="btn btn-outline">&larr; Go Back</a></div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </main>
</div>
</body>
</html>
