<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$id = abs(intval($_GET['id']));
if (!$id || !$team = Table::FetchForce('team', $id) ) {
	redirect('index.php');
}

$discount_price = $team['market_price'] - $team['team_price'];

$partner = Table::Fetch('partner', $team['partner_id']); //调用商家信息

$team['partner_title'] = $partner['title'];
$team['partner_phone'] = $partner['phone'];
$team['partner_address'] = $partner['address'];
$team['longlat'] = $partner['longlat'];
if ($team['longlat']) list($longi,$lati) = preg_split('/[,\s]+/',$team['longlat'],-1,PREG_SPLIT_NO_EMPTY);


$left = array();
$now = time();
$diff_time = $left_time = $team['end_time']-$now;

$left_day = floor($diff_time/86400);
$left_time = $left_time % 86400;
$left_hour = floor($left_time/3600);
$left_time = $left_time % 3600;
$left_minute = floor($left_time/60);
$left_time = $left_time % 60;
function utf8_unicode($name){
	$name=strip_tags($name);
        $name = iconv('UTF-8', 'UCS-2', $name);
		
        $len  = strlen($name);
        $str  = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2){
            $c  = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0){   //两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
                //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
                //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
       // $str = strtoupper($str);//转换为大写
	   

        return $str;
    }
function utf8_unicode2($name){

        $name = iconv('UTF-8', 'UCS-2', $name);
		
        $len  = strlen($name);
        $str  = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2){
            $c  = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0){   //两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
                //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
                //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
       // $str = strtoupper($str);//转换为大写
	   

        return $str;
    }
team_state($team);
include template('team_json');
