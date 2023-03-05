<?php
  session_start();
  
  session_register('meno_uzivatela');
  session_register('stav');

  session_destroy();      
  
  header("location: index.php");
?>