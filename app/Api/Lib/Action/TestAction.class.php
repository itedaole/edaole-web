<?php
/*
	测试专用
*/

define('AUTO_TEST_VER',0.24);

class TestAction extends CommonAction {

    function _initialize(){
        parent::_initialize();
        header('Content-Type:text/html; charset=utf-8');
        if(!APP_DEBUG){//如果配置开启了压缩   
            self::iDie('访问此页面需要开启调试模式!');
        }
    }

	public function index(){
    	global $dbug;
    	$dbug =isset($_GET['debug']);
    	global $admin;
		$admin = (md5(md5($_GET['token']))=='a8414d4421cb45a4113fb81801296e63');
		if ($admin) $dbug=true;

		echo '<html><head><title>自动化测试-',Utils::siteName(),'</title></head><body>';
		echo '<h1>自动化测试 V',AUTO_TEST_VER,'</h1>';
		echo '<a target="_blank" href="http://weibo.com/bcker">@开发者</a>';
		echo '<pre/>';
        echo self::YES('绿色为正常'),' , ',self::NO('红色为错误'),' , ',self::WAR('黄色为警告或提示');

    	$names=preg_grep('/^AutoTest/', get_class_methods($this));
        
        $ignore = explode(',', I('ignore'));

    	foreach ($names as $fun) {
            if(in_array($fun, $ignore))continue;
            $this->$fun();
    	}
    	echo '</body></html>';
    }

    public function AutoTestEnvironment(){
    	echo '<hr/>>>>环境测试<br/>';
    	echo '版本:		',APP_VERSION,'<br/>';
    	echo '框架:		',THINK_VERSION,'<br/>';
    	echo 'HOST:		',Utils::host()?(Utils::host().(url_exists(Utils::host())?self::YES('  √'):self::NO('  x'))):self::iDie('获取失败'),'<br/>';
    	echo 'Site:		',Utils::siteName(),'<br/>';
        echo 'Ip:             ',Utils::getClinetIp(),'<br/>';
    	echo 'IS_CGI:		',IS_CGI?'YES':'NO','<br/>';
    	echo 'IS_CLI:		',IS_CLI?'YES':'NO','<br/>';
    	echo 'IS_WIN:		',IS_WIN?'YES':'NO','<br/>';
    	echo '缓存:		',C('DATA_CACHE_TYPE'),'<br/>';
    	echo 'S缓存		',S('TEST_S','_S')?self::YES():self::NO(),'<br/>';
		echo 'F缓存		',F('TEST_F','_S')?self::YES():self::NO(),'<br/>';
        if(C('OUTPUT_ENCODE')){//如果配置开启了压缩
            if(extension_loaded('zlib')){//检查服务器是否开启了zlib拓展
                echo 'GZIP压缩         ',self::YES(),'<br/>';
            }else{
                echo 'GZIP压缩         ',self::WAR(),'<br/>';
            }
        }
		echo 'SESSION生命期:	',ini_get('session.gc_maxlifetime'),'s<br/>';
        echo '环境		',$_SERVER['SERVER_SOFTWARE'],' php/',PHP_VERSION,' system:',php_uname('s'),'<br/>';
    }

    public function AutoTestFunctions(){
        echo '<hr/>>>>依赖库检测<br/>';
        $list = array('curl_init'=>'请安装curl库',
                    'openssl_get_privatekey'=>'请安装OpenSSL库',
                    'openssl_get_publickey'=>'请安装OpenSSL库',
                    'openssl_pkey_get_private'=>'请安装OpenSSL库',
                    'openssl_pkey_get_public'=>'请安装OpenSSL库');
        foreach ($list as $fun=>$war) {
            $this->_checkFunction($fun,$war);
        }
    }

    private function _checkFunction($fun,$war=NULL,$return=false){
        if ($return) {
            return function_exists($fun);
        }
        $had = function_exists($fun);
        echo $fun,$had?self::YES('√'):self::NO('X');
        if (!$had&&$war) {
            echo '  ',self::NO($war);
        }
        echo '<br/>';
    }

    private function NO($text=''){
    	if ($text==='')$text='NO';
    	return "<font color=red>{$text}</font>";
    }

	private function YES($text=''){
		if ($text==='')$text='YES';
		return "<font color=grid-rows>{$text}</font>";
    }

    private function WAR($text=''){
        if ($text==='')$text='YES';
        return "<font color=#FAAC58>{$text}</font>";
    }

    public function AutoTestImageCDN(){
        echo '<hr/>>>>图片CDN<br/>';

        echo '图CDN前缀:     ',Utils::ImagePrefix()?(Utils::ImagePrefix().(url_exists(Utils::ImagePrefix())?self::YES('  √'):self::NO('  x'))):self::iDie('获取失败'),'<br/>';
        if(C('TuanLargeImageSuffix'))echo '图CDN后缀Large:     ',C('TuanLargeImageSuffix'),'<br/>';
        if(C('TuanSmallImageSuffix'))echo '图CDN后缀Small:     ',C('TuanSmallImageSuffix'),'<br/>';
    }

    public function AutoTestWriteRunTimeDir(){
    	echo '<hr/>>>>测试缓存目录写入权限<br/>';
    	$dirs=array(LOG_PATH,TEMP_PATH,DATA_PATH,CACHE_PATH);
    	$dir = Utils::getAbsolutePath(TEMP_PATH);
    	foreach ($dirs as $dir) {
			echo '目录：',trim($dir,'.'),'	 权限：',Utils::fileperms($dir),'<br/>';
    	}
    	echo '>Temp目录测试 > ';
    	$name = Utils::cfile('test');
    	$error='<br/>>请尝试给予/Api/Runtime/目录下全部文件读写权限';
    	if($name){
    		$pr=Utils::readTokenFile($name);
    		$pd=Utils::delTokenFile($name);
    		if ($pr&&$pd) {
    			echo self::YES('正常');
    		}else if($pd){
    			self::iDie('读取异常'.$error);
    		}else{
    			self::iDie('删除异常'.$error);
    		}
    	}else{
			self::iDie('写入异常'.$error);
    	}
    }

    public function AutoTestBom(){
        global $dbug;
        echo '<hr/>>>>文件BOM头测试<br/>';
        $fileList[]=dirname(dirname(dirname(dirname(__FILE__)))).'/api.php';
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(dirname(__FILE__))).'/Common/'));
        $fileList=array_merge($fileList, Utils::getFileList(Utils::getAbsolutePath(CONF_PATH)));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(__FILE__).'/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(__FILE__)).'/Engine/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(__FILE__)).'/Other/Alipay/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(__FILE__)).'/Other/Tenpay/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(__FILE__)).'/Other/UnionPay/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(dirname(__FILE__))).'/Tpl/Partner/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(dirname(__FILE__))).'/Tpl/PayReturn/'));
        $fileList=array_merge($fileList, Utils::getFileList(dirname(dirname(dirname(__FILE__))).'/Tpl/Tuan/'));
        $errCount=0;
        $notFontCount=0;
        foreach ($fileList as $file) {
            list($_,$name)=explode('app', $file,2);
            if (file_exists($file)) {
                if (Utils::checkFileBOM($file)) {
                    echo $name,'      ',self::NO('异常'),'<br/>';
                    $errCount++;
                }else if($dbug&&!isset($_GET['thin'])) {
                    echo $name,'      ',self::YES('正常'),'<br/>';
                }
            }else{
                $notFontCount++;
                echo $name,self::NO('not found'),'<br/>';
            }
        }
        echo '>','扫描文件:',self::YES(count($fileList)).'个',' 丢失文件:',self::NO($notFontCount).'个',' 错误:',self::NO($errCount).'个';
        if ($errCount==0) {
            echo self::YES('  通过'),'<br/>';
        }else{
            echo self::NO('  失败'),'<br/>';
            echo self::iDie('>BOM头一般由window自带记事本编辑文件后留下，BOM头会影响PHP的正常工作，请下载<a href="http://notepad-plus-plus.org/">notepad++</a>、<a href="http://www.sublimetext.com/">sublimetext</a>等编辑器将有问题的文件去掉BOM头,或者重新用原始文件覆盖'),'<br/>';
        }
    }

    public function AutoTestSafe(){
        echo '<hr/>>>>安全性检测<br/>';
        $dir=dirname(dirname(dirname(__FILE__))).'/Conf/AlipayKeyChain';
        if (file_exists($dir)) {
            self::iDie('请将app/Api/Conf/AlipayKeyChain文件夹删除，并将支付证书填入app/Api/Conf/AliConfig.php中');
        }
        $db=M('order');
        $files=$db->getDbFields();
        if (!in_array('trade_no',$files)) {
            self::iDie('数据库Order 表缺失trade_no字段，可能由于数据库尚未升级，将导致第三方支付无法正常使用，尝试点击后台管理的数据库升级操作');
        }
        $db=M('system');
        $config=$db->find();
        if ($config['value']) {
            $data=$config['value'];
            $json=base64_decode($data);
            $data=json_decode($json,true);
            if (!$data) {
                echo self::WAR('system 表的value字段数据损坏或溢出，这有可能影响主机名等配置的读取，请手动将改字段的属性改为mediumtext,然后去后台设置里点一下保存按钮');    
            }
        }else{
            echo self::WAR('system 表的value字段为空，或无法读取，这有可能影响主机名等配置的读取');
        }
    }

    public function AutoTestCreateLinkToken(){
        $times=defVaule(I('time'),1,500,5);
        echo '<hr/>>>>创建本地连接Token测试('.$times.'次)<br/>';
        echo self::WAR('注意：如果每次普遍耗时过长，例如超过0.1秒，可能你的服务配置有问题,可能造成无法发短信、支付丢单等问题<br/>');
        echo self::WAR('可以通过修改服务器host文件将域名指向本地ip提高本地访问速度和稳定性<br/>');
        $start=microtime();
        $tmp=$start;
        $type='test';
        for ($i=0; $i < $times; $i++) { 
            $token=Utils::cfile($type);
            if (!$token) {
                echo self::NO("$i:token 创建失败:$token"),'<br/>';
                continue;
            }

            $tokenExists=Utils::checkTokenExist($token);
            if (!$tokenExists) {
                echo self::NO("$i:token 读取失败:$token"),'<br/>';
                continue;
            }

            $url=Utils::host();
            $api = $url.'/app/Api/func.php?token='.urlencode($token).'&type='.$type;
            $data=array('type' => $type, 'index'=>$i);
            $res = zthttp_post($api,array('data'=>json_encode($data)));
            $resnew=json_decode($res,true);
            if ($resnew['result']===true) {
                // var_dump($resnew);
                $t=microtime();
                $tion=$t-$tmp;
                $tion=$tion>0?$tion:0;
                if ($tion>0.1) {
                    $tion=self::WAR($tion);
                }else{
                    $tion=self::YES($tion);
                }
                echo "$i:耗时:",$tion,'   id:',$token,'<br/>';
                $tmp=$t;
            }else{

                $tmp=microtime()-$tmp;
                echo self::NO($i.':出错 '.$token),'<br/>----start----<br/>';
                var_dump($resnew);
                echo '----end----<br/>';
            }
        }
        echo '总耗时:',microtime()-$start;
    }

    public function AutoTestSMS(){
    	// return;
        if (isset($_GET['thin']))return;
    	global $dbug,$admin;
    	echo '<hr/>>>>短信测试<br/>';
    	$phone='13800138000';
    	if ($admin)$phone='13726231321';
    	echo '发送到:',$phone,'<br/>';
        $sitename=Utils::SystemConfig('system.sitename');
        $url=Utils::host();
        $content='just a test ,'.$sitename.':'.$url.', rand:'.uniqid().$_GET['smstext'];
        echo 'content:',$content,'<br/>';
        $res=Utils::smsSend($phone, $content);
		if ($res['result']===true) {
			echo self::YES('成功'),'<br/>';
			if($dbug){
				echo ' >路径',var_dump($res),'<br/>';
			}
		}else{
			echo self::NO('失败'),'<br/>';
			echo ' >路径:',var_dump($res).'<br/>';
		}
    }

    public function AutoTestPassWordSigin(){
        echo '<hr/>>>>加密算法检测<br/>';
        $ZUser=dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/include/classes/';
        $res=import('ZUser',$ZUser);
        if ($res) {
            $user=new ZUser();
            $pwd='123';
            if ($user->GenPassword($pwd)==Utils::UserPassword($pwd)) {
                echo self::YES('用户密码加密算法 - 验证通过');
            }else{
                echo self::iDie('用户密码加密算法或密钥不一致,这将导致无法登录等一系列问题');
            }
        }else{
            echo self::NO('加载ZUser.class.php失败');
        }
        echo '<br/>';
        $ZPartner=dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/include/classes/';
        $res=import('ZPartner',$ZPartner);
        if ($res) {
            $partner=new ZPartner();
            $pwd='123';
            if ($partner->GenPassword($pwd)==Utils::ParPassword($pwd)) {
                echo self::YES('商户密码加密算法 - 验证通过');
            }else{
                echo self::iDie('商户密码加密算法或密钥不一致,这将导致无法登录等一系列问题');
            }
        }else{
            echo self::NO('加载ZPartner.class.php失败');
        }
    }

    public function AutoTestPayState(){
    	echo '<hr/>>>>支付状态<br/>';
    	echo '财付通:',C('openTenpay')?'	开启':'	关闭','<br/>';
    	if (C('openTenpay')) {
	    	$tenpay=Utils::getTenpay();
	    	if (trim($tenpay['mid'])&&trim($tenpay['sec'])) {
				echo '当前Key:',Utils::safeDisplayKey($tenpay['mid'],false),'<br/>';
				echo '当前Sec:',Utils::safeDisplayKey($tenpay['sec'],false),'<br/>';
	    	}else{
	    		self::iDie('支付key或sec未设置，请到Api/Conf/aonfig.php下填写');
	    	}
    	}
    	echo '------------<br/>';
    	$alipay_partner=C('alipay_partner');
    	$alipay_seller=C('alipay_seller');
    	echo '支付宝:',($alipay_partner&&$alipay_seller)?'	开启':'	关闭','<br/>';
    	if ($alipay_partner&&$alipay_seller){
				echo '当前ID:',Utils::safeDisplayKey($alipay_partner,false),'<br/>';
				echo '当前帐户:',Utils::safeDisplayKey($alipay_seller,false),'<br/>';
    	}else{
    		self::NO('支付ID或帐户未设置，请到Api/Conf/aonfig.php下填写');
    	}
        echo '------------<br/>';
        $umspay_merchantId=C('umspay_merchantId');
        $umspay_termId=C('umspay_termId');
        echo '银联:',($umspay_merchantId&&$umspay_termId)?' 开启':'   关闭','<br/>';
        if ($umspay_merchantId&&$umspay_termId){
                echo '当前ID:',Utils::safeDisplayKey($umspay_merchantId,false),'<br/>';
                echo '当前帐户:',Utils::safeDisplayKey($umspay_termId,false),'<br/>';
        }else{
            self::NO('支付商户号或终端号未设置，请到Api/Conf/aonfig.php下填写');
        }
    }

    public function AutoTestAlipayChain(){
    	if (!C('alipay_partner')||!C('alipay_partner')) {
    		return;
    	}
    	echo '<hr/>>>>支付宝证书检测','(证书生成辅助工具',"<a target='_blank' href='http://t.cn/8k7tIiw'>http://t.cn/8k7tIiw</a>",')<br>';

    	echo self::WAR('注意：公钥私钥需要完全配对，此检测只能检测格式问题，而不能检测配对问题，所以这里检测没问题不代表支付就能正常'),'<br/>';
    	$priKey=trim(C('PATH_ALI_RSA_PRI_PEM'));
    	echo '商家私钥<br/>';
    	if (!$priKey) {
    		self::iDie('商家私钥不存在，请到Api/Conf/AliConfig.php中填写PATH_ALI_RSA_PRI_PEM项');
    	}else{
	        $res = openssl_get_privatekey($priKey);
	        if (!$res) {
				self::iDie(' >格式错误，请到Api/Conf/AliConfig.php中仔细阅读说明');
	        }else{
	        	echo self::YES(' >格式正常<br/>');
	        }
    	}
		echo '支付宝公钥<br/>';
		$pubKey=trim(C('PATH_ALI_PUB_PEM'));
    	if (!$pubKey) {
    		self::iDie('支付宝公钥不存在，请到Api/Conf/AliConfig.php中填写PATH_ALI_PUB_PEM项');
    	}else{
	        $res = openssl_get_publickey($pubKey);
	        if (!$res) {
	            self::iDie(' >格式错误，请到Api/Conf/AliConfig.php中仔细阅读说明');
	        }else{
	        	echo self::YES(' >格式正常<br/>');
	        }
    	}
    }

    /**
    银联创建订单测试
    */
    public function AutoTestUmspayCreateOrder(){
        if (!self::TestUmspayChain()) {
            return;
        }
        echo '<hr/>>>>银联下单接口测试<br/>';
        $ums=Utils::buildUmspay();
        if (!$ums) {
            self::NO(' >对象初始化失败');
            return false;
        }
        $ums->logDebug=true;
        $cont=$ums->order('最土测试商品ABC',1,'zttext-pay-x'.time());
        if ($cont) {
            echo self::YES(' >下单成功'),'<br/>';
            $cont=$ums->queryOrder($cont['MerOrderId'],$cont['TransId']);
            // pr($cont);
            if ($cont) {
                echo self::YES(' >订单查询成功'),'<br/>';
            }
        }else{
            echo self::NO(' >下单失败'),'<br/>';
        }
    }


    public function TestUmspayChain(){
        if (!C('umspay_merchantId')||!C('umspay_termId')) {
            return false;
        }
        echo '<hr/>>>>银联证书检测<br/>';
        if (!function_exists('openssl_get_privatekey')) {
            echo self::iDie('openssl_get_privatekey 方法不存在');
        }else if(!function_exists('openssl_get_publickey')){
            echo self::NO('openssl_get_publickey 方法不存在');
        }

        echo self::WAR('注意：公钥私钥需要完全配对，此检测只能检测格式问题，而不能检测配对问题，所以这里检测没问题不代表支付就能正常'),'<br/>';
        $priKey=trim(C('PATH_UMS_RSA_PRI_PEM'));
        echo '商家私钥<br/>';
        if (!$priKey) {
            self::iDie('商家私钥不存在，请到Api/Conf/UmsConfig.php中填写PATH_UMS_RSA_PRI_PEM项');
            return false;
        }else{
            $res = openssl_get_privatekey($priKey);
            if (!$res) {
                self::iDie(' >格式错误，请到Api/Conf/UmsConfig.php中仔细阅读说明');
                return false;
            }else{
                echo self::YES(' >格式正常<br/>');
            }
        }
        echo '支付宝公钥<br/>';
        $pubKey=trim(C('PATH_UMS_PUB_PEM'));
        if (!$pubKey) {
            self::iDie('银联公钥不存在，请到Api/Conf/UmsConfig.php中填写PATH_UMS_PUB_PEM项');
            return false;
        }else{
            $res = openssl_get_publickey($pubKey);
            if (!$res) {
                self::iDie(' >格式错误，请到Api/Conf/UmsConfig.php中仔细阅读说明');
                return false;
            }else{
                echo self::YES(' >格式正常<br/>');
                return true;
            }
        }
    }


    public function AutoTestLogs(){
        global $dbug,$admin;
    	echo '<hr/>>>>异常日志<br/>';
        echo self::WAR('注意:此项只会显示当天日志，如果想立即删除，可以尝试手动清理Api/Runtime/Logs'),'<br/>';

        if($admin){
            $logs=Utils::rlog('PAYSUCC');
            if ($logs)echo '支付-成功<br/>';
            foreach($logs as $line){
                echo self::YES($line);
            }
            $logs=Utils::rlog('SMSERR');
            if ($logs)echo '短信-失败<br/>';
            foreach($logs as $line){
                echo self::NO($line);
            }
            
            $logs=Utils::rlog('SMSSUCC');
            if ($logs)echo '短信-成功<br/>';
            foreach($logs as $line){
                echo self::YES($line);
            }
        }else{
            $logs=Utils::rlog('PAYERR');
            if ($logs)echo '支付-失败<br/>';
            foreach($logs as $line){
                echo self::NO($line);
            }

            $logs=Utils::rlog('SMSERR');
            if ($logs)echo '短信-失败<br/>';
            foreach($logs as $line){
                echo self::NO($line);
            }
        }
    }

    

    private function AutoTestRegiste(){
    	echo '<hr/>>>>用户测试<br/>';
    	//往session写入验证码
    	$user=new UserEngine();
		
		$vcode=rand(C('MobileVerifyCodeRangeMin'),C('MobileVerifyCodeRangeMax'));

		$info['tell']=12345678901;
		$info['code']=$vcode;
		$_SESSION['MOBILE_VERIFY']=$info;

    	$name='Test_'.Utils::RandChars(15);
    	$email=$name.'@test.com';
    	$pwd=Utils::RandChars(15);
    	echo '尝试注册 name：',$name,'  email:',$email,'<br/>';
		// $vcode=1;
    	$res=$user->register($name, $vcode, $email, $pwd);
        $res=intval($res);
    	if ($res>0) {
    		echo self::YES('>注册正常'),'<br/>';
    		$login=$user->login($name, $pwd);
    		if ($login) {
    			echo self::YES('>登录正常'),' UID:',$user->userId(),'<br/>';
    		}else{
    			echo self::NO('>登录异常'),'<br/>';
    			echo '>错误信息：',var_dump($user->getError());
    		}
    		//清理测试用户
    		$db=M('user');
    		$map['email']=$email;
            $map['id']=$user->userId();
    		$map['username']=$name;
    		$res=$db->where($map)->limit(1)->delete();
            $user->logout();
    		if ($res) {
    			echo self::YES('>删除测试用户');
    		}else{
    			echo self::NO('>删除测试用户失败');
    		}
    	}else{
    		echo self::NO('注册失败'),'<br/>';
    		echo '>错误信息：',var_dump($user->getError());
    		echo '>验证码：',var_dump($info);
    	}
    }


    public function AutoTestPartnerLogin(){
    	echo '<hr/>>>>商户登录测试<br/>';
        //一般无需测试此项
    	$name='';//商户登录名,
    	$pwd='';//登录密码
    	if (!$name||!$pwd) {
    		$text='如需进行此项测试,请在Api/Lib/Action/TestAction.class.php '.(__LINE__-3).'行填写登录名与密码';
    		echo self::WAR($text),'<br/>';
    		return;
    	}
    	$partner=new PartnerEngine();
		$res=$partner->login($name,$pwd);
		if ($res) {
			echo self::YES('>登录正常'),'<br/>';
		}else{
			echo self::NO('>登录异常'),'<br/>';
			echo '>错误信息：',var_dump($partner->getError());
		}
		$partner->logout();
    }

    public function AutoTestPing(){
        echo '<hr/>>>>Ping主机测试<br/>';
        echo self::WAR('注意：如果每次普遍耗时过长，例如超过1秒，可能你的服务配置有问题,可能造成无法发短信、支付丢单等问题<br/>');
        $url=I('url');
        $url=$url?$url:Utils::host();
        $urls=explode('://',$url);
        if(count($urls)<2)$urls[1]=$url;
        $count=3;
        echo 'host: ',$url,'<br/>';
        $url=explode('/',$urls[1]);
        $url=$url[0];
        
        echo 'fsockopen:',$url,'<br/>';
        $re=array();
        for($i=0;$i<$count;$i++){
            $time=Utils::pingDomain($url);   
            $re[]=$i.'.time='.$time.'ms';
        }

        var_dump(array_diff($re,array('')));
    }


    private function iDie($text){
    	global $admin;
    	echo self::NO($text).'<br/>';
    	if (!$admin){
            echo '</body></html>';
            die();
        }
    }

    // public function AutoTestGoodList(){
    // 	echo '<hr/>>>>接口注册测试<br/>';
    // }
    // public function itest(){
    //     $a=array();
    //     $res=TuanEngine::fillExpressInfo($a);
    //     var_dump($a);
    // }
}