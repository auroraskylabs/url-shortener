<?php
// Include configuration file
include 'urlconfig.php';

try {
    // Establish a connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL statement to create the urls table
    $sql = "CREATE TABLE IF NOT EXISTS urls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(15) NOT NULL UNIQUE,
        url TEXT NOT NULL,
        token VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // Execute the SQL statement
    $pdo->exec($sql);
    echo "Table 'urls' created successfully.";
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>
