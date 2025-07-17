<?php
include '../config/database.php';

$id = $_GET['id'];
$query = "DELETE FROM paket WHERE id = $id";

if ($conn->query($query)) {
    header("Location: index.php");
} else {
    echo "Gagal menghapus paket!";
}
?>
