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
            <a href="#">
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
                <a href="?addseries">Add Series</a>
            </div>
            <a href="?logout">
                <img src="../web/bilder/logout_icon.png" class="nav-icon">
                Abmelden
        </a>
        </div>


    <form>
    <!-- Formular für Genre-Auswahl -->
    <label for="genre">Genre auswählen:</label>
    <select name="genre_id" id="genre" onchange="storeGenreId()">
        <!-- Optionen werden durch JavaScript hinzugefügt -->
    </select>
    <!-- Button zum Anzeigen der Filme -->
    <button type="button" onclick="showMovies(); showSeries();">Anzeigen</button>
    </form>

     <!-- Container für die Filme -->
     <div id="movies-container"></div>

     <!-- Container für die Serien -->
     <div id="serien-container"></div>
    

    <footer>
        <p id="Authors">Authors: Mohammad Freej <br> Dario Kasumovic Carballeira <br> Mohammad Jalal Mobasher Goljani <br> Katharina Nolte</p>
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
        var genreId = null;
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch genres from the API and populate the genre select box
            fetch('http://127.0.0.1:5000/api/genres')
                .then(response => response.json())
                .then(data => {
                    const genreSelect = document.getElementById('genre');
                    genreID = genreSelect;
                    data.forEach(genre => {
                        const option = document.createElement('option');
                        option.value = genre.id;
                        option.textContent = genre.genre;
                        genreSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching genres:', error));
        });


        function storeGenreId() {
            // Funktion zum Speichern der genre_id in einer globalen Variablen
            genreId = document.getElementById("genre").value;
            alert(genreId);
        }

        function showMovies() {
        // Funktion zum Anzeigen der Filme für das ausgewählte Genre
        if (genreId === null) {
            alert("Bitte wählen Sie ein Genre aus.");
            return;
        }
        fetch("http://localhost:5000/api/genres/show/" + genreId)
            .then(response => response.json())
            .then(movies => {
                var moviesContainer = document.getElementById("movies-container");
                moviesContainer.innerHTML = "<h2>Filme</h2><table><thead><tr><th>Titel</th><th>Erscheinungsjahr</th><th>Dauer</th><th>IMDb-Link</th></tr></thead><tbody>";
                movies.forEach(function (movie) {
                    moviesContainer.innerHTML += "<tr><td>" + movie.title + "</td> <td>" + movie.erscheinungsjahr + "</td> <td>" + movie.dauer + "</td> <td><a href='" + movie.link + "'>" + movie.link + "</a></td> </tr><br><br>";
                });
                moviesContainer.innerHTML += "</tbody></table>";
            })
            .catch(error => console.error('Error fetching movies:', error));
        }

        function showSeries() {
        if (genreId === null) {
            alert("Bitte wählen Sie ein Genre aus.");
            return;
        }
        fetch("http://localhost:5000/api/genres/shows/" + genreId)
            .then(response => response.json())
            .then(serien => {
                var serienContainer = document.getElementById("serien-container");
                serienContainer.innerHTML = "<h2>Serien</h2><table><thead><tr><th>Titel</th><th>Erscheinungsjahr</th><th>Staffeln</th><th>IMDb-Link</th></tr></thead><tbody>";
                serien.forEach(function (serie) {
                    serienContainer.innerHTML += "<tr><td>" + serie.title + "</td> <td>" + serie.erscheinungsjahr + "</td> <td>" + serie.staffeln + "</td> <td><a href='" + serie.link + "'>" + serie.link + "</a></td> </tr><br><br>";
                });
                serienContainer.innerHTML += "</tbody></table>";
            })
            .catch(error => console.error('Error fetching series:', error));
        }

    </script>
</body>
</html>
