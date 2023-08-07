<?php session_start() ?>

<?php include ("../functions.php") ; ?>

<!DOCTYPE html>
<html>

<head>
	<title>SWC Connection page</title>
  <link rel="stylesheet" href="../sylvieswebcorner.css" media="only screen and (max-width:400px) , only screen and (max-width:800px) , only screen and (min-width:800px)" />
	<meta name="viewport" content="width=device-width , initial-scale=1.0" />
	<meta charset="UTF-8" />
</head>

<body id="connection_page" >  

  <a href="../index.html" class="navIcon"> <img src="../images/home_icon.jpg" alt="home icon" /> </a>

  <h2>Se connecter / Log in</h2>

  <p>Pour laisser un message, vous devez être inscrit(e) et connecté(e).<br/>To leave a message, you must sign up / sign in.</p>

  <form method="POST" action="access.php"> 
	  <p>
		  <label for="email" >Votre email / Your email : </label> <br/>
		  <input type="email" name="email" required placeholder="obligatoire/required" />
	  </p>

	  <p>
		  <label for="pwd" >Votre mot de passe / Your password : </label> <br/>
		  <input type="password" name="pwd" required placeholder="obligatoire/required"/>
	  </p>

	  <input type="submit" value="OK"  />
  </form>

    <p>Jamais inscrit ? No account ? <a href="subscription.php">  Créer un compte / Sign up here  </a> </p>

    <p>Mot de passe oublié ? Forgot your password ? </p>

    <form method="POST" action="connexion.php" > 
      <input type="email" name="email_pwd" required   placeholder="Votre email / Your email" /> 
      <input type="submit" value="Demander nouveau mot de passe / Request new password" /> 
    </form>

<?php
  // XSS and EMPTY DATA TREATMENT
  my_xss_post() ; 

  if ( isset($_POST["email_pwd"]) ) { 

    $newpwd_ok = send_newpwd($_POST["email_pwd"]) ; 

    if ($newpwd_ok) { 
      echo "Un email vous a été envoyé. Vérifiez bien vos spams.<br/>We sent an email to you. Please check your spams.  " ;  
    }else{ 
      echo "Email non envoyé. Vérifiez votre connexion.<br/>Email not sent. Check your connection. " ; 
    }
  }



?>

</body>

</html>
