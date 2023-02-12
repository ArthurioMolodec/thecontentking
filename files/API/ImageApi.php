<?php


class API
{
   
    public $key;

    public function __construct()
    {
        $this->key = imgApiKey;
    }

    private function callError($msg, $errCode = 500)
    {

        http_response_code($errCode);
        echo $msg;
        exit;
    }
    
    private function callApi($keyword)
    {

        $headers = [
            "Content-Type: application/json",
        ];


        $url = "https://pixabay.com/api/?key=$this->key&per_page=30&q=$keyword";

        $curl = curl_init();

        $curl_info = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $headers,
        ];

        curl_setopt_array($curl, $curl_info);
        $data = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        //if error occur !
        if (curl_errno($curl) || $status != 200) {
           
            $this->callError("Something went wrong, please try later.");
        }

       


        $data = json_decode($data, true);
        $data = $data["hits"];
        $images = [];

        foreach($data as $e){
           
            $images[] = [
                "smallImg" => $e["previewURL"],
                "normalImg" => $e["webformatURL"]
            ];
             
        }

        return $images;

    }

    public function getImages(){
        

        if( (!isset($_GET['keyword']) || empty($_GET['keyword']))){
            $this->callError("Keyword is empty or invalid.");
        }

         /*
        KEYWORDS & TONEOFVOICE IS OPTIONAL !
        */

        $keyword = $_GET['keyword'];
        $keyword = str_replace(" ", "+", $keyword);

        $images = $this->callApi($keyword);

        //if no curl err
        if($images !== false){
            return [
                "status" => "success",
                "msg" => "Successfully generated ai content",
                "images" => $images
            ];
        }
        else{

            $this->callError("Something went wrong, please try later.");
            http_response_code(500);
            exit();
        }

    }
} 

$obj = new API();

$resp = $obj->getImages();

if($resp['status'] === "success"){
    echo  json_encode($resp);
}
else{
    http_response_code(400);
    echo json_encode($resp);
}
