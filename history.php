<?php
// Database connection
include 'urlconfig.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

$token = $_GET['token'] ?? '';

if ($token) {
    // Retrieve the URLs associated with the token from the database
    $stmt = $pdo->prepare("SELECT url, code FROM urls WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $urls = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $history = [];
    foreach ($urls as $url) {
        $history[] = [
            'shortUrl' => "https://url.aurorasky.io/go/{$url['code']}",
            'originalUrl' => $url['url']
        ];
    }
    echo json_encode($history);
}
?>
