<?php
/*
 File Name   : Games.php
 Date        : 3/15/2015
 Description : 
  Manages the records within the Games Table.
*/
 // Required Files
 require_once('Database.php');
 
 class Games { 
  // ******************************************************************************
  // toArray                                                                      *
  // Returns an associative array containing every record in the Games Table.     *
  // ******************************************************************************
  public static function toArray() {
   $str = "
    SELECT id, title, description
    FROM   indPrj_games";
   
   // Obtain an array of games records.
   $records = Database::query($str);
   
   // Push each row into a new array to be outputted.
   $x      = 0;
   $output = array();
   while($row = mysql_fetch_array($records)){
    $output[$x]['id']          = $row['id'];
    $output[$x]['title']       = $row['title'];
    $output[$x]['description'] = $row['description'];
    $x++;
   }
   
   return $output;
  }
 
  // ******************************************************************************
  // GET BY ID                                                                    *
  // Returns an associative array of the Games Record with the matching ID.       *                                                         *
  // ******************************************************************************
  public static function getByID($id) {
   $str = "
    SELECT id, title, description
    FROM   indPrj_games
    WHERE  id = " . $id;
    
   return mysql_fetch_array(Database::query($str));
  }
 
  // ************************
  // DATABASE MANAGEMENT    *
  // ************************
 
  // ************************************************
  // ADD - Adds a record to the database.           *
  // ************************************************
  public static function add($title, $description) {
   $mysqli = Database::getMYSQLI();
   
   $stmt = $mysqli->prepare("
    INSERT INTO indPrj_games (title, description)
    VALUES (?,?)");
   
   $stmt->bind_param('ss', $title, $description);
    
   $stmt->execute();
   $stmt->close();
   $mysqli->close();
  }
    
  // ************************************************
  // UPDATE - Updates a record within the database. *
  // ************************************************
  public static function update($id, $title, $description) {
   $mysqli = Database::getMYSQLI();
   
   $stmt = $mysqli->prepare("
    UPDATE indPrj_games
    SET    title = ?, description = ?
    WHERE  id = ?");
    
   $stmt->bind_param('ssi', $title, $description, $id);
   $stmt->execute();
   $stmt->close();
   $mysqli->close();
  }
  
  // ************************************************
  // DELETE - Deletes a record within the database. *
  // ************************************************
  public static function delete($id) {
   $mysqli = Database::getMYSQLI();
   
   $stmt = $mysqli->prepare("DELETE indPrj_games FROM indPrj_games WHERE id = ?");
   
   $stmt->bind_param('i', $id);
   
   $stmt->execute();
   $stmt->close();
   $mysqli->close();
  }
 } 
?>
