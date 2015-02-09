<?php

	//打印
	function pr($arr){
		echo '<pre>';
		var_dump($arr);
	}

	function logined(){
		return true;
	}

	function AJXERR_Make($code,$info=NULL){
		$error=C($code);
		if ($info) $error[info]=$info;
		return $error;
	}

	function AJXERR_400($info=NULL){
		return AJXERR_Make(__FUNCTION__,$info);
	}

	function AJXERR_401($info=NULL){
		return AJXERR_Make(__FUNCTION__,$info);
	}

	function AJXERR_402($info=NULL){
		return AJXERR_Make(__FUNCTION__,$info);
	}

	function AJXERR_403($info=NULL){
		return AJXERR_Make(__FUNCTION__,$info);
	}

	function AJXERR_409($info=NULL){
		return AJXERR_Make(__FUNCTION__,$info);
	}

	function AJXERR_500($info=NULL){
		return AJXERR_Make(__FUNCTION__,$info);
	}

	function isSucc($res){
		if ($res===false) {
			return false;
		}
		return true;
	}

	function isIOS(){
		return strtolower(I('SYSPlat'))=='ios';
	}

	function isFail($res){
		if ($res===false) {
			return true;
		}
		return false;
	}

	function isYES($v){
		return $v==='Y';
	}

	function getSmallImage($path){
		list($a,$b)=explode('.', $path);
		return $a.'_index.'.$b;
	}

	function url_exists($url){
		if (!function_exists('curl_init')) {
			return false;
		}
      	$ch = curl_init();  
      	curl_setopt($ch, CURLOPT_URL,$url);  
      	curl_setopt($ch, CURLOPT_NOBODY, 1); // 不下载  
      	curl_setopt($ch, CURLOPT_FAILONERROR, 1);  
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
      	return (curl_exec($ch)!==false) ? true : false;  
    }

    function logSqlTrace($db){
    	if($db&&switchLogOn()){
    		global $sqlLogs;
			$sqlLogs[]=$db->getLastSql(); 
    	}
    }

    function switchLogOn(){
    	return (APP_DEBUG&&(isset($_GET['log'])||isset($_POST['log'])));
    }

    function defVaule($obj,$min,$max,$default)
    {
    	if (!$obj) {
    		$obj=$default;
    	}else if ($max!==false&&$obj>$max) {
			$obj=$max;
    	}else if($min!==false&&$obj<$min){
			$obj=$min;
    	}
    	return $obj;
    }

    function defIntVaule($obj,$min,$max,$default){
    	return intval(defVaule($obj,$min,$max,$default));
    }

    function defFloatVaule($obj,$min,$max,$default){
    	return floatval(defVaule($obj,$min,$max,$default));
    }
	
	// function defDoubleVaule($obj,$min,$max,$default){
 //    	return doubleval(defVaule($obj,$min,$max,$default));
 //    }

	function str_prefix($str,$fix){
		return $fix === "" || strpos($str,$fix)===0;
	}
	
	function str_subfix($str,$fix){
		return $fix === "" || substr($str, -strlen($fix)) === $fix;
	}

	function fun_isPublic($class,$fun){
		if (class_exists($class)&&method_exists($class,$fun)) {
			$reflection = new ReflectionMethod($class, $fun);
	        return $reflection->isPublic();
		}
        return false;
    }

    function zthttp_request($url,$post,$data=NULL,$timeOut=15,$verifySSL=true,&$err=NULL){
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//忽略证书错误信息
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
		curl_setopt($ch, CURLOPT_POST, $post);
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeOut);

		// curl_setopt($ch, CURLOPT_SSLVERSION,3);//强制使用版本3
		if ($post&&$data) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if ($err) {
			$err=curl_error($ch);
		}
		curl_close($ch);
		return $result;
    }
    
    function zthttp_post($url,$data,$timeOut=15,$verifySSL=true,&$err=NULL){
		return zthttp_request($url, true, $data, $timeOut, $verifySSL, $err);
    }

    function zthttp_get($url,$timeOut=15,$verifySSL=true,&$err=NULL){
		return zthttp_request($url, false, NULL, $timeOut, $verifySSL, $err);
    }

    if (!function_exists('boolval')) {
    	function boolval($var){
			return $var?true:false;
    	}
    }
?>