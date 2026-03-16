<?php
include "db_conn.php";
$id = $_GET["id"];
$sql = "DELETE FROM `menuproducts` WHERE menu_id = $id";
$result = mysqli_query($conn, $sql);

if ($result) {
    header("Location: index.php?msg=Menu product deleted successfully");
} else {
    echo "Failed: " . mysqli_error($conn);
}
?>
