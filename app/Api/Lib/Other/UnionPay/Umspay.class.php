<?php

define('UMS_IS_TEST', false);//测试环境

//if php version < 5.4.0
if(!function_exists('hex2bin')) {
	function hex2bin($hex) {
		$n = strlen($hex);
		$bin = "";
		$i = 0;
		while($i < $n) {
			$a = substr($hex, $i, 2);
			$c = pack("H*", $a);
			if ($i == 0) {
				$bin = $c;
			} else {
				$bin .= $c;
			}
			$i+=2;
		}
		return $bin;
	}
}

/**
 * 全民捷付sdk for php
 * 
 * @author eslizn <eslizn@live.cn>
 * @copyright  Copyright (c) 2013 Ludo team (http://www.loongjoy.com)
 * @version  $Id$
 */
class Umspay {

	/**
	*	log out
	*/
	var $logDebug;

	/**
	 * 下单接口
	 */
	var $order_create_url ;

	/**
	 * 订单查询接口
	 */

	var $order_query_url ;
	/**
	 * 下单回调接口
	 */

	var $order_callback_url ;

	/**
	 * 商户号
	 * 
	 * @var string
	 */
	var $merchantId;

	/**
	 * 终端号
	 * 
	 * @var string
	 */
	var $termId;


	var $public_mod;//公钥模
	var $public_exp;//指数

	var $public_key;//公钥
	
	var $private_mod;//私钥模
	var $private_exp;//指数

	var $private_key;//私钥


	/**
	 * [__construct description]
	 * @param string $merchantId 商户号
	 * @param string $termId     终端号
	 */
	function __construct($merchantId, $termId) {
		if (UMS_IS_TEST) {
			$this->order_create_url='https://116.228.21.162:8603/merFrontMgr/orderBusinessServlet';//测试地址	
			$this->order_query_url='https://116.228.21.162:8603/merFrontMgr/orderQuerySerlet';//测试地址
		}else{
			$this->order_create_url='https://mpos.quanminfu.com:6004/merFrontMgr/orderBusinessServlet';//生产地址	
			$this->order_query_url='https://mpos.quanminfu.com:6004/merFrontMgr/orderQuerySerlet';//生产地址
		}

		$this->merchantId = $merchantId;
		$this->termId = $termId;
	}

	/**
	 * 签名数据
	 * 
	 * @param  string $data    要签名的数据
	 * @param  string $private 私钥文件
	 * @return string          签名的16进制数据
	 */
	function sign($data) {
		if ($this->private_key) {
			$p = openssl_pkey_get_private($this->private_key);
		}else{
			$private=$this->private2file($this->private_mod, $this->private_exp);
			// pr($private);
			$p = openssl_pkey_get_private(trim($private));
			// pr(openssl_pkey_get_details($p));
		}
		openssl_sign($data, $signature, $p);
		openssl_free_key($p);
		return bin2hex($signature);
	}

	/**
	 * 验签
	 * 
	 * @param  string $data		待验证数据
	 * @param  string $sign		签名数据
	 * @param  string $public 	公钥文件//验签公钥文件应为全民捷付提供的公钥文件
	 * @return bool        		验签状态
	 */
	function verify($data, $sign) {
		if ($this->public_key) {
			$p = openssl_pkey_get_public($this->public_key);
		}else{
			$public=$this->public2file($this->public_mod, $this->public_exp);
			$p = openssl_pkey_get_public(trim($public));
			// pr(openssl_pkey_get_details($p));
			// pr(hex2bin($sign));
		}
		$verify = openssl_verify($data, hex2bin($sign), $p);
		openssl_free_key($p);
		return $verify === 1;
	}

	/**
	 * 以post方式发送请求请求
	 * 
	 * @param  array $data 	请求参数
	 * @param  string $url  请求地址
	 * @return array 		请求结果
	 */
	function post($data, $url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//忽略证书错误信息
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT,15);

		curl_setopt($ch, CURLOPT_SSLVERSION,3);//强制使用版本3

		//数据载体格式
		// echo $data.'<br/>';
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		// echo $content.'<br/>';
		$result = json_decode($content, true);

//debug
		if (!$result['Signature']&&$this->logDebug) {
			$err=curl_error($ch);
			if($err){
				echo "连接错误:".$err,'<br/>';
			}else{
			 	echo '服务器返回:';
			 	var_dump($content);
			 	echo "<br/>";
			}
		}
		curl_close($ch);
		return $result;
	}

	/**
	 * 下单
	 * 
	 * @param  string  $title   订单标题
	 * @param  number  $amt     订单金额,单位元
	 * @param  string  $orderId 订单id,由商户产生
	 * @param  string  $type    交易类型
	 * @param  integer $exp     过期时间
	 * @return mixed
	 */
	function order($title, $amt, $orderId, $type = 'NoticePay', $exp = 0) {
		$time = time();
		$data = array(
			'OrderTime' => date('His', $time),//date hhmmss
			'EffectiveTime' => strval($exp ? $exp - $time : 0),
			'OrderDate' => date('Ymd', $time),//yyyymmdd
			'MerOrderId' => $orderId,
			'TransType' => $type,//NoticePay or Pay
			'TransAmt' => strval($amt*100),
			'MerId' => $this->merchantId,
			'MerTermId' => $this->termId,
			'NotifyUrl' => $this->order_callback_url,
			'OrderDesc' => $title,
			'MerSign' => '',
		);

		$sign=$this->makeSignStr($data);

		$data['TransCode'] = '201201'; //const vaule
		$data['MerSign'] = $this->sign($sign);

		// Utils::log2db('下单接口请求报文:'.json_encode($data));

		//发送请求
		$data = $this->buildPostStr($data);
		// pr($data);
		$result = $this->post($data, $this->order_create_url);
		// pr($this->order_create_url);
		// Utils::log2db('下单接口响应报文:'.json_encode($result));

		//验签

		$verify=$this->makeSignStr($result,array('MerOrderId','ChrCode','TransId','Reserve','RespCode','RespMsg'));
		
		if(!isset($result['Signature']) || !$this->verify($verify, $result['Signature'])){
			//验签失败
			return false;
		}
		
		if(!isset($result['RespCode']) || intval($result['RespCode'])){
			//下单失败 详细信息参考$result['RespMsg']
			return false;
		}


		$result['sigin']=$this->sign($result['TransId'].$result['ChrCode']);

		if (UMS_IS_TEST) {
			$result['isTest']=1;
		}else{
			$result['isTest']=0;
		}

		return $result;
	}


	function buildPostStr($dataArr){
		return 'jsonString='.json_encode($dataArr);
	}

	/**
	 * 响应回调
	 * 
	 */
	function notify(){
		$data = &$_POST;

		#验签
		$verify = '';
		$verify .= isset($data['OrderTime']) ? $data['OrderTime'] : '';
		$verify .= isset($data['OrderDate']) ? $data['OrderDate'] : '';
		$verify .= isset($data['MerOrderId']) ? $data['MerOrderId'] : '';
		$verify .= isset($data['TransType']) ? $data['TransType'] : '';
		$verify .= isset($data['TransAmt']) ? $data['TransAmt'] : '';
		$verify .= isset($data['MerId']) ? $data['MerId'] : '';
		$verify .= isset($data['MerTermId']) ? $data['MerTermId'] : '';
		$verify .= isset($data['TransId']) ? $data['TransId'] : '';
		$verify .= isset($data['TransState']) ? $data['TransState'] : '';
		$verify .= isset($data['RefId']) ? $data['RefId'] : '';
		$verify .= isset($data['Account']) ? $data['Account'] : '';
		$verify .= isset($data['TransDesc']) ? $data['TransDesc'] : '';
		$verify .= isset($data['Reserve']) ? $data['Reserve'] : '';
		
		if(!$this->verify($verify, $data['Signature'])){
			//验签失败!
			return false;
		}
		
		//根据MerOrderId得到本地订单
		//做一些业务处理
		
		//响应数据
		$result = array(
			'TransCode' => '201202',
			'MerOrderId' => $data['MerOrderId'],
			'TransType' => 'NoticePay',
			'MerId' => $data['MerId'],
			'MerTermId' => $data['MerTermId'],
			'TransId' => $data['TransId'],
			'MerPlatTime' => date('YmdHis'),
			'MerOrderState' => '11',
			'Reserve' => '',
			'MerSign' => ''
		);
		
		//状态判断 1待销帐 4销帐成功 5支付失败(可以重试) 6支付失败
		
		switch ($data['TransState']) {
			case 1:
				$result['MerOrderState'] = '00';
				
				break;
			case 4:
				$result['MerOrderState'] = '00';
				
				break;
			case 5:
				
				break;
			case 6:
				
				break;
		}
		
		//签名
		$result['MerSign'] .= isset($result['MerOrderId']) ? $result['MerOrderId'] : '';
		$result['MerSign'] .= isset($result['TransType']) ? $result['TransType'] : '';
		$result['MerSign'] .= isset($result['MerId']) ? $result['MerId'] : '';
		$result['MerSign'] .= isset($result['MerTermId']) ? $result['MerTermId'] : '';
		$result['MerSign'] .= isset($result['TransId']) ? $result['TransId'] : '';
		$result['MerSign'] .= isset($result['MerPlatTime']) ? $result['MerPlatTime'] : '';
		$result['MerSign'] .= isset($result['MerOrderState']) ? $result['MerOrderState'] : '';
		$result['MerSign'] .= isset($result['Reserve']) ? $result['Reserve'] : '';
		
		$result['MerSign'] = $this->sign($result['MerSign']);
		
		//响应回调
		// exit(json_encode($result));
		return $result;;
	}

	/**
	$orderId 订单号
	$transId 交易流水号
	*/
	function queryOrder($orderId,$transId){

		$json = array(
			'ReqTime' => date('YmdHis'),
			'OrderDate' =>  date('Ymd'),
			'MerOrderId' => $orderId,
			'TransId' => $transId, //交易流水
			'MerId' => $this->merchantId, //商户号
			'MerTermId' => $this->termId, //终端号
			'Reserve' => '',
		);

		$sign=$this->makeSignStr($json);

		$json['TransCode']='201203';

		$json['MerSign'] = $this->sign($sign);
		// pr($json);
		// die;
		$data = $this->buildPostStr($json);
		// $data='jsonString='.json_encode($json);
		// pr($data);

		$result = $this->post($data, $this->order_query_url);
		
		// pr($result);

		$keys=array('OrderTime','OrderDate','MerOrderId','TransType',
			        'TransAmt','MerId','MerTermId','TransId','TransState',
			        'RefId','Reserve','RespCode','RespMsg');

		$verify=$this->makeSignStr($result,$keys);
		// pr($result);
		// pr($result['Signature']);

		if(!$result['Signature']||!$this->verify($verify, $result['Signature'])){
			//验签失败!
			return false;
		}
		
		return $result;
	}

	function orderIsPay($order){
			//0:新订单, 1:支付成功 2:失败 3:支付中
		switch ($order['TransState']) {
			case '1':
				return true;
				break;
			default:
				return false;
		}
	}

	function makeSignStr($data,$keys=null){
		if ($keys) {
			foreach ($keys as $key) {
				if (isset($data[$key])) {
					$sign.=$data[$key];
				}
			}
		}else{
			foreach ($data as $key => $value) {
				$sign.=$value;	
			}
		}
		
		return $sign;
	}

	/**
	 * 还原公钥
	 * 
	 * @param  string $mod	16进制公钥指数
	 * @param  string $exp	16进制模数
	 * @return string 		字符串形式的公钥文件
	 */
	function public2file($mod, $exp = '010001') {
		if (intval($exp)==10001) {
			$exp='010001';
		}
		return "-----BEGIN PUBLIC KEY-----\r\n" . wordwrap(base64_encode(hex2bin("30819f300d06092a864886f70d010101050003818d0030818902818100{$mod}0203{$exp}")), 64, "\r\n", true) . "\r\n-----END PUBLIC KEY-----\r\n";
	}

	/**
	 * 从私钥中提取公钥指数和模数
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	function getPrivate($file){
	    $p = openssl_pkey_get_private(file_get_contents($file));
	    $res = openssl_pkey_get_details($p);
	    //var_dump($res);
	    openssl_free_key($p);
	    return array(
	        'n' => bin2hex($res['rsa']['n']),#模数
	        'e' => bin2hex($res['rsa']['e']),#公钥指数
	        'd' => bin2hex($res['rsa']['d']),#私钥指数
	    );
	}

	/**
	 * 生成私钥，这个私钥是不完整的，里面的公钥部分错误，所以只能用于签名
	 * 
	 * @param  string $mod	16进制私钥指数
	 * @param  string $exp	16进制模数
	 * @return string 		字符串形式的私钥文件
	 */
	function private2file($mod, $exp){
		return "-----BEGIN RSA PRIVATE KEY-----\r\n" . wordwrap(base64_encode(hex2bin("3082025c02010002818100{$mod}020301000102818100{$exp}024100da5103d5206afbfa37e186a1ca352c498aa7fa7918178b56678221811c389089d3deb779ad1c208652e28d4d6f51a2d3fe2d32ff0c3dc2bd2ed814f523d601ab024100d6248ff0031e9f7b7b4245c5fc4d9b65dc942e02b4507eb8f0b6fdeb9918aa2ac401c16ab12f3a484ba2e97125472c23e5770d7cf581e50b1cc9937ec173bf7302405252e47814be63004adc2f5189179df8a9618870eb65cd742a9a069a5212fe660acfdc2df4da3b658b91c4a8e3864c39568aa2c54c4f69c4bf0a5a74ca2ba3fb02404a7417df79163298fa38068e59b499ed068e3699161c4e92fa8e85265eea666fcc0a583742378b6a0b722efbf9dc0f0ac4036a9b21b8f1ebb52c98ad9f9120e50240661b7066f92bea9d751bbf0730148794c4a94a534dc3d666086330cb9cbb422f850392110647d4a7b7633dbbe1d9c03772a2a30d54eac7e13361e7aa0f20b14e")), 64, "\r\n", true) . "\r\n-----END RSA PRIVATE KEY-----\r\n";
	}

}
