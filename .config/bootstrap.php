<?php

# Este archivo es cargado automaticamente cada que se  requiera
# autoload.php en cualquier parte del proyecto y asi cargar las
# variables de entorno en ese archivo.

# Para lograr esto se a modificado composer.json.

# carga Composer
require __DIR__ . '/../vendor/autoload.php';

# carga la clase personalizada que gestiona dont .env
(new App\Controller\DotEnvEnvironment)->load(__DIR__);

# Nota: Las variables de entorno ya estan definidas en    .env
#       y solo son de lectura por lo que su contenido no puede
#       cambiar

?>