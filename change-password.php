<?php
session_start();
include('includes/config.php');
error_reporting(0);
if(strlen($_SESSION['login'])==0) {   
    header('location:index.php');
} else { 
    if(isset($_POST['change'])) {
        $password = md5($_POST['password']); // Hash the input current password
        $newpassword = md5($_POST['newpassword']); // Hash the new password
        $email = $_SESSION['login'];

        // Fetch the stored password based on the email
        $sql = "SELECT Password FROM tblstudents WHERE EmailId=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        // Check if a record was found
        if($query->rowCount() > 0) {
            $result = $query->fetch(PDO::FETCH_OBJ);

            // Compare the hashed input password with the stored password
            if ($result->Password == $password) {
                // Password matches, update to the new password
                $con = "UPDATE tblstudents SET Password=:newpassword WHERE EmailId=:email";
                $chngpwd1 = $dbh->prepare($con);
                $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
                $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
                $chngpwd1->execute();
                $msg = "Your Password successfully changed"; // Success message
            } else {
                // Password does not match
                $error = "Your current password is wrong";  // Error message
            }
        } else {
            $error = "No user found."; // Handle case if no user is found
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
    <title>Library Management System | </title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            color: black; /* Set error message color to black */
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            color: black;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>
<script type="text/javascript">
function valid() {
    if (document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value) {
        alert("New Password and Confirm Password Field do not match  !!");
        document.chngpwd.confirmpassword.focus();
        return false;
    }
    return true;
}
</script>

<body>
    <?php include('includes/header.php');?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Student Change Password</h4>
                </div>
            </div>
            <?php if($error) { ?>
                <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div>
            <?php } else if($msg) { ?>
                <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading" style="color:#000;">
                            Change Password
                        </div>
                        <div class="panel-body" style="color:#000;">
                            <form role="form" method="post" onSubmit="return valid();" name="chngpwd">
                                <div class="form-group" style="color:#000;">
                                    <label>Current Password</label>
                                    <input class="form-control" type="password" name="password" autocomplete="off" required />
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>Enter New Password</label>
                                    <input class="form-control" type="password" name="newpassword" autocomplete="off" required />
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>Confirm New Password</label>
                                    <input class="form-control" type="password" name="confirmpassword" autocomplete="off" required />
                                </div>

                                <button type="submit" name="change" class="btn btn-info">Change</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
