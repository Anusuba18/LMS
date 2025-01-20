<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else{ 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Library Management System | Manage Issued Books</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- DATATABLE STYLE  -->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
                <h4 class="header-line">Manage Issued Books</h4>
    </div>
    <div class="row">
    <?php if($_SESSION['error']!="") { ?>
        <div class="col-md-6">
            <div class="alert alert-danger" >
                <strong>Error :</strong> 
                <?php echo htmlentities($_SESSION['error']);?>
                <?php echo htmlentities($_SESSION['error']="");?>
            </div>
        </div>
    <?php } ?>
    <?php if($_SESSION['msg']!="") { ?>
        <div class="col-md-6">
            <div class="alert alert-success" >
                <strong>Success :</strong> 
                <?php echo htmlentities($_SESSION['msg']);?>
                <?php echo htmlentities($_SESSION['msg']="");?>
            </div>
        </div>
    <?php } ?>

    <?php if($_SESSION['delmsg']!="") { ?>
        <div class="col-md-6">
            <div class="alert alert-success" >
                <strong>Success :</strong> 
                <?php echo htmlentities($_SESSION['delmsg']);?>
                <?php echo htmlentities($_SESSION['delmsg']="");?>
            </div>
        </div>
    <?php } ?>

    </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Advanced Tables -->
            <div class="panel panel-default" style="color:#000;">
                <div class="panel-heading" style="color:#000; background-color:#f39e28;">
                  Issued Books 
                </div>
                <div class="panel-body" style="color:#000;">
                    <div class="table-responsive" style="color:#000;">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example" style="color:#000;">
                            <thead>
                                <tr>
                                    <th>S.NO.</th>
                                    <th>Student Name</th>
                                    <th>Book Name</th>
                                    <th>Book ID</th>
                                    <th>ISBN </th>
                                    <th>Issued Date</th>
                                    <th>Return Date</th>
                                    <th>Remaining Days</th>
                                    <th>Fine Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
<?php 
$sql = "SELECT tblstudents.FullName, tblbooks.BookName, tblbooks.ISBNNumber, tblbooks.id, 
               tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id as rid 
        FROM tblissuedbookdetails 
        JOIN tblstudents ON tblstudents.StudentId=tblissuedbookdetails.StudentId 
        JOIN tblbooks ON tblbooks.id=tblissuedbookdetails.BookId 
        ORDER BY tblissuedbookdetails.id DESC";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;

if($query->rowCount() > 0) {
    foreach($results as $result) { 

        $issuedDate = strtotime($result->IssuesDate);
        $dueDate = strtotime($result->IssuesDate . ' + 7 days');
        $returnDate = $result->ReturnDate ? strtotime($result->ReturnDate) : null;
        $currentDate = $returnDate ?: time();

        $daysOverdue = 0;
        $remainingDays = 0;
        $fineAmount = 0;

        if ($currentDate > $dueDate) {
            $daysOverdue = floor(($currentDate - $dueDate) / (60 * 60 * 24));
            if ($daysOverdue <= 5) {
                $fineAmount = $daysOverdue * 1;
            } elseif ($daysOverdue <= 10) {
                $fineAmount = (5 * 1) + (($daysOverdue - 5) * 5);
            } else {
                $fineAmount = (5 * 1) + (5 * 5) + (($daysOverdue - 10) * 20);
            }
        } else {
            $remainingDays = $returnDate ? 0 : floor(($dueDate - $currentDate) / (60 * 60 * 24));
        }
?>
    <tr class="odd gradeX">
        <td class="center"><?php echo htmlentities($cnt);?></td>
        <td class="center"><?php echo htmlentities($result->FullName);?></td>
        <td class="center"><?php echo htmlentities($result->BookName);?></td>
        <td class="center"><?php echo htmlentities($result->id);?></td>
        <td class="center"><?php echo htmlentities($result->ISBNNumber);?></td>
        <td class="center"><?php echo htmlentities(date('Y-m-d', $issuedDate));?></td>
        <td class="center">
            <?php echo $result->ReturnDate ? htmlentities(date('Y-m-d', $returnDate)) : 'Not Returned Yet'; ?>
        </td>
        <td class="center"><?php echo $remainingDays > 0 ? $remainingDays : ($daysOverdue > 0 ? "$daysOverdue days overdue" : "Due today"); ?></td>
        <td class="center"><?php echo htmlentities($fineAmount); ?></td>
        <td class="center">
            <a href="update-issue-bookdeails.php?rid=<?php echo htmlentities($result->rid);?>">
                <button class="btn btn-primary"><i class="fa fa-edit "></i> Edit</button> 
            </a>
        </td>
    </tr>
<?php $cnt=$cnt+1; }} ?>                                      
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
