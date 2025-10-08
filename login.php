<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
$mysqli = new mysqli("localhost", "root", "", "attendance_system");
if($mysqli->connect_error){
    exit(json_encode(["success"=>false,"message"=>"DB connection error: ".$mysqli->connect_error]));
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$schoolid = $data['schoolid'];
$password = $data['password'];

// Fetch student
$stmt = $mysqli->prepare("SELECT fname,lname,schoolid,password,course,year,department FROM students WHERE schoolid=?");
$stmt->bind_param("s",$schoolid);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($fname,$lname,$schoolid_db,$hash,$course,$year,$department);

if($stmt->num_rows === 0){
    echo json_encode(["success"=>false,"message"=>"Account not found"]);
    exit;
}

$stmt->fetch();

if(password_verify($password,$hash)){
    echo json_encode([
        "success"=>true,
        "data"=>[
            "fname"=>$fname,
            "lname"=>$lname,
            "schoolid"=>$schoolid_db,
            "course"=>$course,
            "year"=>$year,
            "department"=>$department
        ]
    ]);
} else {
    echo json_encode(["success"=>false,"message"=>"Incorrect password"]);
}
?>
