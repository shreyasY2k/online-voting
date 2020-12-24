<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to admin page
if(!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] !== true){
    header("location: index.php");
    exit;
}
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $party = $age = $gender = "";
$name_err = $party_err = $age_err = $gender_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate party party
    $input_party = trim($_POST["party"]);
    if(empty($input_party)){
        $party_err = "Please enter an party.";     
    } else{
        $party = $input_party;
    }
    
    // Validate age
    $input_age = trim($_POST["age"]);
    if(empty($input_age)){
        $age_err = "Please enter the age amount.";     
    } elseif(!ctype_digit($input_age)){
        $age_err = "Please enter a positive integer value.";
    }elseif($input_age < 18){
        $age_err = "You are not Eligible to Vote";
    } else{
        $age = $input_age;
    }
    // Validate Gender
    $input_gender = $_POST["gender"];
    if(empty($input_gender)){
        $gender_err = "Please enter an party.";     
    } else{
        $gender = $input_gender;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($party_err) && empty($age_err) && empty($gender_err)){
        // Prepare an update statement
        $sql = "UPDATE candidates SET name=?, party=?, age=? ,gender=? WHERE id=?";
        //$sql1 = "UPDATE results SET name=?,party=? WHERE id=?";
        if($stmt = mysqli_prepare($link, $sql)){
            //$stmt1 = mysqli_prepare($link,$sql1);
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssisi", $param_name, $param_party, $param_age, $param_gender ,$param_id);
            //mysqli_stmt_bind_param($stmt1, "ss", $param_name, $param_party, $param_id);
            // Set parameters
            $param_name = $name;
            $param_party = $party;
            $param_age = $age;
            $param_gender = $gender;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //mysqli_stmt_execute($stmt1);
                // Records updated successfully. Redirect to landing page
                header("location: candidates-add.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        //mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM candidates WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["name"];
                    $party = $row["party"];
                    $age = $row["age"];
                    $gender = $row["gender"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Candidate Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($party_err)) ? 'has-error' : ''; ?>">
                            <label>Party</label>
                            <textarea name="party" class="form-control"><?php echo $party; ?></textarea>
                            <span class="help-block"><?php echo $party_err;?></span>
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
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="admin.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>