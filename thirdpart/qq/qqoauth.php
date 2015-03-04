<?php

require_once 'urlUtils.php';
/**
 * QQ登录
 * @author Kevin
 *
 */
class QQ {

	const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
	const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
	const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
	const GET_INFO_URL = "https://graph.qq.com/user/get_user_info";
	
	public $appid;
	public $redirect_uri;
	public $scope;
	public $state;
	const SCOPE_USERINFO = 'get_user_info';
	public $utils;
	
	
	function __construct(){
		$this->scope = self::SCOPE_USERINFO;
		$this->utils = new urlUtils();
	}
	function getForwardUrl() {
		$params = array (
			'client_id' => $this->appid ,
			'redirect_uri'=>$this->redirect_uri,
			'response_type' => 'code',
			'scope'=>$this->scope,
			'state'=>$this->state
		);
		
		$url = $this->utils->combineURL(self::GET_AUTH_CODE_URL, $params);
		
		return $url;
	}
	
	function setAppid($appid){
		$this->appid = $appid;
	}
	function setRedirectUri($redirectUri){
		$this->redirect_uri = urlencode($redirectUri);
	}
	
	
	function login(){
		$this->appid = QQ_KEY;
		$this->scope = self::SCOPE_USERINFO;
		$this->redirect_uri = urlencode(QQ_CALLBACK);
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
				'client_id' => QQ_KEY,
				'client_secret'=>QQ_SEC,
				'code' => $code,
				'grant_type'=>'authorization_code',
				'redirect_uri'=>urlencode(QQ_CALLBACK)
		);
		
		
		$data = $this->utils->get(self::GET_ACCESS_TOKEN_URL, $params);
		$data = $this->convert2Array($data);
		
		$_SESSION['access_token'] = $data['access_token'];
	}
	
	function get_openid(){
		
	}
	
	/**
	 * convert url params to array
	 * @param unknown $params
	 * @return multitype:Ambigous <>
	 */
	function convert2Array($params){
		$newData = explode('&', $params);
		$return = array();
		foreach ($newData as $str){
			$tmp = explode('=', $str);
			$return[$tmp[0]] = $tmp[1];
		}
		return $return;
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
			);
		}
		else {
			$params = array (
				'access_token' => $_SESSION['access_token'] ,
			);
		}
		
		$openIdData = $this->utils->get(self::GET_OPENID_URL, $params);
		$openIdData = $this->simple_json_parser($openIdData);
		$userParams = array();
		$userParams = array_merge($userParams,$params);
		$userParams['oauth_consumer_key'] = $openIdData->client_id;
		$userParams['openid'] = $openIdData->openid;
		$_SESSION['openid'] = $openIdData->openid;
		$userinfo = $this->utils->get(self::GET_INFO_URL, $userParams);
		return $userinfo;
	}
	
	//简单实现json到php数组转换功能
	private function simple_json_parser($json){
		$leftQ = strpos($json, '{');
		$rightQ = strpos($json, '}');
		$json = substr($json, $leftQ,$rightQ - $leftQ+1);
		$json = json_decode($json);
		return $json;
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
		$type = 'qq';
		$name = $type.'_'.$userinfo->nickname;
		
		$sns = "qq:{$id}";
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
					'email' => "{$prompt_name}@qq.edaole.com.cn",
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