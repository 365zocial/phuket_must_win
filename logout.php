<?php
  session_start();
  
  unset($_SESSION["AUTHEN"]["ID"]);
  unset($_SESSION["AUTHEN"]["ORGANIZATION_ID"]);
  unset($_SESSION["AUTHEN"]["FULLNAME"]);
  
  header("Location: index.php");
  die();
?>