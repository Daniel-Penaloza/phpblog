<?php

    ///////////////////////////
    //// Datos Generles/////
    /////////////////////////
    /*
        Nombre del alumno: Hector Daniel Peñaloza Moran.
        Nombre del maestro: Octavio Aguirre Lozano.
        Materia: Computación en el servidor web.
        Nombre de actividad: Desarrollo Web Avanzado.
    */ 
    ///////////////////////////
    //// Datos Generles/////
    /////////////////////////
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet"type = "text/css" href = "style/style.css" />
    <!-- Llamada a CDN de bootstrap para el estilo -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Llamada a Font Awesome para iconos -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <title>Un Simple Blog</title>
</head>
<body>
    <header class="header">
        <div class="header__title">
            <!-- Utilizando La Funcion strtoupper para convertir todo el texto en mayusculas -->
            <h1 class="main__header"><?php echo strtoupper("Un Simple Blog") ?></h1>
        </div>
    </header>
</body>
</html>

<?php
    /////////////////FUNCIONES/////////////////
    
    // Funcion para acortar descripcion
    function shortContent($the_content){
        // Retornarmos una subcadena acortando la cadena original a 560 caracteres y agregando 3 puntos suspensivos al final.
        return substr($the_content, 0 , 560).'...';
    };


    // TODO: Incluir condicional para detectar cuando estemos en un ambiente de desarrollo o de producción y evitar los comentarios de la aplicación en desarrollo local.
    function stablishConection(){
        
        /* Aplicacion En Produccion */ 
        // Obtenemos la variable de entorno desde nuestro proyecto en Heroku.
        $env_info = parse_url(getenv("CLEARDB_DATABASE_URL"));

        // Creamos las variables necesarias para la conexion a MYSQL
        $user = $env_info["user"];   
        $host = $env_info["host"];
        $password = $env_info["pass"];
        $database = substr($env_info["path"], 1);

        $complete_data = [$user, $host, $password, $database];
        // Hacemos la conexion una vez suempre y cuando el numero de campos del arreglo $complete_data sea igual a 4
        do {
            // Realizamos la conexion a nuestra base de datos
            $connection = new mysqli($host, $user, $password, $database);
            // Establecemos que utilizara el tipo de caracteres utf8 nuestra aplicación para mostrar acentos y ñ´s.
            $connection->set_charset("utf8");            
            // Retornamos la conexion a su llamada
            return $connection;
            $complete_data = [];
        }   while(count($complete_data === 4));
        
        /* Aplicacion En Desarrollo Local */ 
        /*
            $user="root";
            $password="";
            $database="posts";
            $host="localhost";

            $complete_data = [$user, $password, $database, $host];
            do {
                $conection = mysqli_connect($host,$user,$password,$database);
                return $conection;
                $complete_data = [];
            } while(count($complete_data === 4));
        */
    }


    
    // Funcion para revisar si la conexion a SQL tiene exito;
    function checkConnection(){
        // Creacion de variable para tener un true o false dependiendo el resultado
        $conection_ok = stablishConection();
        // Si la conexion se establece mostrar mensaje en consola conexión establecida, si no conexión rechazada.
        $console_output = $conection_ok ? "Established Connection" : "Connection Refused";

        // Desplegamos en consola si la conexion fue establecida o rechazada.
        echo 
        "
            <script type=\"text/javascript\">
                console.log('$console_output');
            </script>
        ";
    }

    function getPosts() {
        // Creando variable booleana connection_statusc on el valor de false
        $connection_status = false;
        // Mientras sea falso el valor de $connection_status ejecutar el contexto dentro del while.
        while(!$connection_status) {
            // Llamando a checkConection con la finalidad de saber si estamos conectados a la base de datos.        
            checkConnection();
            $statement = "select * from posts";
            $result =  stablishConection()->query($statement);
            
            // Cambiamos la variable connection_status a true para salir del ciclo.
            $connection_status = true;

            // Retornamos el resultado de la consulta de SQL.
            return $result;
        }
        
    }   

    // Funcion para retornar un string de bootstrap para desplegar un emoji en donde tenemos como argumento el numero del tipo de reaccion.
    function checkReaction($reaction){
        // Retornamos un arreglo del tipo string con el nombre del icono a desplegar.
        switch($reaction) {
            case 1:
                return ['fa-smile-wink'];
                break;
            case 2:
                return ['fa-smile-beam'];
                break;
            case 3:
                return ['fa-sad-tear'];
                break;
        }
    }
    
    /////////////////FUNCIONES/////////////////

    // Asignando el retorno de la funcion getPosts (objeto) a la variable posts
    $posts = getPosts();    
    

    if ($posts->num_rows>0) {
        // Iterando en un for cada post con su informacion
        foreach($posts as $post) {
            echo"
            <div class='col-md-6 col-offset-6 post__container'>
                <!-- Desplegamos el contenido del arreglo post accediendo a su identificador (post[title] - post[content] - post[created_at])-->
                <h2 class='submain__header'>".$post["title"]."</h1>
                <!-- Mandamos a llamar a la funcion shortContent que se encargara de reducir el tamañoa del contenido del string -->
                <div class='post__content'>".shortContent($post["content"])."</div>
                <!-- Llamamos a la funcion checkReaction pasandole por parametro el valor de post[reaction] para desplegar el icono correspondiente, con el metodo implode obtenemos la informacion del arreglo -->
                <span class='label'>Reaction: <i class='fas ".implode(checkReaction($post["reaction"]))."'></i></span>
            </div>";
        }
    };
?>