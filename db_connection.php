<?php

  $db_connect = array() ; 

  $db_connect['hostname'] = 'localhost' ;
  $db_connect['dbname'] = 'swc' ;  

  $db_connect['username'] = 'utilisateurTest' ;  
  $db_connect['pwd'] = 'abc_123' ; 

  // TO BE INCLUDED AT BEGINNING OF ALL PAGES CALLING PDO OBJECT $db FOR SQL REQUESTS
  try { $db = new PDO ( "mysql:host=".$db_connect['hostname'].";dbname=".$db_connect['dbname'] , $db_connect['username'] , $db_connect['pwd'] , [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION] ) ; }
  catch (\PDOException $e) { throw new \PDOException ($e->getMessage() , $e->getCode()) ; }

  unset($db_connect) ; 

?>


