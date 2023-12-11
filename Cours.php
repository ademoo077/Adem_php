<?php
require_once 'indexC.php';
class Cours {
    public $courseID;
    public $courseName;
    public $professorID;
    public $departmentID;
    public $creditHours;
    public $connect;

    function __construct($id, $name, $professorID, $departmentID, $creditHours, $connect) {
        $this->courseID = $id;
        $this->courseName = $name;
        $this->professorID = $professorID;
        $this->departmentID = $departmentID;
        $this->creditHours = $creditHours;
        $this->connect = $connect;
    }

    function create_table() {
        $request = "CREATE TABLE IF NOT EXISTS `cours` (
                        `courseID` int(11) PRIMARY KEY,
                        `courseName` varchar(50),
                        `professorID` int(11),
                        `departmentID` int(11),
                        `creditHours` int(11),
                        FOREIGN KEY (professorID) REFERENCES professeurs(professorID),
                        FOREIGN KEY (departmentID) REFERENCES departements(departmentID)
                    )";
        $x = $this->connect->prepare($request);
        $e = $x->execute();
        if (!$e) {
            echo "Table cours not created!!<br>";
        } else {
            echo "Table cours bien créée !! ";
        }
    }
    function insert() {
        $request = "INSERT IGNORE INTO `cours` (`CourseID`, `CourseName`, `ProfessorID`, `DepartmentID`, `CreditHours`) values  
                    ('" . $this->courseID . "','" . $this->courseName . "','" . $this->professorID . "','" . $this->departmentID . "','" . $this->creditHours . "')";
        $x = $this->connect->prepare($request);
        $e = $x->execute();
        if (!$e) {
            echo "Les données du cours n'ont pas pu être insérées !! <br>";
        } else {
            echo "Les données du cours ont été insérées avec succès !! <br>";
        }
    }
    function getCoursesByDepartmentID($departmentID) {
        $request = "SELECT * FROM cours WHERE DepartmentID = :departmentID";
        $stmt = $this->connect->prepare($request);
        $stmt->bindParam(':departmentID', $departmentID, PDO::PARAM_INT);
        $stmt->execute();

        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $courses;
    }
    
}
echo "------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>";
$cours = new Cours("1", "Mathematics", "1", "1", "3", $db->connexion);
$cours->create_table();
$cours->insert();

$cours2 = new Cours("3", "Chemistry", "3", "2", "4", $db->connexion);
$cours2->insert();

// Get the list of courses for DepartmentID = 1
$listeCours1 = $cours->getCoursesByDepartmentID(1);

// Get the list of courses for DepartmentID = 2
$listeCours2 = $cours2->getCoursesByDepartmentID(2);

// Vérifier si la liste des cours n'est pas vide
if (!empty($listeCours1)) {
    // Affichage de la liste des cours pour DepartmentID = 1
    foreach ($listeCours1 as $cours) {
        // Vérifier si la clé 'courseName' existe avant de l'accéder
        $courseName = isset($cours['courseName']) ? $cours['courseName'] : "Nom du cours non défini";

        // Vérifier si la clé 'departmentID' existe avant de l'accéder
        $departmentID = isset($cours['departmentID']) ? $cours['departmentID'] : "ID du département non défini";

        // Afficher le nom du cours et le département
        echo "------------------------<br>";
        echo "Nom du cours : $courseName<br>";
        echo "ID du département : $departmentID<br>";
        echo "------------------------<br>";
    }
} else {
    echo "Aucun cours trouvé pour le département 1.<br>";
}

// Vérifier si la liste des cours n'est pas vide
if (!empty($listeCours2)) {
    // Affichage de la liste des cours pour DepartmentID = 2
    foreach ($listeCours2 as $cours) {
        // Vérifier si la clé 'courseName' existe avant de l'accéder
        $courseName = isset($cours['courseName']) ? $cours['courseName'] : "Nom du cours non défini";

        // Vérifier si la clé 'departmentID' existe avant de l'accéder
        $departmentID = isset($cours['departmentID']) ? $cours['departmentID'] : "ID du département non défini";

        // Afficher le nom du cours et le département
        echo "------------------------<br>";
        echo "Nom du cours : $courseName<br>";
        echo "ID du département : $departmentID<br>";
        echo "------------------------<br>";
    }
} else {
    echo "Aucun cours trouvé pour le département 2.<br>";
}

?>