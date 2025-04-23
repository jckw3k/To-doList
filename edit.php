<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "webprogramming";

// create connection to database
$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$taskID = "";
$description = "";
$taskdatecreated = "";
$taskduedate = "";
$note = "";
$imagePath = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"])) {
        header("location: index.php");
        exit;
    } else {
        $taskID = $_GET["id"];
        // read the row of selected task from database table
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
        $note = $row["note"];
        $imagePath = $row["imagePath"];
    }
} else {
    // POST -> update data of task
    $taskID = $_POST["taskID"];
    $description = $_POST["description"];
    $taskduedate = $_POST["taskduedate"];
    $taskdatecreated = $_POST["taskdatecreated"];
    $note = $_POST["note"];
    $imagePath = $_POST["existingImagePath"]; // Retrieve existing image path

    if (isset($_POST['delete_image'])) {
        // Delete the image
        if (!empty($imagePath)) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
                $sql = "UPDATE tasks SET imagePath = NULL WHERE taskID = $taskID";
                $resultDelete = $connection->query($sql);
                if ($resultDelete) {
                    $successMessage = "Image deleted successfully";
                    header("location: edit.php?id=$taskID");
                    exit;
                } else {
                    $errorMessage = "Error deleting image: " . $connection->error;
                }
            }
        }
    } else {
        if (empty($description)) {
            $errorMessage = "Must specify task description.";
        } else {
            // Convert the dates to DateTime objects for comparison
            $createdDate = new DateTime($taskdatecreated);
            $dueDate = new DateTime($taskduedate);
            if ($dueDate < $createdDate) {
                $errorMessage = "Due date must be after the creation date.";
            } else {
                $uploadOk = 1;
                if (!empty($_FILES["imageFile"]["tmp_name"])) {
                    $targetDir = "Projimg/";
                    $targetFile = $targetDir . basename($_FILES["imageFile"]["name"]);
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                    // Check if image file is an actual image or fake image
                    $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
                    if ($check === false) {
                        $errorMessage = "File is not an image.";
                        $uploadOk = 0;
                    }

                    // Check file size
                    if ($_FILES["imageFile"]["size"] > 500000) {
                        $errorMessage = "Sorry, your file is too large.";
                        $uploadOk = 0;
                    }

                    // Allow certain file formats
                    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
                    if (!in_array($imageFileType, $allowedFormats)) {
                        $errorMessage = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                        $uploadOk = 0;
                    }

                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 1) {
                        if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFile)) {
                            $imagePath = $targetFile;
                        } else {
                            $errorMessage = "Error uploading file.";
                            $uploadOk = 0;
                        }
                    }
                }

                if ($uploadOk == 1) {
                    $sql = "UPDATE tasks SET description = '$description', taskduedate = '$taskduedate', note = '$note'";
                    if (empty($imagePath)) {
                        $sql .= ", imagePath = NULL";
                    } else {
                        $sql .= ", imagePath = '$imagePath'"; // .= to append sql statement
                    }
                    $sql .= " WHERE taskID = $taskID";

                    $result = $connection->query($sql);
                    if (!$result) {
                        $errorMessage = "Invalid query: " . $connection->error;
                    } else {
                        $successMessage = "Task updated successfully";
                        header("location: details.php?id=$taskID");
                        exit;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="style.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Edit.php</title>
</head>
<body>
<header>
    <div class="head-nav">
        <a href="#" id="logo"><img src="Projimg/npclogo2W.png" alt="nothing"/></a>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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

    <h1><b>Edit Task Details</b></h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="taskID" value="<?php echo $taskID ?>">
        <input type="hidden" name="existingImagePath" value="<?php echo $imagePath ?>">
        <input type="hidden" name="taskdatecreated" value="<?php echo $taskdatecreated ?>">
       <?php 
        if (!empty($errorMessage)) {
            echo"
            <script>
            Swal.fire({
                title: 'Error',
                text: '$errorMessage',
                icon: 'error',
            });
            </script>";
        }

        if(!empty($successMessage)){
            echo "
            <script>
                Swal.fire({
                icon: 'success',
                title: '$successMessage',
                showConfirmButton: false,
                timer: 1500
                });
             </script>";
        }

        ?>

        <div class="details-outer-container">
            <div class="edit-container">
                <div class="details-label"><b>Task:</b></div>
                <div><input class="edit-item" type="text" name="description" value="<?php echo $description ?>"><br></div>
            </div>


            <div class="details-container">
                <div class="details-label"><b>Created:</b></div>
                <div><span class="details-item"><?php echo $taskdatecreated; ?></span></div>
            </div>

            <div class="edit-container">
                <div class="details-label"><b>Due:</b></div>
                <div><input class="edit-item" type="date" name="taskduedate"
                            value="<?php echo $taskduedate ?>"><br></div>
            </div>

            <div class="edit-container">
                <div class="details-label"><b>Notes:</b></div>
                <div style="padding-top:20px"><textarea class="edit-item" type="text" name="note" id="note" rows="5"
                                                        cols="28"
                                                        placeholder="notes"><?php echo $note ?></textarea></div>
            </div>

            <div class="edit-container">
                <div class="details-label"><b>Image:</b></div>
                <div class="edit-image-container">
                    <div><input class="edit-item" type="file" name="imageFile"><br></div>
                    <div>
                        <?php if (!empty($imagePath)) : ?>
                            <div id="image-container" class="details-item" id="image"><img src="<?php echo $imagePath; ?>"
                                                                                           alt="Task image"></div>
                            <button type="submit" name="delete_image" class="btn btn-danger mt-2"><i
                                    class="bi bi-trash3"></i> Delete Image
                            </button>


                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <br>
            <div class="button-container">
                <div><a href="details.php?id=<?php echo $taskID ?>" class="btn btn-primary" id="back-button"
                        style="color:white"><i class="bi bi-caret-left-fill"></i> Back</a></div>
                <div>
                    <button type="submit" class="btn btn-success" id="back-button" style="color:white">Save changes
                    </button>
                </div>
            </div>
    </form>

</div>
</body>
</html>
