<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to admin page
if(!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] !== true){
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve User</title>
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
                        <h2 class="pull-left">Users Pending for Approval</h2>
                    </div>
                    <div class="clearfix">
                    <a href="candidates-add.php" class="btn btn-primary pull-left">Manage Candidates</a>
                    </div><br>
                    <?php
                    // Include config file
                    require "config.php";                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM users_approval";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Id</th>";
                                        echo "<th>EPIC No</th>";
                                        echo "<th>Email</th>";
                                        echo "<th>User Name</th>";
                                        echo "<th>Age</th>";
                                        echo "<th>Gender</th>";
                                        echo "<th>Requested At</th>";
                                        echo "<th>Approve</th>";
                                        echo "<th>Disapprove</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['epic_no'] . "</td>";
                                        echo "<td>" . $row['email'] . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['age'] . "</td>";
                                        echo "<td>" . $row['gender'] . "</td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        echo "<td>";
                                        echo "<a href='approve.php?id=". $row['id'] ."'><button type='button' class='btn btn-success'>Approve</button></a>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<a href='disapprove.php?id=". $row['id'] ."'><button type='button' class='btn btn-danger'>Disapprove</button></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
                     // Close connection
                    mysqli_close($link);
                    ?>
                </div>
                </div>
                </div>
                </div>
        </body>
</html>