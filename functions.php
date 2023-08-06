<?php

// TO BE INCLUDED AT BEGINNING OF ALL PAGES CALLING PDO OBJECT $db FOR SQL REQUESTS
try { $db = new PDO ( "mysql:host=localhost;dbname=swc" , "utilisateurTest" , "abc_123" , [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION] ) ; }
catch (\PDOException $e) { throw new \PDOException ($e->getMessage() , $e->getCode()) ; }


// -------------------------------------------------------------------------------------------------
function my_xss_post() {   
/* Function to treat $_POST superglobal contents with XSS Failure
   using htmlspecialchars php function. 
 */
  foreach ($_POST as $key => $value) {
    if ( isset($value) ) { htmlspecialchars( $value ) ; }
  }
} // -------------- end of function my_xss_post ----------------------------------------------------



// ---------------------------------------------------------------------------------------------------
function file_storage ($id, $file , $date , $email , $ip , $message) {
/*  Function to store messages data in a file, when a user leaves a message on the contact.php page. 
    The file access is configured "Write only" for non-superusers (chmod -r--). 
    New data are placed at EndOfFile and messages are identified with their msg_id. 

    param : date, email, ip, message
    return : none
*/
  $text = "------- \nMessage ID : " . $id . "\n-------  " . $date . "  -------" . "\n\nFrom : " . $email . " (" . $ip . ") \n" . $message . "\n\n"  ; 
  $messages_file = fopen ($file , "a") or die ("impossible to open file") ; 
  fwrite ($messages_file, $text ) ; 
  fclose ($messages_file) or die ("impossible to close file") ; 
}  // end of file_storage() function



// -----------------------------------------------------------------------------------------------------
function new_pwd() {
  /* Function to generate 8 characters password, made of 2 digit max numbers and upper or lower letters.
      param : none. 
      return : password. 
  */
  $pwd="" ; 
  $upper_chain = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" ; 
  $lower_chain = "abcdefghijklmnopqrstuvwxyz" ; 

  // while password length has not reached 8 characters
  while (strlen($pwd) < 8) {
    $letter_nb = rand(0,1) ;
    if ($letter_nb === 0) {   // choice 0 = pick a number
      $nb = rand (0,99) ; 
      $pwd = $pwd . $nb ; 
    }else{                    // choice 1 = pick a letter
      $pick_letter = rand(1,26) ;
      $letter_caps = rand(0,1) ; 
      if ($letter_caps === 0) {     // choice 0 = upper case
        $pwd = $pwd . $upper_chain[$pick_letter] ; 
      }else{                        // choice 1 = lower case
        $pwd = $pwd . $lower_chain[$pick_letter] ; 
      }
    }    
  }
  return $pwd ; 
}  // --------------------- end of new_pwd() function -------------------------------------------




// -----------------------------------------------------------------------------------------------------------------
function pwd_storage ($newpwd) {
/* Function to save obsolete password in swc.passwords table (former_pwd column) 
   then to save a new password in users.password and passwords.current_password. 
 */
  // DB CONNECTION
  try {
    $db = new PDO ( "mysql:host=localhost;dbname=swc" , "utilisateurTest" , "abc_123" , [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION] ) ; 
  }
  catch (\PDOException $e) { throw new \PDOException ( $e -> getMessage() , $e -> getCode() ) ;  }

  // if an email has been entered
  if ($_POST["email_pwd"] != NULL) {

    $date = date ("d/m/Y h:i:s a") ; 

    // Extract current password from users
    $query1 = "SELECT password FROM users WHERE user_email=:email" ; 
    $pdo_statement1 = $db -> prepare ($query1) ; 
    $pdo_statement1 -> execute ( [ "email" => $_POST["email_pwd"] ] ) ; 
    $pwd_array = $pdo_statement1 -> fetch() ; 
    $pwd_to_save = $pwd_array["password"] ; 

    // Save new password and current password in passwords table (former_pwd)
    $query2 = "INSERT INTO passwords (email , current_pwd , former_pwd , date_change) VALUES ( :useremail , :pwd , :fpwd , :date )" ; 
    $pdo_statement2 = $db -> prepare ($query2) ;
    $pdo_statement2 -> execute ( [ "useremail" => $_POST["email_pwd"] , "pwd" => $newpwd , "fpwd" => $pwd_to_save , "date" => $date ] ) ; 

    // Update users table with new password
    $query3 = "UPDATE users SET password=:pwd WHERE user_email=:useremail" ; 
    $pdo_statement3 = $db -> prepare($query3) ; 
    $pdo_statement3 -> execute ( [ "pwd" => $newpwd , "useremail" => $_POST["email_pwd"] ] ) ; 

  }
}



// ----------------------------------------------------------------------------------------------
function email_exists ($email_to_test) {
/*
  Function to connect to Database first using PDO object, 
  then to test if email field not empty and if that email already exists in Database or not. 
  If doesn't exist, location to subscription.php page. 
  If exists return 1, else, return 0. 

  param : email to test. 
  return : 0 if email not in DB, else, 1.  
*/
  $exists = 0 ; 

  // DB CONNECTION
  try {
    $db = new PDO ( "mysql:host=localhost;dbname=swc" , "utilisateurTest" , "abc_123" , [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION] ) ; 
  }
  catch (\PDOException $e) { throw new \PDOException ( $e -> getMessage() , $e -> getCode() ) ;  }

  // if email field not empty
  if ( $email_to_test != NULL )
  {
    // check if email in DB
    $query_email = "SELECT user_email FROM users WHERE user_email=:email" ; 
    $pdo_statement1 = $db -> prepare ($query_email) ; 
    $pdo_statement1 -> execute ( [ "email" => $email_to_test ] ) ; 
    $query_array = $pdo_statement1 -> fetch() ;

    // if email not in DB, prompt + location subscription.php page
    if ($query_array[0] == NULL) { 
      echo "  <script type='text/javascript' > \n
                if (  confirm ('Cet email n\'existe pas. Vous inscrire ? \\nThis email does not exist. Sign up ?') == true) \n
                { \n
                  window.location.href = 'subscription.php' ;  \n
                }else{  window.location.href = 'connexion.php' ; }  \n
              </script> " ;  
    }else{ $exists = 1 ; }

    return $exists ; 
  }
}  // ---------------------- end of email_exists ($email_to_test) function ---------------------------



// ---------------------------------------------------------------------------------------------------
function send_newpwd ($email) { 
/*
  Function to generate a new pass word then to send an email to user then to update swc database. 
  
  Param : 2 -> email, new password
  Return : 1 if process ok, else, 0. 
*/
  $subject = "Nouveau mot de passe / New password" ; 
  $message = "NE PAS RÉPONDRE À CET EMAIL. Veuillez trouver votre nouveau mot de passe ci-après. Il est fortement recommandé de le modifier suite à votre première connection à votre compte.\nDO NOT REPLY TO THIS EMAIL. Please find your new password hereafter. It is highly recommanded to change it after the first connexion to your account. "  ; 

  $newpwd = new_pwd() ;  // call to new_pwd function to generate a password

  $message = wordwrap($message , 70 , "\r\n") . "\n\n" . $newpwd ; 

  $headers = "Content-Type:text/plain;charset=utf-8\r\n" ; 
  $headers .= "From: isegoria222@gmail.com\r\n"  ; 

  $email_in_db = email_exists($email) ;  // call to email_exists() function

  if ($email_in_db == 1) {   
    $sent = mail($email , $subject , $message, $headers) ; 
  }

  pwd_storage($newpwd) ;  // call to pwd_storage function to update DB with new password

  return $sent ;

}// ------------- end of send_newpwd () function --------------------------------------------------------------------------------



?>


