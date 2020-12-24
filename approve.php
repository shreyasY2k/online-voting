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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Include config file
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/Exception.php");
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/PHPMailer.php");
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/SMTP.php");
require_once "config.php";
$id = $_GET['id'];
$sql = "SELECT * FROM users_approval WHERE id=?";
$stmt = mysqli_prepare($link,$sql);
mysqli_stmt_bind_param($stmt,'i',$id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$array = mysqli_fetch_assoc($result);
$username = $array["username"];
$password= $array['password'];
$email = $array["email"];
$age = $array["age"];
$gender = $array["gender"];
$epic_no = $array["epic_no"];
mysqli_stmt_close($stmt);
$mail = new PHPMailer;
            $admin_mail = 'me@shreyasmk.me';       
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.mailgun.org';                     // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = '';   // SMTP username
            $mail->Password = '';                           // SMTP password
            $mail->Port = 587;                         // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable encryption, only 'tls' is accepted

            $mail->From = $admin_mail;
            $mail->FromName = 'Shreyas M Kaushik';
            $mail->addAddress($email);                 // Add a recipient

            $mail->WordWrap = 150;    // Set word wrap to 50 characters
            $mail->isHTML(true);                                 
            $mailContent = "<p>Hey! $username. You are now approved to Vote.</p>
                            <p>Please Login with correct credentials to Vote.</p>
                            <p>With Regards!</p>
                            <a href=''>Shreyas M Kaushik</a>";
            $mail->Subject = "User Approved!";
            $mail->Body    = $mailContent;
            if($mail->send()) {
                echo '<script>alert("Message Sent ")</script>';
                echo '<script>document.location.replace("index.php")</script>';
            
            } else {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
$sql1 = "INSERT INTO users(username,password) VALUES (?,?)";
$stmt1 = mysqli_prepare($link,$sql1);
mysqli_stmt_bind_param($stmt1,'ss',$username,$password);
mysqli_stmt_execute($stmt1);
mysqli_stmt_close($stmt1);
$sql2 = "INSERT INTO user_data(epic_no,email,username,age,gender) VALUES (?,?,?,?,?)";
$stmt2 = mysqli_prepare($link,$sql2);
mysqli_stmt_bind_param($stmt2,'sssis',$epic_no,$email,$username,$age,$gender);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);
$sql3 = "DELETE FROM users_approval WHERE id= ?";
$stmt3 = mysqli_prepare($link,$sql3);
mysqli_stmt_bind_param($stmt3,'i',$id);
mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);
header("location: user-approval.php");
?>