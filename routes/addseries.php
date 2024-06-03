<?php
// include 'variables.php';

session_start();

if(isset($_GET['library'])) {

    header("Location: library.php");
    exit;
}

if(isset($_GET['mainpage'])) {

    header("Location: mainpage.php");
    exit;
}

if(isset($_GET['genres'])) {

    header("Location: genres.php");
    exit;
}

if(isset($_GET['addmovie'])) {

    header("Location: add.php");
    exit;
}

if(isset($_GET['addseries'])) {

    header("Location: addseries.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../web/css/style.css">    <title>SteamDB</title>
    <style>
        /* CSS-Regeln für die Seite */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Die Mindesthöhe des Body-Elements entspricht der Höhe des Viewports */
        }

        main {
            flex: 1; /* Der Hauptinhalt dehnt sich aus, um den verfügbaren Platz zwischen dem Header und dem Footer auszufüllen */
        }

        footer {
            text-align: center; /* Zentriere den Text im Footer */
            padding: 20px; /* Füge einen Innenabstand hinzu */
            margin-top: auto; /* Setze den oberen Außenabstand auf "auto", um den Footer an den unteren Rand der Seite zu drücken */
        }
    </style>
</head>
<body>
     <!-- Logo oben rechts -->
     <div id="logo-container">
        <img id="logo" src="../web/bilder/logo.png" alt="logo">
    </div>
    <span onclick="openNav()">&#9776;</span>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="?mainpage">
            <img src="../web/bilder/home_icon.png" class="nav-icon">
            Hauptseite
            </a>
            <a href="?library">
                <img src="../web/bilder/library_icon.png" class="nav-icon">
                Meine Liste
            </a>
            <a href="?genres">
                <img src="../web/bilder/genres_icon.png" class="nav-icon">
                Genres
            </a>
            <button class="dropdown-btn" style="padding: 8px 8px 8px 32px;
                text-decoration: none;
                font-size: 20px;
                color: #818181;
                display: block;
                border: none;
                background: none;
                width:100%;
                text-align: left;
                cursor: pointer;
                outline: none;">
                    <img src="../web/bilder/add_icon.png" class="nav-icon">
                    Add
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="?addmovie">Add Movie</a>
                <a href="#">Add Series</a>
            </div>
            <a href="?logout">
                <img src="../web/bilder/logout_icon.png" class="nav-icon">
                Abmelden
        </a>
        </div>
    <form  id="add-series-form" action="addseries.php" method="POST">
    <label for="title">Titel der Serie:</label>
    <input type="text" id="title" name="title" required><br><br>
    
    <label for="erscheinungsjahr">erscheinungsjahr:</label>
    <input type="number" id="erscheinungsjahr" name="erscheinungsjahr" required><br><br>

    <label for="genre">Genre:</label>
    <select id="genre" name="genre" required>
        </select><br><br>


    <label for="staffeln">Staffelnanzahl</label><br>
    <input type="number" id="staffeln" name="staffeln" required><br><br>

    <label for="imdb_link">IMDb-Link:</label>
    <input type="url" id="imdb_link" name="imdb_link"><br><br>
    
    <input type="submit" value="Serie hinzufügen">
</form>

    <footer>
        <p id="Authors">Authors: Mohammad Freej <br> Dario Kasumovic Carballeira <br> Mohammad Jalal Mobasher Goljani <br> Katherina Nolte</p>
        <p id="Mail"><a href="mailto:hege@example.com">dario.carballeira98@web.de</a></p>
    </footer>
    <script>
        function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
        }

        function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
        }
        var dropdown = document.getElementsByClassName("dropdown-btn");
        var i;

        for (i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
            } else {
            dropdownContent.style.display = "block";
            }
        });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch genres from the API and populate the genre select box
            fetch('http://127.0.0.1:5000/api/genres')
                .then(response => response.json())
                .then(data => {
                    const genreSelect = document.getElementById('genre');
                    data.forEach(genre => {
                        const option = document.createElement('option');
                        option.value = genre.id;
                        option.textContent = genre.genre;
                        genreSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching genres:', error));
        });

        // Handle form submission
        document.getElementById('add-series-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const seriesData = {};
            formData.forEach((value, key) => seriesData[key] = value);
            seriesData.email = '<?php echo $_SESSION["email"]; ?>';

            fetch('http://127.0.0.1:5000/api/series', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(seriesData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Fehler: ' + data.error);
                } else {
                    alert('Serie erfolgreich hinzugefügt: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fehler:', error);
            });
        });    

        
        document.addEventListener('DOMContentLoaded', function() {
            fetch('http://127.0.0.1:5000/api/login-status')
                .then(response => response.json())
                .then(data => {
                    if (loggedin) {
                        document.getElementById('welcome-message').innerText = 'Willkommen auf der Hauptseite, ' + data.email + '!';
                        document.getElementById('user-email').innerText = 'Eingeloggt als: ' + data.email;
                    } else {
                        alert('Sie sind nicht eingeloggt. Sie werden zur Login-Seite weitergeleitet.');
                        window.location.href = 'index1.php';
                    }
                })
                .catch(error => console.error('Fehler:', error));
        });

        function logout() {
            fetch('http://127.0.0.1:5000/api/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert('Logout erfolgreich');
                    window.location.href = 'index1.php';
                } else {
                    alert('Fehler: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Fehler:', error);
            });
        }
        </script>
</body>
</html>
