<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
need_auth('market');


$id = abs(intval($_GET['id']));
$ad = $ead = Table::Fetch('adinfo', $id);

if (  $id  && empty($ad) ) {
	redirect( WEB_ROOT . '/manage/market/ad.php' );

}

else if ( is_post() ) {
	$ad_arr = $_POST;
	
	$insert = array(
		    'type',
			'title',
			'image',
			'available',
			'link',
			'displayorder'
			);


	$ad_arr['displayorder'] = abs(intval($ad_arr['displayorder']));
	$ad_arr['available'] = abs(intval($ad_arr['available'])) ? 1 : 0;
	$ad_arr['image'] = $ad_arr['image'] ? upload_adimage('image',$ad_arr['image'],'ad') : upload_adimage('image',$ad['image']);

	$table = new Table('adinfo', $ad_arr);
	//更新
	if ($ad['id'] && $ad['id']==$id) {
		$table->SetPk('id', $id);
		$table->update($insert);

		Session::Set('notice', '编辑广告成功');
		redirect( WEB_ROOT . "/manage/market/ad.php");
	}elseif($ad['id']) {
		Session::Set('error', '编辑广告失败');
		redirect(null);
	}

	//插入
	if ( $table->insert($insert) ) {
		Session::Set('notice', '添加广告成功');
		redirect( WEB_ROOT . "/manage/market/ad.php");
	}
	else {
		Session::Set('error', '添加广告失败');
		redirect(null);
	}
}elseif(!$id){
	$ad['id'] = 0;
	$ad['available'] = 1;
}

$availables = array('1'=>'是','0'=>'否');
include template('manage_market_adedit');

function upload_adimage($input, $image=null, $type='ad') {
	$year = date('Y'); $day = date('md'); $n = time().rand(1000,9999).'.jpg';
	$z = $_FILES[$input];
	if ($z && strpos($z['type'], 'image')===0 && $z['error']==0) {
		if (!$image) { 
			RecursiveMkdir( IMG_ROOT . '/' . "{$type}/{$year}/{$day}" );
			$image = "{$type}/{$year}/{$day}/{$n}";
			$path = IMG_ROOT . '/' . $image;
		} else {
			RecursiveMkdir( dirname(IMG_ROOT .'/' .$image) );
			$path = IMG_ROOT . '/' .$image;
		}
		move_uploaded_file($z['tmp_name'], $path);
		return $image;
	} 
	return $image;
}


function current_market_ad($s=null) {
	$filter = array(
		'ad' => '广告列表',
		'adedit' => '添加广告',
	);
	$a['/manage/market/ad.php'] = '广告列表';
	foreach($filter AS $id=>$name) {
		$a["/manage/market/{$id}.php"] = $name;
	}
	$l = '/manage/market/ad.php';
	if ($s) $l = "/manage/market/{$s}.php";
	return current_link($l, $a, true);
}
