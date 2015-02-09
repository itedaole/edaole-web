<?php
class Configure{
	static function configFiles() {
		return array(
			//system
			// 'db',//db这个文件在config里面读取了
			'memcache',
			// 'webroot',
			'system',
			'bulletin',
			//pay
			'alipay',
			'tenpay',
	        'sdopay',
			'bill',
			'chinabank',
			'paypal',
			'yeepay',
	        'cmpay',
	        'gopay',
			'other',
			//settings
			'option',
			'mail',
			'sms',
			'credit',
			'skin',
			'authorization',
	        //login
			'sina',
			'qq',
			'qzone',
			'wechat'
		);
	}


	static function loadConfigFiles($files=NULL){
		if ($files) {
			$files=explode(',', $files);
		}else{
			$files=self::configFiles();	
		}
		$dir=dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/include/configure/';
		$vs=array();
		foreach ($files as $name) {
			$path=$dir.$name.'.php';
			if (file_exists($path)) {
				// echo $path.'<br/>';
				@require($path);
				if (is_array($value)) {
					// echo "merge:$name<br/>";
					$vs[$name]=$value;
				}
			}
		}
		// var_dump($vs);
		return $vs;
	}

}
?>