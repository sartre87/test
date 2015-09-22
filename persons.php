<?php
ini_set("session.cookie_domain", ".cis355.com");
session_start();

//I took this out so that the annoying error at the top goes away
//ini_set('display_errors', 1);
//error_reporting(e_all);

// ---------- a. display (echo) html head and link to bootstrap css -----------
// moved to section "f" to solve Post/Redirect/Get problem
// displayHTMLHead();

// ---------- b. set connection variables and verify connection ---------------
$hostname="localhost";
$username="";				// CHANGE TO YOUR LOGIN USERNAME
$password="";				// CHANGE TO YOUR LOGIN PASSWORD
$dbname="CIS355javenner";	// CHANGE TO YOUR DATABASE NAME
$usertable="persons";		// CHANGE TO THE TABLE NAME THAT YOU WANT TO WORK WITH

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection

// ---------- if successful connection...
if($mysqli)            
{
    // ---------- c. create table, if necessary -------------------------------
	createTable($mysqli); 
	
	// ---------- d. initialize userSelection and $_POST variables ------------
	$userSelection 		= 0;
	$firstCall 			= 1; // first time program is called
	$insertSelected 	= 2; // after user clicked insertSelected button on list 
	$updateSelected 	= 3; // after user clicked updateSelected button on list 
	$deleteSelected 	= 4; // after user clicked deleteSelected button on list 
	$insertCompleted 	= 5; // after user clicked insertSubmit button on form
	$updateCompleted 	= 6; // after user clicked updateSubmit button on form
	$deleteCompleted 	= 7; // after user clicked deleteSubmit button on form
	
	
	// His template file used to have variables called "Type", "Model", etc.
	// I changed them to these because they are the field names in the persons table
    $role				= $_POST['role'];
	$sec_role			= $_POST['sec_role'];
	$first_name			= $_POST['first_name'];
	$last_name			= $_POST['last_name'];
	$email				= $_POST['email'];
	$password			= $_POST['password'];
	$school 			= $_POST['school'];
	
	$userlocation       = $_SESSION['location'];
	
    // ---------- e. determine what user clicked ------------------------------
	// the $_POST['buttonName'] is the name of the button clicked in browser
	$userSelection = $firstCall; // assumes first call unless button was clicked
	if( isset( $_POST['insertSelected'] ) ) $userSelection = $insertSelected;
	if( isset( $_POST['updateSelected'] ) ) $userSelection = $updateSelected;
	if( isset( $_POST['deleteSelected'] ) ) $userSelection = $deleteSelected;
	if( isset( $_POST['insertCompleted'] ) ) $userSelection = $insertCompleted;
	if( isset( $_POST['updateCompleted'] ) ) $userSelection = $updateCompleted;
	if( isset( $_POST['deleteCompleted'] ) ) $userSelection = $deleteCompleted;
	
	// ---------- f. call function based on what user clicked -----------------
	switch( $userSelection ):
	    case $firstCall: 
			//print_r($_SESSION);
		    $msg = '';
			displayHTMLHead();
		    showList($mysqli, $msg);
			break;
		case $insertSelected:
			displayHTMLHead();
		    showInsertForm($mysqli);
			break;
		case $updateSelected :
			displayHTMLHead();
		    showUpdateForm($mysqli);
			break;
		case $deleteSelected:    
			// displayHTMLHead();		
			// showDeleteForm($mysqli); // currently no form is displayed
			deleteRecord($mysqli);   // delete is immediate (no confirmation)
			displayHTMLHead();
			$msg = 'record deleted';
			showList($mysqli, $msg);
			break;
		case $insertCompleted: // updated to do Post/Redirect/Get (PRG)
		    insertRecord($mysqli);
			header("Location: " . $_SERVER['REQUEST_URI']); // redirect
			displayHTMLHead();
			$msg = 'record inserted';
			showList($mysqli, $msg);
			break;
		case $updateCompleted:
		    updateRecord($mysqli);
			header("Location: " . $_SERVER['REQUEST_URI']);
			displayHTMLHead();
			$msg = 'record updated';
			showList($mysqli, $msg);
			break;
		case $deleteCompleted:        // this case never occurs (see above)
		    deleteRecord($mysqli);
			header("Location: " . $_SERVER['REQUEST_URI']);
			displayHTMLHead();
			$msg = 'record deleted';  
			showList($mysqli, $msg);
			break;
	endswitch;

} // ---------- end if ---------- end main processing ----------

# ========== FUNCTIONS ========================================================

# ---------- checkConnect -----------------------------------------------------
//I didn't touch this one
function checkConnect($mysqli)
{
    if ($mysqli->connect_errno) {
        die('Unable to connect to database [' . $mysqli->connect_error. ']');
        exit();
    }
}
# ---------- createTable ------------------------------------------------------
// I didn't touch this one either
function createTable($mysqli)
{
    global $usertable;
    if($result = $mysqli->query("select id from $usertable limit 1"))
    {
        $row = $result->fetch_object();
		$id = $row->id;
        $result->close();
    }
    if(!$id)
    {
	    $sql = "CREATE TABLE table01 
		       (id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY( id ),";
	    $sql .= "type VARCHAR(20),";
	    $sql .= "brand VARCHAR(30),";
	    $sql .= "model VARCHAR(20),";
	    $sql .= "color VARCHAR(30),";
	    $sql .= "strWind VARCHAR(20),";
	    $sql .= "price VARCHAR(30),";
	    $sql .= "descript VARCHAR(100),";
		$sql .= "location_id INT,";
		$sql .= "user_id INT,";
		$sql .= "FOREIGN KEY (location_id) REFERENCES locations (location_id),";
        $sql .= "FOREIGN KEY (user_id) REFERENCES users (user_id)";
	    $sql .= ")";

        if($stmt = $mysqli->prepare($sql))
        {
            $stmt->execute();
        }
    }
}

# ---------- showList ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"
function showList($mysqli, $msg) 
{
	global $usertable;
	
	// display current user and location_id
	echo "You are logged in as user: ".$_SESSION["user"]." (".$_SESSION["id"].") ".
	    " location: ".$_SESSION["location"]."<br>";
	
    // display html table column headings
	// If you look within this mess, I changed outputs to say the table fields. ex: "Role", "Secondary Role", etc.
	//this is the only thing I changed in this function
	echo 	'<div class="col-md-12">
			<form action="persons.php" method="POST">
			<table class="table table-condensed" 
			style="border: 1px solid #dddddd; border-radius: 5px; 
			box-shadow: 2px 2px 10px;">
			<tr><td colspan="11" style="text-align: center; border-radius: 5px; 
			color: white; background-color:#333333;">
			<h2 style="color: white;">Persons</h2>
			</td></tr><tr style="font-weight:800; font-size:20px;">
			<td>ID</td><td>Role</td><td>Secondary Role</td>
			<td>First Name</td><td>Last Name</td><td>Email</td>
			<td>Password</td><td>School</td></tr>';

	// get count of records in mysql table
	$countresult = $mysqli->query("SELECT COUNT(*) FROM $usertable");
	$countfetch  = $countresult->fetch_row();
	$countvalue  = $countfetch[0];
	$countresult->close();

	// if records > 0 in mysql table, then populate html table, 
	// else display "no records" message
	if( $countvalue > 0 )
	{
			populateTable($mysqli); // populate html table, from mysql table
	}
	else
	{
			echo '<br><p>No records in database table</p><br>';
	}
	
	// display html buttons 
	echo    '</table>
			<input type="hidden" id="hid" name="hid" value="">
			<input type="hidden" id="uid" name="uid" value="">
			<input type="submit" name="insertSelected" value="Add an Entry" 
			class="btn btn-primary"">
			</form></div>';

	// below: JavaScript functions at end of html body section
	// "hid" is id of item to be deleted
	// "uid" is id of item to be updated.
	// see also: populateTable function
	echo "<script>
			function setHid(num)
			{
				document.getElementById('hid').value = num;
		    }
		    function setUid(num)
			{
				document.getElementById('uid').value = num;
		    }
		 </script>";
}

# ---------- populateTable ----------------------------------------------------
// populate html table, from data in mysql table
//I didn't change anything in here, although I could have because there aren't 10 rows in our persons table.
function populateTable($mysqli)
{
	global $usertable;
	
	if($result = $mysqli->query("SELECT * FROM $usertable"))
	{
		while($row = $result->fetch_row())
		{
			echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' . 
			    $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] . 
				'</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' . 
				$row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9] ;
			
            if ($_SESSION["id"]==$row[9]) {			
			echo '</td><td><input name="deleteSelected" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' . 
				$row[0] .')" />' ;
			echo '<input style="margin-left: 10px;" type="submit" 
				name="updateSelected" class="btn btn-primary" value="Update" 
				onclick="setUid(' . $row[0] . ');" />';
			}
		}
	}
	$result->close();
}

# ---------- showInsertForm ---------------------------------------------------
function showInsertForm ($mysqli)
{
    global $userlocation;
	// display current user and location_id
	echo "You are logged in as user: ".$_SESSION["user"].
	    " location: ".$_SESSION["location"]."<br>";
		
	//I changed the label in every single one here.  Just change the labels as you see fit for your table.
    echo '<div class="col-md-4">
	<form name="basic" method="POST" action="persons.php" 
	    onSubmit="return validate();">
		<table class="table table-condensed" style="border: 1px solid #dddddd; 
		    border-radius: 5px; box-shadow: 2px 2px 10px;">
			<tr><td colspan="2" style="text-align: center; border-radius: 5px; 
			    color: white; background-color:#333333;">
			<h2>Insert New Person</h2></td></tr>
			<tr><td>Role: </td><td><input type="edit" name="role" value="" 
			size="20"></td></tr>
			<tr><td>Seconday Role: </td><td><input type="edit" name="sec_role" value="" 
			size="30"></td></tr>
			<tr><td>First Name: </td><td><input type="edit" name="first_name" value="" 
			size="30"></td></tr>
			<tr><td>Last Name: </td><td><input type="edit" name="last_name" 
			value="" size="30"></td></tr>
			<tr><td>Email: </td><td><input type="edit" name="email" value="" 
			size="20"></td></tr>
			<tr><td>Password: </td><td><input type="edit" name="password" value="" 
			size="20"></td></tr>
			<tr><td>School: </td><td><input type="edit" name="school" value="" 
			size="30"></td></tr>';
/*			
		echo '<tr><td>Location ID: </td><td><textarea style="resize: none;" 
			name="location_id" cols="40" rows="3"></textarea></td></tr>';
*/
	
			echo '<tr><td><input type="submit" name="insertCompleted" 
			    class="btn btn-success" value="Add Entry"></td>
			    <td style="text-align: right;"><input type="reset" 
			    class="btn btn-danger" value="Reset Form"></td></tr>
		       </table><a href="persons.php" class="btn btn-primary">
		        Display Database</a></form></div>';
}

# ---------- showUpdateForm --------------------------------------------------
function showUpdateForm($mysqli) 
{
	$index = $_POST['uid'];  // "uid" is id of db record to be updated 
	global $usertable;
	
	if($result = $mysqli->query("SELECT * FROM $usertable WHERE id = $index"))
	{
		while($row = $result->fetch_row())
		{
		    // display current user and location_id
	        echo "You are logged in as user: ".$_SESSION["user"].
	              " location: ".$_SESSION["location"]."<br>";
				  
				  
			// Same thing as the show insert form from above.
			echo '	<br>
					<div class="col-md-4">
					<form name="basic" method="POST" action="persons.php">
						<table class="table table-condensed" 
						    style="border: 1px solid #dddddd; 
							border-radius: 5px; box-shadow: 2px 2px 10px;">
							<tr><td colspan="2" style="text-align: center; 
							border-radius: 5px; color: white; 
							background-color:#333333;">
							<h2>Update Person Form</h2></td></tr>
							<tr><td>Role: </td><td><input type="edit" 
							name="role" value="'. $row[1] .'" size="20">
							</td></tr>
							<tr><td>Secondary Role: </td><td><input type="edit" 
							name="sec_role" value="' . $row[2] . '" size="20">
							</td></tr>
							<tr><td>First Name: </td><td><input type="edit" 
							name="first_name" value="' . $row[3] . '" size="30">
							</td></tr>
							<tr><td>Last Name: </td><td><input type="edit" 
							name="last_name" value="' . $row[4] . '" size="20">
							</td></tr>
							<tr><td>Email: </td><td><input type="edit" 
							name="email" value="' . $row[5] . '" size="30">
							</td></tr>
							<tr><td>Password: </td><td><input type="edit" 
							name="password" value="' . $row[6] . '" size="20">
							</td></tr>
							<tr><td>School: </td><td><input type="edit" 
							name="school" value="' . $row[7] . '" size="30">
							</td></tr>';						
							
							echo '</td></tr>
							
							<tr><td><input type="submit" name="updateCompleted" 
							class="btn btn-primary" value="Update Entry"></td>
							<td style="text-align: right;"><input type="reset" 
							class="btn btn-danger" value="Reset Form"></td></tr>
						</table>
						<input type="hidden" name="uid" value="' . $row[0] . '">
					</form>
				</div>';
							
		}
		$result->close();
	}
}

# ---------- deleteRecord -----------------------------------------------------
// I didn't change anything in here
function deleteRecord($mysqli)
{
	$index = $_POST['hid'];  // "hid" is id of db record to be deleted
	global $usertable;
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id='$index'"))
    {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
		// protects against sql injections
        //$stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- insertRecord -----------------------------------------------------
function insertRecord($mysqli)
{
	// Change the global variable names below to match your DB table
    global $role, $sec_role, $first_name, $last_name, $email, $password, $school;
	global $usertable;
    
	// I'm pretty sure I don't need the if statement here, but you can play with that if you'd like.
	// Just make sure your first guy in the values section is 'NULL' because it's auto incremented in the DB
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("INSERT INTO $usertable (id, role, secondary_role, 
				first_name, last_name, email, password_hash, school) VALUES ('NULL', '$role', '$sec_role', '$first_name', 
				'$last_name', '$email', '$password', '$school')"))
    {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
		// protects against sql injections
        //$stmt->bind_param(NULL, $role, $sec_role, $first_name, $last_name, $email, $password, $school);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- updateRecord -----------------------------------------------------
function updateRecord($mysqli)
{
	//same thing as the insert from above, but you just don't update the id
	global $role, $sec_role, $first_name, $last_name, $email, $password, $school;
	global $usertable;
	$index = $_POST['uid'];  // "uid" is id of db record to be updated 
    
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("UPDATE $usertable SET role = '$role', secondary_role = '$sec_role', first_name = '$first_name',
	    last_name = '$last_name', email = '$email', password_hash = '$password', school = '$school' WHERE id = '$index'"))
    {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
		// protects against sql injections
        //$stmt->bind_param('sssssssiii', $type, $brand, $model, $color, $strWind, 
		//    $price, $descript, $location_id, $user_id, $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- displayHTMLHead -----------------------------------------------------
//I don't touch anything in here
function displayHTMLHead()
{
echo '<!DOCTYPE html>
    <html> 
	<head>
	<title>persons.php</title>
	<link rel="stylesheet" 	href="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" 	href="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/js/bootstrap.min.js">
	</script></head><body>';
	
echo '<div class="col-md-12" style="background-color: tan; border-bottom: 
    2px solid black; box-shadow: 3px 3px 5px #888888;">
	<a href="../student14/landing.php"><img src="../student14/LWIP_logo.png" style="margin-top: 5px;"></a>';
if ($_SESSION["user"] != '')
{
	$user = $_SESSION['user'];
	echo '<p style="font-size:18px; float: right; margin-top: 40px; 
	    margin-right: 20px;">Welcome <b>' .	$user . '</b>!</p>';
}
else
{
	echo '<form class="navbar-form navbar-right" style="margin-top: 35px;" method="POST" 
	    action="../student14/login.php">
		<input type="text" size="9" name="username" class="form-control" placeholder="Username">
		<input type="password" size="9" name="password" class="form-control" placeholder="Password">
		<button type="submit" name="loginSubmit" class="btn btn-success">Submit</button>
	    </form>';
}
echo '<br><br></div>';
}
?>
