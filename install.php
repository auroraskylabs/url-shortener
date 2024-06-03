<?php
// Check if installation lock file exists
if (file_exists('.installer_lock')) {
    die("Installer not available. Please remove the install lock to reinstall.");
}

// Initialize variables
$step = $_GET['step'] ?? 'form';
$host = $dbname = $username = $password = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['host'], $_POST['dbname'], $_POST['username'], $_POST['password'])) {
        $host = $_POST['host'];
        $dbname = $_POST['dbname'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check database connection
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $success = 'Database connection successful.';

            // Create urlconfig.php file
            $configContent = "<?php\n\$host = '$host';\n\$dbname = '$dbname';\n\$username = '$username';\n\$password = '$password';\n?>";
            file_put_contents('urlconfig.php', $configContent);
            $step = 'build';
        } catch (PDOException $e) {
            $error = 'Database connection failed: ' . $e->getMessage();
        }
    } elseif (isset($_POST['build'])) {
        // Run init-db.php to create the table
        include 'urlconfig.php';
        include 'init-db.php';
        
        // Test database by creating and reading a test URL entry
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insert test URL
            $testUrl = 'http://example.com';
            $testCode = 'testcode12345';
            $stmt = $pdo->prepare("INSERT INTO urls (code, url, token, ip_address) VALUES (:code, :url, 'testtoken', '127.0.0.1')");
            $stmt->execute(['code' => $testCode, 'url' => $testUrl]);

            // Read test URL
            $stmt = $pdo->prepare("SELECT url FROM urls WHERE code = :code");
            $stmt->execute(['code' => $testCode]);
            $url = $stmt->fetchColumn();

            if ($url === $testUrl) {
                // Create installer lock file
                file_put_contents('.installer_lock', 'Installation complete');

                $success = 'Install complete.';
                $step = 'complete';
            } else {
                $error = 'Test entry failed. Please check your database.';
            }
        } catch (PDOException $e) {
            $error = 'Database operation failed: ' . $e->getMessage();
        }
    } elseif (isset($_POST['cleanup'])) {
        // Remove installation files
        unlink('install.php');
        unlink('init-db.php');
        echo "Installation files removed. You can now start using your application.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2a3439;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #ffffff;
            color: #000000;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($step === 'form'): ?>
            <h2>Database Configuration</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="host">Database Host</label>
                    <input type="text" class="form-control" id="host" name="host" required>
                </div>
                <div class="form-group">
                    <label for="dbname">Database Name</label>
                    <input type="text" class="form-control" id="dbname" name="dbname" required>
                </div>
                <div class="form-group">
                    <label for="username">Database User</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Database Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php elseif ($step === 'build'): ?>
            <h2>Config Set</h2>
            <p>Click Build Database below to configure your database.</p>
            <form method="POST">
                <input type="hidden" name="build" value="1">
                <button type="submit" class="btn btn-primary">Build Database</button>
            </form>
        <?php elseif ($step === 'complete'): ?>
            <h2>Install Complete</h2>
            <p><?= $success ?></p>
            <p>We highly recommend removing the installation files to prevent anyone else from reconfiguring your install.</p>
            <form method="POST">
                <input type="hidden" name="cleanup" value="1">
                <button type="submit" class="btn btn-danger">Remove Install Files</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
