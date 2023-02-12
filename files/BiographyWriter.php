<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing Biography Writer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

    <style>
        .main-c {
            width: 50%;
        }

        .ck-editor__editable_inline {
            min-height: 530px;
            max-height: 530px;
        }

        @media(max-width : 800px) {
            .main-c {
                width: 100%;
            }
        }
    </style>
</head>


<body>


    <?php

    include("Components/Navbar.php");

    ?>



    <section class="section">

        <h1 class="mb-5 mt-2">TheContentKing Biography Writer</h1>

        <div class="is-flex-direction-column m-auto main-c">

            <div class="field">
                <label class="label mt-2">Personality</label>
                <div class="control">
                    <input id="topic" class="input" type="text" placeholder="Isaac Newton">
                </div>
            </div>

            <div class="control mt-3">
                <button style="width: 100%;" id="writeBtn" class="button is-primary">Write</button>
            </div>

            <div class="field my-2">
                <p class="my-2 mt-4" style="color:red;">It can take few minutes</p>

                <div id="editor"></div>

                <div class="is-flex">

                    <div class="field is-grouped">
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

    let topic = document.getElementById("topic");
    let aiTextBox = document.getElementById("aitextbox");
    let writeBtn = document.getElementById("writeBtn");

    saveBtn.addEventListener("click", () => download("AI-Article.txt", convertToText(editor.getData())));


    const convertToText = (html) => {

        html = html.replace(/<style([\s\S]*?)<\/style>/gi, '');
        html = html.replace(/<script([\s\S]*?)<\/script>/gi, '');
        html = html.replace(/<\/div>/ig, '\n');
        html = html.replace(/<\/li>/ig, '\n');
        html = html.replace(/<li>/ig, '  *  ');
        html = html.replace(/<\/ul>/ig, '\n');
        html = html.replace(/<\/p>/ig, '\n');
        html = html.replace(/<br\s*[\/]?>/gi, "\n");
        html = html.replace(/<[^>]+>/ig, '');

        return html;
    }

    const callAPI = () => {

        axios.post("biographyapi", {
                topic: topic.value,
            })
            .then((e) => {

                //Appending text to end of the editor 
                let txt = '';

                txt += e.data.content;
                txt = txt.replace(/\n/g, "</br>");
                txt = txt.replace(/##1/g, "<b>");
                txt = txt.replace(/##2/g, "</b>");

                let content = `<p>${txt}</p>`;

                const viewFragment = editor.data.processor.toView(content);
                const modelFragment = editor.data.toModel(viewFragment);

                editor.data.set(`<b>${topic.value}</b>` + "</br>");

                //most imp to set cursor at the end
                editor.model.change(writer => {
                    writer.setSelection(writer.createPositionAt(editor.model.document.getRoot(), 'end'));
                });

                editor.model.insertContent(modelFragment, editor.model.document.selection.getFirstPosition(), 'end');


                writeBtn.innerText = "Write";
                writeBtn.disabled = false;

            })
            .catch((err) => {

                if (err.response.status === 400) {
                    alert("Bad Request, Something is empty !");
                } else {
                    alert(err.response.data)
                }

                writeBtn.innerText = "Write";
                writeBtn.disabled = false;

            })
    }

    writeBtn.addEventListener("click", () => {

        if (topic.value !== "" && writeBtn.innerText !== "Writing...") {

            if (editor.data.get() != "") {
                if (confirm("Existing content will be removed, its better to save it somewhere. Do you want to continue?")) {
                    callAPI();
                    writeBtn.innerText = "Writing...";
                    writeBtn.disabled = true;
                }
            } else {
                callAPI();
                writeBtn.innerText = "Writing...";
                writeBtn.disabled = true;
            }

        } else {
            return
        }



    });

    const init = () => {

        let dataFromStorage = localStorage.getItem("biography");

        if (dataFromStorage !== null) {
            let data = JSON.parse(dataFromStorage);

            let d = data.aiTextBox.replace(/\n/g, "</br>");
            editor.data.set(d);

            topic.value = data.topic;

        }

        setInterval(() => {

            let objToSave = {
                topic: topic.value,
                aiTextBox: editor.data.get(),
            }

            localStorage.setItem("biography", JSON.stringify(objToSave));

        }, 3000);
    };
</script>


<script>
    var editor;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', 'bold', 'italic', 'link', 'undo', 'redo', 'numberedList', 'bulletedList']
        })
        .then(e => {
            editor = e;
            init();
        })
        .catch(error => {
            console.error(error);
        });
</script>

</html>
