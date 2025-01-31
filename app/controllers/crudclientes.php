<?php


function crudBorrar($id)
{
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ($resu) {
        $_SESSION['msg'] = " El usuario " . $id . " ha sido eliminado.";
    } else {
        $_SESSION['msg'] = " Error al eliminar el usuario " . $id . ".";
    }
}

function crudTerminar()
{
    AccesoDatos::closeModelo();
    session_destroy();
}

function crudAlta()
{
    $cli = new Cliente();
    $orden = "Nuevo";
    include_once "app/views/formularioNuevo.php";
}


//DETALLES
function crudDetalles($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    include_once "app/views/detalles.php";
}

function crudDetallesSiguiente($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id, $_SESSION["ordenacion"]);
    if (!$cli) {
        $cli = $db->getMinCliente();
    }
    include_once "app/views/detalles.php";
}

function crudDetallesAnterior($id)
{

    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id, $_SESSION["ordenacion"]);
    if (!$cli) {
        $cli = $db->getMaxCliente();
    }
    include_once "app/views/detalles.php";
}

//MODIFICAR
function crudModificar($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden = "Modificar";
    include_once "app/views/formulario.php";
}

function crudModificarSiquiente($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id, $_SESSION["ordenacion"]);
    if (!$cli) {
        $cli = $db->getMaxCliente();
    }
    $orden = "Modificar";
    include_once "app/views/formulario.php";
}
function crudModificarAnterior($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id, $_SESSION["ordenacion"]);
    if (!$cli) {
        $cli = $db->getMinCliente();
    }
    $orden = "Modificar";
    include_once "app/views/formulario.php";
}



function crudPostAlta()
{
    $db = AccesoDatos::getModelo();

    limpiarArrayEntrada($_POST); //Evito la posible inyección de código

    if (!validarTelefono($_POST["telefono"])) {
        $_SESSION['msg'] = "El número de telefono no cumple el formato";
        return;
    }

    if (!validarIp($_POST["ip_address"])) {
        $_SESSION['msg'] = " Error al modificar el usuario, la ip debe seguir el formato correcto";
        return;
    }

    if (!validarEmail($_POST['email'])) {
        $_SESSION['msg'] = " Error al modificar el usuario, el email debe seguir un formato válido";
        return;
    }

    //NO SE REPITE EL EMAIL
    if ($db->emailExistsInDatabase($_POST["email"])) {
        $_SESSION['msg'] = " Error al modificar el usuario, el email ya se encuentra en nuestra base de datos";
        return;
    }

    $cli = new Cliente();
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];
    if ($db->addCliente($cli)) {
        $_SESSION['msg'] = " El usuario " . $cli->first_name . " se ha dado de alta ";
    } else {
        $_SESSION['msg'] = " Error al dar de alta al usuario " . $cli->first_name . ".";
    }
}

function crudPostModificar()
{
    limpiarArrayEntrada($_POST); // Evitar inyección de código
    $db = AccesoDatos::getModelo();

    if (!validarTelefono($_POST['telefono'])) {
        $_SESSION['msg'] = "Error al modificar el usuario, el teléfono debe seguir el formato 999-999-999";
        crudModificar($_POST["id"]);
        return;
    }

    if (!validarIp($_POST["ip_address"])) {
        $_SESSION['msg'] = "Error al modificar el usuario, la IP debe seguir el formato correcto";
        crudModificar($_POST["id"]);
        return;
    }

    // VALIDACION DEL CORREO Y COMPROBACION
    if ($db->getClienteMail($_POST["id"]) != $_POST['email']) {

        if (!validarEmail($_POST['email'])) {
            $_SESSION['msg'] = "Error al modificar el usuario, el email debe seguir un formato válido";
            crudModificar($_POST["id"]);
            return;
        }
        // QUE EL CORREO NO ESTE REPETIDO
        if ($db->emailExistsInDatabase($_POST["email"])) {
            $_SESSION['msg'] = "Error al modificar el usuario, el email ya se encuentra en nuestra base de datos";
            crudModificar($_POST["id"]);
            return;
        }
    }

    // Crear un objeto Cliente con los datos del formulario
    $cli = new Cliente();
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

    // Subir o cambiar la foto del cliente si se ha introducido una
    if (!empty($_FILES['foto']['name'])) {
        crudModificar($_POST["id"]); // Redirigir a la página de modificar el usuario
        return;
    }

    // Modificar el cliente en la base de datos
    if ($db->modCliente($cli)) {
        //SI SE REALIZA, ACTUALIZAR LA FOTO DE PERFIL
        subircambiarFoto($cli->id);
        header('Location: index.php');
        $_SESSION['msg'] = "El usuario ha sido modificado";
    } else {
        $_SESSION['msg'] = "Error al modificar el usuario";
        crudModificar($cli->id);
    }
}


function crudPostvalidar()
{
    $db = AccesoDatos::getModelo();
    //Encriptamos la contraseña; 
    $contraseña = md5($_POST["password"]);
    $usuario = $db->validarCliente($_POST["login"], $contraseña);

    if ($usuario) {
        if ($usuario[3] == 1) {
            $_SESSION["rol"] = 1;
        } else {
            $_SESSION["rol"] = 0;
        }
        return $usuario;
    }
}
function crudImprimir($id)
{
    require_once "vendor/autoload.php";

    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $foto = mostrarFoto($cli->id);
    $bandera = banderaIP($cli->ip);
    $mpdf = new \Mpdf\Mpdf();

    $html = "
    <table id='tabla'>
    <tr>
        <th>Información del Cliente</th>
        <th>Fotografía</th>
        <th>Bandera</th>
    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td>id:</td>
                    <td>$cli->id</td>
                </tr>
                <tr>
                    <td>first_name:</td>
                    <td>$cli->first_name</td>
                </tr>
                <tr>
                    <td>last_name:</td>
                    <td>$cli->last_name</td>
                </tr>
                <tr>
                    <td>email:</td>
                    <td>$cli->email</td>
                </tr>
                <tr>
                    <td>ip_address:</td>
                    <td> $cli->ip_address</td>
                </tr>
                <tr>
                    <td>telefono:</td>
                    <td>$cli->telefono</td>
                </tr>
            </table>
        </td>
    </tr>
    </table>";

    $pdfName = "$cli->first_name$cli->last_name.pdf";
    $mpdf->WriteHTML($html);
    $mpdf->Output($pdfName, "I");
}

function mostrarFoto($id)
{

    $defaultname = '00000000';
    $fotoName = substr($defaultname, 0, strlen($defaultname) - strlen($id));
    $fotoName = $fotoName . $id;
    $fotoRutajpg = "app/uploads/$fotoName.jpg";
    $fotoRutapng = "app/uploads/$fotoName.png";
    $fotoRutajpeg = "app/uploads/$fotoName.jpeg";

    if (file_exists($fotoRutajpg)) {
        return "<img src='app/uploads/" . $fotoName . ".jpg' alt='foto cliente' style='width: 100px; height: 20%'>";
    } else if (file_exists($fotoRutapng)) {
        return "<img src='app/uploads/" . $fotoName . ".png' alt='foto cliente' style='width: 100px; height: 20%'>";
    } else  if (file_exists($fotoRutajpeg)) {
        return "<img src='app/uploads/" . $fotoName . ".jpeg' alt='foto cliente' style='width: 100px; height: 20%'>";
    } else {
        return "<img src='https://robohash.org/$fotoName' alt='foto robo' style='width: 100px; height: 20%'/>";
    }
}

function subircambiarFoto($id)
{

    // VERIFICAR SI SE HA SUBIDO LA FOTO
    if (!isset($_FILES['foto']) || !is_uploaded_file($_FILES['foto']['tmp_name'])) {
        $_SESSION['msg'] = "No se ha subido ningún archivo";
        return false;
    }

    // INFORMACION Y NOMBRE DEL ARCHIVO
    $fileName = $_FILES['foto']['name'];
    $fileTmpName = $_FILES['foto']['tmp_name'];
    $fileType = $_FILES['foto']['type'];
    $fileSize = $_FILES['foto']['size'];

    // VERIFICAR SI LA EXTENSIÓN ES VÁLIDA 
    $allowedExtensions = array("jpg", "jpeg", "png");
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['msg'] = "La extensión del archivo no es válida. Solo se permiten archivos JPG, JPEG y PNG.";
        return false;
    }
    $allowedTypes = array("image/jpeg", "image/jpg", "image/png");
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['msg'] = "El tipo de archivo no es válido. Solo se permiten archivos JPG, JPEG y PNG.";
        return false;
    }

    // COMPROBAR EL TAMAÑO DEL ARCHIVO MENOR A 500KB
    $maxFileSize = 500 * 1024;
    if ($fileSize > $maxFileSize) {
        $_SESSION['msg'] = "El tamaño del archivo excede el límite permitido (500KB).";
        return false;
    }

    // GENERAR EL NOMBRE DE LA FOTO (8 dígitos)
    $fileNameWithFormat = sprintf("%08d", $id) . ".$fileExtension";
    $uploadDirectory = "app/uploads/";

    // SUBIRLO A CARPETA UPLOADS
    if (move_uploaded_file($fileTmpName, $uploadDirectory . $fileNameWithFormat)) {
        $_SESSION['msg'] = "La foto ha sido subida correctamente";
        return true;
    } else {
        $_SESSION['msg'] = "Error al subir la foto";
        return false;
    }
}