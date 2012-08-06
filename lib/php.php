<?php
header('Content-type: text/html;charset=utf-8'); 
require_once('const.php');
function redirect($url) {
    header('Location: '.$url);
    die();
}

function bad_request() {
    header('HTTP/1.1 400 Bad Request');
    die();
}

function forbidden() {
    header('HTTP/1.1 403 Forbidden');
    die();
}

function not_found() {
    header('HTTP/1.1 404 Not Found');
    die();
}

function error() {
    header('HTTP/1.1 500 Internal Error');
    die();
}
function returnError(){
    return json_encode(array('status'=>'error'));
}
function setDrawRate($rate){
    $ret = file_put_contents(dirname(__FILE__).'/drawrate.txt',$rate);
    // echo $ret;die();
    if($ret){
        return 'ok';
    }
}
function getDrawRate(){
    return file_get_contents(dirname(__FILE__).'/drawrate.txt');
}
function toDraw($user,$drawCount){
    $rate = getDrawRate();
    // echo rand();die();
    srand((double)microtime()*1000000);
    if(rand(0,30000)/30000<(float)$rate){
        $localtime = localtime();
        if($user->recordPrize("20元手机充值卡",4,$localtime[4]+1)&&$user->updateDrawCount($drawCount)){
            return 'win';
        }else{
            return  'error_save';
    }

    }else{
        if($user->updateDrawCount($drawCount)){
            return 'no_prize';
            
        }else{
            return 'error_draw';
        }
        
    }
}
function getToken(){
    return sprintf( '%04x%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff )

        // // 16 bits for "time_mid"
        // mt_rand( 0, 0xffff ),

        // // 16 bits for "time_hi_and_version",
        // // four most significant bits holds version number 4
        // mt_rand( 0, 0x0fff ) | 0x4000,

        // // 16 bits, 8 bits for "clk_seq_hi_res",
        // // 8 bits for "clk_seq_low",
        // // two most significant bits holds zero and one for variant DCE1.1
        // mt_rand( 0, 0x3fff ) | 0x8000,

        // // 48 bits for "node"
        // mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
function url_encode($string){
    return urlencode(utf8_encode($string));
}
    
function url_decode($string){
    return utf8_decode(urldecode($string));
}
function set_cookie($name, $value) {
    if (empty($value)) {
        setcookie($name, $value, 0);
    } else {
        setcookie($name, $value, time() + WEEK * 2);
    }
}

?>
