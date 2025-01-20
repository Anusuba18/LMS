<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
} else {
    // Set student ID from session
    $sid = $_SESSION['stdid'];

    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Library Management System | Current Fines</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Current Fines</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading" style="color:#000; background-color: #f39e28;">
                            Issued Books & Current Fines
                        </div>
                        <div class="panel-body" style="color:#000;">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Issue Date</th>
                                            <th>Return Date</th>
                                            <th>Days Remaining / Overdue</th>
                                            <th>Fine (Rs)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Query to retrieve issued book details
                                        $sql = "SELECT tblbooks.BookName, 
                                                       tblbooks.ISBNNumber, 
                                                       tblissuedbookdetails.IssuesDate, 
                                                       tblissuedbookdetails.ReturnDate
                                                FROM tblissuedbookdetails 
                                                JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
                                                JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
                                                WHERE tblstudents.StudentId = :sid 
                                                ORDER BY tblissuedbookdetails.id DESC";
                                        
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;

                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { 
                                                $issueDate = strtotime($result->IssuesDate);
                                                $dueDate = strtotime($result->IssuesDate . ' + 7 days'); // 7-day grace period
                                                $returnDate = $result->ReturnDate ? strtotime($result->ReturnDate) : null;
                                                $currentDate = $returnDate ?: time(); // Use return date if available, otherwise current date

                                                $daysOverdue = 0;
                                                $fineAmount = 0;

                                                // Calculate overdue days and fine
                                                if ($currentDate > $dueDate) {
                                                    $daysOverdue = floor(($currentDate - $dueDate) / (60 * 60 * 24));

                                                    // Fine calculation based on overdue days
                                                    if ($daysOverdue <= 5) {
                                                        $fineAmount = $daysOverdue * 1; // First 5 days after grace period: Rs. 1 per day
                                                    } elseif ($daysOverdue <= 10) {
                                                        $fineAmount = (5 * 1) + (($daysOverdue - 5) * 5); // Next 5 days: Rs. 5 per day
                                                    } else {
                                                        $fineAmount = (5 * 1) + (5 * 5) + (($daysOverdue - 10) * 10); // After 10 days: Rs. 10 per day
                                                    }
                                                }
                                                ?>

                                                <tr class="odd gradeX">
                                                    <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->BookName); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->ISBNNumber); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->IssuesDate); ?></td>
                                                    <td class="center">
                                                        <?php 
                                                        if ($result->ReturnDate == "") {
                                                            echo "<span style='color:red;'>Not Returned Yet</span>";
                                                        } else {
                                                            echo htmlentities($result->ReturnDate);
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="center">
                                                        <?php 
                                                        if ($daysOverdue > 0) {
                                                            echo htmlentities($daysOverdue) . " days overdue";
                                                        } else {
                                                            $remainingDays = floor(($dueDate - $currentDate) / (60 * 60 * 24));
                                                            echo htmlentities($remainingDays) . " days remaining";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="center"><?php echo htmlentities($fineAmount); ?></td>
                                                </tr>
                                        <?php 
                                                $cnt++;
                                            } 
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
