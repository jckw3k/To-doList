<?php
session_start(); // Start the session to retrieve the userID
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "webprogramming";

            // create connection 
            $connection = mysqli_connect($servername, $username, $password, $database);
            if (mysqli_connect_errno()) {
              die("". mysqli_connect_error());
            }
        
        $description = ""; 
        $errorMessage = "";
        $successMessage = "";
       
        //chech if the user is logged in
        if (!isset($_SESSION['userID'])) {
          header("Location: login.php");
          exit;
        } 
      
        // Retrieve the current user's userID from the session
        $currentUserID = $_SESSION['userID']; 

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
          $description = $_POST["description"]; 

          do{
            if(empty($description)){
              $errorMessage = "Cannot add empty task.";
              break;
            }
            //add new task to database
            $sql = "INSERT INTO tasks(description,userID)"."VALUES ('$description', '$currentUserID')";
            $result = $connection->query($sql);
            if(!$result){
              $errorMessage = "Error inserting data into database". $connection->error;
              echo "<script>
              swal({
                   title: 'Error',
                   text: '$errorMessage',
                   icon: 'warning',
               });
               </script>";
              break;
            }
            $description = ""; 
            $successMessage = "Task added successfully";
          }while(false);
        }  

        $choice = 0; // Default choice to display all tasks
        if (isset($_GET['choice'])) {
          $choice = intval($_GET['choice']);
      }
      function sortDueDate($currentUserID){
          $sql = "SELECT * FROM tasks WHERE userID = '$currentUserID' ORDER BY taskduedate";
          return $sql;
      }
      function filterComplete($currentUserID){
          $sql = "SELECT * FROM tasks WHERE userID = '$currentUserID' AND isCompleted = 1 ORDER BY taskduedate";
          return $sql;
      }
      
      function filterIncomplete($currentUserID){
          $sql = "SELECT * FROM tasks WHERE userID = '$currentUserID' AND isCompleted = 0 ORDER BY taskduedate";
          return $sql;
      }


      function displayAll($currentUserID,$choice,$connection){

          switch ($choice) {
              case 1:
                  $sql = sortDueDate($currentUserID);
                  break;
              case 2:
                  $sql = filterComplete($currentUserID);
                  break;
              case 3:
                  $sql = filterIncomplete($currentUserID);
                  break;
              default:
                  $sql = "SELECT * FROM tasks WHERE userID = '$currentUserID' ORDER BY taskdatecreated DESC ";
                  break;
          }
          
           //read all row from data base table
          $result = $connection->query($sql);
          if (! $result) {
              die("". mysqli_error($connection));
          }
                        
          while ($row = $result->fetch_assoc()) {
              $isCompleted = $row['isCompleted'] ? 'active' : '';
              echo "
              <div class='task-container' container-task-id='{$row['taskID']}'>
                <div class='task-container-item' id='task-container-desc'>{$row['description']}</div>
                <div class='task-container-item' id='task-container-duedate'>Due: {$row['taskduedate']}</div>
                <div class='task-container-item' id='task-container-button'>
                  <button type='button' class='tick-button $isCompleted' id='tick-button' data-task-id='{$row['taskID']}'><i class='bi bi-check-lg'></i></button>
                  <button type='button' id='delete-button' class='btn btn-danger' onclick='confirmDelete({$row['taskID']})'><i class='bi bi-trash3'></i></button>
                </div>
              </div>
              <br>
               ";
              }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Index.php</title>
  </head>
  <body>
    <header>
    <div class="head-nav">
      <div><a href="#" id="logo"><img src="Projimg/npclogo2W.png" alt="nothing" /></a></div>
      <div>  <?php 
      $sql = "SELECT fname, lname FROM user WHERE userID = '$currentUserID'";
      $result = $connection->query($sql);
      if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); //retrieve data from row
        $fname = $row['fname'];
        $lname = $row['lname'];
      } else {
        $fname = "User";
        $lname = "";
      }
    
        echo "<div class='welcome-container'><h3>Welcome,<b> {$row['fname']} {$row['lname']}</b> !</h3></div>";
        ?> 
         
       </div>   
      </div>
      
      <div class="btn-group">
        <button
          type="button"
          class="logout-dropdown-button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="bi bi-person-circle"></i> <i style="font-size: 20px; padding:5px;" class="bi bi-caret-down-fill"></i>
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="logout.php">Logout</a>
          </li>
        </ul>
      </div>
    </header>

    <div class="main">
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
    ?>
      <h1><b>TO-DO List <i  class="bi bi-list-check"></i></b></h1>
      <div class="add-task-container">
        <form method="post" action="">
          <input type="text" name="description" class="todo-input" placeholder="Add Task..." value="<?php echo $description; ?>"/>
          <?php 
            if(!empty($successMessage)){
              echo"<script>
              Swal.fire({
                  icon: 'success',
                  title: '$successMessage',
                  showConfirmButton: false,
                  timer: 1500
              });
              </script>";
            }
          ?>
          <button type="submit" id="plus-logo-button">
            <a href="" style="color: white;"><i class="bi bi-plus"></i></a>
          </button>
        </form>
      </div>

      <div class="table-container">
      <div class="count-sort-container">
         <div>
          <?php
              $sqlComplete = "SELECT * FROM tasks WHERE userID = '$currentUserID' AND isCompleted = 0";
              $resultComplete = $connection->query($sqlComplete);
              $completedCount = mysqli_num_rows($resultComplete);
           ?>
          <div class="task-count-container"><i style="font-size: 20px;" class="bi bi-clipboard2-x-fill"></i> <?php echo $completedCount; ?> outstanding tasks</div>
          </div>
          <!-- dropdown -->
          <div class="dropdown-center">
            <button class="dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            sort <i style="font-size: 15px; padding:5px;" class="bi bi-caret-down-fill"></i></button>
            <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="index.php?choice=0">All</a></li>
            <li><a class="dropdown-item" href="index.php?choice=1">By Due Date</a></li>
            <li><a class="dropdown-item" href="index.php?choice=2">Completed</a></li>
            <li><a class="dropdown-item" href="index.php?choice=3">Incomplete</a></li>
            </ul>
          </div>
      </div>
          <!-- end dropdown -->
          <?php
            //read all row from data base table
            displayAll($currentUserID,$choice,$connection)
          ?>
      </div>
    </div>
        </div>

    <!-- Javascript -->
    <script>
      //click task-container->details.php
      document.querySelectorAll('.task-container').forEach(container => { 
        container.addEventListener('click', function () { 
          const taskId = this.getAttribute('container-task-id');
          window.location.href = `details.php?id=${taskId}`;
        });
      });
      //click tick button
      document.querySelectorAll('.tick-button').forEach(button => { //selects all elements with the class .tick-button.
        button.addEventListener('click', function (event) {  //Attaches a click event listener to each button. function (event) { ... }: Defines the function to be executed when the button is clicked.
          event.stopPropagation();
          const taskId = this.getAttribute('data-task-id');
                const isActive = this.classList.toggle('active');
                const isCompleted = isActive ? 1 : 0;

                // Send AJAX request to update isCompleted status in database
                fetch('tickUpdate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ taskID: taskId, isCompleted: isCompleted })
                }).then(response => response.json()).then(data => { //Sends the task ID and completion status as a JSON string in the request body.
                    if (!data.success) {
                        console.error('Failed to update task status');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                });
            });
        });

        //click delete confirmation
        function confirmDelete(id){
          event.stopPropagation(); // Stop the event from propagating to the .task-container
          Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                title: "Deleted!",
                text: "Your task has been deleted.",
                icon: "success",
              }).then(() => {
                  window.location.href = 'delete.php?id=' + id;
              });
             }
          });
        }
    </script> 
    <!-- End Javascript -->
  </body>
</html>

<!-- Unused code -->
<!-- <button type='button' id='done-button' class='btn btn-success'><a href='details.php?id=$row[taskID]' style='color:white'><i class='bi bi-check-lg'></i></a></button> -->

<!-- Explaination -->
<!-- document.querySelectorAll(".tick-button") to select all elements with the class tick-button. Use forEach to iterate over all selected buttons and attach a click event listener to each. -->
<!-- add event listener to each button that toggles the active class when clicked. -->
<!-- event.stopPropagation(); Prevent event from bubbling up to the container's click event -->
<!-- getElementById returns only the first element with the specified ID. Since IDs must be unique within a page, using the same ID for multiple elements causes only the first one to be targeted. -->