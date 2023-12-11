
<?php
require_once 'indexI.php';
class Inscriptions {
    public $registrationID;
    public $studentID;
    public $courseID;
    public $registrationDate;
    public $grade;
    public $connect;

    function __construct($id, $studentID, $courseID, $registrationDate, $grade, $connect) {
        $this->registrationID = $id;
        $this->studentID = $studentID;
        $this->courseID = $courseID;
        $this->registrationDate = $registrationDate;
        $this->grade = $grade;
        $this->connect = $connect;
    }

    function create_table() {
        $request = "CREATE TABLE IF NOT EXISTS `inscriptions` (
                        `registrationID` int(11) PRIMARY KEY,
                        `studentID` int(11),
                        `courseID` int(11),
                        `registrationDate` DATE,
                        `grade` varchar(10),
                        FOREIGN KEY (studentID) REFERENCES etudiants(studentID),
                        FOREIGN KEY (courseID) REFERENCES cours(courseID)
                    )";
        $x = $this->connect->prepare($request);
        $e = $x->execute();
        if (!$e) {
            echo "Table inscriptions not created!!<br>";
        } else {
            echo "Table inscriptions bien créée !! ";
        }
    }
    function insert() {
        $request = "INSERT IGNORE INTO `inscriptions` (`RegistrationID`, `StudentID`, `CourseID`, `RegistrationDate`, `Grade`) values  
                    ('" . $this->registrationID . "','" . $this->studentID . "','" . $this->courseID . "','" . $this->registrationDate . "','" . $this->grade . "')";
        $x = $this->connect->prepare($request);
        $e = $x->execute();
        if (!$e) {
            echo "Les données de l'inscription n'ont pas pu être insérées !! <br>";
        } else {
            echo "Les données de l'inscription ont été insérées avec succès !! <br>";
        }
    }
    
    function getGradeRangeForStudent($studentID) {
        // Récupérer le nom de l'étudiant
        $requestStudentName = "SELECT Prenom, Nom FROM etudiants WHERE StudentID = :studentID";
        $stmtStudentName = $this->connect->prepare($requestStudentName);
        $stmtStudentName->bindParam(':studentID', $studentID, PDO::PARAM_INT);
        $stmtStudentName->execute();
        $studentName = $stmtStudentName->fetch(PDO::FETCH_ASSOC);
    
        // Récupérer la plage de notes
        $request = "SELECT MIN(Grade) AS minGrade, MAX(Grade) AS maxGrade FROM inscriptions WHERE StudentID = :studentID";
        $stmt = $this->connect->prepare($request);
        $stmt->bindParam(':studentID', $studentID, PDO::PARAM_INT);
        $stmt->execute();
    
        $gradeRange = $stmt->fetch(PDO::FETCH_ASSOC);
        $gradeRange['studentName'] = $studentName['Prenom'] . ' ' . $studentName['Nom'];
    
        return $gradeRange;
    }
    function getStudentsByCourseID($courseID) {
        $request = "SELECT e.StudentID, e.Prenom, e.Nom
                    FROM inscriptions i
                    JOIN etudiants e ON i.studentID = e.StudentID
                    WHERE i.courseID = :courseID";
        $stmt = $this->connect->prepare($request);
        $stmt->bindParam(':courseID', $courseID, PDO::PARAM_INT);
        $stmt->execute();
    
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $students;
    }
    function generateRegistrationReportForAllStudents() {
        $request = "SELECT e.Prenom AS studentFirstName, e.Nom AS studentLastName, c.CourseName, i.Grade
                    FROM inscriptions i
                    JOIN etudiants e ON i.StudentID = e.StudentID
                    JOIN cours c ON i.CourseID = c.CourseID";

        $stmt = $this->connect->prepare($request);
        $stmt->execute();

        $registrationReport = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $registrationReport;
    }
   
}

$inscription = new Inscriptions("1", "1", "1", "2023-12-09", "18", $db->connexion);
$inscription->create_table();
$inscription->insert();

$inscription2 = new Inscriptions("2", "3", "1", "2023-12-10", "15", $db->connexion); // Utilisez le même étudiant (1) pour cette inscription
$inscription2->insert();

$rangeNotes = $inscription->getGradeRangeForStudent(1);

echo "Note la plus basse : " . $rangeNotes['minGrade'] . "<br>";
echo "Note la plus élevée : " . $rangeNotes['maxGrade'] . "<br>";

$courseID = 1;
$listeEtudiantsInscrits = $inscription->getStudentsByCourseID($courseID);
if (!empty($listeEtudiantsInscrits)) {
    // Afficher la liste des étudiants
    echo "Liste des étudiants inscrits au cours $courseID :<br>";
    foreach ($listeEtudiantsInscrits as $etudiant) {
        // Vérifier si la clé 'StudentID' existe avant de l'accéder
        $studentID = isset($etudiant['StudentID']) ? $etudiant['StudentID'] : "ID étudiant non défini";

        // Vérifier si la clé 'Prenom' existe avant de l'accéder
        $prenom = isset($etudiant['Prenom']) ? $etudiant['Prenom'] : "Prénom non défini";

        // Vérifier si la clé 'Nom' existe avant de l'accéder
        $nom = isset($etudiant['Nom']) ? $etudiant['Nom'] : "Nom non défini";
        echo "------------------------<br>";
        echo "ID étudiant : $studentID<br>";
        echo "Prénom : $prenom<br>";
        echo "Nom : $nom<br>";
        echo "------------------------<br>";
    }
} else {
    echo "Aucun étudiant inscrit au cours $courseID.<br>";
}
$allRegistrationReport = $inscription->generateRegistrationReportForAllStudents();

if (!empty($allRegistrationReport)) {
    echo "Registration Report for All Students:<br>";
    foreach ($allRegistrationReport as $registration) {
        echo "Student Name: " . $registration['studentFirstName'] . ' ' . $registration['studentLastName'] . "<br>";
        echo "Course Name: " . $registration['CourseName'] . "<br>";
        echo "Grade: " . $registration['Grade'] . "<br>";
        echo "------------------------<br>";
    }
} else {
    echo "Aucune inscription trouvée pour tous les étudiants.";
}


?>