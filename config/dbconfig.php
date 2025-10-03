<?php
$host = 'localhost';
$dbname = 'dtrecorder';
$dbusername = 'root';
$dbpassword = '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect($host, $dbusername, $dbpassword, $dbname);

if (!$conn) {
  exit("Connection failed: " . mysqli_connect_error());
}

if (!mysqli_set_charset($conn, "utf8mb4")) {
  exit("Error setting character set: " . mysqli_error($conn));
}
