<?php

$user = dbHelper->getUser();

$user_type = isset($user['type']) ? $user['type'] : ACCOUNT_TYPE_DEFAULT;

if (!file_exists(getcwd() . '/hook.lock.' . md5(webUrl))) {
    file_put_contents(getcwd() . '/hook.lock.' . md5(webUrl), print_r(stripeApi->setWebhook(), true));
}

$aiImagesBalance = [
    'available' => dbHelper->getUserApiCalls(MODULE_NAME_AI_IMAGES, defaulModulesLimitByType[MODULE_NAME_AI_IMAGES][$user_type]),
    'total' => defaulModulesLimitByType[MODULE_NAME_AI_IMAGES][$user_type],
];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing AI Writer | Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

    <style>
        .ck-editor__editable_inline {
            min-height: 530px;
            max-height: 530px;
        }
    </style>
</head>

<body>

    <?php

    include("Components/Navbar.php");

    ?>

    <section class="section">

        <div class="tool-container is-flex is-align-items-center is-flex-wrap-wrap m-2 is-justify-content-center">

            <a class="tool-link" href="image-generator">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/t12tWCu.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Image Generator</b>
                            <p><?php echo $aiImagesBalance['available'] ?> of <?php echo $aiImagesBalance['total'] ?> remaining</p>
                            <?php if ($_SESSION['id_token'] && $aiImagesBalance['available'] <= 0):?>
                            <p><a href="/premium" style="color: red;" target="_blank"><?php echo BUY_PREMIUM_BUTTON_TEXT ?></a></p>
                            <?php endif; ?>
                            <?php if (!$_SESSION['id_token'] && $aiImagesBalance['available'] <= 0):?>
                            <p><a href="/login?to=buy" style="color: red;" target="_blank"><?php echo BUY_PREMIUM_BUTTON_TEXT ?></a></p>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="/chat">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/K1Xv9x4.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Chat</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="writer">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/t12tWCu.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Long Form Writer</b>
                        </div>

                    </div>

                </div>
            </a>



            <a class="tool-link" href="content">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/872vrCy.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>AI Contents</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="articlewriter">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/9OzBB2c.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Article Writing</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="biographywriter">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/kLqKFWY.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Biography Writer</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="rewriter">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/pZ4cqT3.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Rewriter</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="summarize">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/MUf7Kon.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Summarize</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="improve">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/O9eow87.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Improve Text</b>
                        </div>

                    </div>

                </div>
            </a>

            <a class="tool-link" href="answers">
                <div class="card">

                    <div class="is-flex is-justify-content-center is-flex-wrap-wrap is-align-items-center" style="height: 100%;">


                        <div class="is-flex is-justify-content-center is-align-items-center">
                            <img class="my-3" src="https://i.imgur.com/Lo58Q5O.png" width="80" height="80">
                        </div>

                        <div class="content my-2 mx-4">
                            <b>Answers</b>
                        </div>

                    </div>

                </div>
            </a>

        </div>

    </section>

</body>



</html>