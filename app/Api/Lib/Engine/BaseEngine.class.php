<?php
	/*
	
	*/

class BaseEngine{
	
	protected $error;

	function getError(){
		return $this->error;
	}

	static function objsFromTable($table,$where,$mapKey=false,$order=NULL,&$count=NULL,&$page=NULL){
		// var_dump(func_get_args());
		if (is_string($table))$table=M($table);
		$res=$table->where($where)->limit($count)->page($page)->order($order)->select();
		// echo $table->getLastSql();
		logSqlTrace($table);
		if ($count&&$page){
			$tc=$count;
			$count=intval($table->where($where)->count());
			$page=ceil($count/$tc);	
		}
		if ($mapKey) {
			foreach ($res as $v)$temp[$v[$mapKey]]=$v;
			$res=$temp;
		}
		return $res;
	}

	static function objFromTable($table,$where){
		if (!$where)return false;
		if (is_string($table))$table=M($table);
		$obj=$table->where($where)->find();
		logSqlTrace($table);
		return $obj;
	}

	static function objsCount($table,$where){
		if (is_string($table))$table=M($table);
		return $table->where($where)->count();
	}

	static function objsByIds($table,$ids,$where,$maping=false){
		if (is_string($table))$table=M($table);
		$pk=$table->getPk();
		$mapkey=$maping?$pk:NULL;
		$ids=Utils::uniqueArr($ids);
		if ($ids)$where[$pk]=array('in',$ids);
		if(!$where)return false;
		$objs=self::objsFromTable($table,$where,$mapkey);
		return $objs;
	}
	
	static function objById($table,$id){
		if(!$id)return false;
		if (is_string($table))$table=M($table);
		$res=$table->find($id);
		return $res;
	}

	static function checkRecordExist($table,$key,$value){
		$map[$key]=array('EQ',$value);
		if(self::objsCount($table,$map))return true;
		return false;
	}

}