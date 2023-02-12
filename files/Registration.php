<?php

$errors = [];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($resultCaptcha = validateCaptcha()) !== true) {
        $errors = array_merge($errors, $resultCaptcha);
    }

    list($input, $errorsValidation) = validateFields(['email', 'pass', 'pass_confirmation', 'first_name', 'last_name'], []);
    $errors = array_merge($errors, $errorsValidation);

    if (isset($input['pass']) && isset($input['pass_confirmation']) && $input['pass_confirmation'] != $input['pass']) {
        $errors[] = 'Password and Password confirmation must be the same';
    }

    if (!count($errors)) {
        $userData = [ 
            // 'phone' => $input['phone'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'type' => ACCOUNT_TYPE_DEFAULT,
        ];
        $result = dbHelper->signUpWithEmailPass($input['email'], $input['pass'], $userData);
        if ($result !== true) {
            $error = $result;
        } else {
            redirect('/login?to=' . (isset($_POST['to']) ? $_POST['to'] : 0));
        }
    } else {
        $error = implode('<br>', $errors);
    }
}


?>

<?php

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing AI Writer | Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <style>
        .border-transparent {
            border: 1px solid transparent;
        }
        .border-red {
            border: 1px solid red;
        }
    </style>
    <script>
        function registerSubmit(e) {

            const captcha = document.getElementsByName('h-captcha-response');

            if (!!captcha[0] && !!captcha[0].value) {
                return;
            }

            document.getElementById('captcha-container').classList.remove('border-red');
            document.getElementById('captcha-container').classList.add('border-red');

            e.preventDefault();

        }
    </script>

</head>

<body>

    <?php

    include("Components/Navbar.php");

    ?>

    <section class="section">

        <div class="is-flex is-align-items-center is-flex-wrap-wrap is-justify-content-center" style="min-height: 85vh;">

            <form action="" method="POST" onsubmit="registerSubmit(event)">

            <div class="field">
                    <label class="label">Email</label>
                    <input name="email" class="input is-success" type="email" placeholder="Email">
                </div>
                <div class="field">
                    <label class="label">First name</label>
                    <input name="first_name" class="input is-success" type="text" placeholder="First name">
                </div>
                <div class="field">
                    <label class="label">Last name</label>
                    <input name="last_name" class="input is-success" type="text" placeholder="Last name">
                </div>
                <!-- <div class="field">
                    <label class="label">Phone</label>
                    <input name="phone" class="input is-success" type="phone" placeholder="Phone">
                </div> -->
                <div class="field">
                    <label class="label">Password</label>
                    <input name="pass" class="input is-success" type="password" placeholder="Password">
                </div>
                <div class="field">
                    <label class="label">Confirm Password</label>
                    <input name="pass_confirmation" class="input is-success" type="password" placeholder="Password confirmation">
                </div>

                <div class="h-captcha border-transparent" id="captcha-container" data-sitekey="<?php echo hCaptchaSiteKey ?>"></div>

                <input type="hidden" name="to" value="<?php echo isset($_GET['to']) ? urlencode($_GET['to']) : '0' ?>">

                <p class="help is-danger my-2"><?php echo $error ?></p>

                <div class="field">
                    <div class="control">
                        <button style="width: 100%;" class="button is-primary">Sign Up</button>
                    </div>
                </div>

            </form>


        </div>

    </section>

</body>



</html>