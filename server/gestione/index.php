<?php
session_start();

if (!$_SESSION['sono_guido']) {
    header('Location: login.php');
    exit;
}

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

    $stmt = $pdo->prepare("SELECT * FROM recensioni WHERE approved = 'not yet'");
    $stmt->execute();
    $messages = $stmt->fetchAll();

    $approvedStmt = $pdo->prepare("SELECT * FROM recensioni WHERE approved = 'yes'");
    $approvedStmt->execute();
    $approvedMessages = $approvedStmt->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
        $id = $_POST['id'];
        $approved = $_POST['approved'];

        $updateSQL = "UPDATE recensioni SET approved = :approved WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSQL);
        $updateStmt->execute([
            ':approved' => $approved,
            ':id' => $id,
        ]);

        header("Location: ./");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
        $id = $_POST['id'];

        $updateSQL = "UPDATE recensioni SET approved = 'no' WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSQL);
        $updateStmt->execute([
            ':id' => $id,
        ]);

        header("Location: ./");
        exit;
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}


define('MANDA_LIVE', '<article class="text-end">' .
    '<h2>Carica sul sito</h2>' .
    '<a href="send_outside.php" id="manda-live-btn"><button>Manda live</button></a>' .
    '<p style="line-height: 1.25;margin-top:.5rem">Dopo aver fatto tutte le modifiche<br>' .
    'ricordati di premere il <em>Manda live</em><br>' .
    'per sincronizzare con il sito</p>' .
    '</article>');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Recensioni</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.red.min.css">
    <link rel="stylesheet" href="../assets/css/gestione.css">
</head>

<body>

    <header>
        <h1>Gestionale Guido</h1>
    </header>

    <main>


        <?= MANDA_LIVE ?>



        <h2>Approvazione</h2>
        <?php if (empty($messages)) : ?>
            <article>
                <p>Nessuna recensione da approvare</p>
            </article>
        <?php else: ?>
            <div class="overflow-auto">
                <table>
                    <tr>
                        <th>Categoria</th>
                        <th>Commento</th>
                        <th>Valutazione</th>
                        <th>Azione</th>
                    </tr>
                    <?php foreach ($messages as $message) : ?>
                        <tr>
                            <?php
                            $userType = str_replace('-', ' ', $message['user_type']);
                            $userType = ucfirst($userType);
                            $userType = str_replace(' cpm', ' CPM', $userType);
                            ?>
                            <td><?php echo htmlspecialchars($userType); ?></td>
                            <td><?php echo htmlspecialchars($message['comment']); ?></td>
                            <td><?php echo $message['rating']; ?>/5</td>
                            <td>
                                <form action="./" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                    <select name="approved">
                                        <option value="yes">Approva</option>
                                        <option value="no">Rifiuta</option>
                                    </select>
                                    <button type="submit" name="approve">Conferma</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>

        <h2>Recensioni Approvate</h2>
        <?php if (empty($approvedMessages)) : ?>
            <article>
                <p>Nessuna recensione approvata</p>
            </article>
        <?php else: ?>
            <div class="overflow-auto">
                <table>
                    <tr>
                        <th>Categoria</th>
                        <th>Commento</th>
                        <th>Valutazione</th>
                        <th>Rimuovi</th>
                    </tr>
                    <?php foreach ($approvedMessages as $message) : ?>
                        <tr>
                            <?php
                            $userType = str_replace('-', ' ', $message['user_type']);
                            $userType = str_replace('cpm', 'CPM', $userType);
                            $userType = ucfirst($userType);
                            ?>
                            <td><?php echo htmlspecialchars($userType); ?></td>
                            <td><?php echo htmlspecialchars($message['comment']); ?></td>
                            <td><?php echo $message['rating']; ?></td>
                            <td>
                                <form action="./" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" name="remove">Rimuovi</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </main>






    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let aHref = document.getElementById('manda-live-btn');
            let btn = aHref.querySelector('button');
            aHref.addEventListener('click', (e) => {
                e.preventDefault();
                let text = btn.textContent;
                btn.setAttribute('aria-busy', 'true');
                btn.textContent = '';
                aHref.disabled = true;
                fetch(aHref.href)
                    .then(res => res.text())
                    .then(res => {
                        aHref.removeAttribute('aria-busy');
                        if (res.trim() !== 'ok') alert(res);
                        aHref.disabled = false;

                        btn.textContent = "OK!";
                        btn.setAttribute('aria-busy', 'false');

                        setTimeout(() => btn.textContent = text, 1000);
                    })
                    .catch(() => {
                        btn.removeAttribute('aria-busy');
                        btn.textContent = text;
                        alert('Errore? Riprova!');
                    });
            });
        });
    </script>


</body>

</html>