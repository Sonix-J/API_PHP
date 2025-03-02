<?php
require_once "config\Database.php";
require_once "model\Student.php";
require_once "contract\IBaseRepository.php";
require_once "services\Validate.php";

class StudentRepositories implements IBaseRepository {
    private $databaseContext;
    private $table;
    private $Validate;

    public function __construct($database_context, $table) {
        $this->databaseContext = $database_context; 
        $this->table = $table;
        $this->Validate = new Validate();
    }

    public function getAllStud() : array {
            $query = "SELECT * FROM {$this->table}";
            $result = $this->executeQuery($query, []);
            return $this->BuildResultList($result);
    }

    public function getStudById(?int $id = null) : ?Student {
        if ($id) {
            $query = "SELECT * FROM {$this->table} WHERE STUD_ID = :id";
            $result = $this->executeQuery($query, [':id' => $id]);
            $this->Validate->validateExistingStudent($result,$id);
            return $this->BuildResult($result);
        }
        return null;
    }

    public function addStud($entity) {
        $this->Validate->validateStudent($entity,'POST');

        $STUD_NAME = $entity['STUD_NAME'];
        $STUD_MIDTERM = $entity['STUD_MIDTERM'];
        $STUD_FINAL = $entity['STUD_FINAL'];

        $STUD_GRADE = $this->Validate->calculateGrade($STUD_MIDTERM, $STUD_FINAL);
        $STUD_STATUS = $this->Validate->calculateStatus($STUD_GRADE);

        $student = new Student();
        $student->STUD_NAME = $STUD_NAME;
        $student->STUD_MIDTERM = $STUD_MIDTERM;
        $student->STUD_FINAL = $STUD_FINAL;
        $student->STUD_GRADE =$STUD_GRADE;
        $student->STUD_STATUS = $STUD_STATUS;

        $query = "INSERT INTO {$this->table} (STUD_NAME, STUD_MIDTERM, STUD_FINAL, STUD_GRADE, STUD_STATUS) VALUES (:name, :md_score, :STUD_FINAL, :STUD_GRADE, :stat)";
        $params = [
            ':name' => $student->STUD_NAME,
            ':md_score' => $student->STUD_MIDTERM,
            ':STUD_FINAL' => $student->STUD_FINAL,
            ':STUD_GRADE' => $student->STUD_GRADE,
            ':stat' => $student->STUD_STATUS
        ];

        $this->executeQuery($query, $params);

        return $student;
    }

    public function updateStud($id, $entity): void {
        $existingStudent = $this->getStudById($id);
        if(!$existingStudent){
            return;
        }
        $this->Validate->validateStudent($entity,'PUT');

        $STUD_MIDTERM = $entity['STUD_MIDTERM'];
        $STUD_FINAL = $entity['STUD_FINAL'];

       $STUD_GRADE = $this->Validate->calculateGrade($STUD_MIDTERM, $STUD_FINAL);
        $STUD_STATUS = $this->Validate->calculateStatus($STUD_GRADE);

        $query = "UPDATE {$this->table} 
            SET STUD_MIDTERM = :md_score, STUD_FINAL = :STUD_FINAL, 
            STUD_GRADE = :STUD_GRADE, STUD_STATUS = :stat 
            WHERE STUD_ID = :id";
        
        $params = [
            ':id' => $id,
            ':md_score' => $STUD_MIDTERM,
            ':STUD_FINAL' => $STUD_FINAL,
            ':STUD_GRADE' =>$STUD_GRADE,
            ':stat' => $STUD_STATUS
        ];

        $this->executeQuery($query, $params);
        echo json_encode(["success" => "Student with ID {$id} updated successfully"]);
    }

    public function deleteStud(int $id): void {
        $existingStudent = $this->getStudById($id);

        if (!$existingStudent) {
            echo json_encode(["error" => "Student with ID {$id} not found"]);
            return;
        }

        $query = "DELETE FROM {$this->table} WHERE STUD_ID = :id";
        $params = [':id' => $id];
        $this->executeQuery($query, $params);
        echo json_encode(["success" => "Student with ID {$id} deleted successfully"]);
    }

    public function BuildResult(?array $result) : ?Student {
        if (!$result || empty($result[0])) {
            return null;
        }

        $row = $result[0];
        $stud = new Student();
        $stud->STUD_ID = $row['STUD_ID'];
        $stud->STUD_NAME = $row['STUD_NAME'];
        $stud->STUD_MIDTERM = $row['STUD_MIDTERM'];
        $stud->STUD_FINAL = $row['STUD_FINAL'];
        $stud->STUD_GRADE = $row['STUD_GRADE'];
        $stud->STUD_STATUS = $row['STUD_STATUS'];

        return $stud;
    }

    public function BuildResultList(array $result) : array {
        $students = [];

        foreach ($result as $row) {
            $student = new Student();
            $student->STUD_ID = $row['STUD_ID'];
            $student->STUD_NAME = $row['STUD_NAME'];
            $student->STUD_MIDTERM = $row['STUD_MIDTERM'];
            $student->STUD_FINAL = $row['STUD_FINAL'];
            $student->STUD_GRADE = $row['STUD_GRADE'];
            $student->STUD_STATUS = $row['STUD_STATUS'];
            $students[] = $student;
        }
        return $students;
    }

    public function executeQuery(string $query, array $params) {
        $statementObject = $this->databaseContext->prepare($query);
        $statementObject->execute($params);

        if (stripos($query, "SELECT") === 0) {
            return $statementObject->fetchAll(PDO::FETCH_ASSOC);
        }
        return null;
    }
}
?>