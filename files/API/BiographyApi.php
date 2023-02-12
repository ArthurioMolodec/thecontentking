<?php

class API
{
   
    public $token;

    private $models = [
        // "davinci" => "text-davinci-001",
        // "babbage" => "text-babbage-001",
        // "curies" => "text-curie-001",
        "davinci" => "text-davinci-002"
     ];

    public function __construct()
    {
        $this->token = token;
        
    }

    private function callError($msg, $errCode = 500)
    {

        http_response_code($errCode);
        echo $msg;
        exit;
    }

    private function chooseModel(){

        $num = mt_rand(1, 10);

        //20%
        if($num >= 1 && $num <= 2){
            return $this->models['babbage'];
        }
        //50%
        elseif($num >= 3 && $num <= 7){
            return $this->models['curies'];
        }
        //30%
        elseif($num >= 8 && $num <= 10){
            return $this->models['davinci'];
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
            "presence_penalty" => 1,
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
        if(isset($data['error'])){
            return false;
        }

        return $data['choices'][0]['text'];

    }

    public function getAiContent(){
        

        if( (!isset($_POST['topic']) || empty($_POST['topic']))){
            return [
                "status" => "failed",
                "msg" => "Topic is empty !"
            ];
        }

         /*
        KEYWORDS & TONEOFVOICE IS OPTIONAL !
        */

        $topic = $_POST['topic'];
        $promptLines = [];
        $prompt = "";
        $maxTokens = 350;
        $temperature = 0.7;
        $model = $this->models['davinci'];
        $compltContent = "";
        $outline = [];

        //.................................................
        //outlines
        $promptLines = [];
        $prompt ="";

       $promptLines[] = "#Write a detailed long biography with at least 8 headings on '$topic' :\n#Write detailed long sections for each headings :" ;
       $promptLines[] = "\n\n";

        foreach($promptLines as $e){
            $prompt .= $e;
        }

        //gen outline
        $maxTokens = 1250;
        $temperature = 0.3;
        $content = $this->callOpenAi($prompt, $maxTokens, $temperature, $model);

        $contentFilter = contentFilter($content,$this->token);

        if($contentFilter === "2"){
            $this->callError("Our content filter detected that the generated text contain sensitive content. We cannot show you the text, please try again with different input.", 405);
        }

        $compltContent = $content;

        //if no curl err
        if($content !== false){
            return [
                "status" => "success",
                "msg" => "Successfully generated ai content",
                "content" => $compltContent
            ];
        }
        else{

            http_response_code(500);
            exit();
        }

    }
}

$_POST = json_decode(file_get_contents('php://input'), true);   

$obj = new API();

$resp = $obj->getAiContent();

if($resp['status'] === "success"){
    echo  json_encode($resp);
}
else{
    http_response_code(400);
    echo json_encode($resp);
}

