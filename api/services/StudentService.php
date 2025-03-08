<?php
class StudentService {
    private StudentRepositories $studentRepo;

    public function __construct($studentRepo) {
        $this->studentRepo = $studentRepo;
    }

    public function getAllStud() : array {
        $student = [];
        foreach($this->studentRepo->getAllStud() as $AllStud) {
            $student[] = $this::student($AllStud, 'GET');
        }
        return $student;
    }

    public function student($entity, $request_type) {

        if ($entity == null) { 
            return null;
        }
    
        $student = new Student();
    
        $student->STUD_ID = $entity['STUD_ID'] ?? NULL;
        $student->STUD_NAME = $entity['STUD_NAME'] ?? NULL;
        $student->STUD_MIDTERM = $entity['STUD_MIDTERM'] ?? NULL;
        $student->STUD_FINAL = $entity['STUD_FINAL'] ?? NULL;
    
        if ($request_type == 'PUT' || $request_type == 'POST') { 
            $student->STUD_GRADE = $this::calculateGrade($student->STUD_MIDTERM, $student->STUD_FINAL);
            $student->STUD_STATUS = $this::calculateStatus($student->STUD_GRADE); 
        } else {
            $student->STUD_GRADE = $entity['STUD_GRADE'] ?? NULL;
            $student->STUD_STATUS = $entity['STUD_STATUS'] ?? NULL;
        }
    
        return $student;
    }

    public function getStudById(int $id) : ? Student {
        return $this::student($this->studentRepo->getStudById($id), 'GET');
    }

    public function addStud($entity) {
        $this::validateInput($entity, 'POST');
        $student = $this::student($entity, 'POST');
        $this->studentRepo->addStud($entity);
    }

    public function updateStud(int $id, $entity) {
        $result = $this::validateExistingStudent($id);
        $this::validateInput($entity, 'PUT');
        $student = $this::student($entity, 'PUT');
        $this->studentRepo->updateStud($id, $student);

        return $result;
    }

    public function deleteStud(int $id) {
        $result = $this::validateExistingStudent($id);
        $this->studentRepo->deleteStud($id);

        return $result;
    }

    public function calculateGrade($STUD_MIDTERM, $STUD_FINAL) {
        $STUD_GRADE = (0.4 * $STUD_MIDTERM) + (0.6 * $STUD_FINAL);
        return $STUD_GRADE;
    }

    public function calculateStatus($STUD_GRADE) {
        $STUD_STATUS = $STUD_GRADE >= 75 ? "PASSED" : "FAIL";
        return $STUD_STATUS;
    }

    public function validateInput($entity, string $request_type) {
        if($request_type == 'POST') {
            if (!isset($entity['STUD_NAME']) || empty(trim($entity['STUD_NAME']))) {
                throw new Exception("Student Name is required");
            }
        }
        if (!isset($entity['STUD_MIDTERM']) || empty(trim($entity['STUD_MIDTERM']))) {
                throw new Exception("Student Midterm Score is required");
        }
        if (!isset($entity['STUD_FINAL']) || empty(trim($entity['STUD_FINAL']))) {
                throw new Exception("Student Final Score is required");
        }
    }

    public function validateExistingStudent(int $id) {
        $result = $this::getStudById($id);

        if(!$result) {
            return 'STUDENT NOT FOUND!';
        }
    }
}
?>