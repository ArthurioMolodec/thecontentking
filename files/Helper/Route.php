<?php

//will store all the parameters value in this array
$params = [];

class Route {

    private function simpleRoute($file, $route, $middleware = "", $method){

        //replacing first and last forward slashes
        //$_REQUEST['uri'] will be empty if req uri is /

        if(!empty($_REQUEST['uri'])){
            $route = preg_replace("/(^\/)|(\/$)/","",$route);
            $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
        }else{
            $reqUri = "/";
        }

        if($reqUri == $route){

            if($method !== null && $_SERVER['REQUEST_METHOD'] != $method){
                http_response_code(400);
                exit("Not a valid request !");
            }

            middleware($reqUri, is_callable($middleware) ? null : $middleware);
            if(is_callable($middleware)){
                call_user_func($middleware);
            }

            include($file);
            exit();

        }

    }

    private function validateRoute($route, $file, $middleware = "", $method){
        
         global $params;

         //will store all the parameters names in this array
         $paramKey = [];
 
         //finding if there is any {?} parameter in $route
         preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);
 
         //if the route does not contain any param call simpleRoute();
         if(empty($paramMatches[0])){
             $this->simpleRoute($file,$route,$middleware, $method);
             return;
         }
 
         //setting parameters names
         foreach($paramMatches[0] as $key){
             $paramKey[] = $key;
         }
 
         //replacing first and last slashes
         //$_REQUEST['uri'] will be empty if req uri is /
         if(!empty($_REQUEST['uri'])){
             $route = preg_replace("/(^\/)|(\/$)/","",$route);
             $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
         }else{
             $reqUri = "/";
         }
 
         //exploding route address
         $uri = explode("/", $route);
 
         //will store index number where {?} parameter is required in the $route 
         $indexNum = []; 
 
         //storing index number, where {?} parameter is required with the help of regex
         foreach($uri as $index => $param){
             if(preg_match("/{.*}/", $param)){
                 $indexNum[] = $index;
             }
         }
 
         //exploding request uri string to array to get
         //the exact index number value of parameter from $_REQUEST['uri']
         $reqUri = explode("/", $reqUri);
 
         //running for each loop to set the exact index number with reg expression
         //this will help in matching route
         foreach($indexNum as $key => $index){
         
              //in case if req uri with param index is empty then return
             //because url is not valid for this route
             //0 is not empty
             if(!isset($reqUri[$index])){
                 return;
             }
             
             //setting params with params names
             $params[$paramKey[$key]] = $reqUri[$index];
 
 
             //this is to create a regex for comparing route address
             $reqUri[$index] = "{.*}";
         }
 
         //converting array to sting
         $reqUri = implode("/",$reqUri);
 
         //replace all / with \/ for reg expression
         //regex to match route is ready !
         $reqUri = str_replace("/", '\\/', $reqUri);
 
         //now matching route with regex
         if(preg_match("/$reqUri/", $route))
         {
            if($method !== null && $_SERVER['REQUEST_METHOD'] != $method){
                http_response_code(400);
                exit("Not a valid request !");
            }

            middleware($reqUri, is_callable($middleware) ? null : $middleware);
            if(is_callable($middleware)){
                call_user_func($middleware);
            }
            
            include($file);
            exit();
 
         }
    }

    function get($route,$file, $middleware = ""){

        $this->validateRoute($route, $file, $middleware, "GET");
    }

    function post($route,$file, $middleware = ""){

        $this->validateRoute($route, $file, $middleware, "POST");
    }

    function getpost($route,$file, $middleware = "") {
        $this->validateRoute($route, $file, $middleware, null);
    }

    function notFound($file){
        middleware("404");
        include($file);
        exit();
    }
}
   

?>