<!DOCTYPE html>
<html>

<head>
    <title>The King of Content Creation</title>
    <meta charset="UTF-8">
    <meta name="description" content="The King of Content Creation">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">

    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="assets/style.css">

    <style>
        @import url('https://fonts.cdnfonts.com/css/tahoma');




        .modern-green-gradient {
            background: linear-gradient(to right, #00d1b2, #63bf95) !important;
        }

        textarea.blue {
            height: 100px;
            background-color: #1790e3 !important;
            color: white !important;
        }

        .messages {
            overflow: auto;
        }

        .message {
            background-color: #fff;
        }

        .message:first-child {
            margin-top: auto;
        }

        .prompt-container {
            border: 3px solid #bbbbbb !important
        }

        @font-face {
            font-family: "CodePro";
            src: url("/CodePro/code_pro.otf") format("opentype");
        }

        html {
            height: 100%;
        }

        body {
            /* font-family: 'Tahoma', sans-serif !important;
            background-image: url('/back-top-img-large.jpg?7');
            background-position: center top;
            background-size: cover; */
            height: 100%;
            overflow-y: hidden;
            /* position: fixed; */
            /*           backdrop-filter: brightness(10%); */
        }

        .text-bottom {

            font-family: 'CodePro' !important;

        }

        #messages {
            min-height: 100%;
        }

        .grid-main-container {
            display: grid;
            height: 90%;
            grid-template-rows: auto 4em;
            row-gap: 3em;

        }

        input:focus::placeholder {
            color: transparent;
        }

        .container {
            width: 100%;
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            padding-right: calc(1.5rem * .5);
            padding-left: calc(1.5rem * .5);
            margin-right: auto;
            margin-left: auto;
        }

        .w-100 {
            width: 100% !important;
        }

        .mt-auto {
            margin-top: auto !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }


        .input-group:not(.has-validation)>.dropdown-toggle:nth-last-child(n+3),
        .input-group:not(.has-validation)>.form-floating:not(:last-child)>.form-control,
        .input-group:not(.has-validation)>.form-floating:not(:last-child)>.form-select,
        .input-group:not(.has-validation)>:not(:last-child):not(.dropdown-toggle):not(.dropdown-menu):not(.form-floating) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        textarea.form-control {
            min-height: calc(1.5em + 0.75rem + calc(1px * 2));
            color: white !important;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--bs-body-color);
            background-color: var(--bs-form-control-bg);
            background-clip: padding-box;
            border: 1px solid var(--bs-border-color);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.375rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .input-group>.form-control,
        .input-group>.form-floating,
        .input-group>.form-select {
            position: relative;
            flex: 1 1 auto;
            width: 1%;
            min-width: 0;
        }

        textarea {
            resize: vertical;
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }


        .input-group>:not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
            margin-left: calc(var(--bs-border-width) * -1);
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        button,
        select {
            text-transform: none;
        }

        [type=button],
        [type=reset],
        [type=submit],
        button {
            -webkit-appearance: button;
        }

        .btn {
            --bs-btn-padding-x: 0.75rem;
            --bs-btn-padding-y: 0.375rem;
            --bs-btn-font-family: ;
            --bs-btn-font-size: 1rem;
            --bs-btn-font-weight: 400;
            --bs-btn-line-height: 1.5;
            --bs-btn-color: #212529;
            --bs-btn-bg: transparent;
            --bs-btn-border-width: var(--bs-border-width);
            --bs-btn-border-color: transparent;
            --bs-btn-border-radius: 0.375rem;
            --bs-btn-hover-border-color: transparent;
            --bs-btn-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
            --bs-btn-disabled-opacity: 0.65;
            --bs-btn-focus-box-shadow: 0 0 0 0.25rem rgba(var(--bs-btn-focus-shadow-rgb), .5);
            display: inline-block;
            padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
            font-family: var(--bs-btn-font-family);
            font-size: var(--bs-btn-font-size);
            font-weight: var(--bs-btn-font-weight);
            line-height: var(--bs-btn-line-height);
            color: var(--bs-btn-color);
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            border: var(--bs-btn-border-width) solid var(--bs-btn-border-color);
            border-radius: var(--bs-btn-border-radius);
            background-color: var(--bs-btn-bg);
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .btn-success {
            --bs-btn-color: #fff;
            --bs-btn-bg: #198754;
            --bs-btn-border-color: #198754;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #157347;
            --bs-btn-hover-border-color: #146c43;
            --bs-btn-focus-shadow-rgb: 60, 153, 110;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #146c43;
            --bs-btn-active-border-color: #13653f;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #198754;
            --bs-btn-disabled-border-color: #198754;
        }

        [type=button]:not(:disabled),
        [type=reset]:not(:disabled),
        [type=submit]:not(:disabled),
        button:not(:disabled) {
            cursor: pointer;
        }

        .input-group .btn {
            position: relative;
            z-index: 2;
        }


        .justify-content-center {
            justify-content: center !important;
        }

        .flex-column {
            flex-direction: column !important;
        }

        .d-flex {
            display: flex !important;
        }

        .ms-auto {
            margin-left: auto !important;
        }

        .float-end {
            float: right !important;
        }

        .w-50 {
            width: 50% !important;
        }

        .float-start {
            float: left !important;
        }

        .btn-success {
            background-color: #00CC66;
            /* modern green color */
            color: #FFFFFF;
            /* white text */
            border-radius: 5px;
            /* rounded corners */
            padding: 10px 20px;
            /* button size */
            font-size: 16px;
            /* text size */
            font-weight: bold;
            /* bold text */
            border: none;
            /* remove border */
            cursor: pointer;
            /* change cursor on hover */
            background: linear-gradient(to right, #00d1b2, #63bf95) !important;
            height: 100%;
        }

        textarea {
            outline: none;
        }


        /* @media (max-height: 650px) {
              .grid-main-container {
row-gap: 2em;

          }
} */

        .soundcloud-embed {
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 50%;
        }

        .message textarea {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            white-space: pre;
        }

        #prompt {
            color: black !important;
        }
    </style>
    <script>
        const apiUrl = '/apichat';
        let _cid = '';
        function textAreaHeight(element) {
            element.style['max-width'] = '85%';
            element.style['white-space'] = 'pre';

            element.style.height = 0;
            element.style.height = (element.scrollHeight + 5) + "px";

            element.style.width = '40%';
            element.style.width = (element.scrollWidth + 15) + "px";

            element.style['white-space'] = 'break-spaces';

            element.style.height = 0;
            element.style.height = (element.scrollHeight + 5) + "px";

        }

        function sendMessage(e) {
            const messagesContainer = document.getElementById('messages');
            const container = messagesContainer.parentNode;
            const promptElement = document.getElementById('prompt');
            let prompt = promptElement.value;
            while (prompt.endsWith("\n")) {
                prompt = prompt.slice(0, prompt.length - 1);
            }
            if (prompt === '') return;
            promptElement.value = '';
            const id = `${Date.now()}`;
            messagesContainer.insertAdjacentHTML('beforeend', '<div class="message w-100" id="' + id + '"><div class="w-100 d-flex"><textarea class="form-control w-50 float-end ms-auto"  type="text" disabled>' + prompt + '</textarea></div><br></div>');
            const textArea = document.getElementById(id).getElementsByTagName('textarea')[0];
            if (textArea) {
                textAreaHeight(textArea);
            }
            container.scrollTo(0, container.scrollHeight);
            fetch(apiUrl + '?prompt=' + encodeURIComponent(prompt) + '&conversationId=' + encodeURIComponent(_cid)).then(r => r.json()).then(({answer, cid}) => {
            	_cid = cid;
                document.getElementById(id).insertAdjacentHTML('beforeend', '<div class="w-100 d-flex"><textarea class="w-50 float-start form-control form-rounded border-0 blue" type="text" disabled>' + answer.trim() + '</textarea></div><br>');
                const textArea = document.getElementById(id).getElementsByTagName('textarea')[1];
                if (textArea) {
                    textAreaHeight(textArea);
                }
                container.scrollTo(0, container.scrollHeight);

            }).catch(() => {
                document.getElementById(id).insertAdjacentHTML('beforeend', '<div class="w-100 d-flex"><input class="w-50 float-start form-control" value="ERROR" type="text" disabled/></div>');
                container.scrollTo(0, container.scrollHeight);
            })
        }
    </script>
</head>

<body>

    <?php

    include("Components/Navbar.php");

    ?>


    <div class="grid-main-container">
        <!-- <div class="soundcloud-embed">
        <iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1424423680&color=%23ff5500&auto_play=true&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe><div style="font-size: 10px; color: #cccccc;line-break: anywhere;word-break: normal;overflow: hidden;white-space: nowrap;text-overflow: ellipsis; font-family: Interstate,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Garuda,Verdana,Tahoma,sans-serif;font-weight: 100;"><a href="https://soundcloud.com/prestonzen" title="Жen" target="_blank" style="color: #cccccc; text-decoration: none;">Жen</a> · <a href="https://soundcloud.com/prestonzen/antiai-feat-lil-yoyo-molly" title="AntiAI feat. Lil Yoyo &amp; Molly Marie" target="_blank" style="color: #cccccc; text-decoration: none;">AntiAI feat. Lil Yoyo &amp; Molly Marie</a>
        </div>
        </div> -->

        <div class="container messages">
            <div class="justify-content-center d-flex flex-column" id="messages"></div>

        </div>
        <div class="w-100" style="align-self: end;">
            <div class="container mt-auto mb-1">

                <div class="input-group mb-2 prompt-container">
                    <textarea type="text" class="form-control" id="prompt" onkeyup="if (event.key == 'Enter' && !event.shiftKey){ sendMessage(event)}" placeholder="What could you possibly want to say?"></textarea>
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit" onclick="sendMessage(event)">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>