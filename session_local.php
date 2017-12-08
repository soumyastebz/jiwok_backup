<?php
$lifetime=600;
session_start();
setcookie(session_name(),session_id(),time()+$lifetime);
?>