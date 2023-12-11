<?php
require_once 'indexD.php';
class Departements {
    public $departmentID;
    public $departmentName;
    public $departmentHead;
    public $location;
    public $connect;

    function __construct($id, $name, $head, $location, $connect) {
        $this->departmentID = $id;
        $this->departmentName = $name;
        $this->departmentHead = $head;
        $this->location = $location;
        $this->connect = $connect;
    }

    function create_table() {
        $request = "CREATE TABLE IF NOT EXISTS `departements` (
                        `departmentID` int(11) PRIMARY KEY,
                        `departmentName` varchar(255),
                        `departmentHead` varchar(255),
                        `location` varchar(255)
                    )";
        $x = $this->connect->prepare($request);
        $e = $x->execute();
        if (!$e) {
            echo "Table departements not created!!<br>";
        } else {
            echo "Table departements bien créée !! ";
        }
    }
    function insert() {
        // Vérifier si le département avec le même ID existe déjà
        $checkRequest = "SELECT COUNT(*) AS count FROM departements WHERE DepartmentID = :departmentID";
        $checkStmt = $this->connect->prepare($checkRequest);
        $checkStmt->bindParam(':departmentID', $this->departmentID, PDO::PARAM_INT);
        $checkStmt->execute();
        $rowCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
        if ($rowCount > 0) {
            echo "Le département avec l'ID " . $this->departmentID . " existe déjà !<br>";
        } else {
            // Effectuer l'insertion uniquement si l'ID n'existe pas encore
            $request = "INSERT INTO `departements` (`DepartmentID`, `DepartmentName`, `DepartmentHead`, `Location`) values  
                        (:departmentID, :departmentName, :departmentHead, :location)";
            $insertStmt = $this->connect->prepare($request);
            $insertStmt->bindParam(':departmentID', $this->departmentID, PDO::PARAM_INT);
            $insertStmt->bindParam(':departmentName', $this->departmentName, PDO::PARAM_STR);
            $insertStmt->bindParam(':departmentHead', $this->departmentHead, PDO::PARAM_STR);
            $insertStmt->bindParam(':location', $this->location, PDO::PARAM_STR);
    
            $e = $insertStmt->execute();
    
            // Afficher le résultat même en cas d'erreur
            if ($e) {
                echo "Les données du département ont été insérées avec succès !! <br>";
            } else {
                echo "Les données du département n'ont pas pu être insérées !! <br>";
            }
        }
    }
    function displayDepartments() {
        $request = "SELECT * FROM departements";
        $stmt = $this->connect->prepare($request);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<h2>Liste des départements :</h2>";
            echo "<ul>";
            foreach ($result as $row) {
                echo "<li>ID : " . ($row['departmentID'] ?? 'N/A') . ", Nom : " . ($row['departmentName'] ?? 'N/A') . ", Chef de département : " . ($row['departmentHead'] ?? 'N/A') . ", Localisation : " . ($row['location'] ?? 'N/A') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "Aucun département trouvé.";
        }
    }
    }
echo "------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>";
$departement1 = new Departements("1", "Computer Science", "Dr. Smith", "Building A", $db->connexion);
$departement1->insert();
$departement2 = new Departements("2", "GTR", "Belkhire", "ST", $db->connexion);
$departement2->insert();
$departement1->displayDepartments();
?>