<?php
session_start();
error_reporting(0);
include('includes/config.php');

if($_SESSION['login'] != '') {
    $_SESSION['login'] = '';
}

if(isset($_POST['login'])) {
    $email = $_POST['emailid'];
    $password = ($_POST['password']); 
  echo $email;
  
  echo $password;
    // SQL statement to check login credentials
    $sql = "SELECT EmailId, Password, StudentId, Status FROM tblstudents WHERE EmailId=:email and Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
echo $results;
    // If login is successful
    if($query->rowCount() > 0) {
        $result = $results[0]; // Fetch the first result

        // Check if account is active
        if($result->Status == 1) {
            $_SESSION['login'] = $_POST['emailid'];
            $_SESSION['stdid'] = $result->StudentId;

            // Redirect to student dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Your account is blocked. Please contact admin.');</script>";
        }
    } else {
        // Invalid login details
        echo "<script>alert('Invalid email or password');</script>";
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
    <title>Library Management System | Student Login</title>
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
                    <h4 class="header-line">STUDENT LOGIN FORM</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            LOGIN FORM
                        </div>
                        <div class="panel-body">
                            <form role="form" action="" method="post">
                                <div class="form-group" style="color:#000;">
                                    <label>Enter Email ID</label>
                                    <input class="form-control" type="text" name="emailid" required autocomplete="off" />
                                </div>
                                <div class="form-group" style="color:#000;">
                                    <label>Password</label>
                                    <input class="form-control" type="password" name="password" required autocomplete="off" />
                                    <p class="help-block"><a href="user-forgot-password.php">Forgot Password</a></p>
                                </div>
                                <button type="submit" name="login" class="btn btn-info">LOGIN</button>
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
