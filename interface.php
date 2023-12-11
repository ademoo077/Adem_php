<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Application Universitaire</title>
    <style>
        
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(45deg, #2193b0, #6dd5ed);
            color: #000;
            font-family: 'Arial', sans-serif;
            animation: gradientAnimation 10s infinite alternate;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            color: #fff;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        nav li {
            margin: 0 10px;
            font-weight: bold;
        }

        nav a {
            text-decoration: none;
            color: #000;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #fff;
        }

        nav a:hover {
            background-color: #ddd;
        }

        main {
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #ddd;
        }

        /* Animation du fond */
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 100% 50%;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Votre Application Universitaire</h1>
        <nav>
            <ul>
                <li><a href="./Database.php">DATABASE CONNECT</a></li>
                <li><a href="./Etudiants.php"> étudiants</a></li>
                <li><a href="./Cours.php"> cours</a></li>
                <li><a href="./Professeurs.php">Gérer les professeurs</a></li>
                <li><a href="./Inscriptions.php">Gérer les inscriptions</a></li>
                <li><a href="./Departements.php">Gérer les départements</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $pagePath = "$page.php";
            if (file_exists($pagePath)) {
                include($pagePath);
            } else {
                echo "La page demandée n'existe pas.";
            }
        } 
        ?>
    </main>

    <footer>
        <!-- Ajoutez le contenu du pied de page si nécessaire -->
    </footer>
</body>

</html>
