<?php

require_once 'urlUtils.php';
/**
 * 微信登录
 * @author Kevin
 *
 */
class Wechat {
		
	const GET_AUTH_CODE_URL = 'https://open.weixin.qq.com/connect/qrconnect';//"https://open.weixin.qq.com/connect/qrconnect";
	const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
	const GET_USERINFO_URL = "https://api.weixin.qq.com/sns/userinfo";
	
	public $appid;
	public $redirect_uri;
	public $scope;
	public $state;
	const SCOPE_LOGIN = 'snsapi_login';
	const SCOPE_USERINFO = 'snsapi_userinfo';
	public $utils;
	
	
	function __construct(){
		$this->scope = self::SCOPE_LOGIN;
		$this->utils = new urlUtils();
	}
	function getForwardUrl() {
		$params = array (
			'appid' => $this->appid ,
			'redirect_uri'=>$this->redirect_uri,
			'response_type' => 'code',
			'scope'=>$this->scope,
			'state'=>$this->state
		);
		
		$url = $this->utils->combineURL(self::GET_AUTH_CODE_URL, $params,'#wechat_redirect');
		
		return $url;
	}
	
	function setAppid($appid){
		$this->appid = $appid;
	}
	function setRedirectUri($redirectUri){
		$this->redirect_uri = urlencode($redirectUri);
	}
	
	
	function login(){
		$this->appid = WECHAT_KEY;
		$this->scope = self::SCOPE_LOGIN;
		$this->redirect_uri = urlencode(WECHAT_CALLBACK);
		//-------生成唯一随机串防CSRF攻击
		$state = md5(uniqid(rand(), TRUE));
		$this->state = $state;
		$_SESSION['state'] = $state;
		$login_url =  $this->getForwardUrl();
		
		header("Location:$login_url");
	}
	
	function callback($type=''){
		$code = $_GET['code'];
		$state = $_GET['state'];
		
		if (!$this->validateState($state)) {
			return false;
		}
		
		$params = array (
				'appid' => WECHAT_KEY,
				'secret'=>WECHAT_SEC,
				'code' => $code,
				'grant_type'=>'authorization_code'
		);
		
		
		$data = $this->utils->get(self::GET_ACCESS_TOKEN_URL, $params);
		$return = json_decode($data);
		
		$_SESSION['access_token'] = $return->access_token;
		$_SESSION['openid'] = $return->openid;
	}
	
	function get_openid(){
		
	}
	
	/**
	 * 授权验证
	 * @param unknown $state
	 * @return boolean
	 */
	function validateState($state) {
		$stateInSession = isset ( $_SESSION ['state'] ) ? $_SESSION ['state'] : '';
		return $state == $stateInSession ? true : false;
	}
	
	function getUserInfo($data=''){
		if ($data) {
			$params = array (
				'access_token' =>$data->access_token ,
				'openid'=>$data->openid
			);
		}
		else {
			$params = array (
				'access_token' => $_SESSION['access_token'] ,
				'openid'=>$_SESSION['openid']
			);
		}
		$userInfo = $this->utils->get(self::GET_USERINFO_URL, $params);
		
		return $userInfo;
	}
	
	function app_callback(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state'];
	
		$params = array (
				'appid' => WECHAT_APP_KEY,
				'secret'=>WECHAT_APP_SEC,
				'code' => $code,
				'grant_type'=>'authorization_code'
		);
	
	
		$data = $this->utils->get(self::GET_ACCESS_TOKEN_URL, $params);
		
		$return = json_decode($data);
		$userinfo = $this->getUserInfo($return);
		
		$returnUser =  $this->createUserForApp($userinfo);
		echo  $returnUser;
		exit();
	}
	
	function createUserForApp($userinfo){
		$userinfo= json_decode($userinfo);
		$id = $userinfo->unionid;
		$type = 'wx';
		$name = $type.'_'.$userinfo->nickname;
		
		$sns = "wx:{$id}";
		$exist_user = Table::Fetch('user', $sns, 'sns');
		if ( $exist_user ) {
			Session::Set('user_id', $exist_user['id']);
			$result = $exist_user;
		}
		else {
		
			$prompt_name = $name;
			$exist_user = Table::Fetch('user', $prompt_name, 'username');
			while(!empty($exist_user)) {
				$prompt_name = $name .'_' . rand(100,999);
				$exist_user = Table::Fetch('user', $prompt_name, 'username');
			}
			
			$new_user = array(
					'username' => $prompt_name,
					'realname' => $userinfo->nickname,
					'email' => "{$prompt_name}@wx.edaole.com.cn",
					'password' => rand(10000000,99999999),
					'gender' => $userinfo->sex==1?'M':'F',
					'sns' => $sns,
			);
			
			$user_id = ZUser::Create($new_user, true);
			Session::Set('user_id', $user_id);
			$result = $exist_user = Table::Fetch('user', $sns, 'sns');
		}
		$returnData = array();
		if ($result) {
			$returnData['status'] = 1;
			$returnData['result'] = $result;
		}
		else {
			$returnData['status'] = 0;
			$returnData['result'] = null;
		}
		
		return   json_encode($returnData);
		
	}
}