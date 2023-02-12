<?php

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing Content Generator</title>
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

        <h1 class="mb-5 mt-2">TheContentKing Content</h1>

        <div class="columns">

            <div class="column">

                <div class="field">
                    <label class="label">Topic / Description / Question</label>
                    <div class="control">
                        <textarea id="topic" style="min-height : 200px;" class="textarea" placeholder="Description"></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label mt-2">Type</label>
                    <div class="control">
                        <div style="width:100%;" class="select">
                            <select style="width:100%;" id="type">
                                <option value="intro">Blog Intro</option>
                                <option value="main content">Blog Section</option>
                                <option value="conclusion">Blog Conclusion</option>
                                <option value="blog outline">Blog Outline</option>
                                <option value="blog ideas">Blog Ideas</option>
                                <option value="long form">Blog Long Content</option>
                                <option value="nlpterms">NLP Terms</option>
                                <option value="seometadesc">SEO Meta Description</option>
                                <option value="seometatitle">SEO Meta Title</option>
                                <option value="video ideas">Video Ideas</option>
                                <option value="video desc">Video Description</option>
                                <option value="video channel desc">Video Channel Description</option>
                                <option value="answer">Answer</option>
                                <option value="email">Email</option>
                                <option value="bio">Profile Bio</option>
                                <option value="aida">AIDA</option>
                                <option value="pas">PAS</option>
                                <option value="ad">Ad</option>
                                <option value="google ad">Google Search Ad</option>
                                <option value="call to action">Call to action</option>
                                <option value="business idea pitch">Business Idea Pitch</option>
                                <option value="preview">Product Review</option>
                                <option value="product desc">Product Description</option>
                                <option value="job desc">Job Description</option>
                                <option value="interview questions">Interview Questions</option>
                                <option value="notification">Notification</option>
                                <option value="reply message">Reply to message</option>
                                <option value="reply review">Reply to review</option>
                                <option value="completion">Expand</option>
                                <option value="paraphrase">Paraphrase</option>
                                <option value="summarize">Summarize</option>

                            </select>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label mt-2">Language</label>
                    <div class="control">
                        <div style="width:100%;" class="select">
                            <select style="width:100%;" id="language">
                                <option value="">English</option>
                                <option value="Spanish">Spanish</option>
                                <option value="Turkish">Turkish</option>
                                <option value="Chinese">Chinese</option>
                                <option value="Russian">Russian</option>
                                <option value="Korean">Korean</option>
                                <option value="Swedish">Swedish</option>
                                <option value="German">German</option>
                                <option value="Italian">Italian</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label mt-2">Variants</label>
                    <div class="control">
                        <div style="width:100%;" class="select">
                            <select style="width:100%;" id="variants">
                                <option value="1">1</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="field">
                    <label class="label mt-2">Tone Of Voice</label>
                    <div class="control">
                        <div style="width:100%;" class="select">
                            <select style="width:100%;" id="toneofvoice">
                                <option value="">Default</option>
                                <option value="Informative">Informative</option>
                                <option value="Convincing">Convincing</option>
                                <option value="Casual">Casual</option>
                                <option value="Worried">Worried</option>
                                <option value="Funny">Funny</option>
                                <option value="Joyful">Joyful</option>
                                <option value="Formal">Formal</option>
                                <option value="Inspirational">Inspirational</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="field">
                    <label class="label mt-2">Keywords (Optional)</label>
                    <div class="control">
                        <input id="keywords" class="input" type="text" placeholder="keyword1, keyword2">
                    </div>
                </div>


                <div class="field is-grouped">
                    <div class="control mt-3">
                        <button id="writeBtn" class="button is-primary">Write</button>
                    </div>
                </div>

            </div>



            <div class="column mt-4">

                <div id="editor"></div>

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
    let writeBtn = document.getElementById("writeBtn");
    let textBox = document.getElementById("aitextbox");
    let topic = document.getElementById("topic");
    let type = document.getElementById("type");
    let keywords = document.getElementById("keywords");
    let toneOfVoice = document.getElementById("toneofvoice");
    let language = document.getElementById("language");
    let variants = document.getElementById("variants");

    const hideValues = ['blog outline', 'answer', 'paraphrase', 'blog ideas', 'email', 'ad', 'preview', 'video ideas', 'video channel desc', 'video desc', 'seometadesc', 'seometatitle', 'bio', 'notification', 'product desc', 'job desc', 'interview questions', 'summarize', 'reply message', 'reply review', 'business idea pitch', 'call to action', 'aida', 'pas', 'google ad', 'nlpterms'];
    const hideForLang = ['blog outline', 'paraphrase', 'summarize', 'aida', 'pas', 'google ad'];


    saveBtn.addEventListener("click", () => download("AI-Content.txt", convertToText(editor.getData())));

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

    type.addEventListener("change", (e) => {

        const {
            value
        } = e.target;

        if (value === "long form" || value === "main content") {
            variants.value = "1";
        }

        if (hideForLang.includes(value)) {
            language.disabled = true;
        } else {
            language.disabled = false;
        }

        if (hideValues.includes(value)) {
            keywords.disabled = true;
            toneOfVoice.disabled = true;
        } else {
            keywords.disabled = false;
            toneOfVoice.disabled = false;
        }
    });

    variants.addEventListener("change", (e) => {
        if (type.value === "long form" || type.value === "main content") {
            variants.value = "1";
        }
    })

    const init = () => {

        let dataFromStorage = localStorage.getItem("aicontent");
        let ckScroll = document.querySelector(".ck-editor__editable");

        if (dataFromStorage !== null) {
            
            let data = JSON.parse(dataFromStorage);
            keywords.value = data.keywords;
            toneOfVoice.value = data.toneOfVoice;
            topic.value = data.topic;
            type.value = data.type;
            language.value = data.language;
            variants.value = data.variants;
            let d = data.aiTextBox.replace(/\n/g, "</br>");
            editor.data.set(d);
         
            if (data.type === "long form" || data.type === "main content") {
                variants.value = "1";
            }

            if (hideForLang.includes(data.type)) {
                language.disabled = true;
            } else {
                language.disabled = false;
            }

            if (hideValues.includes(data.type)) {
                keywords.disabled = true;
                toneOfVoice.disabled = true;
            }

        }



        setInterval(() => {

            let objToSave = {
                keywords: keywords.value,
                toneOfVoice: toneOfVoice.value,
                type: type.value,
                topic: topic.value,
                aiTextBox: editor.data.get(),
                language: language.value,
                variants: variants.value,
            }

            localStorage.setItem("aicontent", JSON.stringify(objToSave));

        }, 5000);



        ckScroll.scrollTo(0, ckScroll.scrollHeight);
    }

    const callAPI = () => {

        if (type.value === "long form" || type.value === "main content") {
            variants.value = "1";
        }

        axios.post("contentapi", {
                keywords: keywords.value,
                type: type.value,
                description: topic.value,
                toneOfVoice: toneOfVoice.value,
                language: language.value,
                variants: variants.value,
            })
            .then((e) => {


                let contentArray = e.data.content;

                for (const x of contentArray) {

                    //Appending text to end of the editor 
                    let ckScroll = document.querySelector(".ck-editor__editable");
                    let txt = '';

                    if (editor.data.get() == "") {
                        txt = "";
                    } else {
                        txt = "\n\n_______\n"
                    }

                    txt += x;
                    txt = txt.replace(/\n/g, "</br>");

                    let content = `<p>${txt}</p>`;

                    const viewFragment = editor.data.processor.toView(content);
                    const modelFragment = editor.data.toModel(viewFragment);

                    //most imp to set cursor at the end
                    editor.model.change(writer => {
                        writer.setSelection(writer.createPositionAt(editor.model.document.getRoot(), 'end'));
                    });

                    editor.model.insertContent(modelFragment, editor.model.document.selection.getFirstPosition(), 'end');


                    ckScroll.scrollTo(0, ckScroll.scrollHeight);
                }


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

        if (type.value !== "" && topic.value !== "" && writeBtn.innerText !== "Writing...") {
            callAPI();
        }

        writeBtn.innerText = "Writing...";
        writeBtn.disabled = true;

    });
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
