<?php  
session_start();
include('includes/config.php');
error_reporting(0);
if (strlen($_SESSION['login']) == 0) {   
    header('location:index.php');
} else { 
    if (isset($_POST['update'])) {
        $sid = $_SESSION['stdid'];
        $fname = $_POST['fullanme'];
        $mobileno = $_POST['mobileno'];
    
        // Set the current date for updation
        $updationDate = date('Y-m-d H:i:s'); // Current date and time
    
        // Update the student details in the database
        $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno, UpdationDate=:updationDate WHERE StudentId=:sid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sid', $sid, PDO::PARAM_STR);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
        $query->bindParam(':updationDate', $updationDate, PDO::PARAM_STR);
        $query->execute();
    
        echo '<script>alert("Your profile has been updated")</script>';
    }
    

    // Fetch the student details from the database
    $sid = $_SESSION['stdid'];
    $sql = "SELECT StudentId, FullName, EmailId, MobileNumber, RegDate, UpdationDate, Status FROM tblstudents WHERE StudentId=:sid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Library Management System | My Profile</title>
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
                    <h4 class="header-line">My Profile</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-md-offset-1">
                    <div class="panel panel-danger">
                        <div class="panel-heading" style="color:#000;">
                            My Profile
                        </div>
                        <div class="panel-body">
                            <form name="signup" method="post">
                                <?php 
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) { ?>  
                                        <div class="form-group" style="color:#000;">
                                            <label>Student ID : </label>
                                            <?php echo htmlentities($result->StudentId); ?>
                                        </div>

                                        <div class="form-group" style="color:#000;">
                                            <label>Reg Date : </label>
                                            <?php echo htmlentities($result->RegDate); ?>
                                        </div>
                                        <?php if ($result->UpdationDate != "") { ?>
                                            <div class="form-group" style="color:#000;">
                                                <label>Last Updation Date : </label>
                                                <?php echo htmlentities($result->UpdationDate); ?>
                                            </div>
                                        <?php } ?>

                                        <div class="form-group" style="color:#000;">
                                            <label>Profile Status : </label>
                                            <?php if ($result->Status == 1) { ?>
                                                <span style="color: green">Active</span>
                                            <?php } else { ?>
                                                <span style="color: red">Blocked</span>
                                            <?php } ?>
                                        </div>

                                        <div class="form-group" style="color:#000;">
                                            <label>Enter Full Name</label>
                                            <input class="form-control" type="text" name="fullanme" value="<?php echo htmlentities($result->FullName); ?>" autocomplete="off" required />
                                        </div>

                                        <div class="form-group" style="color:#000;">
                                            <label>Mobile Number :</label>
                                            <input class="form-control" type="text" name="mobileno" maxlength="10" value="<?php echo htmlentities($result->MobileNumber); ?>" autocomplete="off" required />
                                        </div>

                                        <div class="form-group" style="color:#000;">
                                            <label>Enter Email</label>
                                            <input class="form-control" type="email" name="email" id="emailid" value="<?php echo htmlentities($result->EmailId); ?>" autocomplete="off" required readonly />
                                        </div>
                                <?php } } ?>
                                <button type="submit" name="update" class="btn btn-primary" id="submit">Update Now</button>
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
