<?php

// allow guests and prevent authenticated users
if(!empty($_SESSION['user'])){
    header('location:index.php');die;
}

// $_SERVER['HTTP_REFFER'];