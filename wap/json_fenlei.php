<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
$group_id = abs(intval($_GET['group_id']));
$cid = abs(intval($_GET['city_id']));
$pid = abs(intval($_GET['pid']));
$sid = abs(intval($_GET['sid']));
$oid = abs(intval($_GET['oid']));
$did = abs(intval($_GET['did']));
$ncon = trim(strval($_GET['ncon']));
$daytime = strtotime(date('Y-m-d'));
$condition = array(
	'team_type' => 'normal',
	"begin_time <= '{$daytime}'",
	"end_time > '{$daytime}'",
);
//今日新品判断
if($ncon == 'new'){
	$condition[] = "begin_time >= '{$daytime}'";
}
//项目类型判断
switch($pid){
	case '1': $condition[] = 'team_price < 50';
		break;
	case '2': $condition[] = 'team_price >= 50 && team_price < 100';
		break;
	case '3': $condition[] = 'team_price >= 100 && team_price < 300';
		break;
	case '4': $condition[] = 'team_price >= 300 && team_price < 500';
		break;
	case '5': $condition[] = 'team_price >= 500';
		break;
	default: ;
}
//排序类型选择
switch($oid){
	case '1':
		$esc = ($did==0) ? 'DESC':'ASC';
		$order = "begin_time {$esc},";
		break;
	case '2':
		$esc = ($did==0) ? 'DESC':'ASC';
		$order = "now_number {$esc},";
		break;
	case '3':
		$esc = ($did==0) ? 'ASC':'DESC';
		$order = "team_price {$esc},";
		break;
	case '4':
		$esc = ($did==0) ? 'ASC':'DESC';
		$order = "team_price/market_price {$esc},";
		break;
	default:
		$order = "sort_order DESC,";
}
if($sid) $condition['sub_id'] = $sid;
if($cid) $condition[] = "((city_ids like '%@{$cid}@%' or city_ids like '%@0@%') or city_id in(0,{$cid}))";
if ($group_id) $condition['group_id'] = $group_id;

$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);
$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY ' . $order . ' id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));
//判断是否卖光了
foreach($teams AS $id=>$one){
	team_state($one);
	if (!$one['close_time']) $one['picclass'] = 'isopen';
	if ($one['state']=='soldout') $one['picclass'] = 'soldout';
	$teams[$id] = $one;
}

$pagetitle = '团购大全';
include template('json_fenlei');

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
function sort_links($id='0', $city_id='0', $pid='0', $sid='0', $oid='0', $did='0') {
	switch ($oid) {
		//发布时间
	case 1 :
		$dclass = ($did=='0') ? 'on paixu_3':'on paixu_2';
		$did = ($did=='0') ? '1':'0';
		$result = "<a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=0&did={$did}\">默认</a>" .
				"<a class=\"{$dclass}\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=1&did={$did}\">发布时间</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=2&did={$did}\">销量</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=3&did={$did}\">价格</a>" .
//				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$did}\">好评率</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=4&did={$did}\">折扣</a> ";
		break;
		//销量
	case 2 :
		$dclass = ($did=='0') ? 'on paixu_3':'on paixu_2';
		$did = ($did=='0') ? '1':'0';
		$result = "<a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=0&did={$did}\">默认</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=1&did={$did}\">发布时间</a>" .
				"<a class=\"{$dclass}\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=2&did={$did}\">销量</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=3&did={$did}\">价格</a>" .
//				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$did}\">好评率</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=4&did={$did}\">折扣</a> ";
		break;
		//价格
	case 3 :
		$dclass = ($did=='0') ? 'on paixu_2':'on paixu_3';
		$did = ($did=='0') ? '1':'0';
		$result = "<a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=0&did={$did}\">默认</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=1&did={$did}\">发布时间</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=2&did={$did}\">销量</a>" .
				"<a class=\"{$dclass}\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=3&did={$did}\">价格</a>" .
//				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$did}\">好评率</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=4&did={$did}\">折扣</a> ";
		break;
		//折扣
	case 4 :
		$dclass = ($did=='0') ? 'on paixu_3':'on paixu_2';
		$did = ($did=='0') ? '1':'0';
		$result = "<a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=0&did={$did}\">默认</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=1&did={$did}\">发布时间</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=2&did={$did}\">销量</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=3&did={$did}\">价格</a>" .
//				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$did}\">好评率</a>" .
				"<a class=\"{$dclass}\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=4&did={$did}\">折扣</a> ";
		break;
	default :
		$result = "<a class=\"on\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=0&did={$did}\">默认</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=1&did={$did}\">发布时间</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=2&did={$did}\">销量</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=3&did={$did}\">价格</a>" .
//				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$did}\">好评率</a>" .
				"<a class=\"paixu_1\" href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid={$sid}&oid=4&did={$did}\">折扣</a> ";
	}
	return $result;
}


function current_teamcategory_new($group_id='0', $city_id='0', $pid='0', $sid='0', $oid='0', $direction='0', $ncon='') {
	$daytime = strtotime(date('Y-m-d'));
	$condition = array(
		'team_type' => 'normal',
		"begin_time <= '{$daytime}'",
		"end_time > '{$daytime}'",
	);
	if($city_id) $condition[] = "((city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or city_id in(0,{$city_id}))";
	if($sid) $condition['sub_id'] = $sid;
	if($pid) $condition[] = getprice($pid);
	$team_all = Table::Count('team', $condition);
	$newcon = $condition;
	$newcon[] = "begin_time >= '{$daytime}'";
	$team_new = Table::Count('team', $newcon);
//	$a = array(
//			"/team/indexTeam.php?city_id={$city_id}&pid={$pid}&sid={$sid}" => "所有({$team_all})",
//			);
	if($group_id=='0' && $ncon!='new') { $a =  " <a href=\"/team/indexTeam.php?city_id={$city_id}&pid={$pid}&sid=0&oid={$oid}&did={$direction}\" class=\"r_xz\">所有({$team_all})</a> "; }
	else { $a =  " <a href=\"/team/indexTeam.php?city_id={$city_id}&pid={$pid}&sid=0&oid={$oid}&did={$direction}\">所有({$team_all})</a> "; }
	if($ncon=='new') { $a .=  " <a href=\"/team/indexTeam.php?city_id={$city_id}&pid={$pid}&sid=0&oid={$oid}&did={$direction}&ncon=new\" class=\"r_xz\">今日新单({$team_new})</a> "; }
	else { $a .=  " <a href=\"/team/indexTeam.php?city_id={$city_id}&pid={$pid}&sid=0&oid={$oid}&did={$direction}&ncon=new\">今日新单({$team_new})</a> "; }
    $categorys = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => 'group','fid' => '0','display' => 'Y' ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
	));
	$categorys = Utility::OptionArray($categorys, 'id', 'name');
	foreach($categorys AS $id=>$name) {
		if ($id) $condition['group_id'] = $id;
		$team_num = Table::Count('team', $condition);
		//$a["/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}"] = $name . "({$team_num})";
		if($id==$group_id) { $a .=  " <a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid=0&oid={$oid}&did={$direction}\" class=\"r_xz\">$name({$team_num})</a> "; }
		else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}&sid=0&oid={$oid}&did={$direction}\">$name({$team_num})</a> "; }
	}
//	$l = "/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}";
//	if (!$group_id) $l = "/team/indexTeam.php?city_id={$city_id}&pid={$pid}";
	//return current_link_new($l, $a);
	return $a;
}

function current_price_block($pid='0', $group_id='0', $city_id='0', $sid='0', $oid='0', $direction='0') {
	$daytime = strtotime(date('Y-m-d'));
	$condition = array(
		'team_type' => 'normal',
		"begin_time <= '{$daytime}'",
		"end_time > '{$daytime}'",
	);
	if($city_id) $condition[] = "((city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or city_id in(0,{$city_id}))";
	if ($group_id) $condition['group_id'] = $group_id;
	$team_all = Table::Count('team', $condition);
//	$a = array(
//			"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}" => "所有({$team_all})",
//			);
	if($pid=='0') { $a .=  " <a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">所有({$team_all})</a> "; }
	else { $a =  " <a href=\"/team/indexTeam.php?group_id={$id}&city_id={$city_id}&sid={$sid}&oid={$oid}&did={$direction}\">所有({$team_all})</a> "; }

	$condition[100] = 'team_price < 50';
	$team_num = Table::Count('team', $condition);
	if($pid=='1') { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=1&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">50元以下({$team_num})</a> "; }
	else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=1&sid={$sid}&oid={$oid}&did={$direction}\">50元以下({$team_num})</a> "; }

	$condition[100] = 'team_price >= 50 && team_price < 100';
	$team_num = Table::Count('team', $condition);
	if($pid=='2') { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=2&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">50-100元({$team_num})</a> "; }
	else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=2&sid={$sid}&oid={$oid}&did={$direction}\">50-100元({$team_num})</a> "; }

	$condition[100] = 'team_price >= 100 && team_price < 300';
	$team_num = Table::Count('team', $condition);
	if($pid=='3') { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=3&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">100-300元({$team_num})</a> "; }
	else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=3&sid={$sid}&oid={$oid}&did={$direction}\">100-300元({$team_num})</a> "; }

	$condition[100] = 'team_price >= 300 && team_price < 500';
	$team_num = Table::Count('team', $condition);
	if($pid=='4') { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=4&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">300-500元({$team_num})</a> "; }
	else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=4&sid={$sid}&oid={$oid}&did={$direction}\">300-500元({$team_num})</a> "; }

	$condition[100] = 'team_price >= 500';
	$team_num = Table::Count('team', $condition);
	if($pid=='5') { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=5&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">500元以上({$team_num})</a> "; }
	else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid=5&sid={$sid}&oid={$oid}&did={$direction}\">500元以上({$team_num})</a> "; }
//	$l = "/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}";
//	if (!$pid) $l = "/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&sid={$sid}";
//	return current_link_new($l, $a);
	return $a;
}

function current_teamcategory_second($sid='0', $group_id='0', $city_id='0', $pid='0', $oid='0', $direction='0') {
	$daytime = strtotime(date('Y-m-d'));
	$condition = array(
		'team_type' => 'normal',
		"begin_time <= '{$daytime}'",
		"end_time > '{$daytime}'",
	);
	if($city_id) $condition[] = "((city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or city_id in(0,{$city_id}))";
	if($pid) $condition[] = getprice($pid);
	if ($group_id) $condition['group_id'] = $group_id;
	if($group_id=='0') return;

	$team_all = Table::Count('team', $condition);
//	$a = array(
//			"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}" => "所有({$team_all})",
//			);
	if($sid=='0') { $a =  "<div class=\"fenlei_tiaojian_3\"> <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}&oid={$oid}&did={$direction}\" class=\"r_xz\">所有({$team_all})</a> "; }
	else { $a =  "<div class=\"fenlei_tiaojian_3\"> <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}&oid={$oid}&did={$direction}\">所有({$team_all})</a> "; }
	//判断有没有父亲

    $categorys = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => 'group',"fid = $group_id",'display' => 'Y' ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
	));
	if(!$categorys) return ;
	$categorys = Utility::OptionArray($categorys, 'id', 'name');
	foreach($categorys AS $id=>$name) {
		if($id) $condition['sub_id'] = $id;
		$team_num = Table::Count('team', $condition);
		//$a["/team/indexTeam.php?group_id={$id}&city_id={$city_id}&pid={$pid}"] = $name . "({$team_num})";
		if($id==$sid) { $a .=  " <a href=\"/team/indexTeam.php?sid={$id}&group_id={$group_id}&city_id={$city_id}&pid={$pid}&oid={$oid}&did={$direction}\" class=\"r_xz\">$name({$team_num})</a> "; }
		else { $a .=  " <a href=\"/team/indexTeam.php?sid={$id}&group_id={$group_id}&city_id={$city_id}&pid={$pid}&oid={$oid}&did={$direction}\">$name({$team_num})</a> "; }
	}
//	$l = "/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}";
//	if (!$group_id) $l = "/team/indexTeam.php?city_id={$city_id}&pid={$pid}";
//	return current_link_second($l, $a);
	return $a . ' </div>';
}

function current_area_new($city_id='0', $group_id='0', $pid='0', $sid='0', $oid='0', $direction='0') {
	global $city;
	$daytime = strtotime(date('Y-m-d'));
	$condition = array(
		'team_type' => 'normal',
		"begin_time <= '{$daytime}'",
		"end_time > '{$daytime}'",
	);
	if($sid) $condition['sub_id'] = $sid;
	if($pid) $condition[] = getprice($pid);
	if ($group_id) $condition['group_id'] = $group_id;
	$team_all = Table::Count('team', $condition);
//	$a = array(
//			"/team/indexTeam.php?group_id={$group_id}&pid={$pid}" => "所有({$team_all})",
//			);
	if($city_id=='0') { $a =  " <a href=\"/team/indexTeam.php?group_id={$group_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">所有({$team_all})</a> "; }
	else { $a =  " <a href=\"/team/indexTeam.php?group_id={$group_id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$direction}\">所有({$team_all})</a> "; }

    $categorys = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => 'city','fid' => '0','display' => 'Y' ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
	));
	$categorys = Utility::OptionArray($categorys, 'id', 'name');
	foreach($categorys AS $id=>$name) {
		if($id) $condition[100] = "((city_ids like '%@{$id}@%' or city_ids like '%@0@%') or city_id in(0,{$id}))";
//		$con_area = array('team_type' => 'normal', "begin_time <= '{$daytime}'", "end_time > '{$daytime}'", );
//		$con_area = "((city_ids like '%@{$id}@%' or city_ids like '%@0@%') or city_id in(0,{$id})) AND team_type = 'normal' AND begin_time <= $daytime AND end_time > $daytime ";
//		$result = DB::GetQueryResult('select count(*) as num from team where '.$con_area, true);
//		$team_num = $result['num'];
		$team_num = Table::Count('team', $condition);
//		$a["/team/indexTeam.php?group_id={$group_id}&city_id={$id}&pid={$pid}"] = $name . "({$team_num})";
		if($id==$city_id) { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$direction}\" class=\"r_xz\">$name({$team_num})</a> "; }
		else { $a .=  " <a href=\"/team/indexTeam.php?group_id={$group_id}&city_id={$id}&pid={$pid}&sid={$sid}&oid={$oid}&did={$direction}\">$name({$team_num})</a> "; }
	}
//	$l = "/team/indexTeam.php?group_id={$group_id}&city_id={$city_id}&pid={$pid}";
//	if (!$city_id) $l = "/team/indexTeam.php?group_id={$group_id}&pid={$pid}";
//	return current_link_new($l, $a);
	return $a;
}

function getprice($pid){
	switch($pid){
	case '1': return 'team_price < 50';
		break;
	case '2': return 'team_price >= 50 && team_price < 100';
		break;
	case '3': return 'team_price >= 100 && team_price < 300';
		break;
	case '4': return 'team_price >= 300 && team_price < 500';
		break;
	case '5': return 'team_price >= 500';
		break;
	default: return '';
}
}
