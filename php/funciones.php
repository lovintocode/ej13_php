<?php

    // Require //
    require "conexion.php";

    /* FUNCIONES PARA USUARIOS */

    // Inserta un usuario en la tabla "usuarios" (nombre, pass, tipo)
    function insertarUsuario($nombre, $pass, $tipo){
        $insertado = false;
        $conexion_ini = new Conexion();
        $conexion = $conexion_ini->getConector();
        if($conexion){
            $consulta = "INSERT INTO usuarios VALUES (NULL, :nombre, :pwd, :tipo)";
            $stmt = $conexion->prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":pwd", $pass);
            $stmt->bindParam(":tipo", $tipo);
            $stmt->execute();
            $stmt->closeCursor();
            $conexion_ini->cerrar();
        }else mensaje("La conexion no se ha establecido");
        
    }

    // Comprueba si un usuario está validado y guarda el tipo de sesión en $_SESSION['cuenta'] y el nombre del usuario en $_SESSION['nombre']
    // Retorna un booleano
    function validateUser($nombre, $pass){
        $hash = "";
        $validate = false;
        $conexion_ini = new Conexion();
        $conexion = $conexion_ini->getConector();
        if($conexion){
            $consulta = "SELECT cnombre, cpass, ntipo FROM usuarios WHERE cnombre=:nombre";
            $stmt = $conexion -> prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute(array(':nombre' => $nombre));
            if($campo = $stmt->fetch(PDO::FETCH_ASSOC)){
                $hash = $campo['cpass'];
                $_SESSION['cuenta'] = $campo['ntipo']; 
                $_SESSION['nombre'] = $campo['cnombre'];
            }
            if(password_verify($pass, $hash)){
                $validate = true;
            }
            $stmt->closeCursor();
            $conexion_ini->cerrar();
        }else mensaje("La conexion no se ha establecido");
        return $validate;
    }

    /* FUNCIONES PARA CLIENTES */

    // Inserta un cliente en la tabla clientes mediante su (cif, nombre, apellidos, telelfono y direccion)
    function insertarCliente($codigo, $cif, $nombre, $apellidos, $telefono, $direccion){
        $conexion_ini = new Conexion();
        $conexion = $conexion_ini->getConector();
        if($conexion){
            $consulta = "INSERT INTO clientes (ncodigo, ccif, cnombre, capellidos, ctelefono, cdireccion) VALUES (:codigo, :cif, :nombre, :apellidos, :telefono, :direccion)";
            $stmt = $conexion->prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($stmt->execute(array(':codigo' => $codigo,':cif' => $cif, ':nombre' => $nombre, ':apellidos' => $apellidos, ':telefono' => $telefono, ':direccion' => $direccion)))
            $stmt->closeCursor();
            $conexion_ini->cerrar();
        }else mensaje("La conexion no se ha establecido");    
    }

    // Elimina un cliente de la tabla clientes mediante su codigo
    function eliminarCliente($codigo){
        $conexion_ini = new Conexion();
        $conexion = $conexion_ini->getConector();
        if($conexion){
            $consulta = "DELETE FROM clientes WHERE ncodigo=:codigo";
            $stmt = $conexion->prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute(array(':codigo' => $codigo));
            $stmt->closeCursor();
            $conexion_ini->cerrar();
        }else mensaje("La conexion no se ha establecido");
    }

    // Muestra una lista de los clientes en la bbdd
    function mostrarClientes(){
        $conexion_ini = new Conexion();
        $conexion = $conexion_ini->getConector();
        if($conexion){
            $consulta = "SELECT * FROM clientes";
            $stmt = $conexion->prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute();
            echo "<table width='100%'>";
            while($campo = $stmt->fetch(PDO::FETCH_ASSOC)){
                echo "<tr>";
                echo "<td>".$campo['ncodigo']."</td>";
                echo "<td>".$campo['ccif']."</td>";
                echo "<td>".$campo['cnombre']."</td>";
                echo "<td>".$campo['capellidos']."</td>";
                echo "<td>".$campo['cdireccion']."</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<a href='clientes.php?volver=true'>Volver</a>";
            $stmt->closeCursor();
            $conexion_ini->cerrar();
        }else mensaje("La conexion no se ha establecido");
    }

    /* FUNCIONES GENERALES SIN CONEXION BBDD */

    // Encripta una contraseña enviada por "POST" y name "pass"
    function encriptar(){
        $pass = $_POST['pass'];
        return password_hash($pass, CRYPT_BLOWFISH, array("cost"=>10));
    }
    
    // Cambia el nombre del tipo a un int
    function extractTipo(){
        $tipo = $_POST['tipo'];
        if($tipo == 'admin') return 1;
        else return 0;
    }
    
    // Escribe un mensaje alert
    function mensaje($mensaje){
        echo "<script type='text/javascript'>alert('$mensaje');</script>";
    }

    // Se ejecuta cuando se inserta o elimina un cliente
    function delSaveMessage($opcion){
        clearPage();
        echo "<label for='nombre'>Usuario</label>";
        echo "<p id='nombre'>".$_SESSION['nombre']."</p>";
        echo "<a href='clientes.php?cerrar_sesion=true'>Cerrar sesión</a>";
        echo "<br><br>";
        echo "<p>Cliente $opcion</p>";
        echo "<a href='clientes.php?'>Volver</a>";
    }

    // Borra el body de la página
    function clearPage(){
        echo "<script type='text/javascript'>
        let body = document.getElementsByTagName('body')[0];
        body.innerHTML = '';
        </script>";
    }
    
    // Se ejecuta para mostrar la intefaz del usuario normal
    function normalUser($nombre){
        echo "<form action='' method='post' style:width='100%'>";
        echo "<label for='nombre'>Usuario</label>";
        echo "<p id='nombre'>$nombre</p>";
        echo "<a href='clientes.php?cerrar_sesion=true'>Cerrar sesión</a>";
        echo "<br><br>";
        echo "<input type='submit' value='Consultar' name='consultar_form'>";
        echo "</form>";
    }

    // Se ejecuta para mostrar la interfaz del usuario admin
    function adminUser($nombre){
        echo "<form action='' method='post' style:width='100%'>";
        echo "<label for='nombre'>Usuario</label>";
        echo "<p id='nombre'>$nombre</p>";
        echo "<a href='?cerrar_sesion=true'>Cerrar sesión</a>";
        echo "<br><br>";
        echo "<input type='submit' value='Consultar' name='consultar_form'>";
        echo "<input type='submit' value='Insertar' name='insertar_form'>";
        echo "<input type='submit' value='Borrar' name='borrar_form'>";
        echo "</form>";
    }

    /* FUNCIONES GENERALES CON CONEXION BBDD */

    // Comprueba si existe un usuario mediante un campo y un valor en la tabla especificada 
    function checkFieldExists($campo, $tabla, $valor){
        $exists = false;
        $conexion_ini = new Conexion();
        $conexion = $conexion_ini->getConector();
        if($conexion){
            $consulta = "SELECT $campo FROM $tabla WHERE $campo=:valor";
            $stmt = $conexion -> prepare($consulta, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->bindParam(":valor", $valor);
            $stmt->execute();
            if($stmt->fetch(PDO::FETCH_ASSOC)){
                $exists = true;
            }
            $stmt->closeCursor();
            $conexion_ini->cerrar();
        }else mensaje("La conexion no se ha establecido");
        return $exists;
    }
?>      