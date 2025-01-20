<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['issue'])) {
        $studentid = strtoupper($_POST['studentid']);
        $bookid = $_POST['bookdetails'];
        
        // Inserting into tblissuedbookdetails
        $sql = "INSERT INTO tblissuedbookdetails (StudentID, BookId, ReturnStatus) VALUES (:studentid, :bookid, 0)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();

        // Remove the requested book
        $sql = "DELETE FROM tblrequestedbookdetails WHERE StudentID = :studentid AND BookId = :bookid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();

        // Update the issued copies count
        $sql = "UPDATE tblbooks SET IssuedCopies = IssuedCopies + 1 WHERE id = :bookid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['msg'] = "Book issued successfully";
        header('location:manage-issued-books.php');
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
    <title>Library Management System | Issue a new Book</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script>
        // Function to get student name
        function getstudent() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_student.php",
                data: 'studentid=' + $("#studentid").val(),
                type: "POST",
                success: function (data) {
                    $("#get_student_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function () {}
            });
        }

        // Function to get book details
        function getbook() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_book.php",
                data: 'bookid=' + $("#bookid").val(),
                type: "POST",
                success: function (data) {
                    $("#get_book_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function () {}
            });
        }
    </script>
    <style type="text/css">
        .others {
            color: red;
        }
    </style>
</head>
<body>
    <!-- MENU SECTION START -->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END -->
    
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Issue a New Book</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Issue a New Book
                        </div>
                        <div class="panel-body">
                            <form method="post" name="chngpwd" class="form-horizontal" onSubmit="return valid();">
                                <?php
                                $bookid = $_GET['ISBNNumber'];
                                $stdid = $_GET['StudentID'];
                                ?>
                                <div class="form-group" style="color:#000;">
                                    <label>Student ID<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="studentid" id="studentid" value="<?php echo htmlentities($stdid); ?>" onBlur="getstudent()" required />
                                </div>
                                <div class="form-group">
                                    <span id="get_student_name" style="font-size:16px;"></span>
                                </div>
                                <div class="form-group" style="color:#000;">
                                    <label>Book ID<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="bookid" id="bookid" value="<?php echo htmlentities($bookid); ?>" onBlur="getbook()" required="required" />
                                </div>
                                <div class="form-group" style="color:#000;">
                                    Book Title<select class="form-control" name="bookdetails" id="get_book_name" readonly>
                                    </select>
                                </div>
                                <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
                                else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>
                                <button type="submit" name="issue" id="submit" class="btn btn-info">Issue Book</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
