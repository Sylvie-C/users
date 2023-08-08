<?php session_start() ?>

<?php include ("../functions.php") ; ?>

<?php

// ------ DATA TREATMENT FROM subscription.php OR connexion.php PAGES ------

// XSS and EMPTY DATA TREATMENT
my_xss_post() ; 

// CONNEXION.PHP PAGE : 

  // $query1 = select user email in DB if email entered by user in form is in DB
  $query1 = "SELECT user_email FROM users WHERE user_email=:email" ;  

  $pdo_statement1 = $db -> prepare($query1) ; 
  $pdo_statement1 -> execute ( [ "email" => $_POST["email"] ] ) ;   // :email => email entered by user (in _POST)

  // $exists = 1 if query1 result not empty (= if email entered by user is in DB)
  $exists = $pdo_statement1 -> rowCount() ; 


  // $query2 = select username and user email from DB if email/password entered by user in form match DB data (unic email)
  $query2 = "SELECT username,user_email FROM users WHERE user_email=:email AND password=:password" ; // select username for welcome message later on

  $pdo_statement2 = $db -> prepare($query2) ; 
  $pdo_statement2 -> execute ( [ "email" => $_POST["email"]  ,  "password" => $_POST["pwd"] ] ) ; 

  $array = $pdo_statement2 -> fetch() ; 
  $_SESSION["username"] = $array["username"] ; // save username in SESSION super global for availability on whole website
  $_SESSION["email"] = $array["user_email"] ; // save user email in SESSION super global
  

  // $match=1 if password found in DB
  $match = $pdo_statement2 -> rowCount() ; 


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



