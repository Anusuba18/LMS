<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else { 

    if (isset($_POST['add'])) {
        $studentid = $_POST['studentid'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $mobilenumber = $_POST['mobilenumber'];
        $password = $_POST['password']; // Store the password as it is, without md5 or trim
        $status = 1; // Default status is active

        // Set the current date for registration
        $regDate = date('Y-m-d H:i:s'); // Current date and time
        $updationDate = date('Y-m-d H:i:s'); // Initially set to current date

        // SQL statement to insert a new record into the tblstudents table
        $sql = "INSERT INTO tblstudents (StudentId, FullName, EmailId, MobileNumber, Password, Status, RegDate, UpdationDate) 
                VALUES (:studentid, :fullname, :email, :mobilenumber, :password, :status, :regDate, :updationDate)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobilenumber', $mobilenumber, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR); // Bind the password directly without encryption
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':regDate', $regDate, PDO::PARAM_STR);
        $query->bindParam(':updationDate', $updationDate, PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            $_SESSION['msg'] = "Student added successfully";
            header('location:reg-students.php');
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again";
            header('location:reg-students.php');
        }
    }
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Library Management System | Add Student</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Add Student</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <div class="panel panel-info">
                    <div class="panel-heading" style="color:#000;">
                        Student Info
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post">
                            <div class="form-group" style="color:#000;">
                                <label>Student ID<span style="color:red;">*</span></label>
                                <input class="form-control" type="text" name="studentid" autocomplete="off" required />
                            </div>

                            <div class="form-group" style="color:#000;">
                                <label>Full Name<span style="color:red;">*</span></label>
                                <input class="form-control" type="text" name="fullname" autocomplete="off" required />
                            </div>

                            <div class="form-group" style="color:#000;">
                                <label>Email<span style="color:red;">*</span></label>
                                <input class="form-control" type="email" name="email" autocomplete="off" required />
                            </div>

                            <div class="form-group" style="color:#000;">
                                <label>Mobile Number<span style="color:red;">*</span></label>
                                <input class="form-control" type="text" name="mobilenumber" autocomplete="off" required />
                            </div>

                            <div class="form-group" style="color:#000;">
                                <label>Password<span style="color:red;">*</span></label>
                                <input class="form-control" type="password" name="password" autocomplete="off" required />
                            </div>

                            <button type="submit" name="add" class="btn btn-info">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
