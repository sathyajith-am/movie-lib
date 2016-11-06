<?php
  session_start();
  session_destroy();
  unset($_COOKIE['session_id']);
  echo 1;
?>
