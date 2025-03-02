<?php
class Validate {
    public function calculateGrade($STUD_MIDTERM, $STUD_FINAL){
        $STUD_GRADE = (0.4 * $STUD_MIDTERM) + (0.6 * $STUD_FINAL);
        return $STUD_GRADE;
    }
    public function calculateStatus($STUD_GRADE){
        $STUD_STATUS = $STUD_GRADE >= 75 ? "PASSED" : "FAIL";
        return $STUD_STATUS;
    }
    public function validateStudent($entity, string $request_type) {
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
    public function validateExistingStudent($student,$id){
        if(empty($student)){
            echo json_encode(["error" => "Student with ID {$id} not found"]);
        }
        return;
    }
}
?>