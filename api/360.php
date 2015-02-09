<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
header('Content-Type:text/xml;charset=utf-8');
$data = array();
$data['website'] = $INI['system']['sitename'];
$data['siteurl'] =  $INI['system']['wwwprefix'];
$data['items'] = array();

//团购信息
$teams = DB::LimitQuery('team', array(
			'condition' => array(
				'`begin_time` < ' . time(), 
				'`end_time` > ' . time(), 
				),
			'order' => 'ORDER BY id DESC',
			));
$data['items'] = $teams;

//城市信息
$citys = option_category('city', true);

//商家信息
$allPartners = DB::LimitQuery('partner', array(
			'condition' => array(
				'id>0', 
				),
			));
$partners = array();
foreach($allPartners as $v){
	$partners[$v['id']]['title'] = $v['title'];
	$partners[$v['id']]['phone'] = $v['phone'];
	$partners[$v['id']]['address'] = $v['address'];
}
$output = get_team($data);
echo $output;
unset($output);
//$team 包含公共信息和每个团购的数据
function get_team($team){
	if (!$team) return '';
	$xmlitems = '';
	$id=1;
	foreach ($team['items'] as $k => $v){
		/*处理城市*/
		$city = Table::Fetch('category', $team['items'][$k]['city_id']);
		$city_name = $city['name'];
		if($city['name']==""){
			$city_name = '全国';
		}
		/*处理商品类别*/
		$group = Table::Fetch('category', $team['items'][$k]['group_id']);
		$class=$group['name'];
		/*处理热销商品的开始和结束时间*/
		if($team['items'][$k]['team_type']=='goods'){
			$team['items'][$k]['begin_time']=date('YmdHis',time());
			$team['items'][$k]['end_time']=date('YmdHis',time()+(86400));
		}
		else{
			/*格式化时间*/
			$team['items'][$k]['begin_time']=date('YmdHis',$team['items'][$k]['begin_time']);
			$team['items'][$k]['end_time']=date('YmdHis',$team['items'][$k]['end_time']);
		}
		$team['items'][$k]['expire_time']=date('YmdHis',$team['items'][$k]['expire_time']);
		$xmlitems .= get_items($team, $k,$id,$city_name,$class);
		$id++;
	}
	$xmloutput = <<<XML
		<?xml version="1.0" encoding="utf-8" ?>
		<data>
		<site_name>{$team['website']}</site_name>
		<goodsdata>{$xmlitems}</goodsdata>
		</data>
XML;
	unset($xmlitems);
	return $xmloutput;
}

function get_items($team, $key,$id,$city_name,$class){
	global $citys;
	global $partners;
	if (!$team) return '';
	$rebate = round($team['items'][$key]['team_price'] / $team['items'][$key]['market_price'] * 10, 2);
	$xmlitem = <<<XMLITEM
		<goods id="{$id}"> 
		<city_name>{$city_name}</city_name>
		<site_url>{$team['siteurl']}</site_url>
		<title>{$team['items'][$key]['product']}</title>

		<goods_url>{$team['siteurl']}/team.php?id={$team['items'][$key]['id']}</goods_url>
		<desc>{$team['items'][$key]['title']}</desc>
		<class>{$class}</class>
		<img_url>{$team['siteurl']}/static/{$team['items'][$key]['image']}</img_url>
		<original_price>{$team['items'][$key]['market_price']}</original_price>
		<sale_price>{$team['items'][$key]['team_price']}</sale_price>
		<sale_rate>{$rebate}</sale_rate>
		<sales_num>{$team['items'][$key]['now_number']}</sales_num>
		<start_time>{$team['items'][$key]['begin_time']}</start_time>
		<close_time>{$team['items'][$key]['end_time']}</close_time>
		<merchant_name>{$partners[$team['items'][$key]['partner_id']]['title']}</merchant_name>
		<merchant_tel>{$partners[$team['items'][$key]['partner_id']]['phone']}</merchant_tel>
		<spend_start_time>{$team['items'][$key]['begin_time']}</spend_start_time>
		<spend_close_time>{$team['items'][$key]['expire_time']}</spend_close_time>
		<merchant_addr>{$partners[$team['items'][$key]['partner_id']]['address']}</merchant_addr>
		<hot_area></hot_area>
		<longitude></longitude>
		<latitude></latitude>

		</goods>

XMLITEM;
	return $xmlitem;
}
