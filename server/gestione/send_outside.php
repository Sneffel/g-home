<?php

session_start();

if (!$_SESSION['sono_guido']) {
    header('Location: login.php');
    exit;
}


$dbHost = 'sql301.infinityfree.com';
$dbName = 'if0_38085340_guido';
$dbUser = 'if0_38085340';
$dbPass = 'ZRNs2SacbaKlt';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
$conn->set_charset('utf8mb4');  // Use utf8mb4 to support a wider range of characters (including emojis)

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = "recensioni";
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }


    $url = "https://txt.altervista.org/clienti/update_table.php";
    $password = "sticazzi|che|pas5sword|!";



    // Prepare the data to send as POST fields
    $postData = http_build_query([
        'table' => "guido_recensioni",
        'data' => serialize($data), // Serialize data instead of JSON encoding
        'password' => $password
    ]);


    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Send as form data
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded', // This tells the server it's form data
        'Content-Length: ' . strlen($postData)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // echo $response;
} else {
    echo "No data found.";
}

$conn->close();
