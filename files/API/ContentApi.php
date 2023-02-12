<?php

class API
{

    public $token;


    private $models = [
        "babbage" => "text-babbage-001",
        "curies" => "text-curie-001",
        "ada" => "text-ada-001",
        "davinci" => "text-davinci-001",
        "davinci2" => "text-davinci-002",
    ];

    private $types = [
        "intro", "main content", "conclusion", "completion",
        "long form", "answer", "blog outline", 'paraphrase', 'blog ideas', 'email',
        'ad', 'preview', 'video ideas', 'video desc', 'video channel desc',
        'seometadesc', 'seometatitle', 'bio', 'notification',
        'product desc', 'job desc', 'interview questions', 'summarize',
        'reply message', 'reply review', 'business idea pitch', 'call to action',
        'aida', 'pas', 'google ad', 'nlpterms', 'bdesc',"para"

    ];

    private function callError($msg, $errCode = 500)
    {

        http_response_code($errCode);
        echo $msg;
        exit;
    }

    public function __construct()
    {
        $this->token = token;
    }

    private function chooseModel()
    {

        $num = mt_rand(1, 10);

        //20%
        if ($num >= 1 && $num <= 2) {
            return $this->models['babbage'];
        }
        //50%
        elseif ($num >= 3 && $num <= 7) {
            return $this->models['curies'];
        }
        //30%
        elseif ($num >= 8 && $num <= 10) {
            return $this->models['davinci'];
        }
    }


    private function chooseGoodModel()
    {

        $num = mt_rand(1, 10);

        //30%
        if ($num >= 1 && $num <= 3) {
            return $this->models['davinci'];
        }
        //30%
        elseif ($num >= 4 && $num <= 6) {
            return $this->models['curies'];
        }
        //40%
        elseif ($num >= 7 && $num <= 10) {
            return $this->models['davinci2'];
        }
    }

    private function callOpenAi($prompt, $maxTokens, $temperature, $model)
    {

        $fields = [
            'prompt' => $prompt,
            "temperature" => $temperature,
            "max_tokens" => $maxTokens,
            "top_p" => 1,
            "frequency_penalty" => 0,
            "presence_penalty" => 0,
            "user" => "1",
        ];

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer $this->token"
        ];

        $url = "https://api.openai.com/v1/engines/$model/completions";

        $curl = curl_init();

        $curl_info = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => $headers,
        ];

        curl_setopt_array($curl, $curl_info);
        $data = curl_exec($curl);

        //if error occur !
        if (curl_errno($curl)) {
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        $data = json_decode($data, true);

        //if invalid api key !
        if (isset($data['error'])) {
            http_response_code(500);
            echo $data["error"]['message'];
            exit;
            return false;
        }

        return $data['choices'][0]['text'];
    }

    public function getAiContent()
    {


        if ((!isset($_POST['description']) || empty($_POST['description']))) {
            return [
                "status" => "failed",
                "msg" => "Description is empty !"
            ];
        }

        if ((!isset($_POST['variants']) || empty($_POST['variants']))) {
            return [
                "status" => "failed",
                "msg" => "Variants is empty !"
            ];
        }

        if (!isset($_POST['type']) || empty($_POST['type']) || !in_array($_POST['type'], $this->types)) {
            return [
                "status" => "failed",
                "msg" => "Invalid Type !",
            ];
        }

        /*
        KEYWORDS & TONEOFVOICE IS OPTIONAL !
        */

        $keywords = !isset($_POST['keywords']) ? "" : $_POST['keywords'];
        $toneOfVoice = !isset($_POST['toneOfVoice']) ? "" : $_POST['toneOfVoice'];
        $language = !isset($_POST['language']) ? "" : $_POST['language'];
        $description = $_POST['description'];
        $variants = $_POST['variants'];
        $type = $_POST['type'];
        $contentArray = [];

        //=========== making prompt (MOST IMP) =============

        foreach (range(1, $variants) as $var) {

            $description = preg_replace("/[^a-zA-Z 0-9]/", '', $description);

            $promptLines = [];
            $prompt = "";
            $maxTokens = 250;
            $temperature = 0.7;
            $model = $this->chooseModel();

            if ($type === "long form") {

                // $promptLines[] = "\"\"\"Write an article.";
                $promptLines[] = "\"\"\"Write a detailed article on '$description'";
                $promptLines[] = !empty($language) ? " in $language language :" : "";
                $promptLines[] = !empty($keywords) ? "\nKeywords : $keywords" : "";
                $promptLines[] = !empty($toneOfVoice) ? "\nTone Of Voice : $toneOfVoice" : "";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 750;
                $temperature = 0.8;

                $model = $this->models['davinci'];
            } elseif ($type == "answer") {

                $promptLines[] = "\"\"\"Give detailed answer";
                $promptLines[] = !empty($language) ? " in $language language:" : " :";
                $promptLines[] = "Question : $description";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 100;
            } elseif ($type == "paraphrase") {

                $promptLines[] = "#Paraphrase it : ";
                $promptLines[] = "\n$description";
                $promptLines[] = "\n\n";
                $maxTokens = 600;
                $temperature = 0.7;
            } 
            elseif ($type == "para") {

                $promptLines[] = "\"\"\"Write a detailed blog section on '$description' ";
                $promptLines[] = !empty($language) ? " in '$language' language " : "";
                $promptLines[] = !empty($toneOfVoice) ? "and in a $toneOfVoice tone " : "";
                $promptLines[] = ": ";
                $promptLines[] = !empty($keywords) ? "\nRelated To : $keywords" : "";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 450;
                $model = $this->models['davinci2'];

            }
            elseif ($type == "completion") {

                $promptLines[] = "\"\"\"Make it long";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = !empty($keywords) ? "\nKeywords : $keywords" : "";
                $promptLines[] = !empty($toneOfVoice) ? "\nTone Of Voice : $toneOfVoice" : "";
                $promptLines[] = "\n\"\"\"";
                $promptLines[] = "\n$description\n\n";
            } elseif ($type == "blog ideas") {

                $promptLines[] = "\"\"\"\nKeywords : '$description'";
                $promptLines[] = "\nGive some article ideas";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\n\"\"\"";
            } elseif ($type == "email") {

                $promptLines[] = "\"\"\"\nWrite an Email";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nDescription : $description ";
                $promptLines[] = "\n\"\"\"\n";
            } elseif ($type == "ad") {

                $promptLines[] = "\"\"\"\nWrite an Ad";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nDescription: $description";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 350;
                $model = $this->models['davinci'];
            } elseif ($type == "preview") {

                $promptLines[] = "#Write a detailed product review on '$description'";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\n\n";
                $maxTokens = 500;
                $model = $this->models['davinci'];
            }  
            elseif ($type == "blog outline") {

                $promptLines[] = "Generate headings outline for an article about '$description' :";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\n"; 

                $maxTokens = 110;
                $temperature = 0.7;
                $model = $this->models['davinci2'];

            } elseif ($type == "conclusion") {

                $promptLines[] = "\"\"\"Write a conclusion paragraph related to '$description'";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = !empty($keywords) ? "\nKeywords : $keywords" : "";
                $promptLines[] = !empty($toneOfVoice) ? "\nTone Of Voice : $toneOfVoice" : "";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "intro") {

                $promptLines[] = "\"\"\"Write an intro on '$description'";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = !empty($keywords) ? "\nKeywords : $keywords" : "";
                $promptLines[] = !empty($toneOfVoice) ? "\nTone Of Voice : $toneOfVoice" : "";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "video ideas") {

                $promptLines[] = "\"\"\"Generate  Video ideas";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nKeywords : $description";
                $promptLines[] = "\n\"\"\"\n";
            } elseif ($type == "video desc") {

                $promptLines[] = "\"\"\"Write a long Video Description";
                $promptLines[] = !empty($language) ? " in $language language:" : " :";
                $promptLines[] = "\nAbout Video: $description";
                $promptLines[] = "\n\"\"\"\n";
            } elseif ($type == "video channel desc") {

                $promptLines[] = "\"\"\"Generate a long Youtube Channel Description";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nAbout Youtube Channel: $description";
                $promptLines[] = "\n\"\"\"\n";
            } elseif ($type == "seometadesc") {
                $promptLines[] = "\"\"\"\nWrite an SEO Meta Description";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nTopic : $description";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 200;
                $temperature = 0.6;
                $model = $this->models['babbage'];
            } elseif ($type == "seometatitle") {
                $promptLines[] = "\"\"\"\nWrite SEO Meta Title";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nKeywords : $description";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 100;
            } elseif ($type == "notification") {
                $promptLines[] = "\"\"\"\nWrite a notification";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nAbout: $description";
                $promptLines[] = "\n\"\"\"\n";
            } elseif ($type == "bio") {
                $promptLines[] = "\"\"\"\nWrite a bio";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nAbout :  $description";
                $promptLines[] = "\n\"\"\"\n";
            } elseif ($type == "product desc") {
                $promptLines[] = "\"\"\"\nWrite an affiliate article";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nTopic : $description";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 600;
                $model = $this->models['davinci'];
            } elseif ($type == "job desc") {

                $promptLines[] = "\"\"\"\nWrite a long job description";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nAbout the job: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "bdesc") {

                $promptLines[] = "\"\"\"\nWrite a business description";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nBusiness Details: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "interview questions") {

                $promptLines[] = "\"\"\"\nGenerate some interviews questions";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nAbout: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "reply message") {

                $promptLines[] = "\"\"\"\nWrite a reply to this message";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nMessage: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "reply review") {

                $promptLines[] = "\"\"\"\nWrite a reply to this review";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nReview: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "business idea pitch") {

                $promptLines[] = "\"\"\"\nWrite a business idea pitch";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nDescription: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "call to action") {

                $promptLines[] = "\"\"\"\nWrite a call to action";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\nDescription: $description";
                $promptLines[] = "\n\"\"\"\n";
                $model = $this->chooseGoodModel();
            } elseif ($type == "aida") {

                $promptLines[] = "-Description: Gingeretic is an amazing tool for web scraping. It has built-in proxy.\n-Attention: Gingeretic will make your work so much faster and more efficient.\n-Interest: Gingeretic is a powerful scraping tool for anything you can imagine — from social media to web pages, it can download any content you need.\n-Desire: The latest version of Gingeretic has been rebuilt from scratch, with new features that'll save you time and make your life easier.\n-Action: Start using Gingeretic now to make your work easier.";

                $promptLines[] = "\n\n-Description: $description\n";
                $model = $this->models['davinci'];
            } elseif ($type == "pas") {

                $promptLines[] = "-Description: Glato web browser is a powerful browser for phones and pc. It blocks all ads and protects your privacy. Its lightweight, fast and secure.\n-Problem: Ads and popups are a nuisance, and online tracking is an invasion of privacy. It's hard to find a browser that blocks advertising, but that doesn't slow down your computer or drain your battery.\n-Agitate: Glato stands out as the lightweight browser with the best privacy protections, whether you're on your phone or on your laptop. With fast load times, it's perfect for gamers who want to avoid any lag from ads.\n-Solve: Glato web browser has been built with you in mind. Glato does all of the work for you and takes care of ads and popups - so you're free to enjoy browsing the internet and all its articles, videos, pictures, and games";
                $promptLines[] = "\n\n-Description: $description\n";
                $model = $model = $this->models['davinci'];
            } elseif ($type == "google ad") {

                $promptLines[] = "-Description: best glutathione tablets\n-Title: Glutathione pills\n-Ad Copy: Put an end to your chronic health problems by upgrading your diet with gluten's organic glutathione pills.\n-CTA: Buy now";
                $promptLines[] = "\n\n-Description: $description\n";
                $model = $model = $this->models['davinci2'];
            } elseif ($type == "summarize") {

                $promptLines[] = "$description";
                $promptLines[] = "\n\nTl;dr\n\n";
                $maxTokens = 100;
                $model = $this->models['davinci2'];
            } elseif ($type == "nlpterms") {

                $promptLines[] = "#Generate ten NLP terms for '$description'";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = "\n\n";
                $maxTokens = 100;
                $model = $this->chooseGoodModel();
            } else {

                $promptLines[] = "\"\"\"Write blog section on '$description'";
                $promptLines[] = !empty($language) ? " in $language language :" : " :";
                $promptLines[] = !empty($keywords) ? "\nKeywords : $keywords" : "";
                $promptLines[] = !empty($toneOfVoice) ? "\nTone Of Voice : $toneOfVoice" : "";
                $promptLines[] = "\n\"\"\"\n";
                $maxTokens = 650;
                $temperature = 0.7;
                $model = $this->chooseGoodModel();
            }

            foreach ($promptLines as $e) {
                $prompt .= $e;
            }

            $content = $this->callOpenAi($prompt, $maxTokens, $temperature, $model);

            if ($type === "completion") {
                $content = $description . " " . $content;
            }

            $contentArray[] = $content;
        }


         //for filter
        $cForFilter = implode(".",$contentArray);

        $contentFilter = contentFilter($cForFilter,$this->token);

        if($contentFilter === "2"){
            $this->callError("Our content filter detected that the generated text contain sensitive content. We cannot show you the text, please try again with different input.", 405);
        }

        //if no curl err
        if ($content !== false) {
            return [
                "status" => "success",
                "msg" => "Successfully generated ai content",
                "content" => $contentArray
            ];
        } else {

            http_response_code(500);
            exit();
        }
    }
}


$_POST = json_decode(file_get_contents('php://input'), true);

$obj = new API();

$resp = $obj->getAiContent();

if ($resp['status'] === "success") {
    echo  json_encode($resp);
} else {
    http_response_code(400);
    echo json_encode($resp);
}
