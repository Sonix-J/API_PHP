<?php
require_once "config\Database.php";
require_once "model\Student.php";
require_once "contract\IBaseRepository.php";

class StudentRepositories implements IBaseRepository {
    private $databaseContext;
    private $table;

    public function __construct($database_context, $table) {
        $this->databaseContext = $database_context; 
        $this->table = $table;
    }

    public function getAllStud() : array {
            $query = "SELECT * FROM {$this->table}";
            $result = $this->executeQuery($query, []);
            return $this->BuildResultList($result);
    }

    public function getStudById(int $id) : ? array {
        
        $query = "SELECT * FROM {$this->table} WHERE STUD_ID = :id";
        $result = $this->executeQuery($query, [':id' => $id]);
        return $this->BuildResult($result);
        

    }

    public function getParameters($entity, $id) {

        $params = [
            ':STUD_MIDTERM' => $entity->STUD_MIDTERM ?? NULL,
            ':STUD_FINAL' => $entity->STUD_FINAL ?? NULL,
            ':STUD_GRADE' => $entity->STUD_GRADE ?? NULL,
            ':STUD_STATUS' => $entity->STUD_STATUS ?? NULL
        ];
    
        if ($id !== NULL) {
            $params[':id'] = $id;
        }

        if (!empty($entity->STUD_NAME) || $entity->STUD_NAME !==null) {
            $params[':STUD_NAME'] = $entity->STUD_NAME;
        }
    
        return $params;
    }
    

    public function addStud($entity) : void {

        $query = "INSERT INTO {$this->table} (STUD_NAME, STUD_MIDTERM, STUD_FINAL, STUD_GRADE, STUD_STATUS) VALUES (:STUD_NAME, :STUD_MIDTERM, :STUD_FINAL, :STUD_GRADE, :STUD_STATUS)";

        $params = $this::getParameters($entity, $id = NULL);
        $this->executeQuery($query, $params);

    }

    public function updateStud($id, $entity): void {
        $query = "UPDATE {$this->table} 
            SET STUD_MIDTERM = :STUD_MIDTERM, 
                STUD_FINAL = :STUD_FINAL, 
                STUD_GRADE = :STUD_GRADE, 
                STUD_STATUS = :STUD_STATUS 
            WHERE STUD_ID = :id";
    
        $params = $this->getParameters($entity, $id);
    
        $this->executeQuery($query, $params);
    }
    
    public function deleteStud(int $id): void {

        $query = "DELETE FROM {$this->table} WHERE STUD_ID = :id";
        $params = [':id' => $id];
        $this->executeQuery($query, $params);
    }

    public function BuildResult(?array $result) : ? array {
        if (!$result || empty($result[0])) {
            return null;
        }

        $row = $result[0];

        return $row;
    }

    public function BuildResultList(array $result) : array {
        $students = [];

        foreach ($result as $row) {
            $students[] = $row;
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