<?php
// install.php — Installer for Shelly Pro1PM EV Charge Monitor (English)
// - Validates Shelly IPv4
// - Tests MySQL credentials
// - Creates database (if missing) and table `ladehistorie`
// - Writes /config/config.php with variables: $host, $user, $pass, $db, $shelly_ip
// - If config/config.php exists, blocks installation
// - No inline CSS; uses /style/install.css

$configDir  = __DIR__ . '/config';
$configPath = $configDir . '/config.php';

$errors  = [];
$success = false;

// If config already exists: show lock screen (no processing)
if (file_exists($configPath)) {
    $locked = true;
} else {
    $locked = false;
}

if (!$locked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input
    $db_host   = trim($_POST['db_host'] ?? '');
    $db_user   = trim($_POST['db_user'] ?? '');
    $db_pass   = trim($_POST['db_pass'] ?? '');
    $db_name   = trim($_POST['db_name'] ?? '');
    $shelly_ip = trim($_POST['shelly_ip'] ?? '');

    // Basic presence checks
    if ($db_host === '' || $db_user === '' || $db_name === '' || $shelly_ip === '') {
        $errors[] = 'Please fill out all required fields.';
    }

    // Validate Shelly IP (IPv4 as requested)
    if ($shelly_ip !== '' && !filter_var($shelly_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $errors[] = 'Shelly IP must be a valid IPv4 address (e.g., 192.168.0.68).';
    }

    // Test MySQL connection and create DB/table
    if (empty($errors)) {
        mysqli_report(MYSQLI_REPORT_OFF);
        $mysqli = @new mysqli($db_host, $db_user, $db_pass);

        if ($mysqli->connect_errno) {
            $errors[] = 'MySQL connection failed: (' . $mysqli->connect_errno . ') ' . htmlspecialchars($mysqli->connect_error);
        } else {
            // Create database if it doesn't exist
            $db_name_safe = str_replace('`', '``', $db_name);
            $createDbSql = "CREATE DATABASE IF NOT EXISTS `{$db_name_safe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if (!$mysqli->query($createDbSql)) {
                $errors[] = 'Failed to create database: ' . htmlspecialchars($mysqli->error);
            } else {
                // Select DB
                if (!$mysqli->select_db($db_name)) {
                    $errors[] = 'Failed to select database after creation: ' . htmlspecialchars($mysqli->error);
                } else {
                    // Create required table schema
                    $schema = "
                        CREATE TABLE IF NOT EXISTS `ladehistorie` (
                            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            `datum` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            `kmstand` INT(11) NOT NULL,
                            `kwh` DOUBLE NOT NULL,
                            `geladene_kwh` DOUBLE NOT NULL,
                            `verbrauch` DOUBLE NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ";
                    if (!$mysqli->query($schema)) {
                        $errors[] = 'Failed to create table `ladehistorie`: ' . htmlspecialchars($mysqli->error);
                    }
                }
            }
            $mysqli->close();
        }
    }

    // Write config file if no errors so far
    if (empty($errors)) {
        if (!is_dir($configDir)) {
            if (!@mkdir($configDir, 0755, true)) {
                $errors[] = 'Failed to create /config directory. Please create it manually and ensure it is writable.';
            }
        }

        if (empty($errors)) {
            // Prepare config in your exact variable style
            $s_host = addslashes(str_replace(["\r", "\n"], '', $db_host));
            $s_user = addslashes(str_replace(["\r", "\n"], '', $db_user));
            $s_pass = addslashes($db_pass); // passwords can contain special chars
            $s_db   = addslashes(str_replace(["\r", "\n"], '', $db_name));
            $s_ip   = addslashes(str_replace(["\r", "\n"], '', $shelly_ip));

            $configContent = <<<PHP
<?php
\$host = "{$s_host}";
\$user = "{$s_user}";
\$pass = "{$s_pass}";
\$db = "{$s_db}";
\$shelly_ip = "{$s_ip}";
?>
PHP;

            if (@file_put_contents($configPath, $configContent) === false) {
                $errors[] = 'Failed to write config/config.php. Check file/folder permissions.';
            } else {
                @chmod($configPath, 0640); // best-effort permissions
                $success = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Install • Shelly Pro1PM EV Charge Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/install.css" />
</head>
<body>
<div class="container">
    <h1>Installation</h1>

    <?php if ($locked): ?>
        <div class="msg msg-warning">
            Installation is locked: <code>config/config.php</code> already exists.<br>
            Please delete or rename that file before running the installer again.
        </div>
    <?php elseif ($success): ?>
        <div class="msg msg-success">
            Installation completed successfully. Configuration saved and database initialized.
        </div>
        <a class="btn" href="index.php">Go to Dashboard</a>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="msg msg-error">
                <strong>There were problems:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="form">
            <div class="field">
                <label for="db_host">MySQL Server<span class="req">*</span></label>
                <input id="db_host" name="db_host" type="text" placeholder="e.g., localhost" required>
            </div>

            <div class="field">
                <label for="db_user">MySQL User<span class="req">*</span></label>
                <input id="db_user" name="db_user" type="text" placeholder="e.g., root" required>
            </div>

            <div class="field">
                <label for="db_pass">MySQL Password</label>
                <input id="db_pass" name="db_pass" type="password" placeholder="your password">
            </div>

            <div class="field">
                <label for="db_name">MySQL Database Name<span class="req">*</span></label>
                <input id="db_name" name="db_name" type="text" placeholder="e.g., ev_logger" required>
            </div>

            <div class="field">
                <label for="shelly_ip">Shelly IP (IPv4)<span class="req">*</span></label>
                <input id="shelly_ip" name="shelly_ip" type="text" placeholder="IPv4 format: 192.168.0.68" required>
            </div>

            <button class="btn" type="submit">Run Installation</button>
            <p class="hint">Fields marked <span class="req">*</span> are required.</p>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
