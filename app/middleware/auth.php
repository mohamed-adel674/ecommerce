<?php
// allow authenticated users and prevent guests
if(empty($_SESSION['user'])){
    header('location:login.php');die;
}