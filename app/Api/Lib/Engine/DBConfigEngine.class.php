<?php

//数据库配置项操作
define('CFG_CACHE', !APP_DEBUG);//缓存开关

class DBConfigEngine extends BaseEngine{
	
	private $tab;
	private $kCache;
	private $db;
	private $cfgs;

	function __construct(){
		$this->tab=C('DB_CONFIG_TAB');
		$this->kCache='CK'.$this->tab;
		$this->db=M($this->tab);
	}

	function load(){
		$cfg;
		if (CFG_CACHE) {
			$cfg=S($this->kCache);
		}else{
			S($this->kCache,NULL);//清空缓存，以免下次读取到错误数据
		}
		if (!($cfg&&is_array($cfg))) {
			$cfg=$this->loadDb();
			if (!$cfg) {
				$this->initialize();
				$cfg=$this->loadDb();
			}else if($this->checkNeedUpgrade($cfg)){
				$this->upgrade();
				$cfg=$this->loadDb();
			}
			if (CFG_CACHE) {
				S($this->kCache,$cfg);
			}
		}else{
			$this->cfgs=$cfg;
		}
		foreach ($cfg as $c) {
			$v=$c['vaule'];
			$exc;
			// enum('enum', 'string', 'int', 'float', 'bool')  
			switch ($c['type']) {
				case 'enum':
					# code...
					$exc=$this->getVaules($c);
					break;
				case 'string':
					$exc='strval';
					break;
				case 'int':
					$exc='intval';
					break;
				case 'float':
					$exc='floatval';
					break;
				case 'bool':
					$exc='boolval';
					break;
				default:
					break;
			}

			if ($exc) {
				$exc='$v='.$exc.'($v);';
				// echo $exc.'<br/>';
				eval($exc);
			}
			// pr($v);
			// echo $v;
			C($c['key'],$v);
		}
	}

	private function getVaules($v){
		return explode(',',$v);
	}

	private function getLoadExec($row){
		// exec =>load,insert
		$v=$row['exec'];
		$ints=$this->getVaules($v);
		$ints[0];
	}

	private function loadDb(){
		$rows = $this->db->select();
		$rows=$this->toKeyMap($rows);
		$this->cfgs=$rows;
		return $rows;
	}

	private function toKeyMap($cfg){
		if ($cfg) {
			$temp = array();
			foreach ($cfg as $v){
				$temp[$v['key']]=$v;	
			}
			$cfg=$temp;
		}
		return $cfg;
	}

	function checkNeedUpgrade($cfg){
		$row = $cfg['cfg_ver'];
		// return $row['vaule']!=APP_VERSION;
		return true;
	}

	function upgrade(){
		$this->fillCfgsIfNeed();
 //update tab version
		$data['vaule']=APP_VERSION;
		$this->db->where("`key`='cfg_ver'")->save($data);
	}

	function fillCfgsIfNeed(){
		$rows = $this->loadDefRows();
		foreach ($rows as $key => $row) {
			if (!$this->cfgs[$key]) {//记录不存在
				$data=$this->buildRow($row);
				$data['key']=$key;
				$this->db->add($data);
				pr($data);
			}
		}
	}
// 'type'=>'enum','vaule_limit'=>'File,Memcache,Xcache','default_vaule'=>'File','ext_data'=>'','exec'=>'','key_version'=>'1.71','description'=>'缓存类型');
	function buildRow($row){
		$data=array();
		$data['vaule']=$row[2];
		$keys=array('type','vaule_limit','default_vaule','ext_data','exec','key_version','description');
		$index=0;
		foreach ($keys as $key) {
			$v=$row[$index++];
			if ($v!==NULL) {
				$data[$key]=$v;
			}
		}
		return $data;
	}


	function initialize(){
		$this->createTab();
		$this->fillCfgsIfNeed();
	}

	function loadDefRows(){
		$a=@require(CONF_PATH.'cfgRows.php');
		return $a;
	}

	function createTab(){

// 表的结构 `app_config`
		$tab=$this->tab;
		$tab=mysql_real_escape_string($tab);

		$sql=<<<flag
CREATE TABLE IF NOT EXISTS `$tab` (
  `key` varchar(25) NOT NULL,
  `vaule` varchar(256) NOT NULL,
  `type` enum('enum','string','int','float','bool') NOT NULL,
  `vaule_limit` varchar(255) NOT NULL,
  `default_vaule` text NOT NULL,
  `ext_data` text NOT NULL,
  `exec` varchar(255) NOT NULL,
  `key_version` float NOT NULL,
  `create_at` int(11) NOT NULL,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` varchar(256) NOT NULL,
  PRIMARY KEY (`key`),
  UNIQUE KEY `key` (`key`),
  KEY `key_2` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
flag;
	
	$this->db->query($sql);

	}

}