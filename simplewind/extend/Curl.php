<?php

class Curl{
    /**
     * POST 请求
     * @param string $url
     * @param array $post_data
     * @param boolean $type 参数是否http_build_query编译
     * @param array $headers 请求的文件头
     * @param boolean $jsonreturn 是否返回json
     * @return array content
     */
    static public function Post($url,$post_data,$type=false,$headers=array(),$jsonreturn=true){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLINFO_HEADER_OUT, false);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if(!empty($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //传参
        if(!$type){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
        }else{
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }
        //执行命令
        $data = curl_exec($curl);
        //获得返回的数据
        if($jsonreturn) $data = json_decode($data,true);

        //关闭URL请求
        curl_close($curl);
        return $data;
    }

    
    /**
     * @param $url
     * @return bool|mixed
     */
    static public function Get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
}
