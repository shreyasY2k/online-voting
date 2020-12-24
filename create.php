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
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate party
    $input_party = trim($_POST["party"]);
    if(empty($input_party)){
        $party_err = "Please enter an party.";     
    } else{
        $party = $input_party;
    }
    
    // Validate age
    $input_age = trim($_POST["age"]);
    if(empty($input_age)){
        $age_err = "Please enter the age";     
    } elseif(!ctype_digit($input_age)){
        $age_err = "Please enter a positive integer value.";
    }elseif($input_age < 18){
        $age_err = "You are not Eligible to Contest";
    } else{
        $age = $input_age;
    }

    // Validate Gender
    $input_gender = trim($_POST["gender"]);
    if(empty($input_gender)){
        $gender_err = "Please select a Gender.";     
    } else{
        $gender = $input_gender;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($party_err) && empty($age_err) && empty($gender_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO candidates (name, party, age, gender) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssis", $param_name, $param_party, $param_age , $param_gender);
            
            // Set parameters
            $param_name = $name;
            $param_party = $party;
            $param_age = $age;
            $param_gender = $gender;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //Get id from candidates table to update the same in vote_count table
                // $sql_cand = "SELECT id,name,party FROM candidates WHERE name='$param_name' AND party='$param_party'";
                // $cand_result = mysqli_query($link, $sql_cand);
                // $array = mysqli_fetch_array($cand_result);
                // $cand_id = $array['id'];
                // $cand_name = $array['name'];
                // $cand_party = $array['party'];
                // $sql3 = "INSERT INTO results(vc_id,name,party) VALUES ('$cand_id','$cand_name','$cand_party')";
                // $result2 = mysqli_query($link,$sql3);
                // Records created successfully. Redirect to landing page
                header("location: candidates-add.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
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
    <title>Create Record</title>
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
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Candidate Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($party_err)) ? 'has-error' : ''; ?>">
                            <label>Party</label>
                            <input type="text" name="party" class="form-control"><?php echo $party; ?>
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
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="admin.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>