<?php 
function getUserId()
{
  if ($sign = request()->header(SIGN_NAME)) {
    try{
        $decoded = \Firebase\JWT\JWT::decode($sign, config('PUBLIC_KEY'), array('RS256'));
    }catch(\UnexpectedValueException $e){
        // 捕捉异常，直接抛出报错
        json(['code'=>0,'msg'=>$e->getMessage(),'errcode'=>500])->send();
        exit();
    }
    request()->uid = $decoded->uid;
  }else {
      json(['code'=>0,'msg'=>'无权限访问','errcode'=>500])->send();
      exit();
  }
}
    function er