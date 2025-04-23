<?php
    session_start(); // Start the session
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "webprogramming"; 

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
}

    $errorMessage = "";
    $email = "";
    $password = "";
    // $cookie_email = "";
    // $cookie_password = "";

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $email = $_POST['email'];
        $password = $_POST['password'];

        if(!is_numeric($email) && !empty($password)){
            $sql = "SELECT * from user 
            WHERE email = '$email' and password = '$password' ";

            $result = $connection->query($sql); 
            
            $result = mysqli_query($connection, $sql);

            $count = mysqli_num_rows($result);
            if($count > 0){
                $row = $result->fetch_assoc();// fetch the user data 
                setcookie('email', $email, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/"); 
                $_SESSION['userID'] = $row['userID']; // Store userID in session
                header("location: index.php");
                exit;
            }else{
                $errorMessage = "Wrong email or password, please fill in again";
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

    <title>Login</title>
</head>
<body>
    <div class="login">
        <div class="logo-container">
        <div><img src="Projimg/npclogo2.png" width="70px" height="100%"></div>
        <div><h1>Team NPC</h1></div>
        </div>
        
        
        <!-- <h2><b>TO-DO List <i  class="bi bi-list-check"></i></b></h2> -->
        <h2>Login</h2>
        <form class="login-form" method="POST">
            <?php
                if (!empty($errorMessage)) {
                    echo "<p style='color: red;'>$errorMessage</p>";
                }
            ?>
            
            <input type="text" placeholder="Email address" name="email" required> 
            <input type="password" placeholder="Password" name="password" required>
            <button type="submit">Login</button>
            Do not have an account?<a href="signUp.php">SignUp</a>
        </form>
    </div>
</body>
</html>