<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
$mysqli = new mysqli("localhost", "root", "", "attendance_system");
if($mysqli->connect_error){
    exit(json_encode(["success"=>false,"message"=>"DB connection error: ".$mysqli->connect_error]));
}

// Get form data
$data = json_decode(file_get_contents("php://input"), true);
$fname = trim($data['fname']);
$lname = trim($data['lname']);
$schoolid = trim($data['schoolid']);
$password = password_hash(trim($data['password']), PASSWORD_DEFAULT);
$course = $data['course'];
$year = $data['year'];
$department = $data['department'];

// Check if School ID exists
$stmt = $mysqli->prepare("SELECT id FROM students WHERE schoolid=?");
$stmt->bind_param("s", $schoolid);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows > 0){
    exit(json_encode(["success"=>false,"message"=>"School ID already exists"]));
}

// Insert new student
$stmt = $mysqli->prepare("INSERT INTO students (fname,lname,schoolid,password,course,year,department) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("sssssss",$fname,$lname,$schoolid,$password,$course,$year,$department);
$stmt->execute();

echo json_encode(["success"=>true]);
?>
