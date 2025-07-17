<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../config/database.php';

    // Pastikan data POST diterima dengan benar
    $email = isset($_POST['id_user']) ? mysqli_real_escape_string($conn, $_POST['id_user']) : '';
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

    // Cek apakah input kosong
    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email atau Password tidak boleh kosong"]);
        exit;
    }

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah login berhasil
    if ($result->num_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Login Berhasil"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid Email or Password"]);
    }

    // Tutup koneksi
    $stmt->close();
    mysqli_close($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Silakan cek lagi"]);
}
?>
