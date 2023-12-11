<?php
require_once 'indexE.php';
class Etudiants {
    public $studentID;
    public $prenom, $nom, $date_de_naissance;
    public $email, $DepartmentID;
    public $connect;

    function __construct($id, $prenom, $nom, $date_naissance, $email, $departmentID, $connect) {
        $this->studentID = $id;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->date_de_naissance = $date_naissance;
        $this->email = $email;
        $this->DepartmentID = $departmentID;
        $this->connect = $connect;
    }

    function create_table() {
        $request = "CREATE TABLE IF NOT EXISTS `etudiants` (
                        `StudentID` int(11) PRIMARY KEY,
                        `Prenom` varchar(50),
                        `Nom` varchar(50),
                        `Date_de_naissance` date,
                        `Email` varchar(255),
                        `DepartmentID` int(11),
                        FOREIGN KEY (DepartmentID) REFERENCES departements(DepartmentID)
                    )";
        $x = $this->connect->prepare($request);
        $e = $x->execute();
        if (!$e) {
            echo "Table etudiants not created!!<br>";
        } else {
            echo "Table etudiants bien créée !! ";
        }
    }
    function insert() {
        // Vérifier si l'étudiant avec le même ID existe déjà
        $checkRequest = "SELECT COUNT(*) AS count FROM etudiants WHERE StudentID = :studentID";
        $checkStmt = $this->connect->prepare($checkRequest);
        $checkStmt->bindParam(':studentID', $this->studentID, PDO::PARAM_INT);
        $checkStmt->execute();
        $rowCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
        if ($rowCount > 0) {
            echo "L'étudiant avec l'ID " . $this->studentID . " existe déjà !<br>";
        } else {
            // Effectuer l'insertion uniquement si l'ID n'existe pas encore
            $request = "INSERT INTO `etudiants` (`StudentID`, `Prenom`, `Nom`, `Date_de_naissance`, `Email`, `DepartmentID`) values  
                        (:studentID, :prenom, :nom, :date_naissance, :email, :departmentID)";
            $insertStmt = $this->connect->prepare($request);
            $insertStmt->bindParam(':studentID', $this->studentID, PDO::PARAM_INT);
            $insertStmt->bindParam(':prenom', $this->prenom, PDO::PARAM_STR);
            $insertStmt->bindParam(':nom', $this->nom, PDO::PARAM_STR);
            $insertStmt->bindParam(':date_naissance', $this->date_de_naissance, PDO::PARAM_STR);
            $insertStmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $insertStmt->bindParam(':departmentID', $this->DepartmentID, PDO::PARAM_INT);
    
            $e = $insertStmt->execute();
    
            // Afficher le résultat même en cas d'erreur
            if ($e) {
                echo "Les données de l'étudiant ont été insérées avec succès !! <br>";
            } else {
                echo "Les données de l'étudiant n'ont pas pu être insérées !! <br>";
            }
        }
    }
    function getTotalCreditHours($courseIDs = []) {
        // Construire la partie de la requête pour filtrer par les ID de cours si des ID sont fournis
        $courseCondition = '';
        if (!empty($courseIDs)) {
            $courseCondition = " AND c.CourseID IN (" . implode(',', $courseIDs) . ")";
        }
    
        $request = "SELECT SUM(c.CreditHours) AS totalCreditHours
                    FROM inscriptions i
                    JOIN cours c ON i.CourseID = c.CourseID
                    WHERE i.StudentID = :studentID" . $courseCondition;
    
        $stmt = $this->connect->prepare($request);
        $stmt->bindParam(':studentID', $this->studentID, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result !== false && isset($result['totalCreditHours'])) {
                return $result['totalCreditHours'];
            } else {
                return 0; // Aucun cours trouvé pour cet étudiant
            }
        } else {
            // Gestion des erreurs d'exécution de la requête
            $errorInfo = $stmt->errorInfo();
            echo "Erreur lors de l'exécution de la requête : " . $errorInfo[2];
            return false;
        }
    }
    function displayStudents() {
        $request = "SELECT * FROM etudiants";
        $stmt = $this->connect->prepare($request);
        $stmt->execute();
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($result) {
            echo "<h2>Liste des étudiants :</h2>";
            echo "<ul>";
            foreach ($result as $row) {
                echo "<li>ID : " . ($row['studentID'] ?? 'N/A') . ", Prénom : " . ($row['prenom'] ?? 'N/A') . ", Nom : " . ($row['nom'] ?? 'N/A') . ", Date de naissance : " . ($row['date_de_naissance'] ?? 'N/A') . ", Email : " . ($row['email'] ?? 'N/A') . ", ID du département : " . ($row['DepartmentID'] ?? 'N/A') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "Aucun étudiant trouvé.";
        }
    }

}


$etudiant = new Etudiants("1", "Adem", "hezil", "14_juin_2003", "HezilAdem1406@gmail.com", "1", $db->connexion);
$etudiant->create_table();
$etudiant->insert();
$etudiant2 = new Etudiants("3", "Alice", "Smith", "10_octobre_2001", "alice.smith@example.com", "1", $db->connexion);
$etudiant2->insert();
$etudiant2 = new Etudiants("6", "Alice", "Smith", "10_octobre_2001", "alice.smith@example.com", "1", $db->connexion);
$etudiant2->insert();
echo "------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>";
// Étudiant 3
$courseIDs = [1, 2, 3]; // Remplacez cela par les ID des cours que vous souhaitez inclure
$totalCreditHours = $etudiant->getTotalCreditHours($courseIDs);

// Affichez le résultat
echo "Le nombre total d'heures de crédit prises par l'étudiant est : $totalCreditHours <br> ";
$etudiant->displayStudents();
?>