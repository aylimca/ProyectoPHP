<table>
<tr>
        <th><a href="?ordenacion=id">ID</a> </th>
        <th><a href="?ordenacion=first_name">Nombre</a></th>
        <th><a href="?ordenacion=email">Email</a></th>
        <th><a href="?ordenacion=gender">Genero</a>
        <th><a href="?ordenacion=ip_address">IP</a>
        <th><a href="?ordenacion=telefono">Teléfono</a> 
    </tr>
    <?php foreach ($tvalores as $valor) : ?>
        <tr>
            <td><?= $valor->id ?> </td>
            <td><?= $valor->first_name ?> </td>
            <td><?= $valor->email ?> </td>
            <td><?= $valor->gender ?> </td>
            <td><?= $valor->ip_address ?> </td>
            <td><?= $valor->telefono ?> </td>
            <td>Borrar</td>
            <td>Modificar</td>
            <td><a href="?orden=Detalles&id=<?= $valor->id ?>">Detalles</a></td>

        <tr>
        <?php endforeach ?>
</table>

<form>
    <br>
    <button type="submit" name="nav" value="Primero">
        << </button>
            <button type="submit" name="nav" value="Anterior">
                < </button>
                    <button type="submit" name="nav" value="Siguiente"> > </button>
                    <button type="submit" name="nav" value="Ultimo"> >> </button>
                    <br>
</form>
<form action="" style="text-align: right;">
    <button type="submit" name="orden" value="Terminar">Cerrar sesion</button>
</form>