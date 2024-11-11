<?php

require_once 'app/models/productos.model.php';
require_once 'app/models/marcas.model.php';
require_once 'app/views/json.view.php';

class ProductosApiController{

    private $model;
    private $modelMarcas;
    private $view;

    function __construct()
    {
        $this->model = new ProductosModel();
        $this->modelMarcas = new MarcasModel();
        $this->view = new JSONView();

    }

    function getAllProductos($req,$res){
        
        $filterBy = false;
        $filterValue = false;
        if ((isset($req->query->filterBy)) && (isset($req->query->filterValue))) {
            $filterBy = $req->query->filterBy;
            $filterValue = $req->query->filterValue;
        }

        $orderBy = false;
        $orderValue = null;
        if (isset($req->query->orderBy)) {
            $orderBy = $req->query->orderBy;
            if (isset($req->query->orderValue))
                $orderValue = $req->query->orderValue;
        }

        $page = false;
        $limit = false;
        if (isset($req->query->page) && is_numeric($req->query->page) && isset($req->query->limit) && is_numeric($req->query->limit)) {
            $page = $req->query->page;
            $limit = $req->query->limit;
        }

        $productos = $this->model->getAllProductos($filterBy, $filterValue, $orderBy, $orderValue, $page, $limit);
        if(empty($productos))
            return $this->view->response('Productos no encontrados', 404);
        return $this->view->response($productos);
    }

    function getProductoById($req,$res){
        if(!is_numeric($req->params->id))
            return $this->view->response('id inválida', 400);
        $id = $req->params->id;
        $producto = $this->model->getProductoById($id);
        if(empty($producto))
            return $this->view->response('Producto no encontrado', 404);
        return $this->view->response($producto);
    }

    function addProducto($req,$res){
        if(!$res->user)
            return $this->view->response('No autorizado', 401);
        
        if(empty($req->body->nombre_producto) || empty($req->body->peso) || empty($req->body->precio) || empty($req->body->id_marca))
            return $this->view->response('Campos incompletos', 400);
        
        $nombre_producto = $req->body->nombre_producto;
        $peso = $req->body->peso;
        $precio = $req->body->precio;
        $id_marca = $req->body->id_marca;
        $imagen_producto = false;
        if(isset($req->body->imagen_producto))
            $imagen_producto = $req->body->imagen_producto;

        if(empty($this->modelMarcas->getMarcaById($id_marca)))
            return $this->view->response('Marca no encontrada',404);

        if($imagen_producto && $imagen_producto['name'] && ($imagen_producto['type'] == "image/jpg" || $imagen_producto['type'] == "image/jpeg" || $imagen_producto['type'] == "image/png")) 
            $id = $this->model->insertProducto($nombre_producto, $peso, $precio, $id_marca, $imagen_producto);
        else
            $id = $this->model->insertProducto($nombre_producto, $peso, $precio, $id_marca);

        if(!$id)
            return $this->view->response('Error al agregar producto',500);
        $producto = $this->model->getProductoById($id);
        return $this->view->response($producto,201);
    }

    function updateProducto($req,$res){
        if(!$res->user)
            return $this->view->response('No autorizado',401);
        if(!is_numeric($req->params->id))
            return $this->view->response('id inválida', 400);
        $id = $req->params->id;
        $producto = $this->model->getProductoById($id);
        if(!$producto)
            return $this->view->response('Producto no encontrado',404);

        if(empty($req->body->nombre_producto) || empty($req->body->peso) || empty($req->body->precio) || empty($req->body->id_marca))
            return $this->view->response('Campos incompletos', 400);
        $nombre_producto = $req->body->nombre_producto;
        $peso = $req->body->peso;
        $precio = $req->body->precio;
        $id_marca = $req->body->id_marca;
        $imagen_producto = false;
        if(isset($req->body->imagen_producto))
            $imagen_producto = $req->body->imagen_producto;

        if(empty($this->modelMarcas->getMarcaById($id_marca)))
            return $this->view->response('Marca no encontrada',404);

        if($imagen_producto && $imagen_producto['name'] && ($imagen_producto['type'] == "image/jpg" || $imagen_producto['type'] == "image/jpeg" || $imagen_producto['type'] == "image/png")) 
            $this->model->updateProducto($nombre_producto, $peso, $precio, $id_marca, $id, $imagen_producto);
        else
            $this->model->updateProducto($nombre_producto, $peso, $precio, $id_marca, $id);

        $producto = $this->model->getProductoById($id);
        return $this->view->response($producto,201);
    }

    function deleteProducto($req,$res){
        if(!$res->user)
            return $this->view->response('No autorizado',401);
        if(!is_numeric($req->params->id))
            return $this->view->response('id inválida', 400);
        $id = $req->params->id;
        $producto = $this->model->getProductoById($id);
        if(!$producto)
            return $this->view->response('Producto no encontrado',404);
        $this->model->deleteProducto($id);
        return $this->view->response('Producto eliminado');

    }
}