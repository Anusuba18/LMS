<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    // Fetch all issued books and calculate fines
    $sql = "SELECT tblstudents.FullName, tblbooks.BookName, tblbooks.ISBNNumber, 
            tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, 
            tblissuedbookdetails.id as rid
            FROM tblissuedbookdetails 
            JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
            JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
            ORDER BY tblissuedbookdetails.IssuesDate DESC";
    
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    $totalFine = 0; // Initialize grand total
    ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Library Management System | Fines</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" /> <!-- DataTable CSS -->
</head>
<body>
<?php include('includes/header.php'); ?>
<!-- MENU SECTION END-->
    <div class="content-wrapper">
         <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Fines</h4>
    </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default" style="color:#000;">
                        <div class="panel-heading" style="color:#000; background-color:#f39e28;">
                            Fines Calculation
                        </div>
                        <div class="panel-body" style="color:#000;">
                            <div class="table-responsive" style="color:#000;">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example" style="color:#000;">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Student Name</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Issued Date</th>
                                            <th>Return Date</th>
                                            <th>Days Overdue</th>
                                            <th>Remaining Days</th>
                                            <th>Fine Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $cnt = 1;
                                    foreach ($results as $result) {
                                        $issuedDate = strtotime($result->IssuesDate);
                                        $dueDate = strtotime($result->IssuesDate . ' + 7 days'); // 7 days due period
                                        $returnDate = $result->ReturnDate ? strtotime($result->ReturnDate) : null;
                                        $currentDate = $returnDate ?: time(); // Use return date if available, otherwise current date

                                        $daysOverdue = 0;
                                        $remainingDays = 0;
                                        $fineAmount = 0;

                                        if ($currentDate > $dueDate) {
                                            // Calculate overdue days and fine
                                            $daysOverdue = floor(($currentDate - $dueDate) / (60 * 60 * 24));
                                            if ($daysOverdue <= 5) {
                                                $fineAmount = $daysOverdue * 1; // First 5 days overdue, fine of Rs.1 per day
                                            } elseif ($daysOverdue <= 10) {
                                                $fineAmount = (5 * 1) + (($daysOverdue - 5) * 5); // Next 5 days, fine of Rs.5 per day
                                            } else {
                                                $fineAmount = (5 * 1) + (5 * 5) + (($daysOverdue - 10) * 20); // After 10 days, fine of Rs.20 per day
                                            }
                                        } else {
                                            // Calculate remaining days until due date only if the book is not yet returned
                                            $remainingDays = $returnDate ? 0 : floor(($dueDate - $currentDate) / (60 * 60 * 24));
                                        }

                                        $totalFine += $fineAmount; // Add to total fine
                                    ?>
                                        <tr class="odd gradeX">
                                            <td class="center"><?php echo htmlentities($cnt); ?></td>
                                            <td class="center"><?php echo htmlentities($result->FullName); ?></td>
                                            <td class="center"><?php echo htmlentities($result->BookName); ?></td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber); ?></td>
                                            <td class="center"><?php echo htmlentities(date('Y-m-d', $issuedDate)); ?></td>
                                            <td class="center">
                                                <?php echo htmlentities($result->ReturnDate ? date('Y-m-d', $returnDate) : 'Not Returned Yet'); ?>
                                            </td>
                                            <td class="center"><?php echo htmlentities($daysOverdue > 0 ? $daysOverdue : 0); ?></td>
                                            <td class="center"><?php echo htmlentities($remainingDays > 0 ? $remainingDays : 0); ?></td>
                                            <td class="center"><?php echo htmlentities($fineAmount); ?></td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    } 
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <h4>Total Fine Amount: Rs.<?php echo htmlentities($totalFine); ?></h4>
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

    <script>
    $(document).ready(function() {
        // Check if the DataTable already exists and destroy it before reinitializing
        if ($.fn.dataTable.isDataTable('#dataTables-example')) {
            $('#dataTables-example').DataTable().destroy();
        }

        // Initialize DataTable with simplified pagination and numbers between previous and next
        $('#dataTables-example').DataTable({
            "lengthMenu": [5, 10, 25, 50],
            "pagingType": "simple_numbers", // Page numbers with "Previous" and "Next"
            "searching": true,
            "ordering": true,
            "info": true,
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "search": "Search:",
                "paginate": {
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    });
</script>

</body>
</html>
<?php } ?> 

