<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
include 'dbpass.php';
?>

<html>
  <head>
    <meta charset="UTF-8">
    <title>Add Assistant</title>
  </head>
  <body>

<?php
if(("" == trim($_POST['firstName'])) && ("" == trim($_POST['lastName']))) {
  echo "Cannot insert row. You must enter either a first or last name.";
} else {
  $mysqli = new mysqli('oniddb.cws.oregonstate.edu', 'watsokel-db', $dbpass, 'watsokel-db');
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  if(!($stmt = $mysqli->prepare("INSERT INTO medical_office_assistants(first_name, last_name) VALUES (?,?)"))){
    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
  }
  if(!($stmt->bind_param("ss",$_POST['firstName'],$_POST['lastName']))){
    echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
  }
  if(!$stmt->execute()){
      echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
  } else {
    echo "Successfully added " . $stmt->affected_rows . " row(s) to TABLE medical_office_assistants.<br>";
  }
  $stmt->close();
} 
?>
    Return to <a href="assistants.php">View, Filter and Add Medical Office Assistants</a> page
  </body>
</html>