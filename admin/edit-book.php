<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else { 

    if (isset($_POST['update'])) {
        $bookname = $_POST['bookname'];
        $category = $_POST['category'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $Copies = $_POST['copies']; // Get the number of copies from the form
        $bookid = intval($_GET['bookid']);

        // SQL to update book information
        $sql = "UPDATE tblbooks 
                SET BookName = :bookname, CatId = :category, AuthorId = :author, 
                    ISBNNumber = :isbn, BookPrice = :price, Copies = :Copies 
                WHERE id = :bookid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
        $query->bindParam(':category', $category, PDO::PARAM_STR);
        $query->bindParam(':author', $author, PDO::PARAM_STR);
        $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $query->bindParam(':price', $price, PDO::PARAM_STR);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $query->bindParam(':Copies', $Copies, PDO::PARAM_INT);
        $query->execute();

        $_SESSION['msg'] = "Book info updated successfully";
        header('location:manage-books.php');
        exit();
    }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Library Management System | Edit Book</title>
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
                    <h4 class="header-line">Edit Book</h4>
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
                                <?php 
                                $bookid = intval($_GET['bookid']);
                                $sql = "SELECT tblbooks.BookName, tblcategory.CategoryName, 
                                                tblbooks.Copies, tblcategory.id as cid, 
                                                tblauthors.AuthorName, tblauthors.id as athrid, 
                                                tblbooks.ISBNNumber, tblbooks.BookPrice, 
                                                tblbooks.id as bookid 
                                        FROM tblbooks 
                                        JOIN tblcategory ON tblcategory.id = tblbooks.CatId 
                                        JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId 
                                        WHERE tblbooks.id = :bookid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
                                $query->execute();
                                $result = $query->fetch(PDO::FETCH_OBJ);

                                if ($result) { ?>  
                                    <div class="form-group" style="color:#000;">
                                        <label>Book ID</label>
                                        <input class="form-control" type="number" name="bookid" value="<?php echo htmlentities($result->bookid); ?>" readonly />
                                    </div>

                                    <div class="form-group" style="color:#000;">
                                        <label>Book Name<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($result->BookName); ?>" required />
                                    </div>

                                    <div class="form-group" style="color:#000;">
                                        <label>Category<span style="color:red;">*</span></label>
                                        <select class="form-control" name="category" required>
                                            <option value="<?php echo htmlentities($result->cid); ?>"><?php echo htmlentities($result->CategoryName); ?></option>
                                            <?php 
                                            $status = 1;
                                            $sql1 = "SELECT * FROM tblcategory WHERE Status = :status";
                                            $query1 = $dbh->prepare($sql1);
                                            $query1->bindParam(':status', $status, PDO::PARAM_STR);
                                            $query1->execute();
                                            $categories = $query1->fetchAll(PDO::FETCH_OBJ);
                                            
                                            foreach ($categories as $row) {
                                                if ($result->CategoryName !== $row->CategoryName) { ?>  
                                                    <option value="<?php echo htmlentities($row->id); ?>"><?php echo htmlentities($row->CategoryName); ?></option>
                                                <?php }
                                            } ?> 
                                        </select>
                                    </div>

                                    <div class="form-group" style="color:#000;">
                                        <label>Publication<span style="color:red;">*</span></label>
                                        <select class="form-control" name="author" required>
                                            <option value="<?php echo htmlentities($result->athrid); ?>"><?php echo htmlentities($result->AuthorName); ?></option>
                                            <?php 
                                            $sql2 = "SELECT * FROM tblauthors";
                                            $query2 = $dbh->prepare($sql2);
                                            $query2->execute();
                                            $authors = $query2->fetchAll(PDO::FETCH_OBJ);
                                            
                                            foreach ($authors as $ret) {
                                                if ($result->AuthorName !== $ret->AuthorName) { ?>  
                                                    <option value="<?php echo htmlentities($ret->id); ?>"><?php echo htmlentities($ret->AuthorName); ?></option>
                                                <?php }
                                            } ?> 
                                        </select>
                                    </div>

                                    <div class="form-group" style="color:#000;">
                                        <label>ISBN Number<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($result->ISBNNumber); ?>" required />
                                        <p class="help-block">An ISBN is an International Standard Book Number. ISBN must be unique.</p>
                                    </div>

                                    <div class="form-group" style="color:#000;">
                                        <label>No of Copies<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="copies" value="<?php echo htmlentities($result->Copies); ?>" required />
                                    </div>
                                    
                                    <div class="form-group" style="color:#000;">
                                        <label>Price in Rs<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="price" value="<?php echo htmlentities($result->BookPrice); ?>" required />
                                    </div>
                                <?php } ?>
                                <button type="submit" name="update" class="btn btn-info">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Files -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>

<?php } ?>
