<?php

/**
 * 
 */
class db
{
	private $host; // O la dirección IP del servidor MySQL
	private $db;
	private $user;
	private $pass;
	private $charset; 
	
	function __construct()
	{
		$this->host = 'localhost'; // O la dirección IP del servidor MySQL
		$this->db   = 'rancho';
		$this->user = 'root';
		$this->pass = '';
		$this->charset = 'utf8mb4'; 
	}

	function conexion()
	{
		$dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
		$options = [
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		    PDO::ATTR_EMULATE_PREPARES   => false,
		];

		try {
		    $pdo = new PDO($dsn, $this->user, $this->pass, $options);
		    return $pdo;
		} catch (\PDOException $e) {
		    throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}

	}

	function datos($sql,$parametros=false)
	{
		$conn = $this->conexion();
		$result = array();

		// print_r($sql);die();
		try {
			$stmt = $conn->prepare($sql);
			if($parametros)
			{
    			$stmt->execute($parametros);
    		}else{
    			$stmt->execute();
    		}
    		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		        $result[] = $row;
		    }
		    $conn=null;
			return $result;
			
		} catch (Exception $e) {
			die(print_r(sqlsrv_errors(), true));
		}
		
	}
	function sql_string($sql)
	{

		$conn = $this->conexion();
		// print_r($sql);
		try {
			$stmt = $conn->prepare($sql);
    		$stmt->execute();    		
		    $conn=null;
			return 1;
			
		} catch (Exception $e) {
			print_r($e);
			return -1;
			die(print_r(sqlsrv_errors(), true));
		}
	}

}
?>