<?php
header('Content-type: text/html;charset=utf-8'); 
require_once(dirname(__FILE__).'/../lib/const.php');
class DB {
const DB_SERVER= 'localhost';
const DB_USERNAME='root';
const DB_PASSWORD= '';
const DB_NAME= 'foton';
    private $conn;

    public function open() {
        $this->conn = mysql_connect(self::DB_SERVER, self::DB_USERNAME, self::DB_PASSWORD);
        mysql_select_db(self::DB_NAME, $this->conn);
        mysql_query("set names utf8", $this->conn);
        // mysql_query('set names gb2312');
    }

    public function close() {
        mysql_close($this->conn);
    }

    public function execute($cmd) {
        // echo $cmd;
        return mysql_query($cmd, $this->conn);
    }

    public function insert($table, $fields, $values) {
        settype($fields, 'array');
        settype($values, 'array');
        $sql = 'INSERT INTO '.$table.' ('.join(',', $fields).') VALUES ('.join(',', $values).')';
        // echo $sql, "\n";die();
        return $this->execute($sql);
    }

    public function query($table, $conditions, $order = NULL,$start=null,$limit=NULL) { //根据conditions数组从数据库中返回查询结果
        settype($conditions, 'array');
        $conditions_text = join(' and ', $conditions);
        $sql = "SELECT * FROM $table";

        if (count($conditions) > 0) {
            $sql .= " WHERE $conditions_text";
        }
        if (! is_null($order)) {
            $sql .= " ORDER BY $order";
        }
        if(!is_null($start)&&!is_null($limit)){

        	$sql .=" LIMIT ".$start." , ".$limit;
        }
       
        // echo $sql, "\n";
        return $this->execute($sql);
    }
	public function query_count($table,$conditions){
		settype($conditions, 'array');
        $conditions_text = join(' and ', $conditions);
        $sql = "SELECT count(*) FROM $table";
		if (count($conditions) > 0) {
            $sql .= " WHERE $conditions_text";
        }
        $ret = mysql_fetch_array($this->execute($sql));
        return $ret['0'];
	}
    public function update($table, $conditions, $values) {
        settype($values, 'array');
        settype($conditions, 'array');
        $conditions_text = join(' and ', $conditions);
        $values_text = join(', ', $values);
        $sql = "UPDATE $table SET $values_text ";
        if (count($conditions) > 0) {
            $sql .= " WHERE $conditions_text";
        }
        // echo $sql, "\n";die();
        return $this->execute($sql);
    }

    public function delete($table, $conditions) {
        settype($conditions, 'array');
        $conditions_text = join(' and ', $conditions);
        $sql = "DELETE FROM $table";
        if (count($conditions) > 0) {
            $sql .= " WHERE $conditions_text";
        }
        //echo $sql, "\n";
        return $this->execute($sql);
    }

}

?>
