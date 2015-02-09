<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$daytime = time();
$condition = array( 
		'city_id' => array(0, abs(intval($city['id']))),
		"begin_time <  {$daytime}",
		"end_time > {$daytime}",
		);

$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10, true);
$teams = DB::LimitQuery('team', array(
			'condition' => $condition,
			'order' => 'ORDER BY begin_time DESC, id DESC',
			'size' => $pagesize,
			'offset' => $offset,
			));
	
$partner = Table::Fetch('partner', $team['partner_id']); //调用商家信息
$sssss = $partner['title'];
foreach($teams AS $id=>$one){
	team_state($one);
	if ($one['state']=='none') $one['picclass'] = 'isopen';
	if ($one['state']=='soldout') $one['picclass'] = 'soldout';
	$teams[$id] = $one;
}
function ssss($img){
           $partner = Table::Fetch('partner', $img); //调用商家信息

            return $partner['title'];

	}
function utf8_unicode($name){
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
	function imgst($img){
           $img = str_replace("index", "json",$img);
		   $str = $img;
            return $str;

	}
include template('index_json');
