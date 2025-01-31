<?php

/*
 *  Funciones para limpiar la entrada de posibles inyecciones
 */

function limpiarEntrada(string $entrada): string
{
    $salida = trim($entrada); // Elimina espacios antes y después de los datos
    $salida = strip_tags($salida); // Elimina marcas
    return $salida;
}
// Función para limpiar todos elementos de un array
function limpiarArrayEntrada(array &$entrada)
{

    foreach ($entrada as $key => $value) {
        $entrada[$key] = limpiarEntrada($value);
    }
}


function validarTelefono($telefono)
{
    $patron = '/^\d{3}-\d{3}-\d{4}$/';
    if (preg_match($patron, $telefono)) {
        return true; // El formato es válido
    } else {
        return false; // El formato no es válido
    }
}

function validarIp($ip){
    $patronIP = '/^((25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)$/';

    // Realiza la verificación
    if (preg_match($patronIP, $ip)) {
        return true; // La IP es válida
    } else {
        return false; // La IP no es válida
    }
}




//formateo el número 0000XXXX.jpg: 
function numeroFormateado($num)
{

        $formattedNum = sprintf('%08d', $num);
        $directorio = $_SERVER['DOCUMENT_ROOT'] . '\\EjeMockaroo-CRUD\\images';

        $rutacompleta = $directorio . "\\" . $formattedNum . ".jpg";


        if (file_exists($rutacompleta)) {
            return "<img src='".$rutacompleta.">";
        } else {
            return 'https://robohash.org/' . $formattedNum;
        }
    
    
}


function banderaIP($ip)
{
        $url = "http://ip-api.com/json/{$ip}";
        $json = @file_get_contents($url);
    
        if ($json === false) {
            $_SESSION["msg"] = "Error fetching IP information";
            return "https://upload.wikimedia.org/wikipedia/commons/d/d4/World_Flag_%282004%29.svg";
        }
    
        $obj = json_decode($json);
    
        if ($obj !== null && isset($obj->countryCode) && !empty($obj->countryCode)) {
            $country = strtolower($obj->countryCode);
            return "https://flagpedia.net/data/flags/w580/{$country}.webp";
        } else {
            // $_SESSION["msg"] = "Error decoding JSON or missing countryCode";
            return "https://upload.wikimedia.org/wikipedia/commons/d/d4/World_Flag_%282004%29.svg";
        }
 }


 

function validarFotografía($image, $id)
{

    // Directorio donde se almacenarán las imágenes
    $directorio = "app/uploads"; 

    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];

        // Verifica el tamaño del archivo (500 KB)
        if ($image['size'] <= 500 * 1024) {
            // Verifica si el archivo es una imagen
            $imageInfo = getimagesize($image['tmp_name']);

            if ($imageInfo !== false) {
                // El archivo es una imagen
                $imageType = exif_imagetype($image['tmp_name']);

                if ($imageType === IMAGETYPE_JPEG || $imageType === IMAGETYPE_PNG) {
                    $imageExtension = $imageType === IMAGETYPE_JPEG ? 'jpg' : 'png';
                    
                    if ($id < 10) {
                        $var = "000" . $id;
                    } else if ($id < 100) {
                        $var = "00" . $id;
                    } else {
                        $var = $id;
                    }
                    
                    $rutacompleta = $directorio . '/0000' . $var . ".".$imageExtension;

                    // Mueve el archivo al directorio de uploads con el nombre único
                    move_uploaded_file($image['tmp_name'], $rutacompleta);

                    $_SESSION["msg"] = 'Imagen subida exitosamente.';
                    return true; 
                } else {
                    $_SESSION["msg"] = 'El archivo debe ser una imagen JPG o PNG.';
                    return false; 
                }
            } else {
                $_SESSION["msg"] = 'El tamaño del archivo debe ser inferior a 500 KB.';
                return false; 
            }
        } else {
            $_SESSION["msg"] = 'El tamaño del archivo debe ser inferior a 500 KB.';
            return false; 
        }
    }
}

function validarEmail($email){
    {
        $emailFiltrado = filter_var($email, FILTER_VALIDATE_EMAIL);
    
        if ($emailFiltrado !== false && preg_match('/@.+\./', $email)) {
            return true; // El correo electrónico es válido
        } else {
            return false; // El correo electrónico no es válido
        }
    }
}