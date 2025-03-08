<?php

require_once "repositories\StudentRepositories.php";
require_once "config\Database.php";
require_once "services\StudentService.php";

class StudentController {
    private StudentRepositories $stud_repo;
    private StudentService $stud_service;

    public function __construct() {
        $database = new Database();
        $this->stud_repo = new StudentRepositories($database->getConnection(), "student");
        $this->stud_service = new StudentService($this->stud_repo);
    }

    public function getAllStud(): void {
        echo json_encode($this->stud_service->getAllStud(),JSON_PRETTY_PRINT);
    }

    public function getStudById(int $id): void {
        
        $result = $this->stud_service->getStudById($id);

        echo ($result) ? json_encode($result) : "STUDENT WITH ID {$id} DOES NOT EXIST!";
    }

    public function addStud($student) {
        try {
            $this->stud_service->addStud($student);
            echo "Student Added Successfully";
        }

        catch(Exception $e) {
            error_log("REQUEST HANDLING ERROR {$e->getMessage()}") ;
        }
    }

    public function updateStud(int $id, $student) {
        
        try {
            $result = $this->stud_service->updateStud($id, $student);
            echo ($result !== NULL) ? json_encode($result) : "STUDENT UPDATED SUCCESSFULLY!";
        }

        catch(Exception $e) {
            error_log("REQUEST HANDLING ERROR {$e->getMessage()}") ;
        }
    }

    public function deleteStud(int $id) {
        try {
            $result = $this->stud_service->deleteStud($id);
            echo ($result !== NULL) ? json_encode($result) : "STUDENT DELETED SUCCESSFULLY!";
        }

        catch(Exception $e) {
            error_log("REQUEST HANDLING ERROR {$e->getMessage()}") ;
        }
    }
}
?>