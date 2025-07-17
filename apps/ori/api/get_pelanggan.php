<?php
include '../config/database.php';

$result = $conn->query("SELECT * FROM pelanggan ORDER BY nama ASC");
$pelanggan = [];

while ($row = $result->fetch_assoc()) {
    $pelanggan[] = $row;
}

echo json_encode($pelanggan);
?>
