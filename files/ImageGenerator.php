<?php

$user = dbHelper->getUser();

$user_type = isset($user['type']) ? $user['type'] : ACCOUNT_TYPE_DEFAULT;

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
    <title>Image Generator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

    <style>
        .ck-editor__editable_inline {
            min-height: 530px;
            max-height: 530px;
        }

        .result-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .image-container {
            width: 50%;
            border: 2px solid white;
        }

        .preloader-wrapper {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            widht: 100%;
            height: 100%;
            background: #0000004f;
        }

        .preloader {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto;
            top: 45%;
        }

        .square {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 5px;
            border: 5px solid #f3f3f3;
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }

        .d-none {
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .code-red-glow {
            box-shadow: 0 0 10px #ff0000;
            animation: glow 1s ease-in-out infinite;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 5px #ff0000;
            }

            to {
                box-shadow: 0 0 20px #ff0000;
            }
        }
    </style>
</head>

<body>

    <?php

    include("Components/Navbar.php");

    ?>


    <section class="section">

        <h1 class="mb-5 mt-2">Image Generator</h1>
        <p id="total-and-left" style="color: <?php echo $aiImagesBalance['available'] ? 'black' : 'red'; ?>"><?php echo $aiImagesBalance['available'] ?> of <?php echo $aiImagesBalance['total'] ?> remaining</p>
        <div class="columns">

            <div class="column">

                <div class="field">
                    <label class="label mt-2">Image Type</label>
                    <div class="control">
                        <div style="width:100%;" class="select">
                            <select style="width:100%;" id="image_type">
                                <option value="Logos">Logos</option>
                                <option value="Flyers">Flyers</option>
                                <option value="Instagram Post">Instagram Post</option>
                                <option selected value="Video Thumbnails">Video Thumbnails</option>
                                <option value="Pamphlets">Pamphlets</option>
                                <option value="Proposals">Proposals</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">What should AI draw?</label>
                    <div class="control">
                        <textarea id="description" style="min-height : 200px;" class="textarea" placeholder="What should AI draw?"></textarea>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control mt-3">
                        <p class="mb-2" id="redirect"><?php echo $aiImagesBalance['available'] ? '' : '<a style="color: red;" href="' . (isset($_SESSION['id_token']) ? '/premium' : '/login?to=buy') . '">' . BUY_PREMIUM_BUTTON_TEXT . '</a>'; ?></p>
                        <button onclick="generateImage()" class="button is-primary">Generate</button>
                        <p id="text-under-generate-button" style="color: #dbdbdb" class="d-none">click again to create new images</p>
                    </div>
                </div>

            </div>

            <div class="column result-container" id="results">

            </div>

        </div>

    </section>

    <div class="preloader-wrapper d-none" id="preloader">
        <div class="preloader">
            <div class="square"></div>
        </div>
    </div>
</body>

<script>
    const IMAGE_GENERATOR_API = '/imagegeneratorapi';
    const promptInput = document.getElementById('description');
    const resultsContainer = document.getElementById('results');
    const totalAndLeftCount = document.getElementById('total-and-left');
    const linkRedirContainer = document.getElementById('redirect');

    function showPreloader() {
        document.getElementById('preloader').classList.remove('d-none');
    }

    function hidePreloader() {
        document.getElementById('preloader').classList.add('d-none');
    }

    function addAnswer(image, prompt = 'Image') {
        resultsContainer.insertAdjacentHTML('afterBegin', `<div class="image-container" onclick="newTagImage('${prompt.replace("'", "")}', 'data:image/png;base64,${image}')"><img src="data:image/png;base64,${image}"/></div>`);
    }

    function generateImage() {
        const userPrompt = promptInput.value;
        if (!userPrompt) {
            promptInput.classList.add('code-red-glow');
            return;
        }
        promptInput.classList.remove('code-red-glow');
        showPreloader();
        axios({
            url: IMAGE_GENERATOR_API,
            method: "GET",
            params: {
                prompt: userPrompt
            }
        }).then(result => {

            if (result.status !== 200 || !result.data.status) {
                if (!result.data.error) {
                    alert('Error occurred ' + JSON.stringify(result));
                    return;
                }
            }

            if (result.data.error) {
                if (result.data.error && result.data.error === 'unpayed' && result.data.redirect) {
                    linkRedirContainer.innerHTML = '<a style="color:red;" href="' + result.data.redirect + '"><?php echo BUY_PREMIUM_BUTTON_TEXT; ?></a>';
                }
                if (result.data.error && result.data.error === 'needauth' && !result.data.redirect) {
                    linkRedirContainer.innerHTML = '<a style="color:red;" href="/login?to=buy"><?php echo BUY_PREMIUM_BUTTON_TEXT; ?></a>';
                }
            }


            if (result.data.status) {

                Object.values(result.data.result).forEach(image => {
                    addAnswer(image);
                })
            }

            if (result.data.left_count !== undefined) {
                totalAndLeftCount.innerText = `${result.data.left_count} of ${result.data.total_count} remaining`;

                if (result.data.left_count <= 0) {
                    totalAndLeftCount.style.color = 'red';
                } else {
                    totalAndLeftCount.style.color = 'black';
                }
            }
        }).then(v => {
            document.getElementById('text-under-generate-button').classList.remove('d-none');
            hidePreloader();
        }).catch(r => {
            if (r && r.response && r.response.status === 401) {
                location.href = '/';
            }
        })
    }

    function newTagImage(filename, text) {

        var image = new Image();
        image.src = text;

        var w = window.open("");
        w.document.write(image.outerHTML);

    }
</script>

</html>