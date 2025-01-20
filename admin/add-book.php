<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    // Handle form submission
    if (isset($_POST['add'])) {
        $bookname = $_POST['bookname'];
        $category = $_POST['category'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $copies = $_POST['copies'];

        // Validate ISBN to ensure it's a valid number and unique if necessary
        if (!is_numeric($isbn)) {
            $_SESSION['error'] = "ISBN must be a numeric value.";
            header('location:manage-books.php');
            exit();
        }

        // Prepare SQL statement to insert a new record into the tblbooks table
        $sql = "INSERT INTO tblbooks (BookName, CatId, AuthorId, ISBNNumber, BookPrice, Copies, IssuedCopies) 
                VALUES (:bookname, :category, :author, :isbn, :price, :copies, 0)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
        $query->bindParam(':category', $category, PDO::PARAM_INT); // Make sure category is an integer
        $query->bindParam(':author', $author, PDO::PARAM_INT);     // Make sure author is an integer
        $query->bindParam(':isbn', $isbn, PDO::PARAM_INT);         // Ensure ISBN is stored as an integer
        $query->bindParam(':price', $price, PDO::PARAM_INT);       // Ensure price is stored as an integer
        $query->bindParam(':copies', $copies, PDO::PARAM_INT);     // Ensure copies is stored as an integer

        // Execute the query
        if ($query->execute()) {
            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId) {
                $_SESSION['msg'] = "Book added successfully";
                header('location:manage-books.php');
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again";
                header('location:manage-books.php');
            }
        } else {
            $_SESSION['error'] = "Unable to execute the query.";
            header('location:manage-books.php');
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
    <title>Library Management System | Add Book</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Add Book</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading" style="color:#000;">
                            Book Info
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group" style="color:#000;">
                                    <label>Book Name<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="bookname" autocomplete="off" required />
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>Category<span style="color:red;">*</span></label>
                                    <select class="form-control" name="category" required="required">
                                        <option value="">Select Category</option>
                                        <?php 
                                        $status = 1;
                                        $sql = "SELECT * FROM tblcategory WHERE Status = :status";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':status', $status, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { 
                                                echo '<option value="'.htmlentities($result->id).'">'.htmlentities($result->CategoryName).'</option>';
                                            }
                                        } 
                                        ?> 
                                    </select>
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>Publication<span style="color:red;">*</span></label>
                                    <select class="form-control" name="author" required="required">
                                        <option value="">Select Publication</option>
                                        <?php 
                                        $sql = "SELECT * FROM tblauthors";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { 
                                                echo '<option value="'.htmlentities($result->id).'">'.htmlentities($result->AuthorName).'</option>';
                                            }
                                        } 
                                        ?> 
                                    </select>
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>ISBN Number<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="isbn" required="required" autocomplete="off" />
                                    <p class="help-block">An ISBN is an International Standard Book Number. ISBN must be unique.</p>
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>No of Copies<span style="color:red;">*</span></label>
                                    <input class="form-control" type="number" name="copies" autocomplete="off" required="required" min="1" />
                                </div>

                                <div class="form-group" style="color:#000;">
                                    <label>Price<span style="color:red;">*</span></label>
                                    <input class="form-control" type="number" name="price" autocomplete="off" required="required" min="0" step="0.01" />
                                </div>

                                <button type="submit" name="add" class="btn btn-info">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
