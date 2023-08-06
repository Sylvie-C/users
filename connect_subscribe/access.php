<?php session_start() ?>

<?php include ("../functions.php") ; ?>

<?php

// ------ DATA TREATMENT FROM subscription.php OR connexion.php PAGES ------

// XSS and EMPTY DATA TREATMENT
my_xss_post() ; 

// CONNEXION.PHP PAGE : 

  // Save results for email already in DB or not
  $query2 = "SELECT user_email FROM users WHERE user_email=:email" ;  

  $pdo_statement2 = $db -> prepare($query2) ; 
  $pdo_statement2 -> execute ( [ "email" => $_POST["email"] ] ) ;   // :email => email entered by user (in _POST)

  // if email entered = email in DB, $exists > 0
  $exists = $pdo_statement2 -> rowCount() ; 


  // Check if email/password in DB + save results for password match or not
  $query3 = "SELECT username,user_email FROM users WHERE user_email=:email AND password=:password" ; // select username for welcome message later on

  $pdo_statement3 = $db -> prepare($query3) ; 
  $pdo_statement3 -> execute ( [ "email" => $_POST["email"]  ,  "password" => $_POST["pwd"] ] ) ; // pwd = id in connexion.php page only

  $array = $pdo_statement3 -> fetch() ; 
  $_SESSION["username"] = $array["username"] ; // save username from DB in SESSION super global for availability on whole website
  $_SESSION["email"] = $array["user_email"] ; // save user email from DB in SESSION super global
  

  // if password entered = password in DB, $match > 0
  $match = $pdo_statement3 -> rowCount() ; 


// ACCESS TESTS

  // if email and password match, access contact.php page
  if ($match > 0) { 
    echo "<script type='text/javascript'> window.location.href = '../contact.php' ; </script>" ; 

  }else{ // else (email and password don't match), test if email in DB (-> connexion.php page) or not (-> subscription.php page)

    if ( (isset($_POST["email"]))  AND  ($exists > 0) ) { 
      echo " <script type='text/javascript'> alert ('Le mot de passe est incorrect. The password is incorrect.') ; \n
              window.location.href = 'connexion.php' ;  \n
            </script> " ; 
    }else{ 
      echo " <script type='text/javascript' > \n
                if (confirm ('Cet email n\'existe pas. Vous inscrire ? \\nThis email does not exist. Sign up ?') == true) { \n
                  window.location.href = 'subscription.php' ;  \n
                }else{ window.location.href = 'connexion.php' ; }  \n
            </script> " ; 
    }
  }

?>



