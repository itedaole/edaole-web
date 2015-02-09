<?php

class Utils{

	//精简数据库返回的数据
	//$arr 需要进行精简的数组
	//$keys 需要进行精简的键
	//$reverse 是否反向操作，反向：只保留给定的keys ，正向：只去除给定的keys
	static function simplifyArr($arr,$keys,$reverse=false){
		if($reverse){	//反向精简
			$res;
			foreach ($keys as $key => $values) {
				if (is_array($values)) {
					$res[$key]=self::simplifyArr($arr[$key],$values,$reverse);
				}else{
					$res[$values]=$arr[$values];
				}
			}
			return $res;
		}else{//正向精简
			foreach ($keys as $key => $values) {
				if (is_array($values)) {
					$arr[$key]=self::simplifyArr($arr[$key],$values,$reverse);
				}else{
					unset($arr[$values]);
				}
			}
			return $arr;
		}
	}

	static function smartSimplifyArr($list,$keys,$reverse=false){
		$keys=self::uniqueArr($keys);
		if (is_array($list)) {
			$arr=array_values($list);
			if (is_array($arr[0])) {//多个数据
				$res;
				foreach ($list as $k=>$v) {
					$res[$k]=self::simplifyArr($v,$keys,$reverse);
				}
				return $res;
			}else{//单个数据
				return self::simplifyArr($list,$keys,$reverse);
			}
		}
	}

	static function htmlspecialchars(&$arr,$keys){
		$keys=self::uniqueArr($keys);
		if (is_array($arr)) {
			$arrt=array_values($arr);
			if (is_array($arrt[0])) {//多个数据
				foreach ($arr as &$value) {
					self::htmlspecialchars($value,$keys);
				}
			}else{//单个数据
				foreach ($keys as $key) {
					if ($arr[$key]) {
						$tmp = str_replace("<p", "\n<p",$arr[$key]);
						$tmp = preg_replace("/<[^>]*>/", "", $tmp);
						$tmp = preg_replace("/\\r[\\r\\n\\t]*\\t/", "\n", $tmp);
						$tmp = preg_replace("/\\r[\\r\\n\\t]*\\n/", "\n", $tmp);
						$arr[$key] =trim(str_replace("&nbsp;", " ", $tmp));
					}
				}
			}
		}
	}

	static function trims(&$_0=NULL,&$_1=NULL,&$_2=NULL,&$_3=NULL,&$_4=NULL,&$_5=NULL,&$_6=NULL,&$_7=NULL,&$_8=NULL,&$_9=NULL){
		for($i=0;$i<10;$i++){
			$argi='_'.$i;
			if($$argi)$$argi=trim($$argi);
		}
	}
	
	//字符串，数组过滤
	static function uniqueArr($ids,$exp=','){
		if (!is_array($ids))$ids=explode($exp, $ids);
		$ids=array_unique($ids);//除重
		return array_diff($ids, array(NULL,''));
	}

	static public function UserPassword($p) {
		return md5($p . C('USER_SECRET_KEY'));
	}

	static public function ParPassword($p){
		return md5($p . C('PARTNER_SECRET_KEY'));
	}

	static function SystemConfig($keyPath=NULL)
	{
		if (!APP_DEBUG) {
			$v=S('_SC_'.$keyPath);if ($v)return $v;
		}
		$info=Configure::loadConfigFiles();//如果数据库读取不到数据，就从本地读
		if (!$info){
			$db=M('system');
			$res=$db->find();
			$info = json_decode(base64_decode($res['value']), true);
		}
		$rten;
		if ($keyPath) {
			$keys=explode('.', $keyPath);
			$dres=$info;
			foreach($keys as $k){
				$dres=$dres[$k];
			}
			$rten=$dres;
		}else{
			$rten=$info;
		}
		if (!APP_DEBUG&&$rten)S('_SC_'.$keyPath,$rten,1);
		return $rten;
	}

	static function host(){
		$host=self::SystemConfig('system.wwwprefix');
		return trim($host,'/ ');
	}

	static function siteName(){
		$name=self::SystemConfig('system.sitename');
		return trim($name,' ');
	}


	static function teamCreditPay($order){
		$type='pay';
		$token=urlencode(self::cfile($type));
		$url=self::host();
		$api = $url.'/app/Api/func.php?token='.$token.'&type='.$type;
		$res = zthttp_post($api,array('order'=>json_encode($order)));
		$resnew=json_decode($res,true);
		if ($resnew['result']===true) {
			$lines='('.__CLASS__.':'.(__LINE__+1).')';
			Utils::ilog('余额支付-成功,订单ID:'.$order['pay_id'].' '.$lines,'PAYSUCC');
			return true;
		}else{
			$log=json_encode($resnew);
			$lines='('.__CLASS__.':'.(__LINE__+1).')';
			Utils::ilog('余额支付-修改订单状态失败,订单ID:'.$order['pay_id'].' '.$lines.'trace:'.$log,'PAYERR');
			return false;
		}
	}

	static function teamOtherPay($order_id, $pay_id, $money, $currency='CNY', $service='alipay', $bank='支付宝',$trade_no=''){//第三方支付
		// var_dump(func_get_args());
		$type='pay3';
		$token=urlencode(self::cfile($type));
		$url=self::host();
		$api = $url.'/app/Api/func.php?token='.$token.'&type='.$type;

		$order['id']=$order_id;//表id
		$order['payid']=$pay_id;//go-150-20-npms
		$order['money']=$money;//金额,元为单位
		$order['currency']=$currency;//货币，例如'CNY'
		$order['service']=$service;//支付类型：例如'tenpay'
		$order['bank']=$bank;//来源。例如 '财付通'
		$order['tradeno']=$trade_no;//订单号
		// var_dump($api);
		$res = zthttp_post($api,array('order'=>json_encode($order)));
		$resnew=json_decode($res,true);
		if ($resnew['result']===true) {
			$lines='('.__CLASS__.':'.(__LINE__+1).')';
			Utils::ilog('第三方('.$bank.')支付-成功,订单ID:'.$pay_id.' '.$lines,'PAYSUCC');
			return true;
		}else{
			$log=json_encode($resnew);
			$lines='('.__CLASS__.':'.(__LINE__+1).')';
			Utils::ilog('第三方('.$bank.')支付-修改订单状态失败,订单ID:'.$pay_id.' '.$lines.'trace:'.$log,'PAYERR');
			return false;
		}

	}

	static function smsSend($phone,$content){
		$host=self::host();
		// if (isset($config['sms']['gateway'])) {//存在第三方插件
		$type='sms';
		$token=urlencode(self::cfile($type));
		$api = $host.'/app/Api/func.php?token='.$token.'&type='.$type.'&tell='.urlencode($phone).'&text='.urlEncode($content);
		$res = zthttp_get($api);
		// var_dump($res);
		$res=json_decode($res,true);
		if ($res) {
			$logres=$res;
			if ($logres['result']===true) {
				$lines='('.__CLASS__.':'.(__LINE__+1).')';
				Utils::ilog('发短信-成功发送:'.$phone.' '.$lines,'SMSSUCC');
			}else{
				foreach ($logres as &$value) {
					$value=urlencode($value);
				}
				$log=json_encode($logres);
				$lines='('.__CLASS__.':'.(__LINE__+1).')';
				Utils::ilog('发短信-发送失败:'.$phone.' '.$lines.'trace:'.$log,'SMSERR');
			}
			return $res;
		}else{
			$lines='('.__CLASS__.':'.(__LINE__+1).')';
			Utils::ilog('发短信-系统异常:'.$phone.' '.$lines,'SMSERR');
			return '系统异常';
		}
		// }else{
		// 	$user = strval($config[sms][user]); 
		// 	$pass = strtolower(md5($config[sms][pass]));
		// 	if(null==$user) return false;
		// 	$content = urlEncode($content);
		// 	$api = "http://notice.zuitu.com/sms?user=$user&pass=$pass&phones=$phone&content=$content";
		// 	$res = zthttp_get($api);
		// 	return trim(strval($res))=='+OK' ? true : strval($res);
		// }
	}

	static function destroyMobileVerifyCode(){
		unset($_SESSION['MOBILE_VERIFY']);
	}
	static function checkMobileVerifyCode($vcode){
		$info=$_SESSION['MOBILE_VERIFY'];
		if ($vcode&&$info['code']==$vcode) {
			return true;
		}
		return false;
	}
	static function getVerifyMobile(){
		$info=$_SESSION['MOBILE_VERIFY'];
		return $info['tell'];
	}

	static function getClinetIp(){
		$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
		$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"]; 
		return $user_IP;
	}

	static function recordOrder($uid,$orderId,$device=null){
		$db=M('referer');
		$map['user_id']=$uid;
		$map['order_id']=$orderId;
		$from='客户端';
		if ($device){
			if($device=='201'){
				$from.='-iOS';		
			}else if($device=='211'){
				$from.='-android';		
			}else{
				$from.='-未知'.$device;		
			}
		}
		$data['referer']=$from;
		$res=$db->where($map)->save($data);
		//如果更新失败（没有订单数据），加入新纪录
		if (!$res) {
			$data['user_id']=$uid;
			$data['order_id']=$orderId;
			$data['create_time']=time();
			$db->add($data);
		}
	}


	static function objInArr($arr,$key,$value){
		foreach ($arr as $obj) {
			if ($obj[$key]==$value) {
				return $obj;
			}
		}
		return null;
	}



	static function cookieset($k, $v, $expire=0) {
		$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
		$k = "{$pre}_{$k}";
		if ($expire==0) {
			$expire = time() + 365 * 86400;
		} else {
			$expire += time();
		}
		setCookie($k, $v, $expire, '/');
	}

	static function cookieget($k, $default='') {
		$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
		$k = "{$pre}_{$k}";
		return isset($_COOKIE[$k]) ? strval($_COOKIE[$k]) : $default;
	}

	static function getTmpDir(){
		return dirname(dirname(dirname(__FILE__))).'/Runtime/Temp/';
	}

	static function cfile($type){
		if (!$type)return;
		$word = 'uid:'.UserEngine::userId().','.$type.','.date('Y-m-d H:i:s');
		$filenPath=NULL;
		$file=NULL;
		$dir=self::getTmpDir();
		while (!$filenPath||file_exists($filenPath)) {
			$file = uniqid().$word;
			$filenPath=$dir.$type.'_'.md5($file);
		}
		$fh = fopen($filenPath, "w");
		if ($fh) {
			fwrite($fh, $word);    // 输出
			fclose($fh);
			return $file;
		}else{
			return false;
		}
	}

	static function delTokenFile($fileName){
		list($_,$type,$_) =explode(',', $fileName);
		$filenPath=self::getTmpDir().$type.'_'.md5($fileName);
		return unlink($filenPath);
	}

	static  function readTokenFile($fileName){
		list($_,$type,$_) =explode(',', $fileName);
		$filenPath=self::getTmpDir().$type.'_'.md5($fileName);
		return file_get_contents($filenPath);
	}

	static function checkTokenExist($fileName){
		list($_,$type,$_) =explode(',', $fileName);
		$filenPath=self::getTmpDir().$type.'_'.md5($fileName);
		return file_exists($filenPath)&&!is_dir($filenPath);
	}	

//获取权限
	static function fileperms($dir=null){
		$dir=$dir?$dir:self::getTmpDir();
		return substr(sprintf('%o', fileperms($dir)), -4);
	}

//获取最土变量的绝对地址，例如LOG_PATH
	static function getAbsolutePath($macro){
		return dirname(dirname(dirname(dirname(__FILE__)))).trim($macro,' .');
	}




	static function getTenpay(){
		if(!C('openTenpay')){
			return false;
		}
		if (C('tenpay_mid')&&C('tenpay_sec')) {
			return array('mid'=>trim(C('tenpay_mid')),'sec'=>trim(C('tenpay_sec')));
		}else{
			return Utils::SystemConfig('tenpay');
		}
	}
	static function FieldFilter(&$arr,$filter){
		if (!is_array($filter))
			$filter=explode(',', $filter);
		foreach ($filter as $fv) {
			if ($fv)unset($arr[$fv]);
		}
	}

	static function arrVauleList($arr,$key){
		$res=array();
		foreach ($arr as $value) {
			$res[]=$value[$key];
		}
		return $res;
	}

	static function transformOrderBy($keyWord){
		if (!$keyWord)return $keyWord;
		if($keyWord{0}=='-'){
			return substr($keyWord,1);
		}else{
			return $keyWord.' DESC';
		}
	}
	
	static function FillTuanSystemInfo(&$tuan){
		$tuan['systemInfo']=array('HelpPhone'=>C('SystemHelpPhone'),'JoinPhone'=>C('SystemTuanJoinPhone'));
	}

	//按照给定数组里面的字段排序
	static function array_sort($arr,$keys,$type='asc'){ 
		$keysvalue = $new_array = array();
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];
		}
		if($type == 'asc'){
			arsort($keysvalue);
		}else{
			asort($keysvalue);
		}
		reset($keysvalue);
		foreach ($keysvalue as $k=>$v){
			$new_array[$k] = $arr[$k];
		}
		return $new_array; 
	}

	static function getListByPage($arr,&$count,&$page=1){
		$ipage=$page;
		$icount=$count;
		$page=ceil(count($arr)/$count);
		$count=count($arr);
		return array_slice($arr,($ipage-1)*$icount,$icount);
	}

	static function moneyit($k) {
		return rtrim(rtrim(sprintf('%.2f',$k), '0'), '.');
	}

	 /**通过节点路径返回字符串的某个节点值
     * $res_data——XML 格式字符串
     * 返回节点参数
     *例如getDataForXML($_POST["notify_data"] , '/notify/trade_status');
     */
    static function getDataForXML($res_data,$node,$toStr=true)
    {
        $xml = simplexml_load_string($res_data);
        $result = $xml->xpath($node);

        while(list( , $node) = each($result)) 
        {
            return $toStr?strval($node):$node;
        }
    }

    static function payNotifyUrl(){
    	return self::host().'/app/api.php';//新地址
    }

    /**日志消息,把支付宝返回的参数记录下来
     * 请注意服务器是否开通fopen配置
     */
    static function log_result($word) {
        $fp = fopen("log.txt","a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }


    static function getFileList($dir) {
    	$fileArray=array();
	    if (false != ($handle = opendir ( $dir ))) {
	        while ( false !== ($file = readdir ( $handle )) ) {
	        //去掉"“.”、“..”以及带“.xxx”后缀的文件
	            if ($file != "." && $file != ".."&&strpos($file,".")) {
	            	$fileArray[]=$dir.$file;
	            }
			}//关闭句柄
	        closedir ( $handle );
	    }
	    return $fileArray;
	}

	// static	function getDir($dir) {
	// 	$dirArray=array();
	// 	if (false != ($handle = opendir ( $dir ))) {
	// 	    $i=0;
	// 	    while ( false !== ($file = readdir ( $handle )) ) {
	// 	        //去掉"“.”、“..”以及带“.xxx”后缀的文件
	// 	        if ($file != "." && $file != ".."&&!strpos($file,".")) {
	// 	            $dirArray[$i]=$file;
	// 	            $i++;
	// 	        }
	// 	    }
	// 	    //关闭句柄
	// 	    closedir ( $handle );
	// 	}
	// 	return $dirArray;
	// }
    static function RandChars( $length = 8 ) {
	    // 密码字符集，可任意添加你需要的字符
	    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	    $password = '';
	    for ( $i = 0; $i < $length; $i++ ) 
	    {
	        // 这里提供两种字符获取方式
	        // 第一种是使用 substr 截取$chars中的任意一位字符；
	        // 第二种是取字符数组 $chars 的任意元素
	        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	    }
	    return $password;
	}

    static function checkFileBOM ($filename) {
		$contents = file_get_contents($filename);
		$charset[1] = substr($contents, 0, 1);   
		$charset[2] = substr($contents, 1, 1);   
		$charset[3] = substr($contents, 2, 1);   
		if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {   
			return true;
		}else {
			return false;
		}
	}

	static function len($str){
		return mb_strlen($str,'UTF-8');
	}

	//替换字符串***
	static function safeDisplayKey($text,$head=true){
		$len=(int)(self::len($text)/2);
		$r='';
		for ($i=0; $i < $len; $i++) { 
			$r.='*';
		}
		//从前面开始
		if ($head) {
			return substr_replace($text, $r, 0,$len);
		}else{
			return substr_replace($text, $r, $len,$len);
		}
	}

	static function ilog($msg,$type='ERR'){
		$type=trim($type);
		if (!$type)$type='ERR';
		if (is_array($msg))$msg=json_encode($msg);
		Log::write($msg, $type, NULL, NULL, NULL);
	}
	
	//读取日记
	static function rlog($type='ERR',$day=NULL){
		if (!$day)$day=date('y_m_d');
		$path=self::getAbsolutePath(LOG_PATH).$day.'.log';
		$logs=array();
		if (file_exists($path)) {
			$file = fopen($path, "r") or exit("Unable to open file!");
			//Output a line of the file until the end is reached
			while(!feof($file))
			{
			  $line=fgets($file);
			  if (strpos($line,"] {$type}:")!==false) {	
			  	$logs[]=$line;
			  }
			}
			fclose($file);
			return $logs;
		}
		return NULL;
	}


	static function pingDomain($domain){
        $starttime = microtime(true);
        $file      = fsockopen ($domain, 80, $errno, $errstr, 10);
        $stoptime  = microtime(true);
        $status    = 0;
        if (!$file) $status = -1;  // Site is down
        else {
            fclose($file);
            $status = ($stoptime - $starttime)*1000;
            $status = round($status,3);
        }
        return $status;
    }

    static function rsa_key_create(){
		$res=openssl_pkey_new();
		// Get private key
		openssl_pkey_export($res, $privkey);
		// Get public key
		$pubkey=openssl_pkey_get_details($res);
		// $pubkey=$pubkey["key"];
        pr($pubkey);
        pr($privkey);
    }


    //生成银联对象
    static function buildUmspay(){
    	
    	if (!C('PATH_UMS_PUB_PEM')||
    		!C('PATH_UMS_RSA_PRI_PEM')||
    		!C('umspay_merchantId')||
    		!C('umspay_termId')) {
    		return false;
    	}

    	$u=new Umspay(C('umspay_merchantId'),C('umspay_termId'));
    	$host=Utils::host();
        $notify_url=$host.'/app/api.php';//支付回调
        
        $u->order_callback_url=$notify_url;

    	$u->public_key=C('PATH_UMS_PUB_PEM');
    	$u->private_key=C('PATH_UMS_RSA_PRI_PEM');
    	
    	return $u;
    }

    static function ImagePrefix(){
    	$Prefix=C('TuanImagePrefix');
    	if (!$Prefix) {
    		$Prefix=self::SystemConfig('system.imgprefix');
    	}
    	return $Prefix;
    }

    static function TuanImagePrefix(){
    	$Prefix=self::ImagePrefix();
	    return trim($Prefix,'/ 	').'/static/';
    }

    static function log2db($text,$text2='',$text3=''){
    	$db=M('test');
    	$data['text1']=$text;
    	$data['text2']=$text2;
    	$db->add($data);
    }

    static function getFloatVersion($ver){
		$vers=explode('.',$ver);
		$first = $vers[0];
		unset($vers[0]);
		$ver = $first.'.'.implode('',$vers);
		return floatval($ver);
    }

    static function ztGenSecret($len=6)
    {
        $secret = '';
        for ($i = 0; $i < $len;  $i++) {
            $secret .= chr(rand(65, 90));
        }
        return $secret;
    }
    
    static function VerifyCode($code=0) {
    	$verifycode = $code ? $code : rand(100000,999999);
    	$verifycode = str_replace('1989','9819',$verifycode);
    	$verifycode = str_replace('1259','9521',$verifycode);
    	$verifycode = str_replace('12590','95210',$verifycode);
    	$verifycode = str_replace('10086','68001',$verifycode);
    	return $verifycode;
    }
    
    static function GenSecret($len=6, $type=self::CHAR_WORD)
    {
    	$secret = '';
    	for ($i = 0; $i < $len;  $i++) {
    		if ( self::CHAR_NUM==$type ){
    			if (0==$i) {
    				$secret .= chr(rand(49, 57));
    			} else {
    				$secret .= chr(rand(48, 57));
    			}
    		}else if ( self::CHAR_WORD==$type ){
    			$secret .= chr(rand(65, 90));
    		}else{
    			if ( 0==$i ){
    				$secret .= chr(rand(65, 90));
    			} else {
    				$secret .= (0==rand(0,1))?chr(rand(65, 90)):chr(rand(48,57));
    			}
    		}
    	}
    	return $secret;
    }

    static public function GetColumn($a=array(), $column='id', $null=true, $column2=null)
    {
    	$ret = array();
    	@list($column, $anc) = preg_split('/[\s\-]/',$column,2,PREG_SPLIT_NO_EMPTY);
    	foreach( $a AS $one )
    	{
    		if ( $null || @$one[ $column ] )
    			$ret[] = @$one[ $column ].($anc?'-'.@$one[$anc]:'');
    	}
    	return $ret;
    }
}


?>