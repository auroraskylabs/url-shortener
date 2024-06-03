<?php
// Database connection
include 'urlconfig.php';



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Function to generate a random 15-character code
function generateCode($length = 15) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Function to get the user's IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = $_POST['url'];
    $token = $_POST['token'];
    $ipAddress = getUserIP();
    $code = generateCode();

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO urls (code, url, token, ip_address) VALUES (:code, :url, :token, :ip_address)");
    $stmt->execute(['code' => $code, 'url' => $originalUrl, 'token' => $token, 'ip_address' => $ipAddress]);

    $shortUrl = "https://url.aurorasky.io/go/$code";
    echo "<div class='alert alert-success'>Short URL: <a href='$shortUrl'>$shortUrl</a></div>";
}
?>
