<?php

require_once 'libs/router.php';
require_once 'app/controllers/productos.api.controller.php';
require_once 'app/controllers/user.api.controller.php';
require_once 'app/middlewares/jwt.auth.middleware.php';
require_once 'config.php';

$router = new Router();
$router->addMiddleware(new JWTAuthMiddleware());

$router->addRoute('productos', 'GET', 'ProductosApiController', 'getAllProductos');
$router->addRoute('productos/:id', 'GET', 'ProductosApiController', 'getProductoById');
$router->addRoute('productos', 'POST', 'ProductosApiController', 'addProducto');
$router->addRoute('productos/:id', 'PUT', 'ProductosApiController', 'updateProducto');
$router->addRoute('productos/:id', 'DELETE', 'ProductosApiController', 'deleteProducto');

$router->addRoute('usuarios/token', 'GET', 'UserApiController', 'getToken');

$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);