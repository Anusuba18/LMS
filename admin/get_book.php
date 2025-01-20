<?php
include('includes/config.php');

$category = $_POST['category'];
$publication = $_POST['publication'];
$bookname = $_POST['bookname'];

$sql = "SELECT id, BookName FROM tblbooks WHERE 1=1";
if (!empty($category)) {
    $sql .= " AND Category = :category";
}
if (!empty($publication)) {
    $sql .= " AND Publication = :publication";
}
if (!empty($bookname)) {
    $sql .= " AND BookName LIKE :bookname";
}

$query = $dbh->prepare($sql);

if (!empty($category)) {
    $query->bindParam(':category', $category, PDO::PARAM_STR);
}
if (!empty($publication)) {
    $query->bindParam(':publication', $publication, PDO::PARAM_STR);
}
if (!empty($bookname)) {
    $bookname = "%$bookname%";
    $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
}

$query->execute();
$books = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() > 0) {
    foreach ($books as $book) {
        echo "<option value='" . htmlentities($book->id) . "'>" . htmlentities($book->BookName) . "</option>";
    }
} else {
    echo "<option value=''>No books found</option>";
}
?>
