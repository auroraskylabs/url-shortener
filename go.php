<?php
// Database connection
include 'urlconfig.php';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Get the code from the URL
$code = $_GET['code'] ?? '';

if ($code) {
    // Retrieve the original URL from the database
    $stmt = $pdo->prepare("SELECT url FROM urls WHERE code = :code");
    $stmt->execute(['code' => $code]);
    $url = $stmt->fetchColumn();

    if ($url) {
        // Redirect to the original URL
        header("Location: $url");
        exit();
    }
}

// If code is not found or empty, redirect to the homepage
// HTML and CSS for the error message
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Not Found</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
            font-family: Arial, sans-serif;
            color: #343a40;
            text-align: center;
        }
        .error-container {
            max-width: 600px;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .redirect-message {
            font-size: 1rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-message">We\'re sorry, this short URL has either expired or no longer exists.</div>
        <div class="redirect-message">Redirecting in <span id="seconds">5</span> seconds...</div>
    </div>
    <script>
        let seconds = 5;
        const countdown = setInterval(() => {
            seconds--;
            document.getElementById("seconds").textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = "/";
            }
        }, 1000);
    </script>
</body>
</html>';
exit();
?>
