<?php session_start() ?>

<!DOCTYPE html>
<html>

<head>
	<title>SWC S'inscrire / Sign up</title>
  <link rel="stylesheet" href="../sylvieswebcorner.css" media="only screen and (max-width:400px) , only screen and (max-width:800px) , only screen and (min-width:800px)" />
	<meta name="viewport" content="width=device-width , initial-scale=1.0" />
	<meta charset="UTF-8" />
</head>


<body>

  <a href="../index.html" class="navIcon">  <img src="../images/home_icon.jpg" alt="home icon" />  </a>

  <h2> Inscription </h2>

  <form method="POST" action="confirm.php" >
	  <p>
		  <label for="pseudo" >Votre nom d'utilisateur / Your username : </label> <br/>
		  <input type="text" name="username" />
	  </p>

	  <p>
		  <label for="email" >Votre email / Your email : </label> <br/>
		  <input type="text" name="email_subscr" required placeholder="obligatoire/required" />
	  </p>

	  <p>
		  <label for="pwd" >Choisissez un mot de passe / Choose a password : </label> <br/>
		  <input type="password" name="pwd01" required placeholder="obligatoire/required"/>
	  </p>

    <p>
		  <label for="pwd" >Confirmez votre mot de passe / Confirm your password : </label> <br/>
		  <input type="password" name="pwd02" required placeholder="obligatoire/required"/>
	  </p>

	  <input type="submit" value="Envoi/Send" />
  </form>

</body>

</html>
