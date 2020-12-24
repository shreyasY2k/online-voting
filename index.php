<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });

    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Candidates</h2>
                        </div>
                    <?php
                    // Include config file
                    require "config.php";
                    //$session_id = $_SESSION['id'];
                    // $sql1 = "SELECT cnt FROM users WHERE id=$session_id";
                    // $query1 = mysqli_query($link,$sql1);
                    // $array1 = mysqli_fetch_array($query1);
                    // if($array1['cnt'] == 0){          
                    // Attempt select query execution
                    $sql = "SELECT * FROM candidates";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>No</th>";
                                        echo "<th>Name</th>";
                                        echo "<th>Party</th>";
                                        echo "<th>Age</th>";
                                        echo "<th>Gender</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>" . $row['party'] . "</td>";
                                        echo "<td>" . $row['age'] . "</td>";
                                        echo "<td>" . $row['gender'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            echo"<a href = 'vote.php'> <button type='button' class='btn btn-success'>Vote a Candidate</button></a>";
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No Candidate records found.</em></p>";
                            echo "<p class='lead'><em>**Contact Admin to add Candidates**</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
                    //}
                    // else{
                    //     $sql = "SELECT id,name,party,vote_count FROM results";
                    // if($result = mysqli_query($link, $sql)){
                    //     if(mysqli_num_rows($result) > 0){
                    //         echo "<table class='table table-bordered table-striped'>";
                    //             echo "<thead>";
                    //                 echo "<tr>";
                    //                     echo "<th>No</th>";
                    //                     echo "<th>Name</th>";
                    //                     echo "<th>Party</th>";
                    //                     echo "<th>No of Votes</th>";
                    //                 echo "</tr>";
                    //             echo "</thead>";
                    //             echo "<tbody>";
                    //             while($row = mysqli_fetch_array($result)){
                    //                 echo "<tr>";
                    //                     echo "<td>" . $row['id'] . "</td>";
                    //                     echo "<td>" . $row['name'] . "</td>";
                    //                     echo "<td>" . $row['party'] . "</td>";
                    //                     echo "<td>" . $row['vote_count'] . "</td>";
                    //                 echo "</tr>";
                    //             }
                    //             echo "</tbody>";                            
                    //         echo "</table>";
                    //         // Free result set
                    //         mysqli_free_result($result);
                    //     } else{
                    //         echo "<p class='lead'><em>No records were found.</em></p>";
                    //     }
                    // } else{
                    //     echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    // }
                    // } 
                    mysqli_close($link);
                   
                    ?>
                </div>
                
            </div>
            
            <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to Election Voting System</h1>
    </div>
    <p>
    <a href="logout.php" class="btn btn-danger pull-right">Sign Out of Your Account</a>
        <a href="reset-password.php" class="btn btn-warning pull-left">Reset Your Password</a>
       
    </p>    
        </div>
       
    </div>
    
</body>
</html>