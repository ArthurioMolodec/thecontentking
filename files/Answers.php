<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheContentKing Answers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">

    <style>
        .main-c {
            width: 50%;
        }
        .card{
          height : auto;
          width : 100%;
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

        <h1 class="mb-5 mt-2">TheContentKing Answers</h1>


        <div class="is-flex-direction-column m-auto main-c">

            <div class="field">
                <label class="label mt-2">Question</label>
                <div class="control">
                    <input id="question" class="input" type="text" placeholder="Is Crypto profitable ?">
                </div>
            </div>

            <div class="control mt-3">
                <button style="width: 100%;" id="writeBtn" class="button is-primary">Generate</button>
            </div>

            <div class="mt-4" id="card-c">



            </div>


        </div>



    </section>
</body>

<script>
    let question = document.getElementById("question");
    let cardC = document.getElementById("card-c");
    let writeBtn = document.getElementById("writeBtn");


    const callAPI = () => {

        axios.post("answersapi", {
                question: question.value
            })
            .then((e) => {

                let oldHTML = cardC.innerHTML;
                cardC.innerHTML = "";

                for (const x of e.data.content) {
                    cardC.innerHTML += `
                    <div class="card mt-2 mb-2">
                        <div class="card-content">
                            <div class="content">
                               ${x}
                            </div>
                        </div>
                    </div>
                    `;
                }

                cardC.innerHTML += oldHTML;

                writeBtn.innerText = "Generate";
                writeBtn.disabled = false;

            })
            .catch((err) => {
                
                if (err.response.status === 400) {
                    alert("Bad Request, Something is empty !");
                } else {
                    alert(err.response.data)
                }

                writeBtn.innerText = "Generate";
                writeBtn.disabled = false;

            })
    }

    writeBtn.addEventListener("click", () => {

        if (question.value !== "" && writeBtn.innerText !== "Generating...") {
            callAPI();
        }

        writeBtn.innerText = "Generating...";
        writeBtn.disabled = true;

    });
</script>

</html>
