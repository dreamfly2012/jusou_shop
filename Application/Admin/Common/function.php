<?php

	/*
	$rule,要验证的规则名称；
	$uid,用户的id；
	$relation，规则组合方式，默认为‘or’，以上三个参数都是根据Auth的check（）函数来的，
	$t,符合规则后，执行的代码
	$f，不符合规则的，执行代码，默认为抛出字符串‘没有权限’
	*/
	function authcheck($rule,$uid,$relation='or',$t,$f=''){
	   	$auth=new Think\Auth();
        if(in_array($uid,C('AUTH_CONFIG.SUPER_ADMINISTRATOR'))){
            return $t;    //如果是，则直接返回真值，不需要进行权限验证
        }else{
            return $auth->check($rule,$uid,1,'url',$relation)?$t:$f;
        }
    }

    function getUserNameById($user_id){
    	$u = D('User');
    	$user_name = $u->getUserNameById($user_id);
    	return $user_name;
    }

    function getGoodsNameById($goods_id){
    	$g = D('Goods');
    	$goods_name = $g->getGoodsNameById($goods_id);
    	return $goods_name;
    }