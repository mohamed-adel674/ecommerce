<?php
$title = "Register";

include_once "layouts/header.php";
include_once "app/middleware/guest.php";
include_once "layouts/nav.php";
include_once "layouts/breadcrumb.php";
include_once "app/requests/Validation.php";
include_once "app/models/User.php";
include_once "app/services/mail.php"
;
if($_POST){
    // print_r($_POST);die;
    // validation rules
    // first_name=>required,string
    // last_name=>required,string
    // gender=>required,['f','m']
    // email =>required,regular expression(pattern),unique
    // phone => required , regex(pattern) , unique
    // password => required , regex(pattern) , = passwrod_confirmation
    $success = [];
    $emailValidation = new Validation('email',$_POST['email']);
    $emailRequiredResult = $emailValidation->required();
    $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
    if(empty($emailRequiredResult)){
        $emailRegexResult = $emailValidation->regex($emailPattern);
        if(empty($emailRegexResult)){  
            $emailUniqueResult = $emailValidation->unique('users');
            if(empty($emailUniqueResult)){  
                $success['email'] = 'email';
            }
        }
    }

    $phoneValidation = new Validation('phone',$_POST['phone']);
    $phoneRequiredResult = $phoneValidation->required();
    $phonePattern = "/^01[0-2,5,9]{1}[0-9]{8}$/";
    if(empty($phoneRequiredResult)){
        $phoneRegexResult = $phoneValidation->regex($phonePattern);
        if(empty($phoneRegexResult)){  
            $phoneUniqueResult = $phoneValidation->unique('users');
            if(empty($phoneUniqueResult)){  
                $success['phone'] = 'phone';
            }
        }
    }

    $passwordValidation = new Validation('password',$_POST['password']);
    $passwordRequiredResult = $passwordValidation->required();
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/";
    if(empty($passwordRequiredResult)){
        $passwordRegexResult = $passwordValidation->regex($passwordPattern);
        if(empty($passwordRegexResult)){
            $passwordConfirmationResult = $passwordValidation->confirmed($_POST['password_confirmation']);
            if(empty($passwordConfirmationResult)){  
                $success['password'] = 'password';
            }
        }
    }

    // if(
    //     (isset($passwordConfirmationResult) && $passwordConfirmationResult == '')
    //   &&(isset($phoneUniqueResult) &&  $phoneUniqueResult == '') 
    //   &&(isset($emailUniqueResult) && $emailUniqueResult == '')
    //   ){
    //     // no password validation errors , no email validation errors , no phone validation errors
    //     // insert user into in db
    //     echo "insert";
    // }
    if(isset($success['password']) && isset($success['email']) && isset($success['phone'])) {

        // hash for password
        // generate code
        // insert user
        $userObject = new User;
        $userObject->setFirst_name($_POST['first_name']);
        $userObject->setLast_name($_POST['last_name']);
        $userObject->setEmail($_POST['email']);
        $userObject->setPhone($_POST['phone']);
        $userObject->setGender($_POST['gender']);
        $userObject->setPassword($_POST['password']);
        $code = rand(10000,99999);
        $userObject->setCode($code);
        $result = $userObject->create();
        if($result){
            // send mail with code
            // mail to => $_POST['email']
            // mail subject => verification code
            // mail body => hello name , your verification code is:12345 thank u.
            $subject = "verifcation Code";
            $body = "Hello {$_POST['first_name']} {$_POST['last_name']} <br> your verification code is:<br>$code</br> thank you.";
            $mail = new mail($_POST['email'],$subject,$body);
            $mailResult = $mail->send();
            if($mailResult){
                // header to check code page
                // store email in session
                $_SESSION['user-email'] = $_POST['email'];
                header('location:check-code.php?page=register');die;
            }else{
                $error = "<div class='alert alert-danger'> Try Again Later </div>";
            }


            
        }else{
            $error = "<div class='alert alert-danger'> Try Again Later </div>";
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
                        <a class="active" data-toggle="tab" href="#lg2">
                            <h4> register </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg2" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <?php if(isset($error)){echo $error;} ?>
                                    <form  method="post">
                                        <input type="text" name="first_name" placeholder="First Name" value="<?php if(isset($_POST['first_name'])){echo $_POST['first_name'];} ?>">
                                        <input type="text" name="last_name" placeholder="Last Name" value="<?php if(isset($_POST['last_name'])){echo $_POST['last_name'];} ?>">
                                        <input name="email" placeholder="Email" type="email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>">
                                        <?= empty($emailRequiredResult) ? "" : "<div class='alert alert-danger'>$emailRequiredResult</div>" ; ?>
                                        <?= empty($emailRegexResult) ? "" : "<div class='alert alert-danger'>$emailRegexResult</div>" ; ?>
                                        <?= empty($emailUniqueResult) ? "" : "<div class='alert alert-danger'>$emailUniqueResult</div>" ; ?>
                                        <input name="phone" placeholder="phone" type="number" value="<?php if(isset($_POST['phone'])){echo $_POST['phone'];} ?>">
                                        <?= empty($phoneRequiredResult) ? "" : "<div class='alert alert-danger'>$phoneRequiredResult</div>" ; ?>
                                        <?= empty($phoneRegexResult) ? "" : "<div class='alert alert-danger'>$phoneRegexResult</div>" ; ?>
                                        <?= empty($phoneUniqueResult) ? "" : "<div class='alert alert-danger'>$phoneUniqueResult</div>" ; ?>
                                        <input type="password" name="password" placeholder="Password">
                                        <?= empty($passwordRequiredResult) ? "" : "<div class='alert alert-danger'>$passwordRequiredResult</div>" ; ?>
                                        <?= empty($passwordRegexResult) ? "" : "<div class='alert alert-danger'>Minimum eight and maximum 15 characters, at least one uppercase letter, one lowercase letter, one number and one special character</div>" ; ?>
                                        <input type="password" name="password_confirmation" placeholder="Confrim Password">
                                        <?= empty($passwordConfirmationResult) ? "" : "<div class='alert alert-danger'>$passwordConfirmationResult</div>" ; ?>
                                        <select name="gender" class="form-control" id="">
                                            <option <?= (isset($_POST['gender']) && $_POST['gender'] =='m' ) ? 'selected' : '' ?> value="m">Male</option>
                                            <option <?php if(isset($_POST['gender']) && $_POST['gender'] == 'f') {echo "selected";} ?> value="f">Female</option>
                                        </select>
                                        <div class="button-box mt-5">
                                            <button type="submit"><span>Register</span></button>
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
include_once "layouts/footer.php";
include_once "layouts/footer-scripts.php";
?>