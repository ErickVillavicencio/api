<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credential: true");
header("Access-Control-Allow-Methods: PUT,GET,POST,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,Accept,X-Request-Whith,x-xsrf-token");
header("Content-Type: application/json; charset=utf-8");

include 'config.php';

$postjson = json_decode(file_get_contents('php://input'),true);




//Obtengo el valor del punto turistoco
if($postjson['aksi']=="proceso_informacionPunto"){

    $idPunto = $postjson['idPunto'];  
    $idUsuario =  $postjson['idUsuario'];
    
//consulta para obtener los puntos turisticos
    $consulta ="SELECT puntoturistico.id as id, puntoturistico.nombre as nombre,puntoturistico.descripcion as descripcion, puntoturistico.latitud latitud, puntoturistico.longuitud as longuitud,puntoturistico.costo as costoAdulto, puntoturistico.costoN as costoNinio, puntoturistico.tiempoEstimado as tiempoEstimado, puntoturistico.facebook as facebook, puntoturistico.twitter as twitter, puntoturistico.instagram as instagram, parroquia.descripcion as nombreParroquia, categoria.descripcion as categoriaNombre, subcategoria.descripcion as subCategoriaNombre, imagen.direccion as imagen
    FROM puntoturistico
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON puntoturistico.categoriaId = categoria.id
    INNER JOIN subcategoria
    ON puntoturistico.subCategoriaId = subcategoria.id
    INNER JOIN imagen
    ON puntoturistico.id = imagen.idPuntoTuristico
    WHERE puntoturistico.id = $idPunto";
//consulta para validar la consulta
    $logindata= mysqli_fetch_array(mysqli_query($mysqli,"SELECT * FROM puntoturistico  
    WHERE id = $idPunto"));


//lleno el array q se enviara a la pagina home
if ($resultado = $mysqli->query($consulta)) {   
    $puntos = array();
    $i = 0; 
    while ($fila = $resultado->fetch_row()) { 
        $puntos[$i] = array();
        $puntos[$i]['id'] = $fila[0];
        $puntos[$i]['nombre'] = $fila[1];
        $puntos[$i]['descripcion'] = $fila[2];
        $puntos[$i]['latitud'] = $fila[3];
        $puntos[$i]['longuitud'] = $fila[4];
        $puntos[$i]['costoAdulto'] = $fila[5];
        $puntos[$i]['costoNinio'] = $fila[6];
        $puntos[$i]['tiempoEstimado'] = $fila[7];
        $puntos[$i]['facebook'] = $fila[8];
        $puntos[$i]['twitter'] = $fila[9];
        $puntos[$i]['instagram'] = $fila[10];
        $puntos[$i]['nombreParroquia'] = $fila[11];
        $puntos[$i]['categoriaNombre'] = $fila[12];
        $puntos[$i]['subcategoriaNombre'] = $fila[13];
        $puntos[$i]['imagen'] = $fila[14];
        $puntos[$i]['idUsuario'] = $idUsuario;
        $i++;
    }
}

    if($logindata){
        $result = json_encode(array('success'=>true,'result'=>$puntos));
    }
    else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;


}



//Lpuntos para el boton volver
elseif($postjson['aksi']=="proceso_puntosHome"){

    $idUsuario = $postjson['idUsuario'];     
//consulta para obtener los puntos turisticos
    $consulta ="SELECT usuario.id as id,usuario.nombres as nombres, usuario.apellidos as apellidos, usuario.correo as correo, usuario.usuario as usuario, puntoturistico.id as idPunto, puntoturistico.nombre as nombrePunto, puntoturistico.descripcion as puntoDescripcion,
    puntoturistico.latitud as latitud, puntoturistico.longuitud as longuitud, puntoturistico.costo as costoAdulto,puntoturistico.costoN as costoNinio, puntoturistico.tiempoEstimado as tiempoEstimado, parroquia.descripcion as nombreParroquia, categoria.descripcion as categoriaNombre,
    subcategoria.descripcion as subcategoriaNombre, imagen.direccion as imagen
    FROM usuario
    INNER JOIN puntoturistico
    ON puntoturistico.estado = usuario.estado
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON puntoturistico.categoriaId = categoria.id
    INNER JOIN subcategoria
    ON subcategoria.id = puntoturistico.subCategoriaId
    INNER JOIN imagen
    ON imagen.idPuntoTuristico = puntoturistico.id
    WHERE usuario.id = $idUsuario
    AND usuario.estado = 1
    AND puntoturistico.estado = 1
    AND imagen.categoria = 1
    ORDER BY puntoturistico.id ASC";
//consulta para validar el login
    $logindata= mysqli_fetch_array(mysqli_query($mysqli,"SELECT usuario.id as id,usuario.nombres as nombres, usuario.apellidos as apellidos, usuario.correo as correo, usuario.usuario as usuario, puntoturistico.id as idPunto, puntoturistico.nombre as nombrePunto, puntoturistico.descripcion as puntoDescripcion,
    puntoturistico.latitud as latitud, puntoturistico.longuitud as longuitud, puntoturistico.costo as costoAdulto,puntoturistico.costoN as costoNinio, puntoturistico.tiempoEstimado as tiempoEstimado, parroquia.descripcion as nombreParroquia, categoria.descripcion as categoriaNombre,
    subcategoria.descripcion as subcategoriaNombre, imagen.direccion as imagen
    FROM usuario
    INNER JOIN puntoturistico
    ON puntoturistico.estado = usuario.estado
    INNER JOIN parroquia
    ON puntoturistico.idParroquia = parroquia.id
    INNER JOIN categoria
    ON puntoturistico.categoriaId = categoria.id
    INNER JOIN subcategoria
    ON subcategoria.id = puntoturistico.subCategoriaId
    INNER JOIN imagen
    ON imagen.idPuntoTuristico = puntoturistico.id
    WHERE usuario.id = $idUsuario
    AND usuario.estado = 1
    AND puntoturistico.estado = 1
    AND imagen.categoria = 1
    ORDER BY puntoturistico.id ASC"));


//lleno el array q se enviara a la pagina home
if ($resultado = $mysqli->query($consulta)) {   
    $puntos = array();
    $i = 0; 
    while ($fila = $resultado->fetch_row()) { 
        $puntos[$i] = array();
        $puntos[$i]['id'] = $fila[0];
        $puntos[$i]['nombres'] = $fila[1];
        $puntos[$i]['apellidos'] = $fila[2];
        $puntos[$i]['correo'] = $fila[3];
        $puntos[$i]['usuario'] = $fila[4];
        $puntos[$i]['idPunto'] = $fila[5];
        $puntos[$i]['nombrePunto'] = $fila[6];
        $puntos[$i]['puntoDescripcion'] = $fila[7];
        $puntos[$i]['latitud'] = $fila[8];
        $puntos[$i]['longuitud'] = $fila[9];
        $puntos[$i]['costoAdulto'] = $fila[10];
        $puntos[$i]['costoNinio'] = $fila[11];
        $puntos[$i]['tiempoEstimado'] = $fila[12];
        $puntos[$i]['nombreParroquia'] = $fila[13];
        $puntos[$i]['categoriaNombre'] = $fila[14];
        $puntos[$i]['subcategoriaNombre'] = $fila[15];
        $puntos[$i]['imagen'] = $fila[16];
        $i++;
    }
}
    if($logindata){
        $result = json_encode(array('success'=>true,'result'=>$puntos));
    }
    else{
        $result = json_encode(array('success'=>false));
    }

    echo $result;


}













?>