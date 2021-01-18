<?php
	class Conexion{

		private $conexion;

		function __construct(){
			$path = realpath("/opt/lampp/htdocs/ej13_b/php/config.json");
			$json_data = file_get_contents($path);
			$array = json_decode($json_data, true);
			try{
				$this->conexion = new PDO("mysql:host=".$array['ruta'].";dbname=".$array['dbname']."", "".$array['usr']."","".$array['pwd']."");
				$this->conexion->exec("set character set utf8");				
			}catch(Exception $e){
				die('Error : '.$e->GetMessage());
			}
		}

		function getConector(){
			return $this->conexion;
		}
		
		function cerrar(){
			$conexion = null;
		}
	}
?>