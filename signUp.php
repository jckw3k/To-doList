<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "webprogramming";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $errorMessage = "";
    $successMessage = "";

    $fname = "";
    $lname = "";
    $email = "";
    $password = "";
    // $confirmPassword = "";

    if( $_SERVER ["REQUEST_METHOD"] == "POST"){
        $fname = $_POST ["firstName"];
        $lname = $_POST ["lastName"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        // $confirmPassword = $_POST["confirmPassword"];

        
            if(!empty($fname) && !empty( $lname) && !is_numeric($email) && !empty($password)){
                $sql = "INSERT INTO user (fname, lname, email, password)
                VALUES ('$fname', '$lname', '$email', '$password')";

                // mysqli_query($connection, $query);

                if ($connection->query($sql) === TRUE) {
                    $successMessage = "You have successfully registered! Click Login to continue. ";
                        // Clear input fields
                    $fname = "";
                    $lname = "";
                    $email = "";
                    $password = "";
                } else {
                    $errorMessage = "Error: " . $sql . "<br>" . $conn->error;
                }
        }
            
    }

        $connection->close();
   ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <link href="loginStyle.css" rel="stylesheet">

    <title>SignUp</title>

</head>
<body>
    <div class="login">
    <div class="logo-container">
        <div><img src="Projimg/npclogo2.png" width="70px" height="100%"></div>
        <div><h1>Team NPC</h1></div>
        </div>
        <!-- <h2><b>TO-DO List <i  class="bi bi-list-check"></i></b></h2> -->
        <h2>Sign Up</h2>
        <form class="login-form" method="POST" action="">
        <?php
                if (!empty($errorMessage)) {
                    echo "<p style='color: red;'>$errorMessage</p>";
                }
                if (!empty($successMessage)) {
                    echo "<p style='color: green;'>$successMessage</p>";
                }
            ?>
            <input type="text" placeholder="First name" name="firstName" value="<?php echo $fname; ?>" required>
            <input type="text" placeholder="Last name" name="lastName" value="<?php echo $lname; ?>" required>
            <input type="text" placeholder="Email address" name="email" value="<?php echo $email; ?>" required>
            <input type="password" placeholder="Password" name="password" value="<?php echo $password; ?>" required>
            <!-- <input type="password" placeholder="Confirm your password" name="confirmPassword" required> -->
            <button type="submit">Sign Up</button>
            Already have an account?<a href="login.php">Login</a>
        </form>
    </div>
</body>
</html>