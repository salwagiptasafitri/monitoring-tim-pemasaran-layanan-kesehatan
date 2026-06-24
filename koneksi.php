<?php 
	$koneksi = mysqli_connect("localhost","root","","monitoring_pemasaran");



	if (mysqli_connect_errno()) {
		echo "koneksi database gagal : " . mysqli_connect_error();
	}


 ?>