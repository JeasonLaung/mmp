<?php
/**
 * 公共函数
 */
function sendSms($text,$tel){
    $apikey = config("YUNPIAN.SMS_KEY");
    // 发送短信
    $text=urldecode($text);
    $data = array( 'text'=>$text , 'apikey' => $apikey , 'mobile' => $tel);
    $return = \Curl::Post('https://sms.yunpian.com/v2/sms/single_send.json',$data);
    return $return;
}