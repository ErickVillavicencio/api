<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credential: true");
header("Access-Control-Allow-Methods: PUT,GET,POST,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,Accept,X-Request-Whith,x-xsrf-token");
header("Content-Type: application/json; charset=utf-8");

include 'config.php';

$postjson = json_decode(file_get_contents('php://input'), true);


//registrar ruta
if ($postjson['aksi'] == "proceso_registrarRuta") {

    $verRuta = mysqli_fetch_array(mysqli_query($mysqli, "SELECT id, nombre FROM ruta 
    WHERE nombre = '$postjson[nombre]' AND idUsuario =  $postjson[idUsuario]"));

    if ($verRuta['nombre'] == $postjson['nombre']) {
        $result = json_encode(array('success' => false, 'msg' => 'Nombre de la ruta  ya existe'));
    } else {

        $insert = mysqli_query($mysqli, " INSERT INTO ruta SET
   nombre           = '$postjson[nombre]',
   numeroAdultos    = $postjson[numeroAdultos],
   numeroNinios     = $postjson[numeroNinios],
   horaInicio       = '$postjson[horaInicio]',
   idUsuario        = $postjson[idUsuario]
   ");

$idUsuario = $postjson['idUsuario'];
$nombreRuta =$postjson['nombre'];

$consulta =
"SELECT
puntoturistico.id as idPunto, 
puntoturistico.nombre as nombrePunto, 
puntoturistico.descripcion as puntoDescripcion,
puntoturistico.latitud as latitud, 
puntoturistico.longuitud as longuitud,
puntoturistico.costo as costoAdulto,
puntoturistico.costoN as costoNinio, 
puntoturistico.tiempoEstimado as tiempoEstimado, 
parroquia.descripcion as nombreParroquia, 
categoria.descripcion as categoriaNombre,
subcategoria.descripcion as subcategoriaNombre, 
imagen.direccion as imagen
FROM puntoturistico
INNER JOIN parroquia
ON puntoturistico.idParroquia = parroquia.id
INNER JOIN categoria
ON puntoturistico.categoriaId = categoria.id
INNER JOIN subcategoria
ON subcategoria.id = puntoturistico.subCategoriaId
INNER JOIN imagen
ON imagen.idPuntoTuristico = puntoturistico.id
AND puntoturistico.estado = 1
AND imagen.categoria = 1
ORDER BY puntoturistico.id ASC";

        //lleno el array q se enviara 
        if ($insert) {

            $parroquia = "SELECT * FROM parroquia WHERE estado =  1 ";
            if ($resultado = $mysqli->query($parroquia)) {
                $parroquias = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $parroquias[$i]              = array();
                    $parroquias[$i]['id']   = $fila[0];
                    $parroquias[$i]['descripcion']   = $fila[1];
                    $i++;
                }
            }

            $valor = "SELECT * FROM ruta WHERE nombre = '$nombreRuta' AND idUsuario = $idUsuario";
            if ($resultado = $mysqli->query($valor)) {
                $valores = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $valores[$i]                       = array();
                    $valores[$i] ['idRuta']             = $fila[0];
                    $valores[$i] ['nombreRuta']         = $fila[1];   
                    $valores[$i] ['horaInicio']         = $fila[2]; 
                    $valores[$i] ['idUsuario']          = $fila[5];  
                    $valores[$i] ['numeroAdultos']      = $fila[6]; 
                    $valores[$i] ['numeroNinios']       = $fila[7];             
                    $i++;
                }
            }

            $subcategorias = "SELECT * FROM subcategoria";
            if ($resultado = $mysqli->query($subcategorias)) {
                $subcategoriasL = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $subcategoriasL[$i]                       = array();
                    $subcategoriasL[$i]['id']                 = $fila[0];
                    $subcategoriasL[$i]['descripcion']        = $fila[1];
                    $subcategoriasL[$i]['idCategoria']        = $fila[2];
                    $i++;
                }
            }
        
            $categorias = "SELECT * FROM categoria";
            if ($resultado = $mysqli->query($categorias)) {
                $categoriasL = array();
                $categoriasL1 = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $categoriasL[$i]                       = array();
                    $categoriasL[$i]['id']                 = $fila[0];
                    $categoriasL[$i]['descripcion']        = $fila[1];
                    array_push($categoriasL1,$fila[0]);
                    $i++;
                }
            }


            $x = 0;
            if ($resultado = $mysqli->query($consulta)) {
                $ruta = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $ruta[$i]                       = array();
                    $ruta[$i]['id']                 = $fila[0];
                    $ruta[$i]['nombre']             = $fila[1];
                    $ruta[$i]['Descripcion']        = $fila[2];
                    $ruta[$i]['latitud']            = $fila[3];
                    $ruta[$i]['longuitud']          = $fila[4];
                    $ruta[$i]['costoAdulto']        = $fila[5];
                    $ruta[$i]['costoNinio']         = $fila[6];
                    $ruta[$i]['tiempoEstimado']     = $fila[7];
                    $ruta[$i]['nombreParroquia']    = $fila[8];
                    $ruta[$i]['categoriaNombre']    = $fila[9];
                    $ruta[$i]['subcategoriaNombre'] = $fila[10];
                    $ruta[$i]['imagen']             = $fila[11];
                    $x = $i;
                    $i++;
                    
                }
            }

            $ruta[$x+1]['valores']  = $valores;
            $ruta[$x+2]['parroquias']  = $parroquias;
            $ruta[$x+3]['categoria']  = $categoriasL;
            $ruta[$x+4]['subcategoria']  = $subcategoriasL;

            $result = json_encode(array('success' => true, 'result' => $ruta));
        } else {

            $result = json_encode(array('success' => false, 'msg' => 'ocurrio un error'));
        }
    }
    echo $result;
}



//registrar ruta
if ($postjson['aksi'] == "proceso_filtro_agregar") {

$idUsuario = $postjson['idUsuario'];
$nombreRuta =$postjson['nombre'];
$where =$postjson['condicion'];;


$consulta =
"SELECT
puntoturistico.id as idPunto, 
puntoturistico.nombre as nombrePunto, 
puntoturistico.descripcion as puntoDescripcion,
puntoturistico.latitud as latitud, 
puntoturistico.longuitud as longuitud,
puntoturistico.costo as costoAdulto,
puntoturistico.costoN as costoNinio, 
puntoturistico.tiempoEstimado as tiempoEstimado, 
parroquia.descripcion as nombreParroquia, 
categoria.descripcion as categoriaNombre,
subcategoria.descripcion as subcategoriaNombre, 
imagen.direccion as imagen
FROM puntoturistico
INNER JOIN parroquia
ON puntoturistico.idParroquia = parroquia.id
INNER JOIN categoria
ON puntoturistico.categoriaId = categoria.id
INNER JOIN subcategoria
ON subcategoria.id = puntoturistico.subCategoriaId
INNER JOIN imagen
ON imagen.idPuntoTuristico = puntoturistico.id
AND puntoturistico.estado = 1
AND imagen.categoria = 1
$where
ORDER BY puntoturistico.id ASC";

        //lleno el array q se enviara 
 

            $parroquia = "SELECT * FROM parroquia WHERE estado =  1 ";
            if ($resultado = $mysqli->query($parroquia)) {
                $parroquias = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $parroquias[$i]              = array();
                    $parroquias[$i]['id']   = $fila[0];
                    $parroquias[$i]['descripcion']   = $fila[1];
                    $i++;
                }
            }

            $valor = "SELECT * FROM ruta WHERE nombre = '$nombreRuta' AND idUsuario = $idUsuario";
            if ($resultado = $mysqli->query($valor)) {
                $valores = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $valores[$i]                       = array();
                    $valores[$i] ['idRuta']             = $fila[0];
                    $valores[$i] ['nombreRuta']         = $fila[1];   
                    $valores[$i] ['horaInicio']         = $fila[2]; 
                    $valores[$i] ['idUsuario']          = $fila[5];  
                    $valores[$i] ['numeroAdultos']      = $fila[6]; 
                    $valores[$i] ['numeroNinios']       = $fila[7];             
                    $i++;
                }
            }

            $subcategorias = "SELECT * FROM subcategoria";
            if ($resultado = $mysqli->query($subcategorias)) {
                $subcategoriasL = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $subcategoriasL[$i]                       = array();
                    $subcategoriasL[$i]['id']                 = $fila[0];
                    $subcategoriasL[$i]['descripcion']        = $fila[1];
                    $subcategoriasL[$i]['idCategoria']        = $fila[2];
                    $i++;
                }
            }
        
            $categorias = "SELECT * FROM categoria";
            if ($resultado = $mysqli->query($categorias)) {
                $categoriasL = array();
                $categoriasL1 = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $categoriasL[$i]                       = array();
                    $categoriasL[$i]['id']                 = $fila[0];
                    $categoriasL[$i]['descripcion']        = $fila[1];
                    array_push($categoriasL1,$fila[0]);
                    $i++;
                }
            }


            $x = 0;
            if ($resultado = $mysqli->query($consulta)) {
                $ruta = array();
                $i = 0;
                while ($fila = $resultado->fetch_row()) {
                    $ruta[$i]                       = array();
                    $ruta[$i]['id']                 = $fila[0];
                    $ruta[$i]['nombre']             = $fila[1];
                    $ruta[$i]['Descripcion']        = $fila[2];
                    $ruta[$i]['latitud']            = $fila[3];
                    $ruta[$i]['longuitud']          = $fila[4];
                    $ruta[$i]['costoAdulto']        = $fila[5];
                    $ruta[$i]['costoNinio']         = $fila[6];
                    $ruta[$i]['tiempoEstimado']     = $fila[7];
                    $ruta[$i]['nombreParroquia']    = $fila[8];
                    $ruta[$i]['categoriaNombre']    = $fila[9];
                    $ruta[$i]['subcategoriaNombre'] = $fila[10];
                    $ruta[$i]['imagen']             = $fila[11];
                    $x = $i;
                    $i++;
                    
                }
            }

            $ruta[$x+1]['valores']  = $valores;
            $ruta[$x+2]['parroquias']  = $parroquias;
            $ruta[$x+3]['categoria']  = $categoriasL;
            $ruta[$x+4]['subcategoria']  = $subcategoriasL;

            $select = mysqli_query($mysqli, "SELECT
            puntoturistico.id as idPunto, 
            puntoturistico.nombre as nombrePunto, 
            puntoturistico.descripcion as puntoDescripcion,
            puntoturistico.latitud as latitud, 
            puntoturistico.longuitud as longuitud,
            puntoturistico.costo as costoAdulto,
            puntoturistico.costoN as costoNinio, 
            puntoturistico.tiempoEstimado as tiempoEstimado, 
            parroquia.descripcion as nombreParroquia, 
            categoria.descripcion as categoriaNombre,
            subcategoria.descripcion as subcategoriaNombre, 
            imagen.direccion as imagen
            FROM puntoturistico
            INNER JOIN parroquia
            ON puntoturistico.idParroquia = parroquia.id
            INNER JOIN categoria
            ON puntoturistico.categoriaId = categoria.id
            INNER JOIN subcategoria
            ON subcategoria.id = puntoturistico.subCategoriaId
            INNER JOIN imagen
            ON imagen.idPuntoTuristico = puntoturistico.id
            AND puntoturistico.estado = 1
            AND imagen.categoria = 1
            $where
            ORDER BY puntoturistico.id ASC");

    if ($select) {
            $result = json_encode(array('success' => true, 'result' => $ruta));
        } else {

            $result = json_encode(array('success' => false, 'msg' => 'ocurrio un error'));
        }
    
    echo $result;

}




//cargar datos para actualizar ruta
else if ($postjson['aksi'] == "proceso_editarRuta") {

    
    $idRuta =  $postjson['idRuta'];
    $idUsuario = $postjson['idUsuario']; 
    $nombreRuta = $postjson['nombre'];   
    $horaInicio = $postjson['horaInicio'];   
    $numeroAdultos = $postjson['numeroAdultos'];   
    $numeroNinios = $postjson['numeroNinios'];   
  


    $subcategorias = "SELECT * FROM subcategoria";
    if ($resultado = $mysqli->query($subcategorias)) {
        $subcategoriasL = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $subcategoriasL[$i]                       = array();
            $subcategoriasL[$i]['id']                 = $fila[0];
            $subcategoriasL[$i]['descripcion']        = $fila[1];
            $subcategoriasL[$i]['idCategoria']        = $fila[2];
            $i++;
        }
    }

    $categorias = "SELECT * FROM categoria";
    if ($resultado = $mysqli->query($categorias)) {
        $categoriasL = array();
        $categoriasL1 = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $categoriasL[$i]                       = array();
            $categoriasL[$i]['id']                 = $fila[0];
            $categoriasL[$i]['descripcion']        = $fila[1];
            array_push($categoriasL1,$fila[0]);
            $i++;
        }
    }

    $consulta =
    "SELECT
    puntoturistico.id as idPunto, 
    puntoturistico.nombre as nombrePunto, 
    puntoturistico.descripcion as puntoDescripcion,
    puntoturistico.latitud as latitud, 
    puntoturistico.longuitud as longuitud,
    puntoturistico.costo as costoAdulto,
    puntoturistico.costoN as costoNinio, 
    puntoturistico.tiempoEstimado as tiempoEstimado, 
    parroquia.descripcion as nombreParroquia, 
    categoria.descripcion as categoriaNombre,
    subcategoria.descripcion as subcategoriaNombre, 
    imagen.direccion as imagen
    FROM puntoturistico
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON puntoturistico.categoriaId = categoria.id
    INNER JOIN subcategoria
    ON subcategoria.id = puntoturistico.subCategoriaId
    INNER JOIN imagen
    ON imagen.idPuntoTuristico = puntoturistico.id
    AND puntoturistico.estado = 1
    AND imagen.categoria = 1
    ORDER BY puntoturistico.id ASC";
    
    $parroquia = "SELECT * FROM parroquia WHERE estado =  1 ";


    if ($resultado = $mysqli->query($parroquia)) {
        $parroquias = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $parroquias[$i]              = array();
            $parroquias[$i]['id']   = $fila[0];
            $parroquias[$i]['descripcion']   = $fila[1];
            $i++;
        }
    }


if(1==1){
    $valores[0]                       = array();
    $valores[0] ['nombreRuta']         = $nombreRuta;   
    $valores[0] ['idRuta']             = $idRuta; 
    $valores[0] ['horaInicio']         = $horaInicio; 
    $valores[0] ['numeroAdultos']      = $numeroAdultos; 
    $valores[0] ['numeroNinios']       = $numeroNinios;    
    $valores[0] ['idUsuario']          = $idUsuario;   

}


    $puntosRuta = "SELECT * FROM puntosruta WHERE idRuta =  $idRuta ";

    if ($resultado = $mysqli->query($puntosRuta)) {
        $puntos = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $puntos[$i]              = array();
            $puntos[$i]['idPunto']   = $fila[1];
            $i++;
        }
    }

    $x = 0;

  
    if ($resultado = $mysqli->query($consulta)) {
        $ruta = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $ruta[$i]                       = array();
            $ruta[$i]['id']                 = $fila[0];
            $ruta[$i]['nombre']             = $fila[1];
            $ruta[$i]['Descripcion']        = $fila[2];
            $ruta[$i]['latitud']            = $fila[3];
            $ruta[$i]['longuitud']          = $fila[4];
            $ruta[$i]['costoAdulto']        = $fila[5];
            $ruta[$i]['costoNinio']         = $fila[6];
            $ruta[$i]['tiempoEstimado']     = $fila[7];
            $ruta[$i]['nombreParroquia']    = $fila[8];
            $ruta[$i]['categoriaNombre']    = $fila[9];
            $ruta[$i]['subcategoriaNombre'] = $fila[10];
            $ruta[$i]['imagen']             = $fila[11];
            $x = $i;
            $i++;
        }
    }
        $ruta[$x+1]['valores']  = $valores;
        $ruta[$x+2]['parroquias']  = $parroquias;
        $ruta[$x+3]['categoria']  = $categoriasL;
        $ruta[$x+4]['subcategoria']  = $subcategoriasL;
        $ruta[$x+5]['lista']  = $puntos;

    $select = mysqli_query($mysqli, "SELECT * FROM puntosruta WHERE idRuta =  $idRuta ");

    $select2 = mysqli_query($mysqli, "SELECT
    puntoturistico.id as idPunto, puntoturistico.nombre as nombrePunto,  puntoturistico.descripcion as puntoDescripcion,
    puntoturistico.latitud as latitud,  puntoturistico.longuitud as longuitud, puntoturistico.costo as costoAdulto,
    puntoturistico.costoN as costoNinio,  puntoturistico.tiempoEstimado as tiempoEstimado, 
    parroquia.descripcion as nombreParroquia,  categoria.descripcion as categoriaNombre, subcategoria.descripcion as subcategoriaNombre, 
    imagen.direccion as imagen FROM puntoturistico INNER JOIN parroquia  ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria  ON puntoturistico.categoriaId = categoria.id INNER JOIN subcategoria
    ON subcategoria.id = puntoturistico.subCategoriaId  INNER JOIN imagen  ON imagen.idPuntoTuristico = puntoturistico.id AND puntoturistico.estado = 1  AND imagen.categoria = 1  ORDER BY puntoturistico.id ASC");

    if ($select && $select2) {
        $result = json_encode(array('success' => true, 'result' => $ruta));
    } else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al registrar'));
    }

echo $result;

}




//cargar datos para actualizar ruta con condicion de busqueda
else if ($postjson['aksi'] == "proceso_editarRuta_filtro") {

    $where =  $postjson['condicion'];
    $idRuta =  $postjson['idRuta'];
    $idUsuario = $postjson['idUsuario']; 
    $nombreRuta = $postjson['nombre'];   
    $horaInicio = $postjson['horaInicio'];   
    $numeroAdultos = $postjson['numeroAdultos'];   
    $numeroNinios = $postjson['numeroNinios'];   
  


    $subcategorias = "SELECT * FROM subcategoria";
    if ($resultado = $mysqli->query($subcategorias)) {
        $subcategoriasL = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $subcategoriasL[$i]                       = array();
            $subcategoriasL[$i]['id']                 = $fila[0];
            $subcategoriasL[$i]['descripcion']        = $fila[1];
            $subcategoriasL[$i]['idCategoria']        = $fila[2];
            $i++;
        }
    }

    $categorias = "SELECT * FROM categoria";
    if ($resultado = $mysqli->query($categorias)) {
        $categoriasL = array();
        $categoriasL1 = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $categoriasL[$i]                       = array();
            $categoriasL[$i]['id']                 = $fila[0];
            $categoriasL[$i]['descripcion']        = $fila[1];
            array_push($categoriasL1,$fila[0]);
            $i++;
        }
    }

    $consulta =
    "SELECT
    puntoturistico.id as idPunto, 
    puntoturistico.nombre as nombrePunto, 
    puntoturistico.descripcion as puntoDescripcion,
    puntoturistico.latitud as latitud, 
    puntoturistico.longuitud as longuitud,
    puntoturistico.costo as costoAdulto,
    puntoturistico.costoN as costoNinio, 
    puntoturistico.tiempoEstimado as tiempoEstimado, 
    parroquia.descripcion as nombreParroquia, 
    categoria.descripcion as categoriaNombre,
    subcategoria.descripcion as subcategoriaNombre, 
    imagen.direccion as imagen
    FROM puntoturistico
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON puntoturistico.categoriaId = categoria.id
    INNER JOIN subcategoria
    ON subcategoria.id = puntoturistico.subCategoriaId
    INNER JOIN imagen
    ON imagen.idPuntoTuristico = puntoturistico.id
    AND puntoturistico.estado = 1
    AND imagen.categoria = 1
    $where
    ORDER BY puntoturistico.id ASC";
    
    $parroquia = "SELECT * FROM parroquia WHERE estado =  1 ";


    if ($resultado = $mysqli->query($parroquia)) {
        $parroquias = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $parroquias[$i]              = array();
            $parroquias[$i]['id']   = $fila[0];
            $parroquias[$i]['descripcion']   = $fila[1];
            $i++;
        }
    }


if(1==1){
    $valores[0]                       = array();
    $valores[0] ['nombreRuta']         = $nombreRuta;   
    $valores[0] ['idRuta']             = $idRuta; 
    $valores[0] ['horaInicio']         = $horaInicio; 
    $valores[0] ['numeroAdultos']      = $numeroAdultos; 
    $valores[0] ['numeroNinios']       = $numeroNinios;    
    $valores[0] ['idUsuario']          = $idUsuario;   
}


    $puntosRuta = "SELECT * FROM puntosruta WHERE idRuta =  $idRuta ";

    if ($resultado = $mysqli->query($puntosRuta)) {
        $puntos = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $puntos[$i]              = array();
            $puntos[$i]['idPunto']   = $fila[1];
            $i++;
        }
    }

    $x = 0;

  
    if ($resultado = $mysqli->query($consulta)) {
        $ruta = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $ruta[$i]                       = array();
            $ruta[$i]['id']                 = $fila[0];
            $ruta[$i]['nombre']             = $fila[1];
            $ruta[$i]['Descripcion']        = $fila[2];
            $ruta[$i]['latitud']            = $fila[3];
            $ruta[$i]['longuitud']          = $fila[4];
            $ruta[$i]['costoAdulto']        = $fila[5];
            $ruta[$i]['costoNinio']         = $fila[6];
            $ruta[$i]['tiempoEstimado']     = $fila[7];
            $ruta[$i]['nombreParroquia']    = $fila[8];
            $ruta[$i]['categoriaNombre']    = $fila[9];
            $ruta[$i]['subcategoriaNombre'] = $fila[10];
            $ruta[$i]['imagen']             = $fila[11];    
            $x = $i;
            $i++;
        }
    }
        $ruta[$x]['valores']  = $valores;
        $ruta[$x+1]['parroquias']  = $parroquias;
        $ruta[$x+2]['categoria']  = $categoriasL;
        $ruta[$x+3]['subcategoria']  = $subcategoriasL;
        $ruta[$x+4]['lista']  = $puntos;

    $select = mysqli_query($mysqli, "SELECT * FROM puntosruta WHERE idRuta =  $idRuta ");

    $select2 = mysqli_query($mysqli, "SELECT
    puntoturistico.id as idPunto, puntoturistico.nombre as nombrePunto,  puntoturistico.descripcion as puntoDescripcion,
    puntoturistico.latitud as latitud,  puntoturistico.longuitud as longuitud, puntoturistico.costo as costoAdulto,
    puntoturistico.costoN as costoNinio,  puntoturistico.tiempoEstimado as tiempoEstimado, 
    parroquia.descripcion as nombreParroquia,  categoria.descripcion as categoriaNombre, subcategoria.descripcion as subcategoriaNombre, 
    imagen.direccion as imagen FROM puntoturistico INNER JOIN parroquia  ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria  ON puntoturistico.categoriaId = categoria.id INNER JOIN subcategoria
    ON subcategoria.id = puntoturistico.subCategoriaId  INNER JOIN imagen  ON imagen.idPuntoTuristico = puntoturistico.id 
    AND puntoturistico.estado = 1  AND imagen.categoria = 1 
    $where
    ORDER BY puntoturistico.id ASC");

    if ($select && $select2) {
        $result = json_encode(array('success' => true, 'result' => $ruta));
    } else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al registrar'));
    }

echo $result;

}










//registrar punto 
else if ($postjson['aksi'] == "proceso_registrar_punto") {

    $verpunto = mysqli_fetch_array(mysqli_query($mysqli, "SELECT idPunto FROM puntosruta 
    WHERE idRuta = $postjson[idRuta] AND idPunto = $postjson[idPunto]"));

    if ($verpunto['idPunto'] == $postjson['idPunto']) {
        $result = json_encode(array('success' => false, 'msg' => 'El punto ya se encuentra Agregado'));
    } else {

        $insert = mysqli_query($mysqli, " INSERT INTO puntosruta SET
idPunto      = $postjson[idPunto],
idRuta    = $postjson[idRuta]
");

        if ($insert) {
            $result = json_encode(array('success' => true, 'msg' => 'Registrado exitosamente'));
        } else {
            $result = json_encode(array('success' => false, 'msg' => 'error'));
        }
    }
    echo $result;
}

//generar la ruta
else if ($postjson['aksi'] == "proceso_generar_ruta") {

    //obtener la suma del tiempo estimado de los puntos segun la ruta //
    $idUsuario = $postjson['idUsuario'];
    $nombre = $postjson['nombre'];
    $idRuta = $postjson['idRuta'];
    $numeroAdultos = $postjson['numeroAdultos'];
    $numeroNinios = $postjson['numeroNinios'];
    $fecha = $postjson['horaInicio'];

    $consulta = "SELECT 
    SEC_TO_TIME(SUM(TIME_TO_SEC(puntoturistico.tiempoEstimado))) suma,
     sum(costo) as totalCostoA, sum(costoN) as totalCostoN
    FROM puntoturistico INNER JOIN puntosruta
    on puntoturistico.id = puntosruta.idPunto
    WHERE puntosruta.idRuta = $idRuta";

    //lleno el array q se enviara a la pagina home
    if ($resultado = $mysqli->query($consulta)) {
        $sumas = array();
        $i = 0;

        while ($fila = $resultado->fetch_row()) {
            $sumas = array();
            $sumas[$i]['suma']           = $fila[0];
            $sumas[$i]['totalCostoA']    = $fila[1];
            $sumas[$i]['totalCostoN']    = $fila[2];
            $i++;
        }
    }

    $total = $sumas[0]['suma'];
    $costoA = $sumas[0]['totalCostoA'] * $numeroAdultos;
    $costoN = $sumas[0]['totalCostoN'] * $numeroNinios;
    $costo = $costoA + $costoN;

    $puntosRuta = "SELECT puntoturistico.id as id,puntoturistico.nombre as nombre, puntoturistico.descripcion as descripcion,
    puntoturistico.latitud as latitud, puntoturistico.longuitud as longuitud,puntoturistico.costo as costo,puntoturistico.costoN as costoN,
     puntoturistico.tiempoEstimado as tiempoEstimado,imagen.direccion as imagen,parroquia.descripcion as parroquian,categoria.descripcion as catnombre ,
     subcategoria.descripcion as subnombre
    FROM ruta
    INNER JOIN puntosruta
    ON puntosruta.idRuta = ruta.id    
    INNER JOIN puntoturistico 
    ON puntosruta.idPunto = puntoturistico.id 
    INNER JOIN imagen
    ON puntoturistico.id = imagen.idPuntoTuristico
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON categoria.id = puntoturistico.categoriaId
    INNER JOIN subcategoria
    on subcategoria.id = puntoturistico.subCategoriaId
    WHERE  imagen.categoria = 1 AND ruta.id = $idRuta AND puntoturistico.estado = 1 
    ORDER BY puntosruta.id ";

    if ($resultado2 = $mysqli->query($puntosRuta)) {
        $puntosR = array();
        $i = 0;
        while ($fila = $resultado2->fetch_row()) {
            $puntosR[$i] = array();
            $puntosR[$i]['id']              = $fila[0];
            $puntosR[$i]['nombre']          = $fila[1];
            $puntosR[$i]['descripcion']     = $fila[2];
            $puntosR[$i]['latitud']         = $fila[3];
            $puntosR[$i]['longuitud']       = $fila[4];
            $puntosR[$i]['costo']           = $fila[5];
            $puntosR[$i]['costoN']          = $fila[6];
            $puntosR[$i]['tiempoEstimado']  = $fila[7];
            $puntosR[$i]['imagen']          = $fila[8];
            $puntosR[$i]['parroquian']      = $fila[9];
            $puntosR[$i]['catnombre']       = $fila[10];
            $puntosR[$i]['subnombre']       = $fila[11];
            $i++;
        }

        //SEPARO FECHA INICIO
        $obj_fecha = date_create_from_format('Y-m-d', $fecha);
        $date = date_create($fecha);
        $dia = date_format($date, "Y/m/d");
        $hora = date_format($date, "H:i:s");
        $horax = date_format($date, "H:i");

        $longitud = count($puntosR);
        $ListaDistancia = array();
        $ListaTiempo = array();

        $horas;
        $minutos;
        $segundos;
        $inicio =  $hora;
        for ($i = 0; $i < $longitud; $i++) {

            $ListaDistancia[$i]['inicio'] = $puntosR[$i]['nombre'];
            $ListaDistancia[$i]['inicioTrayecto'] = $inicio;
            $ListaDistancia[$i]['tiempoEstimado'] = $puntosR[$i]['tiempoEstimado'];
            $ListaDistancia[$i]['nombre'] = $nombre;
            $ListaDistancia[$i]['idRuta'] = $idRuta;
            $ListaDistancia[$i]['idUsuario'] = $idUsuario;
            $ListaDistancia[$i]['latitud'] = $puntosR[$i]['latitud'];
            $ListaDistancia[$i]['longuitud'] = $puntosR[$i]['longuitud'];
            $ListaDistancia[$i]['imagen'] =  $puntosR[$i]['imagen'];
            $ListaDistancia[$i]['idPunto'] = $puntosR[$i]['id'];
            $ListaDistancia[$i]['costo'] = $costo;
            $j = $i + 1;
            if ($j < $longitud) {
                $lat1 = $puntosR[$i]['latitud'];
                $lat2 = $puntosR[$j]['latitud'];
                $lon1 = $puntosR[$i]['longuitud'];
                $lon2 = $puntosR[$j]['longuitud'];
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distancia = $miles * 1.609344;
                $ListaDistancia[$i]['fin'] = $puntosR[$j]['nombre'];

                //si l distancia entre puntos es menor a 2 kimoletros se caminara
                if ($distancia < 2) {
                    $velocidad = 5.3; // k/h
                    $velocidads = $velocidad * (5 / 18); //transformo k/h a m/s
                    $distanciam = $distancia * 1000; //tranformo km a m
                    $tiempoD = $distanciam / $velocidads;
                    $ListaTiempo[$i] = $tiempoD; //tiempo es s
                    $ListaDistancia[$i]['recorrido'] = 'Caminata';
                    $ListaDistancia[$i]['distancia'] = $distancia;
                    $ListaDistancia[$i]['tiempo'] = $tiempoD; //tiempo es s
                    $tiempoSeg = intval($tiempoD);
                } else {
                    //si l distancia entre puntos es mayor a 2 kimoletros se usara transporte                    
                    $velocidad = 40;
                    $velocidads = $velocidad * (5 / 18); //transformo k/h a m/s
                    $distanciam = $distancia * 1000; //tranformo km a m
                    $tiempoD = $distanciam / $velocidads;
                    $ListaTiempo[$i] = $tiempoD; //tiempo es s
                    $ListaDistancia[$i]['recorrido'] = 'Transporte';
                    $ListaDistancia[$i]['distancia'] = $distancia;
                    $ListaDistancia[$i]['tiempo'] = $tiempoD; //tiempo es s
                    $tiempoSeg = intval($tiempoD);
                }
            }

            //sumo la hora inicio con el tiempo del punto  
            //tiempo estimado en formato h:m:s
            $mifecha2 = new DateTime($puntosR[$i]['tiempoEstimado']);
            $mifecha = new DateTime($inicio); //hora inicio en formato h:m:s
            //sumo las dos horas y obtengo una hora fin
            $mifecha->modify('+' . $mifecha2->format('H') . ' hours');
            $mifecha->modify('+' . $mifecha2->format('i') . ' minute');
            $mifecha->modify('+' . $mifecha2->format('s') . 'second');
            $fin = $mifecha->format('H:i:s');
            $ListaDistancia[$i]['finTrayecto'] = $fin;
            $horafin = $mifecha->format('Y/m/d H:i:s');

            if ($i < ($longitud - 1)) {
                $tiempoSeg = intval($ListaDistancia[$i]['tiempo']);
                $horas = floor($tiempoSeg / 3600);
                $minutos = floor(($tiempoSeg - ($horas * 3600)) / 60);
                $segundos = $tiempoSeg - ($horas * 3600) - ($minutos * 60);
                $mifecha->modify('+' . $horas . ' hours');
                $mifecha->modify('+' . $minutos . ' minute');
                $mifecha->modify('+' . $segundos . 'second');
                $inicio = $mifecha->format(' H:i:s');
            }
        }
    }

    $insert = mysqli_query($mysqli, " UPDATE  ruta SET
   horaFin      = '$horafin',
   costoTotal      = $costo
    WHERE id = $idRuta;
   ");

    if ($insert) {
        $result = json_encode(array('success' => true, 'result' => $ListaDistancia));
    } else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al registrar'));
    }

    echo $result;
}

//generar la ruta
else if ($postjson['aksi'] == "proceso_ver_ruta") {

    $idUsuario         = $postjson['idUsuario'];
    $nombre            = $postjson['nombre'];
    $idRuta            = $postjson['idRuta'];
    $numeroAdultos     = $postjson['numeroAdultos'];
    $numeroNinios      = $postjson['numeroNinios'];
    $fecha             = $postjson['horaInicio'];

    $consulta = "SELECT 
    SEC_TO_TIME(SUM(TIME_TO_SEC(puntoturistico.tiempoEstimado))) suma,
     sum(costo) as totalCostoA, sum(costoN) as totalCostoN
    FROM puntoturistico INNER JOIN puntosruta
    on puntoturistico.id = puntosruta.idPunto
    WHERE puntosruta.idRuta = $idRuta";

    //lleno el array q se enviara a la pagina home
    if ($resultado = $mysqli->query($consulta)) {
        $sumas = array();
        $i = 0;

        while ($fila = $resultado->fetch_row()) {
            $sumas = array();
            $sumas[$i]['suma']           = $fila[0];
            $sumas[$i]['totalCostoA']    = $fila[1];
            $sumas[$i]['totalCostoN']    = $fila[2];
            $i++;
        }
    }

    $total = $sumas[0]['suma'];
    $costoA = $sumas[0]['totalCostoA'] * $numeroAdultos;
    $costoN = $sumas[0]['totalCostoN'] * $numeroNinios;
    $costo = $costoA + $costoN;

    $puntosRuta = "SELECT puntoturistico.id as id,puntoturistico.nombre as nombre, puntoturistico.descripcion as descripcion,
    puntoturistico.latitud as latitud, puntoturistico.longuitud as longuitud,puntoturistico.costo as costo,puntoturistico.costoN as costoN,
     puntoturistico.tiempoEstimado as tiempoEstimado,imagen.direccion as imagen,parroquia.descripcion as parroquian,categoria.descripcion as catnombre ,
     subcategoria.descripcion as subnombre
    FROM ruta
    INNER JOIN puntosruta
    ON puntosruta.idRuta = ruta.id    
    INNER JOIN puntoturistico 
    ON puntosruta.idPunto = puntoturistico.id 
    INNER JOIN imagen
    ON puntoturistico.id = imagen.idPuntoTuristico
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON categoria.id = puntoturistico.categoriaId
    INNER JOIN subcategoria
    on subcategoria.id = puntoturistico.subCategoriaId
    WHERE  imagen.categoria = 1 AND ruta.id = $idRuta AND puntoturistico.estado = 1 
    ORDER BY puntosruta.id ";

    if ($resultado2 = $mysqli->query($puntosRuta)) {
        $puntosR = array();
        $i = 0;
        while ($fila = $resultado2->fetch_row()) {
            $puntosR[$i] = array();
            $puntosR[$i]['id']              = $fila[0];
            $puntosR[$i]['nombre']          = $fila[1];
            $puntosR[$i]['descripcion']     = $fila[2];
            $puntosR[$i]['latitud']         = $fila[3];
            $puntosR[$i]['longuitud']       = $fila[4];
            $puntosR[$i]['costo']           = $fila[5];
            $puntosR[$i]['costoN']          = $fila[6];
            $puntosR[$i]['tiempoEstimado']  = $fila[7];
            $puntosR[$i]['imagen']          = $fila[8];
            $puntosR[$i]['parroquian']      = $fila[9];
            $puntosR[$i]['catnombre']       = $fila[10];
            $puntosR[$i]['subnombre']       = $fila[11];
            $i++;
        }

        //SEPARO FECHA INICIO
        $obj_fecha = date_create_from_format('Y-m-d', $fecha);
        $date = date_create($fecha);
        $dia = date_format($date, "Y/m/d");
        $hora = date_format($date, "H:i:s");
        $horax = date_format($date, "H:i");
        $longitud = count($puntosR);
        $ListaDistancia = array();
        $ListaTiempo = array();
        $horas;
        $minutos;
        $segundos;
        $inicio =  $hora;
        for ($i = 0; $i < $longitud; $i++) {

            $ListaDistancia[$i]['inicio'] = $puntosR[$i]['nombre'];
            $ListaDistancia[$i]['inicioTrayecto'] = $inicio;
            $ListaDistancia[$i]['tiempoEstimado'] = $puntosR[$i]['tiempoEstimado'];
            $ListaDistancia[$i]['nombre'] = $nombre;
            $ListaDistancia[$i]['idRuta'] = $idRuta;
            $ListaDistancia[$i]['idUsuario'] = $idUsuario;
            $ListaDistancia[$i]['latitud'] = $puntosR[$i]['latitud'];
            $ListaDistancia[$i]['longuitud'] = $puntosR[$i]['longuitud'];
            $ListaDistancia[$i]['imagen'] =  $puntosR[$i]['imagen'];
            $ListaDistancia[$i]['idPunto'] = $puntosR[$i]['id'];
            $ListaDistancia[$i]['costo'] = $costo;

            $j = $i + 1;
            if ($j < $longitud) {
                $lat1 = $puntosR[$i]['latitud'];
                $lat2 = $puntosR[$j]['latitud'];
                $lon1 = $puntosR[$i]['longuitud'];
                $lon2 = $puntosR[$j]['longuitud'];
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distancia = $miles * 1.609344;
                $ListaDistancia[$i]['fin'] = $puntosR[$j]['nombre'];

                //si l distancia entre puntos es menor a 2 kimoletros se caminara
                if ($distancia < 2) {
                    $velocidad = 5.3; // k/h
                    $velocidads = $velocidad * (5 / 18); //transformo k/h a m/s
                    $distanciam = $distancia * 1000; //tranformo km a m
                    $tiempoD = $distanciam / $velocidads;
                    $ListaTiempo[$i] = $tiempoD; //tiempo es s
                    $ListaDistancia[$i]['recorrido'] = 'Caminata';
                    $ListaDistancia[$i]['distancia'] = $distancia;
                    $ListaDistancia[$i]['tiempo'] = $tiempoD; //tiempo es s
                    $tiempoSeg = intval($tiempoD);
                } else {
                    //si l distancia entre puntos es mayor a 2 kimoletros se usara transporte                    
                    $velocidad = 40;
                    $velocidads = $velocidad * (5 / 18); //transformo k/h a m/s
                    $distanciam = $distancia * 1000; //tranformo km a m
                    $tiempoD = $distanciam / $velocidads;
                    $ListaTiempo[$i] = $tiempoD; //tiempo es s
                    $ListaDistancia[$i]['recorrido'] = 'Transporte';
                    $ListaDistancia[$i]['distancia'] = $distancia;
                    $ListaDistancia[$i]['tiempo'] = $tiempoD; //tiempo es s
                    $tiempoSeg = intval($tiempoD);
                }
            }

            //sumo la hora inicio con el tiempo del punto  
            //tiempo estimado en formato h:m:s
            $mifecha2 = new DateTime($puntosR[$i]['tiempoEstimado']);
            $mifecha = new DateTime($inicio); //hora inicio en formato h:m:s
            //sumo las dos horas y obtengo una hora fin
            $mifecha->modify('+' . $mifecha2->format('H') . ' hours');
            $mifecha->modify('+' . $mifecha2->format('i') . ' minute');
            $mifecha->modify('+' . $mifecha2->format('s') . 'second');
            $fin = $mifecha->format('H:i:s');
            $ListaDistancia[$i]['finTrayecto'] = $fin;
            $horafin = $mifecha->format('Y/m/d H:i:s');

            if ($i < ($longitud - 1)) {
                $tiempoSeg = intval($ListaDistancia[$i]['tiempo']);
                $horas = floor($tiempoSeg / 3600);
                $minutos = floor(($tiempoSeg - ($horas * 3600)) / 60);
                $segundos = $tiempoSeg - ($horas * 3600) - ($minutos * 60);
                $mifecha->modify('+' . $horas . ' hours');
                $mifecha->modify('+' . $minutos . ' minute');
                $mifecha->modify('+' . $segundos . 'second');
                $inicio = $mifecha->format(' H:i:s');
            }
        }
    }

    $insert = mysqli_query($mysqli, " UPDATE  ruta SET
   horaFin      = '$horafin',
   costoTotal      = $costo
    WHERE id = $idRuta;
   ");

    if ($insert) {
        $result = json_encode(array('success' => true, 'result' => $ListaDistancia));
    } else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al registrar'));
    }


    echo $result;
}

//mis rutas
else if ($postjson['aksi'] == "proceso_rutas") {
    $idUsuario = $postjson['idUsuario'];

    $puntosRuta = "SELECT * FROM ruta where idUsuario = $idUsuario ";

    if ($resultado = $mysqli->query($puntosRuta)) {
        $rutas = array();
        $i = 0;
        while ($fila = $resultado->fetch_row()) {
            $rutas[$i] = array();
            $rutas[$i]['idUsuario']       = $idUsuario;
            $rutas[$i]['id']              = $fila[0];
            $rutas[$i]['nombre']          = $fila[1];
            $rutas[$i]['horaInicio']     = $fila[2];
            $rutas[$i]['horaFin']         = $fila[3];
            $rutas[$i]['costoTotal']       = $fila[4];
            $rutas[$i]['numeroAdultos']       = $fila[6];
            $rutas[$i]['numeroNinios']       = $fila[7];
            $i++;
        }
    }

    $select = mysqli_query($mysqli, "SELECT * FROM ruta where idUsuario = $idUsuario");

    if ($select) {
        $result = json_encode(array('success' => true, 'result' => $rutas));
    } else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al registrar'));
    }

    echo $result;
}

//eliminar rutas
else if ($postjson['aksi'] == "eliminar") {

    $idUsuario = $postjson['idUsuario'];
    $idRuta = $postjson['idruta'];

    $delete = mysqli_query($mysqli, "DELETE FROM puntosruta
    WHERE idRuta = $idRuta;
   ");

   if ($delete) {
        $result = json_encode(array('success' => true, 'msg' => 'vale'));

        $deleteRuta = mysqli_query($mysqli, "DELETE FROM ruta
    WHERE id = $idRuta;
   ");

        if ($deleteRuta) {

                $result = json_encode(array('success' => true));

        } else {
            $result = json_encode(array('success' => false, 'msg' => 'un error al eliminar ruta'));
        }

         }else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al eliminar puntos ruta'));
    }
  
    echo $result;

}




//eliminar rutas
else if ($postjson['aksi'] == "proceso_eliminar_punto") {

    $idPunto = $postjson['idPunto'];
    $idRuta = $postjson['idRuta'];

    $delete = mysqli_query($mysqli, "DELETE FROM puntosruta
    WHERE idRuta = $idRuta AND idPunto = $idPunto");


        if ($delete) {
                $result = json_encode(array('success' => true));
         }else {
        $result = json_encode(array('success' => false, 'msg' => 'un error al eliminar el punto de la ruta'));
    }
  
    echo $result;

}


?>