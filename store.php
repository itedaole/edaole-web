<?php
require_once(dirname(__FILE__) . '/app.php');

/*商家分类*/
$partner_cat = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'partner',
		  'display'=>'Y',
		  ), 
		'order' => 'ORDER BY display ,sort_order DESC, id DESC',
		'size'=>5
));
$cat_partner = array();
foreach($partner_cat as $key=>$val){
        $cat_partner[$key]['name'] = $val[name];
		$cat_partner[$key]['ename'] = $val[ename];
		$cat_partner[$key]['id'] = $val[id];
		$cat_partner[$key]['number'] = Table::Count('partner', array('group_id'=>$val[id],'city_id'=>$cid,'display'=>'Y'));
		$cat_partner[$key]['partner'] = DB::LimitQuery('partner', array(
			'condition' => array('group_id'=>$val[id],'city_id'=>$city[id],'display'=>'Y','brands'=>'Y'),
			'order' => "ORDER BY head DESC, id DESC",
		));
}




include template('store');


