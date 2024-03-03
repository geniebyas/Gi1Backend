<?php
use Illuminate\Support\Str;

if(!function_exists('p')){
    function p($data){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}


    function generateReferCode(){
        return strtolower(Str::random(6));
    }


?>