<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="assets/writer.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
  <title>TheContentKing Writer</title>
</head>

<body>

  <nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?php echo webUrl; ?>">
        <!-- <img src="/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="30" height="24" class="d-inline-block align-text-top"> -->
        TheContentKing Writer
      </a>
    </div>
  </nav>


  <section>

    <?php

    include("Components/Modal.php");
    include("Components/ArticleModal.php");
    include("Components/WriteMoreModal.php");
    include("Components/TranslateModal.php");

    ?>

    <div id="snackbar"></div>

    <div class="container-fluid">

      <div class="row">

        <div class="col-9 middle-side">

          <div class="editor-div">

            <div id="editor" placeholder="Type the content here!"></div>
          </div>

          <div class="bottom-writer d-flex">
            <!-- <p class="text-center">BOTTOM-WRITER</p> -->
            <button type="button" id="writeMoreBtn" class="btn app-btn mt-1 b-btn">Write</button>

            <button type="button" id="sectionBtn" class="btn app-btn mt-1 b-btn ms-2">Section</button>

            <button type="button" id="expandBtn" class="btn app-btn mt-1 b-btn ms-2">Expand</button>

            <button type="button" id="rewriteBtn" class="btn app-btn mt-1 b-btn ms-2">Rewrite</button>

            <button type="button" id="summarizeBtn" class="btn app-btn mt-1 b-btn ms-2">Summarize</button>

            <button type="button" id="translateBtn" class="btn app-btn mt-1 b-btn ms-2">Translate</button>

            <button type="button" id="articleBtn" class="btn app-btn mt-1 b-btn ms-2">Article</button>

          </div>

        </div>

        <div class="col-3 right-side my-2">

          <div class="tabs">

            <div onclick="changeTab(this,'content-container')" class="tab active-tab">
              <i class="ri-pen-nib-fill tab-icon"></i>
              <div class="tab-text">Content</div>
            </div>

            <div class="vl"></div>

            <div onclick="changeTab(this,'image-container')" class="tab">
              <i class="ri-image-fill tab-icon"></i>
              <div class="tab-text">Image</div>
            </div>


          </div>

          <!-- <p class="text-center">COL - 3</p> -->

          <div class="tabs-container">

            <div class="content-container">
              <?php include("Components/ContentContainer.php") ?>
            </div>

            <div class="image-container">
              <?php include("Components/ImageContainer.php") ?>
            </div>
          </div>



        </div>


      </div>

    </div>


  </section>


</body>

<script src="assets/writer.js"></script>
<script src="assets/content.js"></script>
<script src="assets/articlewriter.js"></script>
<script src="assets/image.js"></script>

<script>


  var editor = new Quill('#editor', {
    theme: 'snow',
    placeholder: 'Start Typing...',
    modules: {
      toolbar: [
        [{
          header: [1, 2, 3,false]
        }],
        ['bold', 'italic', 'underline', 'link'],
        [{ 'align': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['image', 'blockquote'],
        [{ 'color': [] }]
      ],
      imageResize: {
        parchment: Quill.import('parchment'),
        modules: ['Resize', 'DisplaySize']
      }
    },
  });

  Quill.register('modules/imageResize', ImageResize);

  init();

  const myModal = new bootstrap.Modal(document.getElementById('myModal'));

  const articleModal = new bootstrap.Modal(document.getElementById('articleModal'));

  const writeMoreModal = new bootstrap.Modal(document.getElementById('writeMoreModal'));

  const translateModal = new bootstrap.Modal(document.getElementById('translateModal'));

</script>








</html>