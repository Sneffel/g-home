<?php
header('Access-Control-Allow-Origin: https://guidogiordana.net');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}


header('Content-Type: application/json');

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

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;

    $offset = ($page - 1) * $limit;

    $countStmt = $pdo->query("SELECT COUNT(*) FROM recensioni WHERE approved = 'yes'");
    $totalCount = $countStmt->fetchColumn();
    $totalPages = ceil($totalCount / $limit);

    // Query for approved entries with pagination
    $stmt = $pdo->prepare("SELECT * FROM recensioni WHERE approved = 'yes' LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll();

    // Remove the 'id' field from each message
    foreach ($messages as &$message) {
        unset($message['id']);
    }

    // Determine pagination status
    $previousPage = ($page > 1);
    $nextPage = ($page < $totalPages);

    // Create the response
    $response = [
        'data' => $messages,
        'pagination' => [
            'previous_page' => $previousPage,
            'next_page' => $nextPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
        ],
    ];

    // Send the response as JSON
    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
