<hr>
<?php $_SESSION["msg"]; ?>
<form method="POST">
    <table>
        <tr>
        <tr>
            <td>Imagen usuario: </td>
            <td> <input type="file" name="foto" accept=".jpg, .png"></td>
        </tr>
        <td>ID:</td>
        <td><input type="number" name="id" value="<?= $cli->id ?>" readonly></td>
        </tr>
        </tr>
        <tr>
            <td>Nombre: </td>
            <td><input type="text" name="first_name" value="<?= $cli->first_name ?>" autofocus></td>
        </tr>
        </tr>
        <tr>
            <td>Apellido: </td>
            <td><input type="text" name="last_name" value="<?= $cli->last_name ?>"></td>
        </tr>
        </tr>
        <tr>
            <td>Email: </td>
            <td><input type="email" name="email" value="<?= $cli->email ?>"></td>
        </tr>
        </tr>
        <tr>
            <td>Género: </td>
            <td><input type="text" name="gender" value="<?= $cli->gender ?>"></td>
        </tr>
        </tr>
        <tr>
            <td>IP: </td>
            <td><input type="text" name="ip_address" value="<?= $cli->ip_address ?>"></td>
        </tr>
        </tr>
        <tr>
            <td>Teléfono: </td>
            <td><input type="tel" name="telefono" value="<?= $cli->telefono ?>"></td>
        </tr>
        </tr>
    </table>
    <input type="submit" name="orden" value="<?= $orden ?>">
    <input type="submit" name="orden" value="Volver">

</form> <br>