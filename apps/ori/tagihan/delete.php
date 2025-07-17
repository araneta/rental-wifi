<?php
include '../config/database.php';

$id = $_GET['id'];
$sql = "DELETE FROM tagihan WHERE id = $id";

if ($conn->query($sql)) {
    header("Location: index.php");
} else {
    echo "<div class='alert alert-danger'>Gagal menghapus tagihan.</div>";
}
?>
