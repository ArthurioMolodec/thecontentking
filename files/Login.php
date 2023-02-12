<?php

$errors = [];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || $_POST['email'] == '') {
        $errors[] = 'Email is required';
    }

    if (!isset($_POST['pass']) || $_POST['pass'] == '') {
        $errors[] = 'Password is required';
    }

    if (!count($errors)) {
        $result = dbHelper->loginWithEmailPass($_POST['email'], $_POST['pass']);
        if ($result !== true) {
            $error = $result;
        } else {

            if (isset($_POST['to'])) {
                if (urldecode($_POST['to']) === 'buy') {
                    redirect('/premium');
                }
            }
            redirect('/');
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
    <title>TheContentKing AI Writer | Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

</head>

<body>

    <?php

    include("Components/Navbar.php");

    ?>

    <section class="section">

        <div class="is-flex is-align-items-center is-flex-wrap-wrap is-justify-content-center" style="min-height: 85vh;">

            <form action="" method="POST">

                    <div class="field">
                        <label class="label">Email</label>
                        <input name="email" class="input is-success" type="email" placeholder="Email">
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <input name="pass" class="input is-success" type="password" placeholder="Password">
                    </div>

                    <input type="hidden" name="to" value="<?php echo isset($_GET['to']) ? urlencode($_GET['to']) : '0' ?>">

                    <p class="help is-danger my-2"><?php echo $error ?></p>

                    <div class="field">
                        <div class="control">
                            <button style="width: 100%;" class="button is-primary">Login</button>
                        </div>
                    </div>

                    <p>Don't have account yet? <a href="/registration?to=<?php echo isset($_GET['to']) ? urlencode($_GET['to']) : '0' ?>">Sign Up</a></p>

            </form>


        </div>

    </section>

</body>



</html>