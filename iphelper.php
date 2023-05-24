<?php


function get_detail_address()
{
	$key = 'sWkz5OPjBb9AaSQ7RNGKieTX';
	$callback = 'renderReverse';

	//$location = '43.89833761,125.31364243';

	$ip = get_real_ip();

	$ip_url = 'http://api.map.baidu.com/location/ip?ak=' . $key . '&ip=' . $ip . '&coor=bd09ll';

	$ip_content = file_get_contents($ip_url);

	$ip_info = json_decode($ip_content);

	$location_x = $ip_info->content->point->x;

	$location_y = $ip_info->content->point->y;

	$location = $location_y . ',' . $location_x;

	$location_url = 'http://api.map.baidu.com/geocoder/v2/?ak=' . $key . '&callback=' . $callback . '&location=' . $location . '=&output=json&pois=0';

	$location_content = file_get_contents($location_url);

	$location_content = trim($location_content,'renderReverse&&renderReverse');
	$location_content = trim($location_content,'(');
	$location_content = trim($location_content,')');

	$location_info = json_decode($location_content);

	if($location_info->status==0)
	{
		return $location_info;
	}
	else
	{
		return -1;
	}
}



function get_real_ip() {
    $ip = false;
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = FALSE;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!eregi("^(10|172.16|192.168).", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}
