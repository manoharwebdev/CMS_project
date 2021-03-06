<?php 

//Include the php file being used to connect to server and database
require_once ("include/db.php");
//Include datetime php file
require_once ("include/datetime.php");
//Include the session file for category name error message
require_once ("include/sessions.php");
//Include the function file
require_once ("include/functions.php");
//Check if admin is logged in - If not, redirect to login.php - Check function file
confirmLogin ();

if (isset($_POST["Submit"])) {

    //Get the category name
    //mysqli_real_escape_string() is used to avoid sql injection
    $categoryName = $_POST["Category"];
    $categoryName = mysqli_real_escape_string($connection, $_POST["Category"]);

    //Check if the category name field is empty
    
    if (empty($categoryName)) {

        $_SESSION["errorMessage"] = "Category Name Cannot Be Empty. Try Again!";
        redirect_To ("Categories.php"); //Created a function for header redirects - See Function file
        exit;

    } elseif (strlen($categoryName) > 100 || strlen($categoryName) < 3) { //Validation of $categoryName > 100 charactres

        $_SESSION["errorMessage"] = "Category Name Must Be Between 3 to 100 Characters Long";
        redirect_To ("Categories.php"); //Created a function for header redirects - See Function file
        exit;

    } else {

        //Create a Category Creater name
        $creatorName = $_SESSION["user_name"];

        //Insert Values in to category table
        $createQuery = "INSERT INTO categories(datetime, name, creatorname) VALUES ('$dateTime','$categoryName','$creatorName')";

        //Run the query to insert the value in to the database
        $run_createQuery = mysqli_query($connection, $createQuery);

        //If values are inserted succesfully or not validation
        if ($run_createQuery) {
            $_SESSION["successMessage"] = "Category: {$categoryName} Succesfully Created";
            redirect_To ("Categories.php"); //Created a function for header redirects - See Function file
            exit;
        } else {
            echo mysqli_error($connection);
        }
    }
}    

?>
<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta http-equiv="x-ua-compatible" content="ie=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/adminstyle.css">
        <title>Manage Categories</title>
    </head>

    <body>

        <!-- Nav Section -->
        <div class="col-md-12 bg-primary pb-2">
                        
        </div>
        <nav class="navbar navbar-expand-md bg-dark navbar-dark">
            <a href="Dashboard.php" class="navbar-brand pl-5">WEB-SKILLS</a>
            <div class="container">
                <button class="navbar-toggler" data-target="#navbarNav" data-toggle="collapse"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a href="Dashboard.php" class="nav-link">Home</a></li>
                        <li class="nav-item"><a href="blog.php?page=1" target="_blank" class="nav-link">Blog</a></li>
                        <li class="nav-item"><a href="aboutus.php" target="_blank" class="nav-link">About Us</a></li>
                        <li class="nav-item"><a href="services.php" target="_blank" class="nav-link">Services</a></li>
                    </ul>
                    <div class="text-white ml-auto small">
                        <u>Admin Logged in : 
                            <?php
                                login ();
                                if (login()) {
                                    echo $_SESSION["user_name"];
                                } else {
                                    echo "None";
                                }
                            ?>
                        </u>
                    </div>
                    <form action="blog.php" class="form-inline ml-auto">
                        <input class="form-control mr-2" placeholder="Search" name="Search" type="text">
                        <button class="btn btn-success" name="searchButton">Go</button>
                    </form>
                </div>
            </div>
        </nav>
        
        <section>
            <div class="container-fluid">
                <div class="row">
                    <!-- Left Side Area-->
                    <div class="col-sm-2 pt-4">
                        <ul id="side_Menu" class="nav nav-pills d-block nav-fill">
                            <li class="nav-item"><a id="navHover" class="nav-link" href="Dashboard.php">
                                <i class="fa fa-th" aria-hidden="true"></i>&nbsp;Dashboard</a></li>
                            <li class="nav-item"><a id="navHover" class="nav-link" href="addnewpost.php">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>&nbsp;Add New Post</a></li>
                            <li class="nav-item"><a id="navHover" class="nav-link active" href="categories.php">
                                <i class="fa fa-tags" aria-hidden="true"></i>&nbsp;Categories</a></li>
                            <li class="nav-item"><a id="navHover" class="nav-link" href="manageadmins.php">
                            <i class="fa fa-user" aria-hidden="true"></i>&nbsp;Manage Admins</a></li>
                            <li class="nav-item"><a id="navHover" class="nav-link" href="comments.php">
                                <i class="fa fa-comment" aria-hidden="true"></i>&nbsp;Comments
                                <?php

                                                //Count For Total UnApproved comments 

                                                //Select and count all comments from comments table where the id matches
                                                $queryTotalUnapprovedComments = "SELECT COUNT(*) FROM comments WHERE status='OFF'";

                                                //Run query
                                                $run_queryTotalUnapprovedComments = mysqli_query ($connection, $queryTotalUnapprovedComments);

                                                //Fetch the counted array
                                                $rowsTotalUnapproved = mysqli_fetch_array($run_queryTotalUnapprovedComments);
                                                $totalUnapproved = array_shift($rowsTotalUnapproved);

                                                if ($totalUnapproved > 0) {

                                            ?>
                                            <span class="badge badge-warning m-2">
                                                <?php    
                                                    echo $totalUnapproved;
                                                ?>
                                            </span>
                                            <?php } ?>
                            </a></li>
                            <li class="nav-item"><a id="navHover" class="nav-link" href="blog.php?page=1" target="_blank">
                                <i class="fa fa-spinner" aria-hidden="true"></i>&nbsp;Live Blog</a></li>
                            <li class="nav-item"><a id="navHover" class="nav-link" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;Logout</a></li>
                        </ul>
                    </div>

                    <!-- Add Category Area-->
                    <div class="col-sm-10">
                        <h1 class="text-center py-3 text-info">MANAGE CATEGORIES</h1>
                        <!-- Error/Success Message for Category Name Field -->
                        <div class="text-center">
                            <?php 
                            echo categoryNameEmptyMessage(); 
                            echo categoryNameSuccessMessage(); 
                            ?>
                        </div>
                        <h2 class="lead py-3">Add New Category:</h2>
                        <form action="Categories.php" method="POST">
                            <div class="input-group">
                                <input type="text" name="Category" class="form-control" placeholder="Category Name" aria-describedby="basic-addon1">
                                <div class="input-group-append">
                                    <input type="submit" name="Submit" class="btn btn-success" value="Add Category" id="basic-addon1">
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive py-5">
                            <h3 class="text-muted pb-3 text-center">List of Categories</h3>
                            <table class="table table-hover text-center table-bordered">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Created Date</th>
                                        <th>Category</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 

                                        //ORDER BY id desc - selects the data by datetime value in descending order
                                        
                                        //Select Table
                                        $selectQuery = "SELECT * FROM categories ORDER BY id desc";
                                        //Run query
                                        $run_selectQuery = mysqli_query ($connection, $selectQuery);

                                        //We do not want id no. from database which will be scattered - Instead we want an ordered no.
                                        $serialNumber = 0;

                                        //Write the selected array data in table
                                        while ($rows = mysqli_fetch_array($run_selectQuery)) {

                                            $categoryId = $rows["id"];
                                            $createdDate = $rows["datetime"];
                                            $createdCategory = $rows["name"];
                                            $createdBy = $rows["creatorname"];
                                            $serialNumber++;

                                    ?>    
                                    <tr>
                                        <td><?php echo $serialNumber; ?></td>
                                        <td><?php echo $createdDate; ?></td>
                                        <td><?php echo $createdCategory; ?></td>
                                        <td><?php echo $createdBy; ?></td>
                                        <td><a href="deletecategories.php?id=<?php echo $categoryId; ?>" class="btn btn-danger btn-sm">Delete</a></td>
                                    </tr>
                                    <!-- Close the loop after the data is written in to tbody so that it loops again and writes to tbody again - Not just once -->
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer Section-->
        <section id="footer" class="bg-dark text-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-center pt-3">Designed & Developed By Manohar S | &copy; 2018 - All Rights Reserved</p>
                    </div>
                    <div class="col-md-12 bg-primary pb-2">
                        
                    </div>
                </div>
            </div>
        </section>

        <script src="js/jquery.min.js"></script>
        <script src="js/popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>