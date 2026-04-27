<?php
require_once('config.php');

class Db extends PDO{

private $result;

function __construct(){

	parent::__construct(
		'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', 
		DB_USER, 
		DB_PASS,
		[]);
	$info = $this->errorInfo();
	if ($info[0] !== '00000' && !empty($info[0])) { 
		throw new Exception(sprintf("Подключение к серверу MySQL невозможно. Код ошибки: %s\n", var_export($info,1))); 
	} 
	$this->result = NULL;

}

public function getJsonData($table,$data){
	$pr = $this->prepare('SELECT * FROM `'.$table.'` WHERE `id`=:id');
	$pr->execute(['id' => $data['id']]);
	$row = $pr->fetch(PDO::FETCH_ASSOC);
	if(!isset($row) && !isset($row[$data['field']]))
		return null;
	return json_decode($row[$data['field']],1);
}
public function setJsonData($table,$data){
	$data_ = json_encode($data['value'],JSON_UNESCAPED_UNICODE);
	$pr = $this->prepare('UPDATE `'.$table.'` SET '.$data['field'].'=:'.$data['field'].' WHERE `id`=:id');
	$pr->execute([$data['field'] => $data_,'id' => $data['id']]);

}
public function getJsonValue($value){
	if(!isset($value))
		return null;
	return json_decode($value,1);
}
public function setJsonValue($value){
	return json_encode($value,JSON_UNESCAPED_UNICODE);
}
public function getRow($table,$data){
	$where = $this->prepareWhereData($data);
	$pr = $this->prepare('SELECT * FROM `'.$table.'` WHERE '.$where['prep']);
	$pr->execute($where['exec']);

	$row = $pr->fetch(PDO::FETCH_ASSOC);

	return $row;
}
public function getRows($table,$data){
	$where = $this->prepareWhereData($data);

	if(!empty($where)){
		$pr = $this->prepare('SELECT * FROM `'.$table.'` WHERE '.$where['prep']);
		$pr->execute($where['exec']);
	}else{
		$pr = $this->prepare('SELECT * FROM `'.$table.'` ');
		$pr->execute();
	}

	$rows = $pr->fetchAll(PDO::FETCH_ASSOC);

	return $rows;
}
public function getCountRows($table,$data){
	$where = $this->prepareWhereData($data);

	$sql = 'SELECT COUNT(*) FROM `'.$table.'`'.$where;
	$res = $this->query($sql);

	if(!empty($where)){
		$pr = $this->prepare('SELECT COUNT(*) FROM `'.$table.'` WHERE '.$where['prep']);
		$pr->execute($where['exec']);
	}else{
		$pr = $this->prepare('SELECT COUNT(*) FROM `'.$table.'`');
		$pr->execute();
	}

	$row = $pr->fetch(PDO::FETCH_NUM);
	if(!$row || !is_array($row))
		return 0;
	return $row[0];
}
private function prepareWhereData($data){
	$where = ['prep' => '', 'exec' =>[]];
	$prep = [];
	if(isset($data)){
		if(is_array($data)){
			foreach($data as $key=>$value){
				$prep[] = '`'.$key.'`= :'.$key;
				$where['exec'][$key] = $value;
			}
			$where['prep']  = implode(' AND ',$prep);
			return $where;
		}
	}
	return NULL;
}

public function getBySQL($sql,$fvlist){

	$pr = $this->prepare($sql);
	$pr->execute($fvlist);

	$rows = $pr->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}


public function Insert($table,$data){
	$prep = []; $exec = [];
	foreach($data as $key=>$value){
		$prep[] = '`'.$key.'`=:'.$key;
		$exec[$key] = $value;
	}
	$pr = $this->prepare('INSERT INTO `'.$table.'` SET '.implode(', ',$prep));
	$pr->execute($exec);
	return $this->lastInsertId();

}
public function Replace($table,$data){
	$prep = []; $exec = [];
	foreach($data as $key=>$value){
		$prep[] = '`'.$key.'`=:'.$key;
		$exec[$key] = $value;
	}

	$pr = $this->prepare('REPLACE INTO `'.$table.'` SET '.implode(', ',$prep));
	$pr->execute($exec);

	return $this->lastInsertId();

}
public function Update($table,$data){
	$prepf = []; $prepw = []; $exec = [];
	foreach($data['fields'] as $key=>$value){
		$prepf[] = '`'.$key.'`=:'.$key;
		$exec[$key] = $value;
	}
	foreach($data['where'] as $key=>$value){
		$prepw[] = '`'.$key.'`=:'.$key;
		$exec[$key] = $value;
	}

	$pr = $this->prepare('UPDATE `'.$table.'` SET '.implode(', ',$prepf).' WHERE '.implode(' AND ',$prepw));
	$pr->execute($exec);


}
public function setFieldsData($table,$id,$data){
	$prep = []; $exec = [];
	$exec['id'] = $id;
	foreach($data as $key=>$value){
		$prep[] = '`'.$key.'`=:'.$key;
		$exec[$key] = $value;
	}

	$pr = $this->prepare('UPDATE `'.$table.'` SET '.implode(', ',$prep).' WHERE `id`=:id');
	$pr->execute($exec);
}


}

?>