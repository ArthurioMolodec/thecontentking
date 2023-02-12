<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing Summarize</title>
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

        <h1 class="mb-5 mt-2">TheContentKing Summarize</h1>

        <div class="columns">

            <div class="column">

                <div class="field">
                    <label class="label">Text | Break the text in small para for better results</label>
                    <div class="control">
                        <textarea id="copiedArticle" style="height : 80vh;white-space: pre-line;" class="textarea"></textarea>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control mt-3">
                        <button id="rewriteBtn" class="button is-primary">Summarize</button>
                    </div>
                </div>


            </div>



            <div class="column">

                <div class="field">
                    <label class="label">Summarized by AI</label>
                    <div class="control">
                        <textarea id="aitextbox" style="height : 80vh;white-space: pre-line;" class="textarea"></textarea>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control mt-3">
                        <button id="saveBtn" class="button is-primary">Save as txt</button>
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
    let rewriteBtn = document.getElementById("rewriteBtn");
    let copiedArticle = document.getElementById("copiedArticle");

    saveBtn.addEventListener("click", () => download("AI-Summarize.txt", textBox.value));

    const init = () => {

        let dataFromStorage = localStorage.getItem("summarize");

        if (dataFromStorage !== null) {
            let data = JSON.parse(dataFromStorage);
            textBox.value = data.aiTextBox;
            copiedArticle.value = data.copiedArticle;
        }

        setInterval(() => {

            let objToSave = {
                aiTextBox: textBox.value,
                copiedArticle: copiedArticle.value,
            }

            localStorage.setItem("summarize", JSON.stringify(objToSave));

        }, 5000);
    }

    init();

    const callAPI = async (para) => {

        try {

            const api = await axios.post("summarizeapi", {
                para: para,
            });

            if (textBox.value.match(/\S/g)) {
                textBox.value += "\n";
            }

            textBox.value += api.data.content
            textBox.scrollTo(0, textBox.scrollHeight);

        } catch (err) {

            if (err.response.status === 400) {
                alert("Bad Request, Something is empty !");
            } else {
                alert(err.response.data)
            }

            rewriteBtn.innerText = "Summarize";
            rewriteBtn.disabled = false;

        }


    }

    rewriteBtn.addEventListener("click", async () => {

        if (copiedArticle.value !== "" && rewriteBtn.innerText !== "Summarizing...") {

            rewriteBtn.innerText = "Summarizing...";
            rewriteBtn.disabled = true;


            // let paras = copiedArticle.value.split("\n");
            // let parasArray = [];

            // for (const x of paras) {
            //     if (x.match(/[a-z]/gi)) {
            //         parasArray.push(x);
            //     }
            // }

            // for (const x of parasArray) {

            //     await callAPI(x);

            // }

            let paras = copiedArticle.value;
            await callAPI(paras);

            rewriteBtn.innerText = "Summarize";
            rewriteBtn.disabled = false;

        }


    });
</script>

</html>