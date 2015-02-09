<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
$searchName = isset($_GET['searchName'])?trim(strip_tags($_GET['searchName'])):'';
$start_time = isset($_GET['start_time'])?$_GET['start_time']:'';
$search_end_time = isset($_GET['end_time'])?$_GET['end_time']:'';
$daytime = strtotime(date('Y-m-d'));
$condition = array(
	'team_type' => 'normal',
	"begin_time <= '{$daytime}'",
	"end_time > {$daytime}",
);
if(!empty($start_time)){
$s_time = strtotime($start_time);
$condition []= "( begin_time >= '{$s_time}' )";
}
if(!empty($search_end_time)){
$e_time = strtotime($search_end_time);
$condition []= "( begin_time <= '{$e_time}' )";
}
$condition[] = "( title like '%".mysql_escape_string($searchName)."%' )";
$city_id = abs(intval($city['id']));
$condition[] = "((city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or city_id in(0,{$city_id}))";

if (!option_yes('displayfailure')) {
	$condition['OR'] = array(
		"now_number >= min_number",
		"end_time > '{$daytime}'",
	);
}

$group_id = abs(intval($_GET['gid']));
if ($group_id) $condition['group_id'] = $group_id;

$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);
$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY begin_time DESC, sort_order DESC, id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));
foreach($teams AS $id=>$one){
	team_state($one);
	if (!$one['close_time']) $one['picclass'] = 'isopen';
	if ($one['state']=='soldout') $one['picclass'] = 'soldout';
	$teams[$id] = $one;
}

$category = Table::Fetch('category', $group_id);
$pagetitle = '商品搜索';
include template('json_search');
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
function current_teamcategory($gid='0') {
	global $city;
	$a = array(
			'/team/searchGoods.php' => '全部',
			);
    $categorys = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => 'group','fid' => '0','display' => 'Y' ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
	));
	$categorys = Utility::OptionArray($categorys, 'id', 'name');
	foreach($categorys AS $id=>$name) {
		$a["/team/index.php?gid={$id}"] = $name;
	}
	$l = "/team/index.php?gid={$gid}";
	if (!$gid) $l = "/team/index.php";
	return current_link($l, $a, true);
}
