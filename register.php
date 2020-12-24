<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Include config file
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/Exception.php");
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/PHPMailer.php");
require("/home/ubuntu/vendor/phpmailer/phpmailer/src/SMTP.php");
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $age = $email = $gender = $epic_no = "";
$username_err = $password_err = $confirm_password_err = $age_err = $email_err = $gender_err = $epic_no_err ="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (empty(trim($_POST["email"]))) {
        $email_err = "Email is Required";
      } else {
        $email = trim($_POST["email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $email_err = "Invalid email format";
        }
      }

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } 
            else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    //Validate Epic
    if(empty(trim($_POST["epic_no"]))){
        $epic_no_err = "Please enter your EPIC number.";     
    } elseif(strlen(trim($_POST["epic_no"])) != 8){
        $epic_no_err = "Please enter a valid EPIC number. EPIC number has 8 Characters";
    } else{
        $epic_no = trim($_POST["epic_no"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    // Validate age
    $input_age = trim($_POST["age"]);
    if(empty($input_age)){
        $age_err = "Please enter the age";     
    } elseif(!ctype_digit($input_age)){
        $age_err = "Please enter a positive integer value.";
    } elseif($input_age < 18){
        $age_err = "You are not Eligible to Vote";
    }else{
        $age = $input_age;
    }
    // Validate Gender
    $input_gender = $_POST["gender"];
    if(empty($input_gender)){
        $gender_err = "Please select a Gender.";     
    } else{
        $gender = $input_gender;
    }
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($age_err) && empty($email_err) && empty($gender_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users_approval (epic_no,email,username, password,age,gender) VALUES (?, ?, ?, ?, ?,?)";
//        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssis", $param_epic, $param_email, $param_username, $param_password,$param_age,$param_gender);
//            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Set parameters
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_age = $age;
            $param_gender = $gender;
            $param_epic = $epic_no;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
            $mail = new PHPMailer;
            $admin_mail = '';
            $mymail = '';        
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.mailgun.org';                     // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = '';   // SMTP username
            $mail->Password = '';                           // SMTP password
            $mail->Port = 587;                         // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable encryption, only 'tls' is accepted

            $mail->From = $admin_mail;
            $mail->FromName = '';
            $mail->addAddress($mymail);                 // Add a recipient

            $mail->WordWrap = 150;    // Set word wrap to 50 characters
            $mail->isHTML(true);                                 
            $mailContent = "<p>Hey Shreyas a new user with username: $username and email: $email has registered. Please verify and approve</p>
                            <a href = ''><button type='button'>Approve</button></a>";
            $mail->Subject = "User Approval";
            $mail->Body    = $mailContent;
            if($mail->send()) {
                echo '<script>alert("Message Sent ")</script>';
                echo '<script>document.location.replace("index.php")</script>';
            
            } else {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head> 

<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($epic_no_err)) ? 'has-error' : ''; ?>">
                <label>EPIC Number</label>
                <input type="text" name="epic_no" class="form-control" value="<?php echo $epic_no; ?>">
                <span class="help-block"><?php echo $epic_no_err; ?></span>
            </div>
        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($age_err)) ? 'has-error' : ''; ?>">
                <label>Age</label>
                <input type="text" name="age" class="form-control" value="<?php echo $age; ?>">
                <span class="help-block"><?php echo $age_err;?></span>
            </div>  
            <div class="form-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                <label>Gender</label><br>
                <input type="radio" id="male" name="gender" value="Male">
                <label for="male">Male</label><br>
                <input type="radio" id="female" name="gender" value="Female">
                <label for="female">Female</label><br>
                <input type="radio" id="other" name="gender" value="Other">
                <label for="other">Other</label>
                <span class="help-block"><?php echo $gender_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>
