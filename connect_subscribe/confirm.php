<?php session_start() ?>

<?php include ("../functions.php") ; ?>

<?php

// XSS and EMPTY DATA TREATMENT
my_xss_post() ; 


// SUBSCRIPTION.PHP PAGE

  // Check if email entered by user already exists in DB
  $query1 = "SELECT user_email FROM users WHERE user_email=:email" ;  // query select emails in DB where email entered = email in DB
  $pdo_statement1 = $db -> prepare($query1) ; 
  $pdo_statement1 -> execute ( [ "email" => $_POST["email_subscr"] ] ) ; // email_subscr = email entered by user in subscription.php page

  $exists = $pdo_statement1 -> rowCount() ; 

  $query2 = "INSERT INTO users (username, user_email, password) VALUES ( :user_name , :email , :pwd )" ; // query update users table
  $pdo_statement2 = $db -> prepare($query2) ; 

  $query3 = "INSERT INTO passwords (email, current_pwd, former_pwd, date_change) VALUES (:useremail , :pwd , '' , '' )" ; // query update passwords table
  $pdo_statement3 = $db -> prepare($query3) ; 


  // If email entered already exists in DB, back to subscription page + prompt. 
  if ($exists > 0) { 
    echo " <script type='text/javascript'>  \n
            alert('Un compte existe déjà avec cet email. \\nAn account already exists with this email.') ;  \n
            window.location.href = 'subscription.php' ; \n
          </script> " ; 
  // else (email/account does not exist) , if pwd01 entered AND passwords not identical, back to subscription.php page + prompt
  }else{ 
    if ( ($_POST["pwd01"] !== NULL)  AND  ($_POST["pwd01"] !== $_POST["pwd02"]) ) 
    {  
      echo " <script type='text/javascript'> alert ('Les mots de passe ne sont pas identiques. \\nPasswords are not identical. ') ; \n
              window.location.href = 'subscription.php' ;  \n
            </script> " ;
    }

    // else (email/account does not exist), if pwd01 entered AND passwords strictly identical, insert data into DB (users and passwords tables) + prompt
    if (  ($_POST["pwd01"] !== NULL)  AND  ($_POST["pwd01"] === $_POST["pwd02"])  ) 
    {
      // update users table
      $pdo_statement2 -> execute( [ "user_name" => $_POST["username"]  ,  "email" => $_POST["email_subscr"]  ,  "pwd" => $_POST["pwd01"] ] ) ; 
      echo "<script type='text/javascript'> alert('Inscription réussie. Subscription successfull.') ;  window.location.href = 'connexion.php' ; </script>" ; 

      // update passwords table
      $pdo_statement3 -> execute ( [ "useremail" => $_POST["email_subscr"] , "pwd" => $_POST["pwd01"] ] ) ; 


    }
  }

?>


