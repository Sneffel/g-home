<?php
setlocale(LC_TIME, 'it_IT.UTF-8'); // For date and time formatting in Italian
setlocale(LC_ALL, 'it_IT.UTF-8'); // For all locale settings (currency, time, etc.)
date_default_timezone_set('Europe/Rome');


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
    $pdo->exec("SET time_zone = '+01:00'"); // Rome's time zone offset is UTC+1


    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS recensioni (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_type VARCHAR(255) NOT NULL,
            comment TEXT NOT NULL,
            rating INT NOT NULL,
            approved ENUM('not yet', 'yes', 'no') DEFAULT 'not yet',
            created_at DATETIME DEFAULT NULL
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
    INSERT INTO recensioni (user_type, comment, rating, approved, created_at)
    VALUES (:user_type, :comment, :rating, :approved, :created_at)
";

        $createdAt = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare($insertSQL);
        $stmt->execute([
            ':user_type' => $form['user-type'],
            ':comment' => $form['comment'],
            ':rating' => $form['rating'],
            ':approved' => $form['approved'],
            ':created_at' => $createdAt,
        ]);

        header("Location: ok.html");
        exit;
    } else {
        echo "Invalid data provided.";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
