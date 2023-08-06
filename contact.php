<?php session_start() ?>

<?php  include ('functions.php') ; ?>

<!DOCTYPE html>
<html>

<head>
	<title>SWC Contact page</title>
  <link rel="stylesheet" href="sylvieswebcorner.css" media="only screen and (max-width:400px) , only screen and (max-width:800px) , only screen and (min-width:800px)" />
	<meta name="viewport" content="width=device-width , initial-scale=1.0" />
	<meta charset="UTF-8" />
</head>

<body> 
  <a href="index.html" class="navIcon"> <img src="images/home_icon.jpg" alt="home icon" /> </a>

  <h2> Contact </h2>
  <form method="POST" action="contact.php">
	  <p>
		  <?php  
        echo "<br/>Bonjour " . $_SESSION["username"] . ". Vous pouvez écrire votre message ci-dessous. " ;  
        echo "<br/>Hello " . $_SESSION["username"] . ". You can leave your message below. " ; 
      ?>
      <br/>
		  <textarea type="text" name="message" rows=8 cols=45></textarea>
	  </p>
	  <input type="submit" value="Envoi/Send"  />
  </form>
</body>

<?php

// -------  PHP MESSAGE TREATMENT  ------- 

// XSS and EMPTY DATA TREATMENT
my_xss_post() ; 

// VARIABLES

  // local date
$local_date = date("d/m/Y h:i:s a")  ; 

  // file preset name from current date
$file_pre = date("Y_m") ; 
$file = "messages/" . $file_pre . ".txt" ; 

  // Remote Server Ip Address
$ip = $_SERVER["REMOTE_ADDR"] ;


// MESSAGE INPUT

// if a message is written only
if ( ($_POST['message']) != NULL ) {

  // update the DB with file name where message will be saved, local date and email
  $query_db1 = "INSERT INTO messages (message, local_date_time, email, ip_address) VALUES (:file , :ldate , :email , :ip) " ; 
  $pdo_statement1 = $db -> prepare($query_db1) ; 
  $pdo_statement1 -> execute ( [ "file" => $file  ,  "ldate" => $local_date  ,  "email" => $_SESSION["email"] , "ip" => $ip ] ) ; 



  // SERVER REQUEST DATE AND TIME TREATMENT
 
  // after request execution, extract and convert server timestamp request
  $server_timestamp = $_SERVER["REQUEST_TIME"] ; // request at timestamp (from 01/01/1970 00:00:01)

  // Conversion timestamp to readable date and time
  $query_server_dt = "SELECT FROM_UNIXTIME ( $server_timestamp ) " ; // query = conversion to readable date and time
  $pdo_statement2 = $db -> query($query_server_dt) ;  // query execution
  $data = $pdo_statement2 -> fetch() ;  // query fetch

  $query_dt = $data[0] ;  // readable request date and time saved in variable


  // AFTER REQUEST, EXTRACT LAST AUTO_INCREMENTED ID NUMBER

  $query_id = "SELECT LAST_INSERT_ID()" ; 
  $pdo_statement3 = $db -> prepare ($query_id) ; 
  $pdo_statement3 -> execute () ;
  $id_array = $pdo_statement3 -> fetchAll() ; 

  $id = $id_array[0]["LAST_INSERT_ID()"] ;  // last message id saved in variable


  // UPDATE FILE STORAGE (with message ID, message, ...)
  if ( isset ($_POST["message"]) ) {  // If message written only
  file_storage ( $id, $file , $local_date , $_SESSION["email"] , $ip , $_POST["message"] ) ; 
  }

  // UPDATE DB WITH SERVER DATE AND TIME REQUEST
  $query_db2 = "UPDATE messages SET server_date_time=:server_dt WHERE msg_id=:id" ; 
  $pdo_statement4 = $db -> prepare ($query_db2) ; 
  $pdo_statement4 -> execute ( [ "server_dt" => $query_dt  , "id" => $id ]  ) ; 


  // MESSAGE AKNOWLEDGE RECEIPT
  if (  $_POST["message"] != NULL  ) {
    echo "<br/><br/><strong>Votre message a bien été transmis. </strong><br/> Merci et à bientôt. " ; 
  }

}

?>



</html>

