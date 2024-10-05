<?php
try {
    $db = new PDO('sqlite:base.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )";
    $db->exec($sql);
    echo "Database and table created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>