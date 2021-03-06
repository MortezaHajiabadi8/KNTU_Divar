<?php
session_start();
// Initialize the session

// // Check if the user is already logged in, if yes then redirect him to welcome page
// if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
//     header("location: quize.php");
//     exit;
// }

// Include config file
require_once "config.php";



// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username ,password FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;
            var_dump($stmt);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {

                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Store data in COOKie variables
                            $expire = time() + 60 * 60 * 24 * 30;
                            setcookie("username", $username, $expire, "/");
                            header("location: ../home.php");
                            exit;
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                            $_SESSION['password_err'] = "گذرواژه وارد شده درست نمیباشد";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                    $_SESSION['username_err'] =  "اکانتی با این نام کاربری وجود ندارد";

                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            if (!isset($_COOKIE['username'])) {
                // Redirect user to login page
                echo"klfjas";
                header("location: ../login.php");
            }

            // Close statement
            mysqli_stmt_close($stmt);
  

        }
    }
    // var_dump(get_defined_vars());
    mysqli_close($link);
    header("location: ../login.php");
    

}

?>
