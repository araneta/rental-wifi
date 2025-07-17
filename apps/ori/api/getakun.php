<?php 

	error_reporting(0);
	
	
	//Importing database
	include '../config/database.php';
	$telepon = $_GET['id_user'];
	
	//Membuat SQL Query dengan pegawai yang ditentukan secara spesifik sesuai ID
	$sql = "SELECT * FROM `users` WHERE email = '$telepon' ";
	
	//Mendapatkan Hasil 
	$r = mysqli_query($conn,$sql);
	
	//Memasukkan Hasil Kedalam Array
	$result = array();
	while ($row = mysqli_fetch_array($r)){
	// array_push($result,array(
			$result[] = $row;
}
	//Menampilkan dalam format JSON
	echo json_encode(array('result'=>$result));
	
	mysqli_close($conn);
?>