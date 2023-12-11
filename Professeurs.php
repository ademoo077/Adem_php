<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre titre</title>
    <style>
        /* Styles spécifiques pour le formulaire */
form {
    margin: 20px 0;
}

input[type="text"] {
    padding: 5px;
    width: 200px;
}

input[type="submit"] {
    padding: 8px 15px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

/* Styles pour la liste des professeurs */
#professors-list {
    list-style: none;
    padding: 0;
}

.professor-item {
    border: 1px solid #ccc;
    margin: 10px 0;
    padding: 10px;
}

/* Styles pour les sections des professeurs */
.professor-section {
    margin-top: 30px;
}


        body {
            font-family: Arial, sans-serif;
        }

        form {
            margin-bottom: 20px;
        }

        /* Ajoutez d'autres styles selon vos besoins */
    </style>
</head>
<body>
    <?php
    require_once 'indexP.php';
    class Professeurs {
        public $professorID;
        public $prenom, $nom, $email, $departmentID;
        public $connect;
    
        function __construct($id, $prenom, $nom, $email, $departmentID, $connect) {
            $this->professorID = $id;
            $this->prenom = $prenom;
            $this->nom = $nom;
            $this->email = $email;
            $this->departmentID = $departmentID;
            $this->connect = $connect;
        }
    
        function create_table() {
            $request = "CREATE TABLE IF NOT EXISTS `professeurs` (
                            `professorID` int(11) PRIMARY KEY,
                            `prenom` varchar(50),
                            `nom` varchar(50),
                            `email` varchar(255),
                            `departmentID` int(11),
                            FOREIGN KEY (departmentID) REFERENCES departements(departmentID)
                        )";
            $x = $this->connect->prepare($request);
            $e = $x->execute();
            if (!$e) {
                echo "Table professeurs not created!!<br>";
            } else {
                echo "Table professeurs bien créée !! ";
            }
        }
        function insert() {
            $request = "INSERT IGNORE INTO `professeurs` (`ProfessorID`, `Prenom`, `Nom`, `Email`, `DepartmentID`) values  
                        ('" . $this->professorID . "','" . $this->prenom . "','" . $this->nom . "','" . $this->email . "','" . $this->departmentID . "')";
                
            try {
                $x = $this->connect->prepare($request);
                $e = $x->execute();
                if (!$e) {
                    echo "Les données du professeur n'ont pas pu être insérées !! <br>";
                } else {
                    echo "Les données du professeur ont été insérées avec succès !! <br>";
                }
            } catch (PDOException $ex) {
                if ($ex->errorInfo[1] == 1062) {
                    // Error code 1062 indicates a duplicate entry (primary key violation)
                    echo "Un professeur avec l'ID " . $this->professorID . " existe déjà dans la base de données. Veuillez choisir un autre ID.<br>";
                } else {
                    // Handle other PDO exceptions
                    echo "Une erreur s'est produite lors de l'insertion du professeur: " . $ex->getMessage() . "<br>";
                }
            }
        }
        
        function getProfessorsWithMostCourses() {
            $request = "SELECT p.professorID, p.prenom, p.nom, p.email, COUNT(c.courseID) AS courseCount
                        FROM professeurs p
                        JOIN cours c ON p.professorID = c.professorID
                        GROUP BY p.professorID
                        ORDER BY courseCount DESC";
            
            $stmt = $this->connect->prepare($request);
            $stmt->execute();
            
            $professorsWithMostCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $professorsWithMostCourses;
        }
        function updateProfessorID($newProfessorID) {
            $oldProfessorID = $this->professorID;
    
            $request = "UPDATE `professeurs` SET `ProfessorID` = :newProfessorID WHERE `ProfessorID` = :oldProfessorID";
            
            $stmt = $this->connect->prepare($request);
            $stmt->bindParam(':newProfessorID', $newProfessorID, PDO::PARAM_INT);
            $stmt->bindParam(':oldProfessorID', $oldProfessorID, PDO::PARAM_INT);
    
            $e = $stmt->execute();
    
            if (!$e) {
                echo "L'ID du professeur n'a pas pu être mis à jour !! <br>";
            } else {
                echo "L'ID du professeur a été mis à jour avec succès !! <br>";
            }
        }
        function getAllProfessors() {
            $request = "SELECT * FROM professeurs";
            $stmt = $this->connect->prepare($request);
            $stmt->execute();
    
            $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $professors;
        }
        function getProfessorsTeachingMostCourses() {
            $request = "SELECT p.professorID, p.prenom, p.nom, p.email, COUNT(c.courseID) AS courseCount
                        FROM professeurs p
                        INNER JOIN cours c ON p.professorID = c.professorID
                        GROUP BY p.professorID
                        ORDER BY courseCount DESC
                        LIMIT 5"; // Change the LIMIT value based on how many professors you want to display
        
            $stmt = $this->connect->prepare($request);
            $stmt->execute();
        
            $professorsTeachingMostCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $professorsTeachingMostCourses;
        }
    } 
    
    $professeur1 = new Professeurs("232", "John", "Doe", "john.doe@example.com", "1", $db->connexion);
    $professeur1->create_table();
    $professeur1->insert();
    
    // Create and insert the second professor
    $professeur2 = new Professeurs("8", "Jane", "Smith", "jane.smith@example.com", "2", $db->connexion);
    $professeur2->insert();
    
    // Create and insert the third professor
    $professeur3 = new Professeurs("3283", "Bob", "Johnson", "bob.johnson@example.com", "1", $db->connexion);
    $professeur3->insert();
    echo "<form method='post'>";
    echo "Nouvel ID du professeur: <input type='text' name='newProfessorID' />";
    echo "<input type='submit' value='Mettre à jour l\'ID' />";
    echo "</form>";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newProfessorID = isset($_POST['newProfessorID']) ? $_POST['newProfessorID'] : null;
    
        if ($newProfessorID !== null) {
            // Update the professor's ID
            $professeur1->updateProfessorID($newProfessorID);
        }
    }
    $allProfessors = $professeur1->getAllProfessors();
    
    // Get professors with the most courses
    $professorsWithMostCourses = $professeur1->getProfessorsWithMostCourses();
    
    if (!empty($professorsWithMostCourses)) {
        echo "Professors with the most courses:<br>";
        foreach ($professorsWithMostCourses as $professor) {
            echo "------------------------<br>";
            echo "ID: " . $professor['professorID'] . "<br>";
            echo "Prénom: " . $professor['prenom'] . "<br>";
            echo "Nom: " . $professor['nom'] . "<br>";
            echo "Email: " . $professor['email'] . "<br>";
            echo "Nombre de cours enseignés: " . $professor['courseCount'] . "<br>";
        }
        echo "------------------------<br>";
    } else {
        echo "Aucun professeur trouvé. <br>";
    }
    $professorsTeachingMostCourses = $professeur1->getProfessorsTeachingMostCourses();
    
    if (!empty($professorsTeachingMostCourses)) {
        echo "Professors currently teaching the most courses:<br>";
        foreach ($professorsTeachingMostCourses as $professor) {
            echo "------------------------<br>";
            echo "ID: " . $professor['professorID'] . "<br>";
            echo "Prénom: " . $professor['prenom'] . "<br>";
            echo "Nom: " . $professor['nom'] . "<br>";
            echo "Email: " . $professor['email'] . "<br>";
            echo "Nombre de cours enseignés: " . $professor['courseCount'] . "<br>";
        }
        echo "------------------------<br>";
    } else {
        echo "Aucun professeur trouvé.";
    }
    ?>
    
</body>
</html>
