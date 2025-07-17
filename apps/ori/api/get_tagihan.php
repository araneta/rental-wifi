<?php
include '../config/database.php';

// Query untuk mendapatkan daftar tagihan yang belum dibayar
$query = "SELECT t.id, p.nama AS pelanggan, p.alamat, t.bulan_tahun, t.jumlah, t.status 
          FROM tagihan t 
          JOIN pelanggan p ON t.pelanggan_id = p.id 
          WHERE t.status = 'belum bayar' 
          ORDER BY t.bulan_tahun DESC";

$result = $conn->query($query);
$response = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = [
            'id' => $row['id'],
            'pelanggan' => $row['pelanggan'],
            'bulan_tahun' => $row['bulan_tahun'],
            'jumlah' => $row['jumlah'],
            'alamat' => $row['alamat'],
            'status' => $row['status']
        ];
    }
}

// Set response JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
