<?php

namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        if(!$this->checkLogin()){
            $this->display('Login/login');
        }else{
            $this->display('Index/index');
        }
    }

    private function checkLogin(){
    	if(is_null(session('uid'))){
    		return false;
    	}else{
    		return true;
    	}
    }

 }