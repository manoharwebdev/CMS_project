<?php 
//Include the php file being used to connect to server and database
require ("include/db.php");
//Include datetime php file
require ("include/datetime.php");
//Include the session file for category name error message
require ("include/sessions.php");
//Include the function file
require ("include/functions.php");

if (isset($_GET["id"])) {

    $idFromUrl = $_GET["id"];
    $admin = $_SESSION["user_name"];

    //Update Query
    $updateQuery = "UPDATE comments SET status = 'ON', approvedby = '$admin'  WHERE id = '$idFromUrl'";

    //Run Query
    $run_updateQuery = mysqli_query($connection, $updateQuery);

    //Check if update is succesfull
    if ($run_updateQuery) {
            $_SESSION["successMessage"] = "Comment Approved Succesfully";
            redirect_To ("comments.php"); //Created a function for header redirects - See Function file
            exit;
        } else {
            echo mysqli_error($connection);
    }
}

?>

