<?php 
session_start(); 
error_reporting(0); 
include('includes/config.php');  

if(strlen($_SESSION['login']) == 0) {      
    // header('location:index.php'); 
} else { 
?> 

<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
    <meta charset="utf-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
    <meta name="description" content="" /> 
    <meta name="author" content="" /> 
    <title>Library Management System | Student Dashboard</title> 
    <link href="assets/css/bootstrap.css" rel="stylesheet" /> 
    <link href="assets/css/font-awesome.css" rel="stylesheet" /> 
    <link href="assets/css/style.css" rel="stylesheet" /> 
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' /> 
    <style>
        .notification {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 999;
            display: none;
            background-color: #f39e28;
            padding: 15px;
            border-radius: 5px;
            color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .notification p {
            margin: 0;
            font-size: 14px;
        }

        .notification .close-btn {
            float: right;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head> 
<body> 
    <?php include('includes/header.php'); ?> 
    
    <div class="content-wrapper"> 
        <div class="container"> 

            <!-- Notifications Section for Remaining Days and Fines -->
            <div class="row"> 
                <div class="col-md-12"> 
                    <div class="alert alert-danger back-widget-set"> 
                        <h4>Notifications</h4> 
                        <?php 
                        $sid = $_SESSION['stdid']; 
                        $sql4 = "SELECT BookName, DATEDIFF(ReturnDate, CURDATE()) AS daysLeft, fine FROM tblissuedbookdetails 
                                 JOIN tblbooks ON tblbooks.id=tblissuedbookdetails.BookId 
                                 WHERE StudentID=:sid AND ReturnStatus=0"; 
                        $query4 = $dbh->prepare($sql4); 
                        $query4->bindParam(':sid', $sid, PDO::PARAM_STR); 
                        $query4->execute(); 
                        $notifications = $query4->fetchAll(PDO::FETCH_OBJ); 
                        foreach ($notifications as $notif) { 
                            $daysLeft = $notif->daysLeft; 
                            $fineAmount = $notif->fine; 
                            if ($daysLeft > 0) {
                                echo "<p><strong>Book:</strong> " . htmlentities($notif->BookName) . " - <strong>Days Left:</strong> " . $daysLeft . " days</p>";
                            } else {
                                echo "<p><strong>Book:</strong> " . htmlentities($notif->BookName) . " - <strong>Fine:</strong> " . ($fineAmount > 0 ? $fineAmount : 0) . " INR</p>";
                            }
                        } 
                        ?> 
                    </div> 
                </div> 
            </div> 

            <!-- Dashboard Stats -->
            <div class="row pad-botm"> 
                <div class="col-md-12"> 
                    <h4 class="header-line">Student Dashboard</h4> 
                </div> 
            </div> 

            <div class="row"> 
                <div class="col-md-3 col-sm-3 col-xs-6"> 
                    <div class="alert alert-info back-widget-set text-center"> 
                        <i class="fa fa-bars fa-5x"></i> 
                        <?php 
                        $sql1 = "SELECT id FROM tblissuedbookdetails WHERE StudentID=:sid"; 
                        $query1 = $dbh->prepare($sql1); 
                        $query1->bindParam(':sid', $sid, PDO::PARAM_STR); 
                        $query1->execute(); 
                        $issuedbooks = $query1->rowCount(); 
                        ?> 
                        <h3><?php echo htmlentities($issuedbooks); ?> </h3> 
                        Books Issued 
                    </div> 
                </div> 

                <div class="col-md-3 col-sm-3 col-xs-6"> 
                    <div class="alert alert-warning back-widget-set text-center"> 
                        <i class="fa fa-recycle fa-5x"></i> 
                        <?php 
                        $rsts = 0; 
                        $sql2 = "SELECT id FROM tblissuedbookdetails WHERE StudentID=:sid AND ReturnStatus=:rsts"; 
                        $query2 = $dbh->prepare($sql2); 
                        $query2->bindParam(':sid', $sid, PDO::PARAM_STR); 
                        $query2->bindParam(':rsts', $rsts, PDO::PARAM_INT); 
                        $query2->execute(); 
                        $returnedbooks = $query2->rowCount(); 
                        ?> 
                        <h3><?php echo htmlentities($returnedbooks); ?></h3> 
                        Books Not Returned Yet 
                    </div> 
                </div> 

                <div class="col-md-3 col-sm-3 col-xs-6"> 
                    <div class="alert alert-info back-widget-set text-center"> 
                        <i class="fa fa-money fa-5x"></i> 
                        <?php 
                        $sqlFine = "SELECT SUM(fine) AS totalFine FROM tblissuedbookdetails WHERE StudentID=:sid AND fine > 0"; 
                        $queryFine = $dbh->prepare($sqlFine); 
                        $queryFine->bindParam(':sid', $sid, PDO::PARAM_STR); 
                        $queryFine->execute(); 
                        $fineResult = $queryFine->fetch(PDO::FETCH_OBJ); 
                        $totalFine = $fineResult->totalFine !== null ? $fineResult->totalFine : 0; 
                        ?> 
                        <h3><?php echo htmlentities($totalFine); ?> </h3> 
                        Total Fine 
                    </div> 
                </div> 

                <div class="col-md-3 col-sm-3 col-xs-6"> 
                    <div class="alert alert-success back-widget-set text-center" onclick="showRecentBooks()"> 
                        <i class="fa fa-book fa-5x"></i> 
                        <h3>Recently Added Books</h3> 
                        <p>Click to view recent books</p> 
                    </div> 
                </div> 
            </div> 

            <div id="recentBooksModal" class="modal fade" tabindex="-1" role="dialog"> 
                <div class="modal-dialog" role="document"> 
                    <div class="modal-content" style="color:#000;"> 
                        <div class="modal-header" style="background-color: #f39e28;color:#000;"> 
                            <h5 class="modal-title" style="color:#000;">Recently Added Books</h5> 
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                                <span aria-hidden="true">&times;</span> 
                            </button> 
                        </div> 
                        <div class="modal-body"> 
                            <?php 
                            $sql3 = "SELECT BookName FROM tblbooks ORDER BY id DESC LIMIT 5"; 
                            $query3 = $dbh->prepare($sql3); 
                            $query3->execute(); 
                            $recentBooks = $query3->fetchAll(PDO::FETCH_OBJ); 
                            foreach ($recentBooks as $book) { 
                                echo "<p>" . htmlentities($book->BookName) . "</p>"; 
                            } 
                            ?> 
                        </div> 
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="color:#f86748">Close</button> 
                        </div> 
                    </div> 
                </div> 
            </div> 

            <!-- Notification Area -->
            <div class="notification" id="notificationBox">
                <span class="close-btn" onclick="closeNotification()">&times;</span>
                <p id="notificationContent"></p>
            </div>
        </div> 
    </div> 

    <script src="assets/js/jquery-1.10.2.js"></script> 
    <script src="assets/js/bootstrap.js"></script> 
    <script src="assets/js/custom.js"></script> 
    <script> 
        function showRecentBooks() { 
            $('#recentBooksModal').modal('show'); 
        } 

        // Show notification when remaining days are available
        function showNotification(message) {
            var notificationBox = document.getElementById('notificationBox');
            var notificationContent = document.getElementById('notificationContent');
            notificationContent.innerHTML = message;
            notificationBox.style.display = "block";
        }

        function closeNotification() {
            var notificationBox = document.getElementById('notificationBox');
            notificationBox.style.display = "none";
        }

        <?php 
        // PHP logic to show notifications
        foreach ($notifications as $notif) {
            $daysLeft = $notif->daysLeft;
            $fineAmount = $notif->fine;

            if ($daysLeft > 0) {
                echo "showNotification('<strong>Book:</strong> " . htmlentities($notif->BookName) . " - <strong>Days Left:</strong> " . $daysLeft . " days');";
            } else {
                echo "showNotification('<strong>Book:</strong> " . htmlentities($notif->BookName) . " - <strong>Fine:</strong> " . ($fineAmount > 0 ? $fineAmount : 0) . " INR');";
            }
        }
        ?>
    </script> 
</body> 
</html> 

<?php } ?> 
