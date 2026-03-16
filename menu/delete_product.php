<?php
include "db_conn.php";
$id = $_GET["id"];

// First remove any references to this product in the menuproducts table
$delete_relations_sql = "DELETE FROM `menuproducts` WHERE product_id = $id";
mysqli_query($conn, $delete_relations_sql);

$sql = "DELETE FROM `products` WHERE ID = $id";
$result = mysqli_query($conn, $sql);

if ($result) {
    header("Location: index.php?msg=Product deleted successfully");
} else {
    echo "Failed: " . mysqli_error($conn);
}