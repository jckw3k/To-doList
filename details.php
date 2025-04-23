<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "webprogramming";

// Create connection to database
$connection = new mysqli($servername, $username, $password, $database);

$taskID = "";
$description = "";
$taskdatecreated = "";
$taskduedate = "";
$note="";
$imagePath  = "";

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // GET -> show data of task
    if (!isset($_GET['id'])) {
        header("location: index.php");
        exit;
    } else {
        $taskID = $_GET["id"];
        // Read the row of selected task from database table
        $sql = "SELECT * FROM tasks WHERE taskID=$taskID";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            header("location: index.php");
            exit;
        }

        $description = $row["description"];
        $taskdatecreated = $row["taskdatecreated"];
        $taskduedate = $row["taskduedate"];
        $note= $row["note"];
        $imagePath = $row["imagePath"]; 
    }
} else {
    // Redirect to index.php if request method is POST
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <title>Details.php</title>
</head>
<body>
<header>
    <div class="head-nav">
        <a href="index.php" id="logo"><img src="Projimg/npclogo2W.png" alt="nothing" /></a>
    </div>
    <div class="btn-group">
        <button
          type="button"
          class="btn btn-success dropdown-toggle"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="bi bi-person-circle"></i>
        </button>
        <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="logout.php">Logout</a>
          </li>
        </ul>
    </div>
</header>
<div class="main">
    <h1><b>Task Details</b></h1>
    <div class='details-outer-container'>
        <div class="details-container">
          <div class="details-label" ><b>Task:</b></div>
          <div><span class="details-item"><?php echo $description; ?></span></div>
        </div>

        <div class="details-container">
        <div class="details-label" ><b>Created:</b></div>
        <div><span class="details-item"><?php echo $taskdatecreated; ?></span></div>
        </div>

        <div class="details-container">
        <div class="details-label" ><b>Due:</b></div>
        <div><span class="details-item"><?php echo $taskduedate; ?></span></div>
        </div>

        <div class="details-container">
        <div class="details-label" ><b>Notes:</b></div>
        <div  id="notes-container"  class="details-item" id="tasknotes" ><span><?php echo $note; ?></span></div>
        </div>

        <!-- wont display anything if there isn't any image -->
        <?php if (!empty($imagePath)) : ?> 
        <div class="details-container">
        <div class="details-label" ><b>Image:</b></div>
        <div id="image-container"  class="details-item" id="image" > <img src="<?php echo $imagePath; ?>" alt="Task image" ></div>
      </div>
      <?php endif; ?> 
        <br>
        <div class="button-container">
        <div><a href="index.php"  class="btn btn-primary" id="back-button" style="color:white"><i class="bi bi-caret-left-fill"></i> Back</a></div>
        <div><a href="edit.php?id=<?php echo $taskID; ?>" class="btn btn-primary" id="edit-button" style="color:white"><i class="bi bi-pencil-square"></i></a></div>
        </div>
    </div>
</div>
</body>
</html>
