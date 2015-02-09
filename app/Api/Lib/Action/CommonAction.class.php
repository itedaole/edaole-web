<?php

define('APP_VERSION', '1.7.3c alpha1');

class CommonAction extends Action{

	protected $hinfo;

	public function _initialize(){
		//load congfig from db
		// $cfg=new DBConfigEngine;
		// $cfg->load();
	}
	
	protected function isLogin(){
		return UserEngine::isLogin();
	}	

	protected function uid(){
		return UserEngine::userId();
	}

	public function version(){
		header('Content-Type:text/html; charset=utf-8');
    	echo '版本:',APP_VERSION,'<br/>';
    	echo '框架:',THINK_VERSION,'<br/>';
	}

	//空方法
	public function _empty(){
		header('HTTP/1.1 404 Not Found');
 		echo 'Not Found:-(';	
    }

    protected function _debug($data){
		if (APP_DEBUG&&isset($_GET['debug'])) {
			header('Content-Type:text/html; charset=utf-8');
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
			exit();
		}
    }

    protected function _help(){
    	if (APP_DEBUG&&isset($_GET['help'])) {
			header('Content-Type:text/html; charset=utf-8');
			echo '<html>';
			echo '<body>';
			echo '<pre>';
			echo $this->hinfo;
			echo '</pre>';
			echo '</body>';
			echo '</html>';
			exit();
    	}
    }

    protected function out($data){
    	if (switchLogOn()){
	    	global $sqlLogs;
			$data['debug_trace']=trace();
			$data['debug_trace']['sql_trace']=$sqlLogs;
    	}
    	self::_debug($data);
    	if ($_GET['format']=='xml') {
			$this->ajaxReturn($data,'XML'); 
		}else{
			$this->ajaxReturn($data,'JSON'); 
		}
    }

    
    protected function succReturn($data,$type='arr'){
    	$tmp['status']=1;
    	if (empty($data)) {
    		if ($type=='arr') {
				$tmp['data']=array();
	    	}else{
				$tmp['data']=array('.ignore'=>'palceholder');
	    	}
    	}else{
    		$tmp['data']=$data;
    	}
    	$this->out($tmp);
    }

	protected function failReturn($err){
    	$tmp['status']=0;
    	$tmp['error']=$err;
    	$this->out($tmp);
    }

    /**
    *自动加载模板
    *page:模板名称
    *params:需要传入模板的参数
    */
    protected function _autoShowPage($page,$params=null){

		foreach ($params as $key => $value) {
			$this->assign($key,	$value);
		}

    	$prefix=isIOS()?'_ios':'_android';
		$tpls[]=$page.$prefix;
		$tpls[]=$page;
		$action=$this->getActionName();

		foreach ($tpls as $tpl) { 
			$path = dirname(dirname(dirname(dirname(__FILE__)))).substr(TMPL_PATH,1)."/$action/".$tpl.'.html';
			if(file_exists($path)){
				$content=$this->display($tpl);
				return;
			}		
		}
		echo "page \"$page\" not found -_-";
    }


}
?>