<?php
session_start();
// initializing variables
$firstname    = "";
$lastname    = "";
$username = "";
$email    = "";
$phone    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'n0vaan00');
if (!$db) {
    die("Error connecting to database: " . mysqli_connect_error());
}
// define global constant
define('BASE_URL', 'http://localhost/reglog/');

// create tables
function createTable(PDO $db){
    $sql = "CREATE TABLE IF NOT EXISTS users(
        firstname varchar(50) NOT NULL,
        lastname varchar(50) NOT NULL,
        username varchar(50) NOT NULL,
        password varchar(200) NOT NULL,
        PRIMARY KEY (username)
        )";
    
    $db->exec($sql);
}
function createTable2(PDO $db){
$sql2 = "CREATE TABLE IF NOT EXISTS contact (
    username varchar(50) NOT NULL,
    email varchar(100),
    phone varchar(20),
    FOREIGN KEY (username) REFERENCES users(username)
    )";

$dbcon->exec($sql2);
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username
  $user_check_query = "SELECT * FROM users WHERE username='$username' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  

    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = $db->prepare("INSERT INTO users (firstname, lastname, username,password) VALUES(?, ?, ?, ?)");
    $query->bind_param("ss", $firstname, $lastname, $username, $password);
    $query->execute();

    $query2 = $db->prepare("INSERT INTO contact (username, email, phone) VALUES(?, ?, ?)");
    $query2->bind_param("sss", $username, $email, $phone);
    $query2->execute();
  	
  	$_SESSION['username'] = $_POST["username"];
    $_SESSION['firstname'] = $firstname;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

 // LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
  
    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        
        $results = mysqli_query($db, $query);
        if (mysqli_num_rows($results) == 1) {
          $query2 = "SELECT * FROM contact WHERE username='$username'";
          $results = mysqli_query($db, $query2);
          $_SESSION['username'] = $_POST["username"];
          $_SESSION['success'] = "You are now logged in";
          header('location: index.php');
        }else {
            array_push($errors, "Wrong username/password combination");
        }
   } 
}

function getInfo() {
  
	// use global $db object in function
	global $db;
  $username= $_SESSION['username'];
	$sql = "SELECT * FROM contact WHERE username = '$username'";
	$result = mysqli_query($db, $sql);

	// fetch all info as an associative array called $contact
	$contact = mysqli_fetch_all($result, MYSQLI_ASSOC);

	return $contact; 
} 

if (isset($_POST['edit_phone'])) {
    // receive all input values from the form
    $username= $_SESSION['username'];
    $phone = $_POST['phone'];
    
        $query = "UPDATE contact SET phone='$phone' WHERE username = '$username'";
        $result=mysqli_query($db, $query);

        if($result)
        {echo 'Data updated';
        }else{
          echo 'Data not updated';
        }
   }
  ?>