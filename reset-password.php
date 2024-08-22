<?php
$title = "Reset Password";
include_once "layouts/header.php";
include_once "app/middleware/guest.php";
if(empty($_SESSION['user-email'])){
    header('location:login.php');die;
}
include_once "app/requests/Validation.php";
include_once "app/models/User.php";
if($_POST){
    // validation
    // (password=>required,regex,confirmed )
    
        $errors = [];
        $passwordValidation = new Validation('password',$_POST['password']);
        $passwordRequiredResult = $passwordValidation->required();
        if(empty($passwordRequiredResult)){
            $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/";
            $passwordRegExResult   = $passwordValidation->regex($passwordPattern);
            if(empty($passwordRegExResult)){
                $passwordConfrimResult   =  $passwordValidation->confirmed($_POST['password_confirmation']);
                if(!empty($passwordConfrimResult)){
                    $errors['password']['confirm'] = $passwordConfrimResult;
                }
            }else{
                $errors['password']['regex'] = "Minimum eight and maximum 15 characters, at least one uppercase letter, one lowercase letter, one number and one special character";
            }
        }else{
            $errors['password']['required'] = $passwordRequiredResult;
        }

        // (password_confirmation=>requried )
        $confrimPasswordValidation = new Validation('confrim password',$_POST['password_confirmation']);
        $confrimPasswordRequiredResult = $confrimPasswordValidation->required();
        if(!empty($confrimPasswordRequiredResult)){
            $errors['confirm']['required'] = $confrimPasswordRequiredResult;
        }
        // [
        //     'password'=>[
        //         'required'=>'',
        //          'regex'=>'',
        //     ],
        //         'confrim'=>[
        //             'required'
        //     ]
        // ]
        if(empty($errors)){
            // update user password
            // header to login
            $userObject = new user;
            $userObject->setPassword($_POST['password']);
            $userObject->setEmail($_SESSION['user-email']);
            $result = $userObject->updatePasswordByEmail();
            if($result){
                unset($_SESSION['user-email']);
                $success = "Your Password Has Been Successfully Updated";
                header('Refresh:3; url=login.php');
            }else{
                $errors['password']['wrong'] = "Something Went Wrong";
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
                                        <?php 
                                            if(isset($success)){
                                                echo "<div class='alert alert-success text-center'>$success</div>";
                                            }
                                        ?>
                                        <form  method="post">                                            
                                            <input type="password" name="password" placeholder="Password">
                                            <?php 
                                                if(!empty($errors['password'])){
                                                    foreach ($errors['password'] as $key => $value) {
                                                        echo "<div class='alert alert-danger'> $value </div>";
                                                    }
                                                }
                                            ?>
                                            <input type="password" name="password_confirmation" placeholder="Confrim Password">
                                            <?php 
                                                if(!empty($errors['confirm'])){
                                                    foreach ($errors['confirm'] as $key => $value) {
                                                        echo "<div class='alert alert-danger'> $value </div>";
                                                    }
                                                }
                                            ?>
                                            <div class="button-box">
                                                <button type="submit" name="login"><span><?= $title ?></span></button>
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
