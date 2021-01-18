<?php
    session_start();
    require "php/funciones.php";

    // Comprueba si el usuario ya está conectado en la sesión y crea la interfaz en caso positivo
    if(!isset($_SESSION['conectado'])){
        include "html/inicio.html";
    }else{
        $nombre = $_SESSION['nombre'];
        if($_SESSION['cuenta'] == '0'){
            normalUser($nombre);
        }else{
            adminUser($nombre);
        }
    }

    // Comprueba si el usuario se ha validado y no está iniciado y crea la interfaz 
    if(isset($_POST['iniciar']) && !isset($_SESSION['conectado'])){
        $nombre = $_POST['usuario'];
        $pass = $_POST['pass'];
        if(validateUser($nombre, $pass)){
            $_SESSION['conectado'] = '1';
            clearPage();
            if($_SESSION['cuenta'] == '0'){
                normalUser($nombre);
            }else{
                adminUser($nombre);
            }
        }
    }

    // Cierra la sesión actual
    if(isset($_GET['cerrar_sesion'])){
        session_destroy();
        echo "<script type='text/javascript'>
        window.location.href='clientes.php';
        </script>";
    }

    // Muestra los clientes
    if(isset($_POST['consultar_form'])){
        mostrarClientes();
    }

    // Inserta el html para insertar un cliente
    if(isset($_POST['insertar_form'])){
        include "html/insertar.html";
    }

    // Inserta un cliente
    if(isset($_POST['insertar'])){
        $codigo = $_POST['codigo'];
        $cif = $_POST['cif'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];

        if(!checkFieldExists("ncodigo", "clientes", $codigo)){
            insertarCliente($codigo, $cif, $nombre, $apellidos, $telefono, $direccion);
            mensaje("Cliente insertado");
            delSaveMessage("insertado");
        }else mensaje("El codigo del cliente ya está siendo utilizado");
    }

    // Inserta el html para eliminar un cliente
    if(isset($_POST['borrar_form'])){
        include "html/eliminar.html";
    }

    // Elimina un cliente
    if(isset($_POST['eliminar'])){
        $codigo = $_POST['codigo'];
        if(checkFieldExists("ncodigo", "clientes", $codigo)){
            eliminarCliente($codigo);
            mensaje("Cliente eliminado");
            delSaveMessage("eliminado");
        }else mensaje("El codigo no existe");
    }
?>