<?php

$dbHost = 'sql301.infinityfree.com'; # cspell: disable-line
$dbName = 'if0_38085340_guido';
$dbUser = 'if0_38085340';
$dbPass = 'ZRNs2SacbaKlt'; # cspell: disable-line

try {
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);


    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS recensioni (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_type VARCHAR(255) NOT NULL,
            comment TEXT NOT NULL,
            rating INT NOT NULL,
            approved ENUM('not yet', 'yes', 'no') DEFAULT 'not yet',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createTableSQL);

    $form = [
        'user-type' => $_GET['user-type'] ?? 'default',
        'comment' => $_GET['comment'] ?? '',
        'rating' => filter_var($_GET['rating'] ?? 0, FILTER_VALIDATE_INT) ?? 0,
        'approved' => 'not yet',
    ];

    if (!empty($form['user-type']) && !empty($form['comment']) && $form['rating'] > 0) {
        $insertSQL = "
            INSERT INTO recensioni (user_type, comment, rating, approved)
            VALUES (:user_type, :comment, :rating, :approved)
        ";
        $stmt = $pdo->prepare($insertSQL);
        $stmt->execute([
            ':user_type' => $form['user-type'],
            ':comment' => $form['comment'],
            ':rating' => $form['rating'],
            ':approved' => $form['approved'],
        ]);
        // echo "Data inserted successfully!";
        header("Location: ok.html");
    } else {
        echo "Invalid data provided.";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
