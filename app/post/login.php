<?php
session_start();
if(!isset($_POST['login'])){
    header('location:../../layouts/errors/404.php');die;
}
include_once "../requests/Validation.php";
include_once "../models/User.php";
// validation
// email => required , regex ,
$emailValidation = new Validation('email',$_POST['email']);
$emailRequriedResult = $emailValidation->required();
if(empty($emailRequriedResult)){
    $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
    $emailRegExResult  = $emailValidation->regex($emailPattern);
    if(!empty($emailRegExResult)){
        $_SESSION['errors']['email']['regex'] = $emailRegExResult;
    }
}else{
    $_SESSION['errors']['email']['required'] = $emailRequriedResult;
}

// password => required , regex
$passwordValidation = new Validation('password',$_POST['password']);
$passwordRequiredResult = $passwordValidation->required();
if(empty($passwordRequiredResult)){
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/";
    $passwordRegExResult   = $passwordValidation->regex($passwordPattern);
    if(!empty($passwordRegExResult)){
        $_SESSION['errors']['password']['regex'] = $passwordRegExResult;
    }
}else{
    $_SESSION['errors']['password']['required'] = $passwordRequiredResult;
}
// if no errors 
if(empty($_SESSION['errors'])){
    // search in db
    $userObject = new User;
    $userObject->setEmail($_POST['email']);
    $userObject->setPassword($_POST['password']); // 123
    $result = $userObject->login(); // one user || no user
    if($result){
        // correct credentionals
        $user = $result->fetch_object();
        if($user->status == 1){ // verified
            // home
            if(isset($_POST['remember_me'])){
                setcookie('remember_me',$_POST['email'],time() + (24*60*60) * 30 * 12 , '/');
            }
            $_SESSION['user'] = $user;
            header('location:../../index.php');die;
        } elseif($user->status == 0){ // not verified
            // check code
            $_SESSION['user-email'] = $_POST['email'];
            header('location:../../check-code.php');die;
        }else{
            // 2 block
            // error
            $_SESSION['errors']['email']['block'] = "Sorry , Your Account Has Been Blocked";
        }
        // status => 1 => home
        // status => 0 => check code page
        // status => 2 => alert with block message
    }else{
        $_SESSION['errors']['email']['wrong'] = "Failed Attempt";
    }
}

// [
//     'errors'=>[
//         "email"=>[
//             'required'=>'email is required',
//             'regex'=>'email is invalid'
//         ],
//         "password"=>[
//             'required'=>'password is required',
//             'regex'=>'password is invalid'
//         ]
//     ]
// ];

header('location:../../login.php');



