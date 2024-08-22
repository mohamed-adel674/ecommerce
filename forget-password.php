<?php
$title = "Forget Password";
include_once "layouts/header.php";
include_once "app/middleware/guest.php";
include_once "app/models/User.php";
include_once "app/requests/Validation.php";
include_once "app/services/mail.php";
if($_POST){
    // validation
    // email => required , regex ,
    $errors = [];
    $emailValidation = new Validation('email',$_POST['email']);
    $emailRequriedResult = $emailValidation->required();
    if(empty($emailRequriedResult)){
        $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
        $emailRegExResult  = $emailValidation->regex($emailPattern);
        if(!empty($emailRegExResult)){
            $errors['email-regex'] = $emailRegExResult;
        }
    }else{
        $errors['email-required'] = $emailRequriedResult;
    }
    // search on email in db
    if(empty($errors)){
        $userObject = new user;
        $userObject->setEmail($_POST['email']);
        $result = $userObject->getUserByEmail(); // 1
        if($result){
            // correct email
            $user = $result->fetch_object(); // user variable has all user data in db
            // if exists => generate code ,  
            $code = rand(10000,99999);
            $userObject->setCode($code);
            $updateResult = $userObject->updateCodeByEmail();
            if($updateResult){
                // , save code 
                // send code
                // , header check-code.php
            $subject = "Forget Password Code";
            $body = "Hello {$_POST['first_name']} {$_POST['last_name']} <br> your Forget Password code is:<br>$code</br> thank you.";
            $mail = new mail($_POST['email'],$subject,$body);
            $mailResult = $mail->send();
            if($mailResult){
                // header to check code page
                // store email in session
                $_SESSION['user-email'] = $_POST['email'];
                header('location:check-code.php?page=forget');die;
            }else{
                $errors['try-again'] = "Try Again Later";
            }
            }else{
                $errors['some-wrong'] = "Something Went Wrong";
            }
        }else{
            // wrong email
            // if not exists => error (this dosen't match our records)
            $errors['email-wrong'] = "this email dosen't match our records";
        }
    }
    
  
}
?>
    <div class="login-register-area ptb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 ml-auto mr-auto">
                    <div class="login-register-wrapper">
                        <div class="login-register-tab-list nav">
                            <a class="active" data-toggle="tab" href="#lg1">
                                <h4> <?= $title ?> </h4>
                            </a>
                        </div>
                        <div class="tab-content">
                            <div id="lg1" class="tab-pane active">
                                <div class="login-form-container">
                                    <div class="login-register-form">
                                        <form method="post">
                                            <input  type="email" name="email" placeholder="Enter Your Email Address">
                                            <?php
                                                if(!empty($errors)){
                                                    foreach ($errors as $key => $value) {
                                                        echo "<div class='alert alert-danger'>$value</div>";
                                                    }
                                                }
                                            ?>
                                            <div class="button-box">
                                                <button type="submit"><span>Verify Email Address</span></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
include_once "layouts/footer-scripts.php";
?>
       