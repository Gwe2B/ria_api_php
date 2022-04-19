<?php

use App\Util\HTTP;

define('ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('DB', array(
    'DSN' => 'mysql:host=localhost;dbname=ria_tp1;charset=utf8',
    'USR' => 'root',
    'PWD' => null,
    'OPT' => array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    )
));

$GLOBALS['pdo'] = null;
try {
    $GLOBALS['pdo'] = new \PDO(
        DB['DSN'],
        DB['USR'],
        DB['PWD'],
        DB['OPT']
    );
} catch (\PDOException $e) {
    echo "Database connection error!!<br>";
}

require ROOT.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if($_SERVER['REQUEST_METHOD'] != 'OPTIONS') {
    $router = new App\Router\Router(App\Util\Get::get('url'));

    /* ------------------------------- GET routes ------------------------------- */
    $router->get('/histo/:pdtId', 'Produit#getHisto')
           ->with('pdtId', '[0-9]+');

    $router->get('/:pdtId', 'Produit#getFromId')
           ->with('pdtId', '[0-9]+');

    $router->get('/:pdtName', 'Produit#getFromName')
           ->with('pdtName', '[a-zA-Z]+');

    $router->get('/', 'Produit#getAll');

    /* ------------------------------- POST routes ------------------------------ */
    $router->post('/add/', 'Produit#add');

    /* ------------------------------ PATCH routes ------------------------------ */
    $router->patch('/:pdtId', 'Produit#update')
           ->with('pdtId', '[0-9]+');

    /* ------------------------------ DELETE routes ----------------------------- */
    $router->delete('/:idPdt', 'Produit#remove');

    try {
        $router->run();
    } catch(App\Router\RouterException $ex) {
        HTTP::response(HTTP::CODE_4XX_BADREQUEST, $ex->getMessage());
    }
} else {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    HTTP::response(HTTP::CODE_2XX_SUCCESS, '');
}