<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
include 'dbpass.php';

$mysqli = new mysqli('oniddb.cws.oregonstate.edu', 'watsokel-db', $dbpass, 'watsokel-db');
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>CS340 Final Project</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jquery-ui.css">
    <link rel="stylesheet" href="jquery.timepicker.css">
  </head>
  <body>
    <header>
      <table width="100%">
        <tr>
          <th style="text-align:left"><h1>CS 340 Final Project</h1><br><span id="headLine">VIEW, FILTER AND ADD APPOINTMENTS</span></th>
          <td valign="top">Programmed by <strong>Kelvin Watson</strong><br>OSU ID: 932540242<br>ONID ID: watsokel</td>
        </tr>
      </table>
    </header>
    
    <nav>
      <table border="1" width="100%" id="navigation"><caption><h3>Choose an Action</h3></caption>
        <tr>
          <td><strong>BASIC OPERATIONS</strong></td>
          <td><a href="index.php">View, Add and Sort Appointments</a></td>
          <td><a href="patients.php">View, Add and Sort Patients</a></td>
          <td><a href="assistants.php">View, Add and Sort Medical Office Assistants</a></td>
          <td><a href="providers.php">View, Add, Filter and Sort Healthcare Providers</a></td>
          <td><a href="conditions.php">View, Add and Sort Medical Conditions</a></td>
          <td><a href="medications.php">View, Add and Sort Medications</a></td>
        </tr>
        <tr>
          <td><strong>ADVANCED OPERATIONS</strong></td>
          <td><a href="diagnosis.php">Add/Update Patient Diagnoses</a></td>
          <td><a href="prescription.php">Add/Update Patient Prescription</a></td>
          <td><a href="updatePatient.php">Update Patient Demographics</a></td>
          <td><a href="updateAssistant.php">Update Medical Office Assistant Information</a></td>
          <td><a href="updateProvider.php">Update Healthcare Provider Information</a></td>
          <td></td>
        </tr>
      </table>
    </nav>
    
    
    <hr>

    <section>
      <table border="1" id="databaseData"><caption><h2>Appointments</h2></caption>
        <thead>
          <tr>
            <th>Patient First Name</th>
            <th>Patient Last Name</th>
            <th>Appointment Date/Time</th>
            <th>Healthcare Provider</th>
            <th>Reason for Appointment</th>
            <th>Scheduled By (Medical Office Assistant)</th>
          </tr>
        </thead>
         <tbody>
            <?php
            if(!($stmt = $mysqli->prepare("SELECT " . "pt.first_name,pt.last_name,ap.date_time,hp.first_name,hp.last_name,ap.reason,oa.first_name,oa.last_name 
            FROM appointments ap 
            INNER JOIN patients pt ON pID = patient_ID 
            INNER JOIN healthcare_providers hp ON hID = provider_ID 
            LEFT JOIN medical_office_assistants oa ON mID = assistant_ID"))){
              echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
            }

            if(!$stmt->execute()){
              echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            if(!$stmt->bind_result($firstName, $lastName, $dateTime, $MDFirstName, $MDLastName, $reason, $oaFirstName, $oaLastName)){
              echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            while($stmt->fetch()){
             echo "<tr><td>" .$firstName. "</td><td>" .$lastName. "</td><td>" .$dateTime. "</td><td>" . "$MDFirstName $MDLastName". 
             "</td><td>" .$reason. "</td><td>" ."$oaFirstName $oaLastName". "</td></tr>";
            }
            $stmt->close();
            ?>
        </tbody>
      </table>
     </section> 

    <section>
      <h2>Add Patient Appointments</h2>
      <form action="addAppointment.php" method="post">
        <fieldset>
        <legend>Patient Demographics</legend>
        <table>
          <tr>
            <td><label for="selectPatient">Patient Name</label></td>
            <td><select name="selectPatient" id="selectPatient" required>
            <option selected="true" disabled="disabled"></option>
            <?php
            if(!($stmt = $mysqli->prepare("SELECT pID,first_name,last_name FROM patients"))){
              echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
            }
            if(!$stmt->execute()){
              echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            if(!$stmt->bind_result($patientID,$firstName,$lastName)){
              echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            while($stmt->fetch()){
              echo "<option value=\"$patientID\">$firstName $lastName</option>";
            }
            $stmt->close();
            ?>
            </select>
            </td>
          </tr>
          <tr>
            <td><label for="datepicker">Appointment Date</label></td>
            <td><input type="text" name="apptDate" id="datepicker" placeholder="Click to Select a Date" required></td>
          </tr>
          <tr>
            <td><label for="timepicker">Appointment Time</label></td>
            <td><input type="text" class="timepicker" name="apptTime" id="timepicker" placeholder="Click to Select a Time" required></td>
          </tr>
          <tr>
            <td><label for="selectProvider">Healthcare Provider</label></td>
            <td><select name="selectProvider" id="selectProvider" required>
            <option selected="true" disabled="disabled"></option>
            <?php
            if(!($stmt = $mysqli->prepare("SELECT hID,first_name,last_name,profession FROM healthcare_providers"))){
              echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
            }
            if(!$stmt->execute()){
              echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            if(!$stmt->bind_result($providerID,$firstName,$lastName,$profession)){
              echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            while($stmt->fetch()){
              echo "<option value=\"$providerID\">$firstName $lastName, $profession</option>";
            }
            $stmt->close();
            ?>
            </select>
            </td>
          </tr>
          <tr>
            <td><label for="reason">Reason for appointment</label></td>
            <td><input type="text" name="reason" id="reason" placeholder="Enter reason" required></td>
          </tr>
          <tr>
            <td><label for="selectAssistant">Scheduled by (assistant)</label></td>
            <td><select name="selectAssistant" id="selectAssistant" required>
            <option selected="true" disabled="disabled"></option>
            <?php
            if(!($stmt = $mysqli->prepare("SELECT mID,first_name,last_name FROM medical_office_assistants"))){
              echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
            }
            if(!$stmt->execute()){
              echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            if(!$stmt->bind_result($assistantID,$firstName,$lastName)){
              echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            while($stmt->fetch()){
              echo "<option value=\"$assistantID\">$firstName $lastName</option>";
            }
            $stmt->close();
            ?>
            </select>
            </td>
          </tr>
          <tr>
            <td><input type="submit" value="Add Appointment"></td>
          </tr> 
        </table>
        </fieldset>
      </form>
    </section>

    <section>
      <h2>Sort Appointments</h2>
      <form action="sortAppointments.php" method="post">
        <fieldset>
        <legend>Sort by</legend>
        <table>
          <tr>
            <td><input type="radio" name="sort" value="pt.first_name">Patient First Name</td>
            <td><input type="radio" name="sort" value="pt.last_name">Patient Last Name</td>
            <td><input type="radio" name="sort" value="ap.date_time">Date and Time</td>
            <td><input type="radio" name="sort" value="hp.first_name">Healthcare Provider</td>
            <td><input type="radio" name="sort" value="ap.reason">Reason</td>
            <td><input type="radio" name="sort" value="oa.first_name">Scheduled by (assistant)</td>
            <td><input type="submit" value="Sort"></td>
          </tr>        
        </table>
        </fieldset>
      </form>
    </section>
    <footer><em>Kelvin Watson, OSU ID 932540242, CS340 Final Project</em></footer>
  </body>

<script src="jquery-1.9.1.js"></script>
<script src="jquery-ui.js"></script>
<script> 
<!--code.runnable.com/UdTuotHbZoQNAABq/adding-a-date-picker-to-an-input-form-field-using-jquery-->
$(document).ready(
  function () {
    $( "#datepicker" ).datepicker({
      changeMonth: true,//this option for allowing user to select month
      changeYear: true, //this option for allowing user to select from year range
      yearRange: '1900:2015',
      dateFormat: "yy-mm-dd"
    });
  }
);
</script>
<script src="jquery.timepicker.js"></script>
<script>
$(document).ready(function(){
    $('input.timepicker').timepicker({ timeFormat: 'HH:mm' });
});
</script>
</html>