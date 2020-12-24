<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Initialize the session
session_start(); 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/Exception.php");
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/PHPMailer.php");
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/SMTP.php");
require_once "config.php";

$session_id = $_SESSION['id'];
$candi_id = $_GET['id'];
$voted = 1;
//Select vote count
$sql = "SELECT * FROM results WHERE vc_id = ?";
//prepare the select statement
$stmt = mysqli_prepare($link, $sql);
//bind
mysqli_stmt_bind_param($stmt, "i", $candi_id);
//ececute
mysqli_stmt_execute($stmt);
//update vote count by taking previous vote count
$result = mysqli_stmt_get_result($stmt);
$array = mysqli_fetch_assoc($result);
$prev_votes = $array['vote_count'];
$candidate_name = $array['name'];
$new_vote_count = $prev_votes + 1;
//close
mysqli_stmt_close($stmt);
$sql2 = "UPDATE results SET vote_count = ? WHERE vc_id= ?";
$stmt2 = mysqli_prepare($link, $sql2);
mysqli_stmt_bind_param($stmt2, "ii", $new_vote_count, $candi_id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);
$sql3 = "DELETE FROM users WHERE id=?";
$stmt3 = mysqli_prepare($link, $sql3);
mysqli_stmt_bind_param($stmt3, "i", $session_id);
mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3); 
// $sql4 = "DELETE FROM users_approval WHERE id=?";
// $stmt4 = mysqli_prepare($link, $sql4);
// mysqli_stmt_bind_param($stmt4, "i", $session_id);
// mysqli_stmt_execute($stmt4);
// mysqli_stmt_close($stmt4);
$sql5 = "UPDATE user_data SET voted = ? WHERE id = ?";
$stmt5 = mysqli_prepare($link, $sql5);
mysqli_stmt_bind_param($stmt5, "ii", $voted, $session_id);
mysqli_stmt_execute($stmt5);
mysqli_stmt_close($stmt5);
$sql6 = "SELECT * FROM user_data WHERE id= ?";
$stmt6 = mysqli_prepare($link, $sql6);
mysqli_stmt_bind_param($stmt6, "i", $session_id);
mysqli_stmt_execute($stmt6);
$result2 = mysqli_stmt_get_result($stmt6);
$array2 = mysqli_fetch_assoc($result2);
$email = $array2['email'];
$name = $array2['username'];

$admin_mail = '';

$mail = new PHPMailer;
          
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.mailgun.org';                     // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = '';   // SMTP username
$mail->Password = '';                           // SMTP password
$mail->Port = 587;                         // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, only 'tls' is accepted

$mail->From = $admin_mail ;
$mail->FromName = 'Shreyas M Kaushik';
$mail->addAddress($email);                 // Add a recipient

$mail->WordWrap = 150;    // Set word wrap to 50 characters
$mail->isHTML(true);                                 
$mailContent = "<h2>Thanks for Voting $name</h2>
                <p>This is to Confirm That you have Successfully Voted $candidate_name.</p>
                <p>With Regards</p>
                <a href = ''>Shreyas M Kaushik</a>";
$mail->Subject = "Vote Acknowledgement";
$mail->Body    = $mailContent;
if($mail->send()) {
    echo '<script>alert("Message Sent ")</script>';
    echo '<script>document.location.replace("index.php")</script>';
   
} else {
  echo 'Mailer Error: ' . $mail->ErrorInfo;
}
mysqli_stmt_close($stmt6);
$_SESSION = array();
session_destroy();
header("location: results.php");
?>