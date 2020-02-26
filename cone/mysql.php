<?php
/**
 * 
 */
class conectarBD {

	public $conexion;
	public $con_serv;
	public $con_base;
	public $con_user;
	public $con_pass;
	public $con;
	public $error;

	public function conectarBD(){
		$this->con_serv="192.168.1.102";
		$this->con_base="hmg";
		$this->con_user="sistemas";
		$this->con_pass="linuxmoya";
		if (!($this->con=mysqli_connect($this->con_serv,$this->con_user,$this->con_pass,$this->con_base))){
			echo "ERROR AL CONECTAR LA BASE DE DATOS.";
			exit();
		}
		$mysqli = new mysqli($this->con_serv,$this->con_user,$this->con_pass,$this->con_base);
		if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  exit;
		}
		$mysqli->query("SET NAMES 'utf8'");
		return $mysqli;
	}

	public function actualizar($tabla, $campos, $condiciones){
		$con = $this->conectarBD();
		$sql=('update ' .$tabla. ' set '.$campos.' where '.$condiciones);
		$res=$con->query($sql) or die ("Error en actualización => ".$con->error);
		mysqli_close($con);
	}

	public function eliminar($tabla,$condiciones){
		$con= $this->conectarBD();
		$sql =('delete from '.$tabla.' where '.$condiciones);
		$res=$con->query($sql) or die ("Error en eliminación => ".$con->error);
		mysqli_close($con);
	}

	public function insertar($tabla,$valores){
		$con = $this->conectarBD();
		$sql =('insert into '.$tabla.' values '.$valores);
		$res=$con->query($sql) or die ("Error en inserción => ".$con->error);
		mysqli_close($con);
	}

	public function consultar($atributo, $tablas, $codiciones, $valido){
		if ($valido == 4){
			$con= $this->conectarBD();
			$sql = ('select ' .$atributo.' from '.$tablas.' where '.$codiciones);
			//echo "SQL---".$sql."---SQL";
			$res=$con->query($sql) or die ("Error en consulta => ".$con->error);
			return $res;	
			mysqli_close($con);
		}
		// si $valido == 3 consulta la ultima factura y el numero
		if ($valido == 3){
			$con= $this->conectarBD();
			$sql = ('select ' .$atributo.' from '.$tablas);
			$res=$con->query($sql) or die ("Error en consulta => ".$con->error);
			return $res;
			mysqli_close($con);
		}
		// si $valido == 2 consulta y valida si el registro existe
		if ($valido == 2){
			$con= $this->conectarBD();
			$sql = ('select ' .$atributo.' from '.$tablas.' where '.$codiciones);
			$res=$con->query($sql) or die ("Error en consulta => ".$con->error);
			$var=mysqli_num_rows($res);
			return $var;
			mysqli_close($con);
		} 
		if ($valido == 1){
			$con= $this->conectarBD();
			$sql = ('select ' .$atributo.' from '.$tablas.' where '.$codiciones);
			$res=$con->query($sql) or die ("Error en consulta => ".$con->error);
			$var=mysqli_fetch_array($res);
			return $var;
			mysqli_close($con);
		}
	}
}	
?>