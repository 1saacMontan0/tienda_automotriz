<?php

# conexion a la base de datos.

# Debes de asegurarte  que antes de llamar la funcion debes cargar autoload
# en donde quieras hacer una conexion y luego llamar la funcion exactamente
# de la siguiente manera.

# $conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

# Nota: el nombre de la variable donde guardas el retorno no importa.

require ('../vendor/autoload.php');

function conexion($host, $user, $secret, $db) {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $secret);
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    return $pdo;
}

?>