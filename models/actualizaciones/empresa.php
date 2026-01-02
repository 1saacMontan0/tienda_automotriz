
<?php

function actualizar_empresa($conexion, $id_empresa, $nombre, $telefono, $correo, $direccion, $nit, $nrc, $giro) {
    try {
        $sentencia = "UPDATE empresa SET 
                        nombre = ?, 
                        telefono = ?, 
                        correo = ?, 
                        direccion = ?, 
                        nit = ?, 
                        nrc = ?, 
                        giro = ? 
                      WHERE id_empresa = ?";
        
        $consulta = $conexion->prepare($sentencia);
        
        return $consulta->execute([
            $nombre, 
            $telefono, 
            $correo, 
            $direccion, 
            $nit, 
            $nrc, 
            $giro, 
            $id_empresa
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

?>