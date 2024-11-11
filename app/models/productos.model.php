<?php

require_once 'app/models/model.php';

class ProductosModel extends Model{

    function getAllProductos($filterBy, $filterValue, $orderBy, $orderValue, $page, $limit){
        $sql = 'SELECT id_producto, nombre_producto, peso, precio, productos.id_marca, imagen_producto, marcas.nombre_marca as nombre_marca FROM productos, marcas WHERE productos.id_marca=marcas.id_marca';

        if($filterBy && $filterValue){
            switch ($filterBy) {
                case 'id_producto':
                    $sql .= ' AND id_producto = ?';
                    break;
                case 'nombre_producto':
                    $sql .= ' AND nombre_producto = ?';
                    break;
                case 'peso':
                    $sql .= ' AND peso = ?';
                    break;
                case 'precio':
                    $sql .= ' AND precio = ?';
                    break;
                case 'id_marca':
                    $sql .= ' AND productos.id_marca = ?';
                    break;
                default:
                    $sql .= ' AND nombre_producto = ?';
                    break;
            }
        }
        
        if($orderBy){
            switch ($orderBy) {
                case 'nombre_producto':
                    $sql .= ' ORDER BY nombre_producto';
                    break;
                case 'peso':
                    $sql .= ' ORDER BY peso';
                    break;
                case 'precio':
                    $sql .= ' ORDER BY precio';
                    break;
                case 'id_marca':
                    $sql .= ' ORDER BY id_marca';
                    break;
                default:
                    $sql .= ' ORDER BY id_producto';
                    break;
            }
            if(isset($orderValue) && $orderValue=='DESC'){
                $sql .= ' DESC';
            }else{
                $sql .= ' ASC';
            }
        }
        
		if($page && $limit)
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . (($page-1)*$limit);
      
        $query = $this->db->prepare($sql);
        if($filterBy && $filterValue){
            $query->execute([$filterValue]);
        }else{
            $query->execute();
        }

        $productos = $query->fetchAll(PDO::FETCH_OBJ);
        return $productos;
    }
    
    function getProductoById($id) {
        $query = $this->db->prepare('SELECT id_producto, nombre_producto, peso, precio, productos.id_marca, imagen_producto, marcas.nombre_marca as nombre_marca FROM productos, marcas WHERE id_producto = ? AND productos.id_marca=marcas.id_marca');
        $query->execute([$id]);
        $producto = $query->fetch(PDO::FETCH_OBJ);
        return $producto;
    }

    function insertProducto($nombre_producto, $peso, $precio, $id_marca, $imagen_producto=null) {
        $query = $this->db->prepare('INSERT INTO productos (nombre_producto, peso, precio, id_marca) VALUES(?,?,?,?)');
        $query->execute([$nombre_producto, $peso, $precio, $id_marca]);
        $id = $this->db->lastInsertId();
        if ($imagen_producto) {
            $filePath = $this->moveUploadedFile($imagen_producto);
            $query = $this->db->prepare('UPDATE productos SET imagen_producto=? WHERE id_producto=?'); 
            $query->execute([$filePath, $id]);    
        }
        return $id;
    }

    function moveUploadedFile($imagen_producto){
        $filePath = "img/" . uniqid("", true) . "." . strtolower(pathinfo($imagen_producto['name'], PATHINFO_EXTENSION));
        move_uploaded_file($imagen_producto['tmp_name'], $filePath);
        return $filePath;
    }

    function updateProducto($nombre_producto, $peso, $precio, $id_marca, $id_producto, $imagen_producto=null){
        $query = $this->db->prepare('UPDATE productos SET nombre_producto=?, peso=?, precio=?, id_marca=? WHERE id_producto = ?');
        $query->execute([$nombre_producto, $peso, $precio, $id_marca, $id_producto]);
        if ($imagen_producto){
            $producto = $this->getProductoById($id_producto);
            unlink($producto->imagen_producto);
            $filePath = $this->moveUploadedFile($imagen_producto);
            $query = $this->db->prepare('UPDATE productos SET imagen_producto=? WHERE id_producto=?'); 
            $query->execute([$filePath, $id_producto]);
        }
    }

    function deleteProducto($id) {
        $producto = $this->getProductoById($id);
        if ($producto->imagen_producto)
            unlink($producto->imagen_producto);
        $query = $this->db->prepare('DELETE FROM productos WHERE id_producto = ?');
        $query->execute([$id]);
    }
}