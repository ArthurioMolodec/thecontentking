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
        "writemore", "expand", "rewrite", "summarize", "translate","section"

    ];


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
        //30%
        elseif ($num >= 3 && $num <= 5) {
            return $this->models['curies'];
        }
        //50%
        elseif ($num >= 6 && $num <= 10) {
            return $this->models['davinci2'];
        }
    }


    private function chooseGoodModel()
    {

        $num = mt_rand(1, 10);

        //20%
        if ($num >= 1 && $num <= 3) {
            return $this->models['davinci'];
        }
        //50%
        elseif ($num >= 4 && $num <= 6) {
            return $this->models['curies'];
        }
        //30%
        elseif ($num >= 7 && $num <= 10) {
            return $this->models['davinci2'];
        }
    }

    private function callOpenAi($prompt, $maxTokens, $temperature, $model, $fp, $pp)
    {


        $fields = [
            'prompt' => $prompt,
            "temperature" => $temperature,
            "max_tokens" => $maxTokens,
            "top_p" => 1,
            "frequency_penalty" => $fp,
            "presence_penalty" => $pp,
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

    private function chooseRewriterPrompt(){
        
        $array = ["#Make this paragraph plagiarism free :","#Paraphrase this :", "#Write it in your own words : ", "#Write it again in your own words and make it unique :"];
        
        $key = array_rand($array);
        $value = $array[$key];
        
        return $value;
    }

    private function callError($msg, $errCode = 500)
    {

        http_response_code($errCode);
        echo $msg;
        exit;
    }

    public function getAiContent()
    {


        if ((!isset($_POST['description']) || empty($_POST['description']))) {
            return [
                "status" => "failed",
                "msg" => "Description is empty !"
            ];
        }

        if (!isset($_POST['language'])) {
            return [
                "status" => "failed",
                "msg" => "Language is not valid !"
            ];
        }

        if (!isset($_POST['type']) || empty($_POST['type']) || !in_array($_POST['type'], $this->types)) {
            return [
                "status" => "failed",
                "msg" => "Invalid Type !",
            ];
        }

        // if (isset($_POST['description']) && strlen($_POST['description']) > 200) {
        //     $this->callError("Topic text should not be more than 200 characters");
        // }

        /*
        KEYWORDS & TONEOFVOICE IS OPTIONAL !
        */

        $description = $_POST['description'];
        $language = $_POST['language'];
        $type = $_POST['type'];

        //=========== making prompt (MOST IMP) =============

        // $description = preg_replace("/[^a-zA-Z 0-9]/", '', $description);
            

        $promptLines = [];
        $prompt = "";
        $maxTokens = 250;
        $temperature = 0.7;
        $fp = 0;
        $pp = 0;
        $model = $this->models['davinci2'];

        if ($type == "writemore") {

            $tempDesc  = preg_replace("/\s+/m", "", $description);
            $toArray = str_split($tempDesc);

            if($toArray[count($toArray) - 1] === "."){

                $promptLines[] = $description;
                 $promptLines[] = "\n\n\"\"\"\nWrite more related text :\n\"\"\"";
                //  $promptLines[] = "\n\n\"\"\"\nWrite more , make this text long :\n\"\"\"";
                //$promptLines[] = "\n\n\"\"\"\nRead this and make it long :\n\"\"\"";
                $temperature = 0.3;
                $fp = 1.8;
                $pp = 1.4;
            }
            else{
                //$promptLines[] = "#Write in details :\n\n";
                $promptLines[] = "$description";
            }

            $maxTokens = 400;
        } 
        elseif ($type == "rewrite") {

            $promptLines[] = $this->chooseRewriterPrompt();
            $promptLines[] = "\n\n$description";
            $promptLines[] = "\n\n";
            $temperature = 0.7;
        }
        elseif($type == "expand"){
            $promptLines[] = $description;
            $promptLines[] = "\n\n\"\"\"\nWrite a detailed blog section :\n\"\"\"";
            $temperature = 0.2;
            $maxTokens = 400;
        }
        elseif($type == "section"){
            $promptLines[] = "\"\"\"\nWrite a detailed blog section on '$description' :\n\"\"\"\n";
            $temperature = 0.5;
            $maxTokens = 450;
        }
        elseif($type == "translate"){
            $promptLines[] = "#Translate this in '$language' language : ";
            $promptLines[] = $description;
            $temperature = 0.7;
            $maxTokens = 300;
        }
        elseif($type == "summarize"){
            $promptLines[] = "$description\n\n";
           
            $promptLines[] = "\n\nTl;dr\n\n";
        }

        foreach ($promptLines as $e) {
            $prompt .= $e;
        }

    //     $arr = ["any" => $description];
    //     echo json_encode($arr);
    //    // var_dump($prompt);
    //     exit();

        $content = $this->callOpenAi($prompt, $maxTokens, $temperature, $model, $fp, $pp);

        $contentFilter = contentFilter($content,$this->token);

        if($contentFilter === "2"){
            $this->callError("Our content filter detected that the generated text contain sensitive content. We cannot show you the text, please try again with different input.", 405);
        }

        //if no curl err
        if ($content !== false) {

            return [
                "status" => "success",
                "msg" => "Successfully generated ai content",
                "content" => $content
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

