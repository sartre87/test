<?php
/*
 File Name   : listGames.php
 Date        : 3/15/2015
 Description : 
  Displays a list of all the games in the database.
  Allows administrators to add, edit, and delete games.
*/
 // Required Files
 require_once("header.php");
 require_once("gamesUI.php");
 
 // Require Active Session
 requireSession();
 
 // Add the header to the page.
 addHeader();
    
 // ************************************************************************
 // If the Add Game button was pressed from the listGames.php page...
 if (isset($_POST['createGame'])) { 
  addGameForm();
  backButton();
 }
 
 // If the Add Entry button was pressed from the listGames function...
 elseif (isset($_POST['submit']) && $_POST['submit'] == "Add Entry") {
  Games::add($_POST['title'], $_POST['description']);
  header('Location: listGames.php');
 }
 
 // If the View Game button was pressed from the listGames function...
 elseif (isset($_POST['selectGame'])) {
  $rec = unserialize(stripslashes($_POST['record']));
  header('Location: viewGame.php?id=' . $rec['id']);
 }
 
 // If the Edit Game button was pressed from the listGames function...
 elseif (isset($_POST['editGame'])) { 
  editGameForm();
  backButton();
 }
 
 // If the Update Record button was pressed from the editGameForm function...
 elseif (isset($_POST['submit']) && $_POST['submit'] == "Update Record") {
  Games::update($_POST['id'], $_POST['title'], $_POST['description']);
  header('Location: listGames.php');
 }
 
 // If the Delete Game button was pressed from the listGames function...
 elseif (isset($_POST['deleteGame'])) {
  $rec = unserialize(stripslashes($_POST['record']));
  Games::delete($rec['id']);
  header('Location: listGames.php');
 }
  
 // No button was pressed...
 else firstVisit();
 // ************************************************************************
 
 // ************************************************************************
 // FIRST VISIT - Actions to be performed when no button has been pressed. *
 // ************************************************************************
 function firstVisit() { 
  // If the user is an administrator display the Add Game button.
  if ($_SESSION['admin'] == true) {
   echo "
    <form method='POST' action='listGames.php'>
     <input type='submit' name='createGame' value='Add Game'>
    </form>
    <form method='POST' action='handleReports.php'>
     <input type='submit' name='veiwReports' value='View Reports'>
    </form>";
  }
  
  // Display Games List
  listGames();
 }
?>
