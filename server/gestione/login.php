<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if ($password === 'sonoguido') { # cspell: disable-line
        $_SESSION['sono_guido'] = true;
        header('Location: ./');
        exit;
    } else {
        $message =  "Password errata";
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.red.min.css">
</head>

<body>

    <header>
        <h1>Login</h1>
    </header>

    <main>

        <?php
        if ($message) {
            echo "<article>$message</article>";
        }
        ?>

        <form method="POST" action="">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Accedi</button>
        </form>

    </main>

</body>

</html>