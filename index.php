<?php
session_start();
define('FPAG', 10); // Número de filas por página

if (!isset($_SESSION["ordenacion"])) {
    $_SESSION["ordenacion"] = "id";
    $_SESSION["ordenAD"]="ASC"; 
}

require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';

//---- PAGINACIÓN ----
$midb = AccesoDatos::getModelo();
$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
    $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------

// Borro cualquier mensaje "
$_SESSION['msg'] = " ";

ob_start(); // La salida se guarda en el bufer
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!isset($_SESSION["login"])) {
        $_SESSION["error"] = 0;
        include_once "app/views/login.php";
    }
    //Ordenación: 
    if (isset($_GET["ordenacion"])) {
        //si la sesion es igual a lo que me pasan
        if ($_SESSION["ordenacion"] == $_GET["ordenacion"]) {
            //cambio de orden 
            if (isset($_GET["ordenacion"]))
            if ($_SESSION['ordenAD'] == "ASC") {
                $_SESSION['ordenAD'] = "DESC";
            } else {
                $_SESSION['ordenAD'] = "ASC";
            }
        }else{
            $_SESSION['ordenAD'] = "ASC";
        }
        $_SESSION["ordenacion"] = $_GET["ordenacion"];
    }
    // Proceso las ordenes de navegación
    if (isset($_GET['nav'])) {
        switch ($_GET['nav']) {
            case "Primero":
                $posAux = 0;
                break;
            case "Siguiente":
                $posAux += FPAG;
                if ($posAux > $posfin) $posAux = $posfin;
                break;
            case "Anterior":
                $posAux -= FPAG;
                if ($posAux < 0) $posAux = 0;
                break;
            case "Ultimo":
                $posAux = $posfin;
        }
        $_SESSION['posini'] = $posAux;
    }


    // Proceso las ordenes de navegación en detalles
    if (isset($_GET['nav-detalles'])) {
        switch ($_GET['nav-detalles']) {
            case "Siguiente":
                crudDetallesSiguiente($_GET["id"]);
                break;

            case "Anterior":
                crudDetallesAnterior($_GET["id"]);
                break;
            case "Imprimir":
                crudImprimir($_GET['identificador']);
                break;
        }
    }

    // Proceso las ordenes de navegación en modificar
    if (isset($_GET['nav-modificar']) && isset($_GET['id'])) {
        switch ($_GET['nav-modificar']) {
            case "Siguiente":
                if ($_GET['id'] != 1000) {
                    crudModificarSiquiente($_GET['id']);
                    break;
                }
            case "Anterior":
                if (($_GET['id'] != 1)) {
                    crudModificarAnterior($_GET['id']);
                    break;
                }
            case "Imprimir":
                crudImprimir($_GET['identificador']);
                break;
        }
    }

    // Proceso de ordenes de CRUD clientes
    if (isset($_GET['orden'])) {
        switch ($_GET['orden']) {
            case "Nuevo":
                crudAlta();
                break;
            case "Borrar":
                crudBorrar($_GET['id']);
                break;
            case "Modificar":
                if ($_SESSION["rol"] == 1 && isset($_SESSION["acceso"])) {
                    crudModificar($_GET['id']);
                    break;
                }
            case "Detalles":
                crudDetalles($_GET['id']);
                break;
            case "Terminar":
                crudTerminar();
                include_once "app/views/login.php";
                break;
        }
    }
}
// POST Formulario de alta o de modificación
else {
    //Comprobar si el usuario existe: 
    if (!empty($_POST["login"]) && !empty($_POST["password"])) {
        $_SESSION["login"] = 1;

        if (isset($_SESSION["error"]) && $_SESSION["error"] >= 3) {
            echo "Debes reiniciar el buscador";
            die();
        } else if (!crudPostvalidar()) {
            include_once "app/views/login.php";
            $_SESSION["error"]++;
        } else {
            $_SESSION["error"] = 0;
            $_SESSION["acceso"] = 1;
        }
    }

    if (isset($_POST['orden'])) {
        switch ($_POST['orden']) {
            case "Nuevo":
                crudPostAlta();
                break;
            case "Modificar":
                crudPostModificar();
                break;
            case "Detalles":; // No hago nada
        }
    }
}

// Si no hay nada en la buffer 
// Cargo genero la vista con la lista por defecto
if (ob_get_length() == 0) {
    $valor = 0;
    $db = AccesoDatos::getModelo();
    $posini = $_SESSION['posini'];

    //Ordenación: 
    (isset($_SESSION["ordenacion"]) ? $valor = $_SESSION["ordenacion"] : $valor = "id");
    (isset($_SESSION["ordenAD"]) ? $valorAD = $_SESSION["ordenAD"] : $valorAD = "ASC");

    $tvalores = $db->getClientes($posini, FPAG, $valor, $valorAD);

    //Si tiene rol y está acceso entonnces: 
    if ($_SESSION["rol"] == 1 && isset($_SESSION["acceso"])) {
        require_once "app/views/listAdmin.php";
    } else if ($_SESSION["rol"] == 0 && isset($_SESSION["acceso"])) {
        require_once "app/views/list.php";
    }
}
$contenido = ob_get_clean();
$msg = $_SESSION['msg'];
// Muestro la página principal con el contenido generado
require_once "app/views/principal.php";