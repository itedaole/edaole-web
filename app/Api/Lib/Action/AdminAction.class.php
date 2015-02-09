<?php

class AdminAction extends CommonAction {
    
    // getClinetIp

    // function index(){
    //     if (!$this->isLogin()) {
    //         header("location: ?s=Admin/login");
    //     }else{
    //         $fun=I('p');
    //         if ($fun) {
    //             if (!str_prefix($fun,'_')) {
    //             $fun='_'.$fun;
    //             }
    //             if(fun_isPublic(__CLASS__,$fun)){
    //                 $this->$fun();    
    //             }
    //         }else{
    //            $this->display(); 
    //         }
    //     }
    // }

    // protected function isLogin(){
    //     return $_SESSION['admin_id']?true:false;
    // }

    // function login(){
    //     if ($this->isLogin()) {
    //         header("location: ?s=Admin");
    //     }else{
    //         $this->display();
    //     }
    // }

    // public function _fun($_){
    //     if ($_[0]==='_') {
    //         return substr($_, 1);
    //     }
    //     return $_;
    // }

    // public function _dev(){


    // }


    // public function _index(){
    //    header("location: ?s=Admin/index"); 
    // }

    // public function _settings(){
    //     $tpl = $this->_fun(__FUNCTION__);

    //     $this->display($tpl);
    // }

}
