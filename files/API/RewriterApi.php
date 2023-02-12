<?php


class API
{
   
    public $token;

    private $models = [
        "curies" => "text-curie-001",
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
        if($num >= 1 && $num <= 5){
            return $this->models['davinci'];
        }
        //50%
        elseif($num >= 6 && $num <= 10){
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
    
    private function choosePrompt(){
        
        $array = ["#Make this paragraph plagiarism free :","#Paraphrase this :", "#Write it in your own words : ", "#Write it again in your own words and make it unique :"];
        
        $key = array_rand($array);
        $value = $array[$key];
        
        return $value;
    }

    public function getAiContent(){
        

        if( (!isset($_POST['para']) || empty($_POST['para']))){
            return [
                "status" => "failed",
                "msg" => "Para is empty !"
            ];
        }

         /*
        KEYWORDS & TONEOFVOICE IS OPTIONAL !
        */

        $para = $_POST['para'];
        $promptLines = [];
        $prompt = "";
        $maxTokens = 800;
        $temperature = 0.7;
        $model = $this->chooseModel();

        //............................................
       
        // $promptLines[] = "#Paraphrase this :";
        $promptLines[] = $this->choosePrompt();
        $promptLines[] = "\n\n$para";
        $promptLines[] = "\n\n";


        foreach($promptLines as $e){
            $prompt .= $e;
        }

        
        $content = $this->callOpenAi($prompt, $maxTokens, $temperature, $model);

        $contentFilter = contentFilter($content,$this->token);

        if($contentFilter === "2"){
            $this->callError("Our content filter detected that the generated text contain sensitive content. We cannot show you the text, please try again with different input.", 405);
        }

        //if no curl err
        if($content !== false){
            return [
                "status" => "success",
                "msg" => "Successfully generated ai content",
                "content" => $content
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
