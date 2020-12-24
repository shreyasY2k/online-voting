<?php
$password = trim($_POST['password']);
$password_err = "";
require_once "config.php";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sql = "SELECT password FROM admin";
    $stmt = mysqli_prepare($link,$sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $array = mysqli_fetch_assoc($result);
    $admin_password = $array["password"];
    $md_admin_password = md5($password);
    mysqli_stmt_close($stmt);
    if($admin_password == $md_admin_password){
        // Password is correct, so start a new session
        session_start();
    
        // Store data in session variables
        $_SESSION["logged-in"] = true;                    
        // Redirect user to welcome page
        header("location: options.php");
    }
    else{
        $password_err = "Password Invalid";
    }
     
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Admin</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">  
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>    
</body>
</html>