<?php
ini_set("display_errors", "On");
date_default_timezone_set("PRC");

/**国家省份城市地区**/
define('COUNTRY_TYPE',            0); // 国家
define('PROVINCE_TYPE',           1); // 省份
define('CITY_TYPE',               2); // 城市
define('DISTRICT_TYPE',            3); // 地区

/* 订单状态 */
define('OS_UNCONFIRMED',            0); // 未确认
define('OS_CONFIRMED',              1); // 已确认
define('OS_CANCELED',               2); // 已取消
define('OS_INVALID',                3); // 无效
define('OS_RETURNED',               4); // 退货
define('OS_SPLITED',                5); // 已分单
define('OS_SPLITING_PART',          6); // 部分分单

/* 配送状态 */
define('SS_UNSHIPPED',              0); // 未发货
define('SS_SHIPPED',                1); // 已发货
define('SS_RECEIVED',               2); // 已收货
define('SS_PREPARING',              3); // 备货中
define('SS_SHIPPED_PART',           4); // 已发货(部分商品)
define('SS_SHIPPED_ING',            5); // 发货中(处理分单)

/* 支付状态 */
define('PS_UNPAYED',                0); // 未付款
define('PS_PAYING',                 1); // 付款中
define('PS_PAYED',                  2); // 已付款

$debugArr = array(
    //'SHOW_PAGE_TRACE' => true
);


$sysArr = array(
    'APP_GROUP_LIST'=>'Home,Admin',
    'DEFAULT_GROUP' =>'Home',
    'URL_CASE_INSENSITIVE'  =>    true,     // URL地址是否不区分大小写
    'APP_DEBUG'             =>    true,     // 是否开启调试模式
    
    'URL_MODEL'             =>    2,        // URL访问模式
    'LANG_SWITCH_ON'        =>    false,     // 默认关闭多语言包功能
    'LANG_AUTO_DETECT'      =>    false,    // 自动侦测语言 开启多语言功能后有效
    'TMPL_DETECT_THEME'     =>    false,    // 自动侦测模板主题
    'DATA_CACHE_TIME'	    =>    600,      // 数据缓存有效期
    'DATA_CACHE_TYPE'		=>    'File',   // 数据缓存类型
    'TMPL_CACHE_ON'         =>    true,    // 默认开启模板编译缓存 false 的话每次都重新编译模板
    'ACTION_CACHE_ON'       =>    true,    // 默认关闭Action 缓存
    'HTML_CACHE_ON'         =>    true,    // 默认关闭静态缓存
    'TMPL_STRIP_SPACE'      =>    false,   // 是否去除模板文件里面的html空格与换行
	'MAIL_ADDRESS'          =>    '2380909990@qq.com', // 邮箱地址
	'MAIL_SMTP'             =>    'smtp.qq.com', // 邮箱SMTP服务器
	'MAIL_LOGINNAME'        =>    '2380909990@qq.com', // 邮箱登录帐号
	'MAIL_PASSWORD'         =>    'jusou4567891', // 邮箱密码
    'MAIL_PORT'             =>     465, //smtp 端口号
    'MAIL_NICKNAME'         =>     'IT追梦人', //
    'PASSWORD_IS_SALT'               =>     true,  //'密码进行加盐处理'
    'UPLOAD_MAX_SIZE'       =>      '3145728',
);

$databaseArr = array(
    'DB_TYPE'=>'pdo',
    'DB_HOST'=>'127.0.0.1',
    'DB_NAME'=>'jsshop',
    'DB_USER'=>'root',
    'DB_PWD'=>'root',
    'DB_PORT'=>'3306',
    //'DB_PREFIX'=>'js_',
    'DB_DSN'=>'mysql:host=localhost;dbname=jsshop;'
);

$aliPayConfig = array(
    'aliPayConfig' => array(
        'partner' =>'',

        'key'=>'',

        'sign_type'=>strtoupper('MD5'),

        'input_charset'=> strtolower('utf-8'),

        'transport'=> 'http',

        //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
        'seller_email'=>'',

        //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
        'notify_url'=>'http://my.jsshop.com/Pay/notifyUrl',

        //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
        'return_url'=>'http://my.jsshop.com/works/lvsenshop/Pay/returnUrl'
    )
);


return array_merge($sysArr, $databaseArr, $aliPayConfig);
