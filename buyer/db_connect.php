<?Php
$con = mysqli_connect("localhost", "root", "", "ecostore");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>