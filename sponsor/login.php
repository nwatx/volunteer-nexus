<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to dashboard
if(isset($_SESSION["sponsor_loggedin"]) && $_SESSION["sponsor_loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

// Include config file
require_once "../config.php";

// Define variables and initialize with empty values
$username = "";
$password = "";
$username_error = "";
$password_error = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_error = "Please enter your email address.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_error = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_error) && empty($password_error)){
        // Prepare a select statement
        $sql = "SELECT sponsor_id, sponsor_name, contribution_type, username, password FROM sponsors WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $sponsor_id, $sponsor_name, $contribution_type, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            if(session_status() !== PHP_SESSION_ACTIVE) session_start();

                            // Store data in session variables
                            $_SESSION["sponsor_loggedin"] = true;
                            $_SESSION["sponsor_id"] = $sponsor_id;
                            $_SESSOPM["username"] = $username;
                            $_SESSION["sponsor_name"] = $sponsor_name;
                            $_SESSION["contribution_type"] = $contribution_type;

                            // Redirect user to dashboard
                            header("location: dashboard.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_error = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_error = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Sponsor Login</title>

        <!--Load required libraries-->
        <?php $pageContent='Form'?>
        <?php include '../head.php'?>

    <style type="text/css">
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sponsor Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_error)) ? 'has-error' : ''; ?>">
                <label>Email Address</label>
                <input type="email" name="username" class="form-control" placeholder="Email" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_error; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_error)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" class="form-control">
                <span class="help-block"><?php echo $password_error; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>
