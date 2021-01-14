<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

        require "conexion.php";
        include "registro.html";
        
        // Se ejecuta al crear un usuario
        if(isset($_POST['crear'])){
            $nombre = $_POST['usuario'];
            $pass = encriptar();
            $tipo = extractTipo();
            if(!checkExists("cnombre", $nombre, "usuarios")){
                insertarUsuario($nombre, $pass, $tipo);
            }
        }

        // Encripta una contraseña enviada por "POST" y name "pass"
        function encriptar(){
            $pass = $_POST['pass'];
            return password_hash($pass, CRYPT_BLOWFISH, array("cost"=>10));
        }

        // Inserta un usuario en la tabla "usuarios" (nombre, pass, tipo)
        function insertarUsuario($nombre, $pass, $tipo){
            $conexion_ini = new Conexion();
            $conexion = $conexion_ini->getConector();
            $consulta = "INSERT INTO usuarios VALUES (NULL, :nombre, :pwd, :tipo)";
            $stmt = $conexion->prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":pwd", $pass);
            $stmt->bindParam(":tipo", $tipo);
            $stmt->execute();
            $stmt->closeCursor();
            $conexion_ini->cerrar();
            echo "Usuario insertado";
        }

        // Comprueba si existe un usuario mediante un codigo en la tabla especificada
        function checkExists($campo, $valor, $tabla){
            $exists = false;
            $conexion_ini = new Conexion();
            $conexion = $conexion_ini->getConector();
            $consulta = "SELECT $campo FROM $tabla WHERE cnombre=:nombre";
            $stmt = $conexion -> prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->bindParam(":nombre", $valor);
            if($stmt->execute()){
                if($stmt->fetch(PDO::FETCH_ASSOC)){
                    $exists = true;
                }
                $stmt->closeCursor();
            }
            $conexion_ini->cerrar();
            return $exists;
        }

        // Comprueba que los datos introducidos son correctos
        function checkData($nombre, $pass){
            if($nombre != '' && $pass != ''){
                return true;
            }
            return false;
        }
        
        // Cambia el nombre del tipo a un int
        function extractTipo(){
            $tipo = $_POST['tipo'];
            if($tipo == 'admin') return 1;
            else return 0;
        }
    ?>
</body>
</html>