<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to admin page
if(!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] !== true){
    header("location: index.php");
    exit;
}
?>
<?php
require_once "config.php";
$id = $_GET['id'];
$sql = "DELETE FROM users_approval WHERE id= ?";
$stmt = mysqli_prepare($link,$sql);
mysqli_stmt_bind_param($stmt,'i',$id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
header("location: user-approval.php");
?>