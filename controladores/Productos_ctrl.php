<?php
class Productos_ctrl
{

    public $M_Producto = null;

    public function __construct()
    {
        $this->M_Producto = new M_Productos();
    }

    public function listarProductosSql($f3)
    {
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from productos ";

        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje' => count($items) > 0 ? 'Operacion exitosa' : 'No hay registro para esa consulta',
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    } //fin listarSql





    public function listarProductosxidSql($f3)
    {
        $producto_id = $f3->get('POST.producto_id'); //debe tener este nombre al momento de enviar desde el cliente
        $cadenaSql = "select * ";
        $cadenaSql = $cadenaSql . " from productos ";
        $cadenaSql = $cadenaSql . " where id = " . $producto_id;

        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje' => count($items) > 0 ? 'Operacion exitosa' : 'No hay registro para esa consulta',
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    }



    public function insertarProductos($f3)
    {
        $productoModel = new M_Productos();
        $mensaje = "";
        $nuevoId = 0;
        $codigoProducto = $f3->get('POST.producto_codigo');
        $nombreProducto = $f3->get('POST.producto_nombre');

        // Verificar si ya existe un producto con el mismo código
        $productoModel->load(['codigo=?', $codigoProducto]);

        if ($productoModel->loaded() > 0) {
            $mensaje = "El el código $codigoProducto ya existe";
        } else {
            // Verificar si ya existe un producto con el mismo nombre
            $productoModel->load(['nombre=?', $nombreProducto]);

            if ($productoModel->loaded() > 0) {
                $mensaje = "El producto: $nombreProducto ya existe";
            } else {
                // Crear un nuevo producto
                $productoModel->reset(); // Asegurarse de que el objeto no esté cargado con datos anteriores
                $productoModel->set('codigo', $codigoProducto);
                $productoModel->set('nombre', $nombreProducto);
                $productoModel->set('stock', $f3->get('POST.producto_stock'));
                $productoModel->set('precio', $f3->get('POST.producto_precio'));
                $productoModel->set('activo', $f3->get('POST.producto_activo'));
                $productoModel->set('imagen', $f3->get('POST.producto_imagen'));
                $productoModel->save();

                $mensaje = "El producto se ha registrado correctamente";
                $nuevoId = $productoModel->get('id');
            }
        }

        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $nuevoId
            ]
        );
    } // Fin insertarProductos


    public function editarProductos($f3)
    {
        $producto = new M_Productos();
        $mensaje = "";
        $newId = 0;
        $codigoProducto = $f3->get('POST.producto_codigo');

        // Cargar los datos del producto existente con el codigo proporcionado
        $producto->load(['codigo=?', $codigoProducto]);

        if ($producto->loaded() > 0) {
            // Actualizar los datos del producto
            $producto->set('nombre', $f3->get('POST.producto_nombre'));
            $producto->set('stock', $f3->get('POST.producto_stock'));
            $producto->set('precio', $f3->get('POST.producto_precio'));
            $producto->set('activo', $f3->get('POST.producto_activo'));
            $producto->set('imagen', $f3->get('POST.producto_imagen'));

            // Guardar los cambios en la base de datos
            $producto->save();

            $mensaje = "Se ha editado correctamente";
            $newId = $producto->get('id');
        } else {
            $mensaje = "La Producto con el codigo: $codigoProducto no existe";
        }

        // Devolver la respuesta en formato JSON
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]
        );
    }











    public function eliminarProductos($f3)
    {

        $mensaje = "";
        $newId = 0;
        $producto_id = $f3->get('POST.producto_id');

        $this->M_Producto->load(['id=?',  $producto_id]);
        if ($this->M_Producto->loaded() > 0) {
            $this->M_Producto->erase();
            $mensaje = "Se ha eliminado correctamente";
            $newId = 1;
        } else {
            $mensaje = "La Producto con el codigo que intentas eliminar no existe";
        }
        echo json_encode([
            'mensaje' => $mensaje,
            'id' => $newId,
            'comprobacion' => "Los datos eliminados son con el id: " . $this->M_Producto->get('id'),
            'codigo' => $this->M_Producto->get('codigo'),
            'nombre' => $this->M_Producto->get('nombre'),
            'stock' => $this->M_Producto->get('stock'),
            'precio' => $this->M_Producto->get('precio'),
            'activo' => $this->M_Producto->get('activo'),
            'imagen' => $this->M_Producto->get('imagen')
        ]);
    } //fin eliminarProductos


} //fin class
