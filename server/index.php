<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recensisci Guido</title>



    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.red.min.css">


    <style>
        <?php require 'assets/css/recensioni.css' ?>
    </style>
</head>

<body class="bg-dark text-light">




    <header>
        <h1>Recensisci Guido</h1>
    </header>

    <main>

        <form action="recensioni/ricevi.php">
            <div>
                <label for="user-type">Sono uno</label>
                <select id="user-type" name="user-type">
                    <option value="studente-cpm">Studente CPM</option>
                    <option value="studente-esterno">Studente esterno</option>
                    <option value="altro">Altro</option>
                </select>



            </div>
            <div>
                <label for="comment" class="form-label text-body-emphasis">Scrivi un commento</label>
                <textarea id="comment" name="comment" class="form-control" rows="4"
                    placeholder="Inserisci il tuo commento qui..." required></textarea>
            </div>
            <div class="ratings">
                <label for="rating" class="form-label text-body-emphasis mb-0">Valutazione <svg id="tick" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                    </svg></label>

                <div class="rating d-flex justify-content-center">
                    <?php
                    for ($i = 5; $i >= 1; $i--) {
                        $checked = $i === 5 ? 'checked' : ''; // Default the first radio (5 stars) as checked
                        $title = "$i stelle";
                        echo "<input type='radio' name='rating' id='star$i' value='$i' $checked>" .
                            "<label for='star$i' title='$title'>★</label>";
                    }
                    ?>
                </div>



            </div>


            <div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </div>
        </form>
        </div>

        <script>
            function showTick() {
                let tick = document.querySelector('#tick');
                console.log(tick)
                tick.style.opacity = 1;
                setTimeout(() => {
                    tick.style.opacity = 0;
                }, 1500);
            }

            let labels = document.querySelectorAll('.rating label')
            labels.forEach(radio => {
                radio.addEventListener('click', (event) => {
                    showTick();
                });
            });
        </script>
</body>

</html>