<?php
class DBExt
{
	static private $_debug = false;
	static private $_exit = false;
	static private $_dbname = null;
	static private $_db = null;
	
	function _init()
	{
		global $wpdb;
		self::$_db = $wpdb;
	}

	static public function bug()
	{
		self::$_debug = true;
	}

	static public function exits()
	{
		self::$_exit = true;
	}
	 
	public function getDBName()
	{
		if(!self::$_dbname){
			self::$_dbname = '';
		}
		return self::$_dbname;
	}

	//$row = sqlQuery('select * from test');
	public static function sqlQuery($sql)
	{
		self::_init();
		if(self::$_debug) echo $sql . "<br><br>\n";
		if(self::$_exit) exit;
		if(!$sql) return false;
		

		$result = self::$_db->get_row($sql, 'ARRAY_A', 0);;
		if (!$result) return false;
		
		return $result;
	}

	public static function sqlQuerys($sql)
	{
		self::_init();
		if(self::$_debug) echo $sql . "<br><br>\n";
		if(self::$_exit) exit;
		if(!$sql) return false;
		
		$result = self::$_db->get_results($sql, 'ARRAY_A', 0);;
		if (!$result) return false;

		return $result;
	}

	//sql execut
	public static function sqlExecut($sql)
	{
		self::_init();
		if(self::$_debug) echo $sql  . "<br><br>\n";
		if(self::$_exit) exit;
		if(!$sql) return false;

		$result = self::$_db->query($sql);
		return $result;
	}

	public function getRow($where, $tableName, $select = '*')
	{
		if(!$where || !$tableName) return false;
		if(!is_array($where)){
			$sql = "SELECT $select FROM $tableName WHERE $where LIMIT 1";
		}else{
			$w = '1=1';
			foreach ($where as $f => $v){
				$w = "$f='$v' AND " . $w; 
			}
			$where = $w;
			$sql = "SELECT $select FROM $tableName WHERE $where LIMIT 1";
		}
		$row = self::sqlQuery($sql);
		return $row;
	}

	public function getRows($where, $tableName, $select = '*', $order = null)
	{
		if(!$where || !$tableName) return false;
		if($order) $order = "ORDER BY $order";
		if(!is_array($where)){
			$sql = "SELECT $select FROM $tableName WHERE $where $order";
		}else{
			$w = '1=1';
			foreach ($where as $f => $v){
				$w = "$f='$v' AND " . $w;
			}
			$where = $w;
			$sql = "SELECT $select FROM $tableName WHERE $where $order";
		}
		$rows = self::sqlQuerys($sql);
		return $rows;
	}

	public function getColumnName($tableName)
	{
		if(!$tableName) return false;
		$dbname = self::getDBName();

		$select = "column_name";
		$where = "table_name='$tableName' AND `TABLE_SCHEMA`='$dbname'";
		$tableName = "information_schema.columns";
		$rows = self::getRows($where, $tableName, $select);
		$row = array();
		foreach ($rows as $k => $v){
			$row[] = $v['column_name'];
		}
		return $row;
	}

	public function update($where, $tableName, $set)
	{
		if(!$where || !$tableName || !$set) return false;

		$sql = "UPDATE $tableName SET $set WHERE $where";
		$rs = self::sqlExecut($sql);
		return $rs;
	}

	public function updates($tableName, $set)
	{
		if(!$tableName || !$set) return false;

		$sql = "UPDATE $tableName SET $set";
		$rs = self::sqlExecut($sql);
		return $rs;
	}

	public function insert($tableName, $col, $val)
	{
		if(!$tableName || !$col || !$val) return false;

		$sql = "INSERT INTO $tableName ($col) VALUES ($val)";
		$rs = self::sqlExecut($sql);
		return $rs;
	}

	public function del($where, $tableName)
	{
		if(!$where || !$tableName) return false;

		$sql = "DELETE FROM $tableName WHERE $where";
		$rs = self::sqlExecut($sql);
		return $rs;
	}

	public function insertByArray($tableName, array $data)
	{
		if(!$tableName || !$data || !is_array($data)) return false;

		$col = '';
		$i = 0;
		foreach ($data as $k => $v){
			if(!$i){
				$col = $k;
				$i=1;
				continue;
			}
			$col = $col .",". $k;
		}
		$i = 0;
		$val = '';
		foreach ($data as $k => $v){
			if(!$i){
				$val = "'$v'";
				$i=1;
				continue;
			}
			$val = $val .",'$v'";
		}
		//$val = implode(',', $data);
		return self::insert($tableName, $col, $val);
	}

	public function updateByArray($tableName, array $condition, array $sets)
	{
		if(!$tableName || !$condition || !is_array($condition) || !$sets || !is_array($sets)) return false;

		$where = '';
		$i = 0;
		$and = null;
		foreach ($condition as $k => $v){
			if($i > 0) $and = " AND ";
			$where = $where .  "$and `$k`='$v'";
		}

		$set = '';
		foreach ($sets as $k => $v){
			$set = $set . ", `$k`='$v'";
		}
		$set = substr($set, 1);

		$rs = self::update($where, $tableName, $set);
		return $rs;
	}

	//$wpdb->update( $table, $data, $where, $format = null, $where_format = null );
	//$wpdb->insert( $table, $data, $format );
	//$wpdb->$insert_id;
	//$user_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users;"));
	//echo '<p>User count is ' . $user_count . '</p>';
	
	//----------------------------------------- page
	static function allCount($tableName, $con = null, $col = null)
	{
		if(!$tableName) return array();
		self::_init();
		if(!$col){
			$count = self::$_db->get_var("SELECT COUNT(*) FROM $tableName");
		}else{
			$count = self::$_db->get_var("SELECT COUNT(*) FROM $tableName WHERE $col");
		}
		return $count;
	}
	
	static function pageRows($start = 0, $end = 0, $tableName = null, $where = null)
	{
		if(!$tableName) $tableName = 'table';
		self::_init();
		if($where) $where = " WHERE $where ";
		if(!$end){
			$rows = self::$_db->get_results("SELECT * FROM `$tableName` $where ORDER BY `sort` DESC", 'ARRAY_A');
		}else{
			$rows = self::$_db->get_results("SELECT * FROM `$tableName` $where ORDER BY `sort` DESC LIMIT $start, $end", 'ARRAY_A');
		}
		if(!$rows) return array();
		return $rows;
	}
	
}
