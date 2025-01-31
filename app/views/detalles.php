<?php
// Obtener latitud y longitud de la IP
$json_data = file_get_contents("http://ip-api.com/json/$cli->ip_address?fields=57538");
$data = json_decode($json_data, true);
$latitud = 0;
$longitud = 0;
if (isset($data['lat']) && isset($data['lon'])) {
    $latitud = $data['lat'];
    $longitud = $data['lon'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información del Cliente</title>
    <link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            max-width: 900px;
            margin: auto;
        }
        .cliente-info {
            list-style: none;
            padding: 0;
        }
        .cliente-info li {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .cliente-info li img{
width: 40px;
height: 24px;
border-radius: 3px;
        }
        .cliente-info img {
            width: 30px;
            height: 20px;
            margin-left: 10px;
        }

        #map {
            clear: both;
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Información del cliente</h2>
        <?= mostrarFoto($cli->id) ?>
        <!-- <?= numeroFormateado($cli->id) ?> -->
        <ul class="cliente-info">
            <li><strong>Nombre:     </strong> <?= $cli->first_name ?> <img src="<?= banderaIP($cli->ip_address) ?>" alt="Bandera"></li>
            <li><strong>Apellido:   </strong> <?= $cli->last_name ?></li>
            <li><strong>Email:      </strong> <?= $cli->email ?></li>
            <li><strong>IP: </strong> <?= $cli->ip_address ?></li>
            <li><strong>Teléfono:   </strong> <?= $cli->telefono ?></li>
        </ul>
        <h2>Localización</h2>
        <div id="mapa">
        <div id="map"></div>
        </div>
    </div>
    
<form>

    <button type="submit" name="nav-detalles" value="Anterior"> Anterior << </button>
            <button type="submit" name="nav-detalles" value="Siguiente"> Siguiente >> </button> <br><br><button onclick="location.href='./'"> Volver </button>
            <?= $_SESSION["msg"] ?>
</form>

    <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script>
    <script>
        var latitud = <?php echo $latitud; ?>;
        var longitud = <?php echo $longitud; ?>;

        // Crear mapa
        var map = new ol.Map({
            target: 'map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat([longitud, latitud]),
                zoom: 12
            })
        });

        // Añadir marcador
        var marker = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([longitud, latitud]))
        });

        var markerStyle = new ol.style.Style({
            image: new ol.style.Icon({
                src: 'https://openlayers.org/en/latest/examples/data/icon.png'
            })
        });

        marker.setStyle(markerStyle);

        var vectorSource = new ol.source.Vector({
            features: [marker]
        });

        var markerVectorLayer = new ol.layer.Vector({
            source: vectorSource
        });

        map.addLayer(markerVectorLayer);
    </script>
</body>
</html>
