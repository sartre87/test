<?php
/*
 File Name   : gamesUI.php
 Date        : 3/15/2015
 Description : 
  Contains functions for adding UI Forms pertaining to the Games Table.
*/
 // Required Files
 require_once('Games.php');
 require_once('Reviews.php');
 
 // ************************************************************************ 
 // ADD GAME FORM                                                          *
 // Displays a form for adding a game to the database.                     *
 // ************************************************************************
 function addGameForm() {
  // Name of the file that is calling this function
  $fileName = basename($_SERVER['SCRIPT_FILENAME']);
  
  echo "
   <form name='inputForm' method='POST' action='$fileName'>
    <!-- hidden field used for storing user id -->
    <input type='hidden' name='id' id='id' value=''>
    <table>
     <tr>
      <td>Title: </td>
      <td><input type='text' name='title' id='title' value='' size='30'></td>
     </tr><tr>
      <td>Description: </td>
      <td><textarea name='description' rows='10' cols='50'></textarea></td>
     </tr><tr>
      <td><input type='submit' name='submit' id='submit' value='Add Entry'></td>
      <td><input type='reset' value='Reset Form'></td>
     </tr>
    </table>
   </form>";
 }
 
 // ************************************************************************ 
 // EDIT GAME FORM                                                         *
 // Displays a form for editing a game within the database.                *
 // ************************************************************************
 function editGameForm() {
  addGameForm();
  $rec = unserialize(stripslashes($_POST['record']));
  $desc = str_replace(array( "\n", "\r" ), array( "\\n", "\\r" ), $rec[description]);
  echo '
   <script>
    document.getElementById("id").value    = \'' . $rec[id]     . '\';
    document.getElementById("title").value = \'' . $rec[title]  . '\';
    document.inputForm.description.value  += \'' . $desc        . '\';
    // Change the text on the submit button.
    document.getElementById("submit").value = "Update Record";
   </script>';
 }
 
 // ************************************************************************ 
 // VIEW GAME                                                              *
 // Displays a form containing a game's information.                       *
 // ************************************************************************
 function viewGame($id) {
  // Place Holder Image
  echo "<table><tr><td><img src='img/cartridge.jpg''></td>";
  
  // Get Game Record
  $record = Games::getByID($id);
  
  // Game Information
  echo "<td valign='top'><p><h2>" . $record['title'] . "</h2>" . $record['description'];
  
  // Average Rating
  echo "</p>Average Rating: " . Reviews::averageRating($_GET['id']) . "</td></tr></table>";
 }
 
 // ************************************************************************ 
 // LIST GAMES                                                             *
 // Displays a list of every game's title.                                 *
 // ************************************************************************
 function listGames() {
  // Get an array containing all the lessons records.
  $records = Games::toArray();
  
  // Used by the select button within the table.
  echo "
   <!-- function for setting the serialized record -->
   <script>
    function setRecord(rec) { 
     document.getElementById('record').value = rec;
    }
   </script>";
   
  // Name of the file that is calling this function
  $fileName = basename($_SERVER['SCRIPT_FILENAME']);
     
  // Opening Tags
  echo "
   <form method='POST' action='$fileName'>
   <table>";
  
  // Hidden field for storing serialized record.
  echo "<input type='hidden' id='record' name ='record'>";
  
  // Add the table headers.
  echo "<tr><th>Title</th></tr>";
  
  // Display each row.
  foreach ($records as $record) {
   $str = addslashes(addslashes(serialize($record)));
   $serializedRecord = str_replace(array("\n", "\r", "'"), array("\\n", "\\r", " "), $str);
   echo "
    <tr>
     <td>$record[title]</td>
     <td>
      <!-- buttons for selecting a game -->
      <input name='selectGame' type='submit' value='view'
	   onclick='setRecord(\"" . $serializedRecord . "\")' />";
       
    // These buttons only appear if the current user is an administrator.
    if ($_SESSION['admin'] == true)
     echo "
      <input name='editGame' type='submit' value='edit' 
       onclick='setRecord(\"" . $serializedRecord . "\")' />
      <input name='deleteGame' type='submit' value='delete'
	   onclick='setRecord(\"" . $serializedRecord . "\")' />";
        
    echo "</td></tr>";
  }
  
  // Closing Tags
  echo "</table></form>";
 }
?>
