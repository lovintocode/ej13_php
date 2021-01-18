<?php
    require "php/funciones.php";
    include "html/registro.html";

    // Extrae la informacion del formulario y inserta al usuario
    if(isset($_POST['crear'])){
        $nombre = $_POST['usuario'];
        $pass = encriptar();
        $tipo = extractTipo();
        if(!checkFieldExists("cnombre", "usuarios", $nombre)){
            insertarUsuario($nombre, $pass, $tipo);
            mensaje("Usuario insertado");
        }else mensaje("El usuario existe o faltan campos por completar");
    }
?>