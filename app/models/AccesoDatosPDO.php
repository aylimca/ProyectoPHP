<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería PDO *******************
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos
{

    private static $modelo = null;
    private $dbh = null;

    public static function getModelo()
    {
        if (self::$modelo == null) {
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }



    // Constructor privado  Patron singleton

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DATABASE . ";charset=utf8";
            $this->dbh = new PDO($dsn, DB_USER, DB_PASSWD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión " . $e->getMessage();
            exit();
        }
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo()
    {
        if (self::$modelo != null) {
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo cuantos filas tiene la tabla

    public function numClientes(): int
    {
        $result = $this->dbh->query("SELECT id FROM Clientes");
        $num = $result->rowCount();
        return $num;
    }


    // SELECT Devuelvo la lista de Usuarios
    public function getClientes($primero, $cuantos, $orden, $ordenAD): array
    {
        $tuser = [];
        // Crea la sentencia preparada  
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes order by $orden $ordenAD limit $primero,$cuantos");
        // Si falla termina el programa
        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

        if ($stmt_usuarios->execute()) {
            while ($user = $stmt_usuarios->fetch()) {
                $tuser[] = $user;
            }
        }
        // Devuelvo el array de objetos
        return $tuser;
    }


    // SELECT Devuelvo un usuario o false
    public function getCliente(int $id)
    {
        $cli = false;
        $stmt_cli   = $this->dbh->prepare("select * from Clientes where id=:id");
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cli->bindParam(':id', $id);
        if ($stmt_cli->execute()) {
            if ($obj = $stmt_cli->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }

    /**
     * Obtiene el cliente con el valor mínimo en la columna de ordenación actual.
     */
    public function getMinCliente()
    {
        $cli = false;
        $ordenacion = $_SESSION["ordenacion"];
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes order by $ordenacion ASC limit 1");

        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        if ($stmt_usuarios->execute()) {
            if ($obj = $stmt_usuarios->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }

    //elegir el mayor cliente: 
    public function getMaxCliente()
    {
        $cli = false;
        $ordenacion = $_SESSION["ordenacion"];
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes order by $ordenacion DESC limit 1");

        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        if ($stmt_usuarios->execute()) {
            if ($obj = $stmt_usuarios->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }

    //GET MAIL: 
    public function getClienteMail($id)
    {
        $stmt_cliIP = $this->dbh->prepare("SELECT email FROM Clientes WHERE id=:id");
        $stmt_cliIP->bindParam(':id', $id);

        if ($stmt_cliIP->execute()) {
            // Obtén el resultado de la consulta
            $email = $stmt_cliIP->fetchColumn();

            if ($email !== false) {
                // El correo electrónico fue encontrado, devuelve el valor
                return $email;
            } else {
                // No se encontró el correo electrónico
                return false;
            }
        } else {
            // Manejo de errores si la ejecución de la consulta falla
            return false;
        }
    }

    //email existe en la BBDD:
    public function emailExistsInDatabase($email)
    {
        $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM Clientes WHERE email = :email");
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            // Obtiene el resultado de la consulta
            $count = $stmt->fetchColumn();

            // Si el contador es mayor que cero, significa que el correo electrónico existe
            return $count > 0;
        } else {
            // Manejo de errores si la ejecución de la consulta falla
            return false;
        }
    }

    //GET IP: 
    public function getClienteIPAddress($ip)
    {
        $stmt_cliIP   = $this->dbh->prepare("select * from Clientes where ip_address=:ip");
        $stmt_cliIP->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cliIP->bindParam(':ip', $ip);
        if ($stmt_cliIP->execute()) {
            return ($stmt_cliIP->rowCount() > 0);
        }
    }


    public function getClienteSiguiente($id, $ordenacion)
    {
        $cli = false;

        // $stmt_cli   = $this->dbh->prepare("select * from Clientes where id >? limit 1");
        // $stmt_cli   = $this->dbh->prepare("select * from Clientes where $ordenacion > (select $ordenacion from Clientes where $ordenacion like :id) order by $ordenacion limit 1");
        $stmt_cli   = $this->dbh->prepare("select * from Clientes where $ordenacion > ? order by $ordenacion  limit 1");

        // Enlazo $id con el primer ? 
        $stmt_cli->bindValue(1, $id);

        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        if ($stmt_cli->execute()) {
            if ($obj = $stmt_cli->fetch()) {
                $cli = $obj;
            }
        }


        return $cli;
    }

    public function getClienteAnterior($id)
    {

        $cli = false;

        $stmt_cli   = $this->dbh->prepare("select * from Clientes where id <? order by id DESC limit 1");
        // Enlazo $id con el primer ? 
        $stmt_cli->bindParam(1, $id);
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        if ($stmt_cli->execute()) {
            if ($obj = $stmt_cli->fetch()) {
                $cli = $obj;
            }
        }

        return $cli;
    }


    //VALIDAR CLIENTE
    public function validarCliente($nombre, $contrasena)
    {

        $stmt_val   = $this->dbh->prepare("select * from Usuarios where login=:name and password_hash=:password");
        $stmt_val->bindParam(":name", $nombre);
        $stmt_val->bindParam(":password", $contrasena);

        $cli = false;

        if ($stmt_val->execute()) {
            if ($obj = $stmt_val->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }



    // UPDATE TODO
    public function modCliente($cli): bool
    {

        $stmt_moduser   = $this->dbh->prepare("update Clientes set first_name=:first_name,last_name=:last_name" .
            ",email=:email,gender=:gender, ip_address=:ip_address,telefono=:telefono WHERE id=:id");
        $stmt_moduser->bindValue(':first_name', $cli->first_name);
        $stmt_moduser->bindValue(':last_name', $cli->last_name);
        $stmt_moduser->bindValue(':email', $cli->email);
        $stmt_moduser->bindValue(':gender', $cli->gender);
        $stmt_moduser->bindValue(':ip_address', $cli->ip_address);
        $stmt_moduser->bindValue(':telefono', $cli->telefono);
        $stmt_moduser->bindValue(':id', $cli->id);

        $stmt_moduser->execute();
        $resu = ($stmt_moduser->rowCount() == 1);
        return $resu;
    }


    //INSERT 
    public function addCliente($cli): bool
    {

        // El id se define automáticamente por autoincremento.
        $stmt_crearcli  = $this->dbh->prepare(
            "INSERT INTO `Clientes`( `first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`)" .
                "Values(?,?,?,?,?,?)"
        );
        $stmt_crearcli->bindValue(1, $cli->first_name);
        $stmt_crearcli->bindValue(2, $cli->last_name);
        $stmt_crearcli->bindValue(3, $cli->email);
        $stmt_crearcli->bindValue(4, $cli->gender);
        $stmt_crearcli->bindValue(5, $cli->ip_address);
        $stmt_crearcli->bindValue(6, $cli->telefono);
        $stmt_crearcli->execute();
        $resu = ($stmt_crearcli->rowCount() == 1);
        return $resu;
    }


    //DELETE 
    public function borrarCliente(int $id): bool
    {


        $stmt_boruser   = $this->dbh->prepare("delete from Clientes where id =:id");

        $stmt_boruser->bindValue(':id', $id);
        $stmt_boruser->execute();
        $resu = ($stmt_boruser->rowCount() == 1);
        return $resu;
    }


    // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    {
        trigger_error('La clonación no permitida', E_USER_ERROR);
    }
}