<?php

class PagesAction extends CommonAction {
    

    function index(){
        if (!APP_DEBUG)return;
        header('Content-Type:text/html; charset=utf-8');
        echo '<html>';
        echo '<body>';
        echo '<pre/>';
        echo '新页面入口:<br/>';
        echo '|<br/>';
        echo '|-<a href="'.__APP__.'?s=Pages/help&help">/help&help</a> ：帮助页面（帮助）<br/>';
        echo '|  |-<a href="'.__APP__.'?s=Pages/help">/help</a> ：帮助页面<br/>';
        echo '|<br/>';
        echo '</body>';
        echo '</html>';
    }


    public function help(){

    	$this->hinfo='
		/**
        *团购帮助页面入口（可自动识别平台）	
        *	
        *请求方式:GET
        *@return html 操作结果
                        */';

		$this->_help();
		
		$res['siteName']=Utils::siteName();
		$res['helpPhone']=C('SystemHelpPhone');
		
		$this->_autoShowPage(__FUNCTION__,$res);
    }

}
