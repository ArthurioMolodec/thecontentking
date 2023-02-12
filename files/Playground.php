<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing Playground</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
</head>

<body>

    <?php

    include("Components/Navbar.php");

    ?>


    <section class="section">

        <h1 class="mb-5 mt-2">TheContentKing Playground</h1>

        <div class="columns">

            <div class="column">

                <div class="field">
                    <label class="label">Talk with AI, Give instruction and get output</label>
                    <div class="control">
                        <textarea placeholder="Write a paragraph on Crypto Currencies" id="aitextbox" style="height : 70vh;white-space: pre-line;" class="textarea"></textarea>
                    </div>
                </div>

                <div class="is-flex">

                    <div class="field is-grouped">
                        <div class="control mt-3">
                            <button id="writeBtn" class="button is-primary">Write</button>
                        </div>
                    </div>

                    <div class="field is-grouped ml-3">
                        <div class="control mt-3">
                            <button id="saveBtn" class="button is-primary">Save as txt</button>
                        </div>
                    </div>

                </div>



            </div>

        </div>

    </section>
</body>

<script>
    function download(filename, text) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    }


    let saveBtn = document.getElementById("saveBtn");
    let textBox = document.getElementById("aitextbox");
    let writeBtn = document.getElementById("writeBtn");

    saveBtn.addEventListener("click", () => download("AI-Playground.txt", textBox.value));

    const init = () => {

        let dataFromStorage = localStorage.getItem("playground");

        if (dataFromStorage !== null) {
            let data = JSON.parse(dataFromStorage);
            textBox.value = data.aiTextBox;
        }

        setInterval(() => {

            let objToSave = {
                aiTextBox: textBox.value,
            }

            localStorage.setItem("playground", JSON.stringify(objToSave));

        }, 5000);
    }

    init();

    const callAPI = async (text) => {

        try {

            const api = await axios.post("playgroundapi", {
                text: text,
            });

            if (textBox.value.match(/\S/g)) {
                textBox.value += "\n";
            }

            var i = 0;
            var txt = api.data.content;
            var speed = 20;

            var typeWriter = () => {
                if (i < txt.length) {
                    textBox.value += txt.charAt(i);
                    i++;
                    textBox.scrollTo(0, textBox.scrollHeight);
                    setTimeout(typeWriter, speed);
                } else {

                    writeBtn.innerText = "Write";
                    writeBtn.disabled = false;

                }
            }

            typeWriter();

        } catch (err) {

            if (err.response.status === 400) {
                alert("Bad Request, Something is empty !");
            } else {
                alert(err.response.data)
            }

            writeBtn.innerText = "Write";
            writeBtn.disabled = false;

        }


    }

    writeBtn.addEventListener("click", async () => {

        if (writeBtn.innerText !== "Writing...") {

            writeBtn.innerText = "Writing...";
            writeBtn.disabled = true;

            let value = textBox.value

            if (value.length > 3000) {
                value = value.substr(-3000);
                alert("Input length should be less than 3000 characters !");
                return;
            }

            await callAPI(value);



        }


    });
</script>

</html>