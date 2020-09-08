<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credential: true");
header("Access-Control-Allow-Methods: PUT,GET,POST,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,Accept,X-Request-Whith,x-xsrf-token");
header("Content-Type: application/json; charset=utf-8");

include 'config.php';

$postjson = json_decode(file_get_contents('php://input'),true);


//registrar usuario
if($postjson['aksi']=="proceso_registrar"){

    $verUsuario= mysqli_fetch_array(mysqli_query($mysqli,"SELECT usuario, correo FROM usuario 
    WHERE usuario = '$postjson[usuario]' || correo = '$postjson[correo]'"));

    if($verUsuario['usuario'] == $postjson['usuario'] ){
        $result = json_encode(array('success'=>false, 'msg'=>'Nombre de usuario  ya se esta Usando Actualmente'));
    }else if($verUsuario['correo'] == $postjson['correo'] ){
        $result = json_encode(array('success'=>false, 'msg'=>'Correo ya se esta Usando Actualmente'));
    }else{

        //encripto la clave
   $clave=password_hash($postjson['clave'],PASSWORD_DEFAULT);
   //$clave = md5($postjson['clave']);     
   $insert = mysqli_query($mysqli," INSERT INTO usuario SET
   nombres      = '$postjson[nombres]',
   apellidos    = '$postjson[apellidos]',
   usuario      = '$postjson[usuario]',
   correo       = '$postjson[correo]',
   clave        = '$clave',
   idRol = 3,
   estado = 1
   ");

    if($insert){
        $result = json_encode(array('success'=>true,'msg'=>'Registrado exitosamente'));
    }
    else{
        $result = json_encode(array('success'=>false,'msg'=>'Registrado exitosamente'));
    }
}
    echo $result;


}


//Login usuario
elseif($postjson['aksi']=="proceso_login"){


    $usuario   =  $postjson['usuario'];   
    $pass      =  $postjson['clave']; 
   
$usr= "SELECT * FROM usuario WHERE usuario= '$usuario' AND estado = 1 AND idRol=3";

if ($resultado = $mysqli->query($usr)) {   
    $usrs = array();
    $i = 0; 
    while ($fila = $resultado->fetch_row()) { 
        $usrs[$i] = array();
        $usrs[$i]['id'] = $fila[0];
        $usrs[$i]['nombres'] = $fila[1];
        $usrs[$i]['apellidos'] = $fila[2];
        $usrs[$i]['correo'] = $fila[3];
        $usrs[$i]['usuario'] = $fila[4];
        $usrs[$i]['clave'] = $fila[5];
        $i++;
    }  
$hash = $usrs[0]['clave'];
$id = $usrs[0]['id'];
$nombres = $usrs[0]['nombres'];
$apellidos = $usrs[0]['apellidos'];
$correo = $usrs[0]['correo'];
$usuario = $usrs[0]['usuario'];
$clave = $usrs[0]['clave'];
}

if (password_verify($pass, $hash)) {	

//consulta para obtener los puntos turisticos
$consulta ="SELECT puntoturistico.id as idPunto,puntoturistico.nombre as nombrePunto,
puntoturistico.descripcion as puntoDescripcion,
puntoturistico.latitud as latitud, puntoturistico.longuitud as longuitud,
puntoturistico.costo as costoAdulto,puntoturistico.costoN as costoNinio, 
puntoturistico.tiempoEstimado as tiempoEstimado, parroquia.descripcion as nombreParroquia, 
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
WHERE puntoturistico.estado = 1
AND imagen.categoria = 1
ORDER BY puntoturistico.id ASC";

//lleno el array q se enviara a la pagina home
if ($resultado = $mysqli->query($consulta)) {   
$puntos = array();
$i = 0; 
while ($fila = $resultado->fetch_row()) { 
    $puntos[$i] = array();
    $puntos[$i]['id'] = $id;
    $puntos[$i]['nombres'] = $nombres;
    $puntos[$i]['apellidos'] = $apellidos;
    $puntos[$i]['correo'] = $correo;
    $puntos[$i]['usuario'] = $usuario;
    $puntos[$i]['idPunto'] = $fila[0];
    $puntos[$i]['nombrePunto'] = $fila[1];
    $puntos[$i]['puntoDescripcion'] = $fila[2];
    $puntos[$i]['latitud'] = $fila[3];
    $puntos[$i]['longuitud'] = $fila[4];
    $puntos[$i]['costoAdulto'] = $fila[5];
    $puntos[$i]['costoNinio'] = $fila[6];
    $puntos[$i]['tiempoEstimado'] = $fila[7];
    $puntos[$i]['nombreParroquia'] = $fila[8];
    $puntos[$i]['categoriaNombre'] = $fila[9];
    $puntos[$i]['subcategoriaNombre'] = $fila[10];
    $puntos[$i]['imagen'] = $fila[11];
    $i++;
}
}

$result = json_encode(array('success'=>true,'result'=>$puntos));
}
else{
$result = json_encode(array('success'=>false));
}

echo $result;

}
