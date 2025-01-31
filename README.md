1.Mostrar e implementar en detalles y en modificar la opción de siguiente y anterior

2. Mejorar las operaciones de Nuevo y Modificar para que chequee que los datos son correctos:  correo electrónico (no repetido), IP correcta y  teléfono con formato 999-999-9999

3. Mostrar una imagen asociada al cliente almacenada previamente en uploads o una imagen por defecto aleatoria generada por https://robohash.org.  sin no existe. En nombre de las fotos tiene el formato 00000XXX.jpg para el cliente con id XXX.

4. Permitir subir o cambiar la foto del cliente en modificar y en nuevo (La imagen no es obligatoria). Hay que controlar que el fichero subido sea una imagen jpg  o png de un tamaño inferior a 500 Kbps. 

5. Mostrar en detalles una bandera del país asociado a la IP ( utilizar https://ip-api.com/  y  https://flagpedia.net/ )

6. No

7. No

8. Crear una nueva tabla en la BD de usuarios de la aplicación (User)  con tres campos: login, password( encriptada )  y rol (0/1), definir varios usuarios y controlar el acceso a la aplicación sólo si se introduce el login y el password correctos. Si se realizan más de tres intentos erróneos se solicitará que se reinicie el navegador.
  
9. No 

10. Utilizar geoip y el api para javascript https://openlayers.org o similar para mostrar la localización geográfica del cliente  en un mapa en función de su IP.
