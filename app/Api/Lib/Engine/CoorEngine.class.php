<?php
	/*
	坐标转换
	*/
define(CoordTypeBaidu, 'baidu');
define(CoordTypeMars, 'Mars');

class CoorEngine{
	
	static function conver($coor1,$coor2,&$lng,&$lat){
		if ($coor1==CoordTypeBaidu&&$coor2==CoordTypeMars) {
			self::converBaidu2Mars($lng, $lat);
		}elseif ($coor1==CoordTypeMars&&$coor2==CoordTypeBaidu) {
			self::converMars2Baidu($lng, $lat);
		}else{
			return $coor1;	
		}
		return $coor2;
	}
	static function converBaidu2Mars(&$lng,&$lat){//百度坐标到火星坐标
		$lng-=0.0065;
		$lat-=0.0060;
	}

	static function converMars2Baidu(&$lng,&$lat){//百度坐标到火星坐标
		$lng+=0.0065;
		$lat+=0.0060;
	}

	static function lng2m($log){
		return 85390*$log;
	}
	static function m2lng($m)
	{
		return $m/85390.0;
	}

	static function lat2m($lat){
		return 111111*$lat;
	}
	static function m2lat($m){
		return $m/111111.0;
	}

	//计算距离
	static function lnglat2m($lng,$lat,$olng,$olat){
		return strval(sqrt(pow(self::lng2m($lng-$olng),2)+pow(self::lat2m($lat-$olat),2)));
	}



}