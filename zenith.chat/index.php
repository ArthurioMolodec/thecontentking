<?php

// echo var_dump( phpinfo() );
// return;
//start session on web page

//unset($_SESSION['access_token']);

include "../vendor/autoload.php";

session_start();

include "../files/Helper/Route.php";
include "../files/Helper/Variables.php";
include "../files/Helper/firebase.php";
include "../files/Helper/StripeApi.php";
const dbHelper = new DBHelper();
const stripeApi = new StripeApi(stripeKey, dbHelper, stripeWebHookSec, webUrl . '/webhook-stripe');
include "../files/Helper/middleware.php";
include "../files/Helper/hCaptcha.php";
include "../files/Helper/Support.php";

cors();

$route = new Route();

$route->get("/", "../files/Home.php", 'auth');

$route->get("/home", "../files/Home.php", 'auth');

$route->getpost("/login", "../files/Login.php");

$route->getpost("/registration", "../files/Registration.php");

$route->get("/content", "../files/Content.php", 'auth');

$route->get("/articlewriter", "../files/ArticleWriter.php", 'auth');

$route->get("/rewriter", "../files/Rewriter.php", 'auth');

$route->get("/improve", "../files/Improve.php", 'auth');

$route->get("/answers", "../files/Answers.php", 'auth');

$route->get("/logout", "../files/Logout.php", 'auth');

$route->get("/summarize", "../files/Summarize.php");

$route->get("/writer", "../files/Writer.php", 'auth');

$route->get("/biographywriter", "../files/BiographyWriter.php", 'auth');

$route->get("/image-generator", "../files/ImageGenerator.php", 'auth');

$route->get("/chat", "../files/Chat.php", 'auth');


$route->get("/premium", "../files/RedirectToBuyPremium.php", 'auth');

//api
$route->post("/contentapi", "../files/API/ContentApi.php", 'auth-api');

$route->post("/articleapi", "../files/API/ArticleApi.php", 'auth-api');

$route->post("/rewriterapi", "../files/API/RewriterApi.php", 'auth-api');

$route->post("/improveapi", "../files/API/ImproveApi.php", 'auth-api');

$route->post("/answersapi", "../files/API/AnswersApi.php", 'auth-api');

$route->post("/playgroundapi", "../files/API/PlaygroundApi.php", 'auth-api');

$route->post("/summarizeapi", "../files/API/SummarizeApi.php", 'auth-api');

$route->post("/writerapi", "../files/API/WriterApi.php", 'auth-api');

$route->get("/imageapi", "../files/API/ImageApi.php", 'auth-api');

$route->get("/imagegeneratorapi", "../files/API/ImageGeneratorApi.php", 'auth-api');

$route->post("/biographyapi", "../files/API/BiographyApi.php", 'auth-api');

$route->post("/webhook-stripe", "../files/API/StripeHook.php");

$route->post("/authapi", "../files/API/AuthApi.php");

$route->getpost("/apichat", "../files/API/ChatApi.php");

$route->notFound("404.php");
