<?php

// Database host 
$host = "localhost";

// Database name
$dbname = "axion";

// MySQL username
$username = "root";

// MySQL password
$password = "********";

try {
    // Create a new PDO instance
    $connection = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username,
        $password
    );

    // This ensures that PDO will throw an exception if something goes wrong
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Return the PDO connection object so it can be used in other files
    return $connection;
} catch (PDOException $e) {
    // If connection fails, stop execution and display the error
    die("Connection failed: " . $e->getMessage());
}
