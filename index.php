<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php
        require "bbdd.php";
        include "registro.html";
        

        if(isset($_POST['crear'])){
            $nombre = $_POST['usuario'];
            $pass = $_POST['pass'];
            $tipo = $_POST['tipo'];
            $crypted_pass = encriptar($pass);
            

        }
        function encriptar($pass){
            return password_hash($pass, PASSWORD_DEFAULT);
        }
        function checkExists($nombre){
            $conexion = 
            $consulta = "SELECT cnombre FROM usuarios WHERE cnombre=:nombre";
            $stmt = $conexion -> prepare($consulta);
            $stmt->bindParam(":nombre", $nombre);
            if($stmt->execute()){

                if($stmt->fetch()){
                    print_r("Existe");
                }
                while($fila = $stmt->fetch()){
                    print_r("Existe");
                }
                $stmt->exit();
            }
        }
    ?>
</body>
</html>