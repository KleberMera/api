<?php
class Pedidos_detalles_ctrl
{
    public $M_Pedido_detalle = null;
    public $M_Producto = null;
    public function __construct()
    {
        $this->M_Pedido_detalle = new M_Pedidos_detalles();
        $this->M_Producto = new M_Productos();
    }

    public function listarPedidos_detallesSql($f3){
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from pedidos_detalle ";
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    }

    public function listarPedidos_detallesxidSql($f3){
        $pedido_detalle_id=$f3->get('POST.pedido_detalle_id'); 
        $cadenaSql="";
        $cadenaSql=$cadenaSql." select * ";
        $cadenaSql=$cadenaSql." from pedidos_detalle ";
        $cadenaSql=$cadenaSql." where id = ".$pedido_detalle_id;

        //echo $cadenaSql
        $items=$f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje'=> count($items)>0?'Operacion exitosa':'No hay registro para esa consulta',
                'cantidad'=>count($items),
                'data'=> $items
            ]

        );
    }

    public function insertarPedidosDetalles($f3) {
        $mensaje = "";
        $newId = 0;
    
        // Obtener los datos del detalle del pedido desde el POST
        $productoId = $f3->get('POST.detalle_pedido_producto_id');
        $pedidoId = $f3->get('POST.detalle_pedido_id');
        
        $cantidad = $f3->get('POST.detalle_pedido_cantidad');
    
        // Verificar si el pedido existe
        $pedido = new M_Pedidos();
        $pedido->load(['id=?', $pedidoId]);
    
        if ($pedido->loaded() == 0) {
            $mensaje = "El pedido con el id especificado no existe";
        } else {
            // Verificar si el producto existe
            $producto = new M_Productos();
            $producto->load(['id=?', $productoId]);
    
            if ($producto->loaded() == 0) {
                $mensaje = "El producto con el id especificado no existe";
            } else {
                // Calcular el precio total
                $precioUnitario = $producto->get('precio');
                $precioTotal = $precioUnitario * $cantidad;
    
                // Insertar el detalle del pedido
                $detallePedido = new M_Pedidos_detalles();
                $detallePedido->set('producto_id', $productoId);
                $detallePedido->set('pedido_id', $pedidoId);
                $detallePedido->set('cantidad', $cantidad);
                $detallePedido->set('precio', $precioTotal);
                $detallePedido->save();
    
                $mensaje = "Se ha registrado correctamente el detalle del pedido";
                $newId = $detallePedido->get('id'); // Cambia 'id' por el nombre real de la columna si es diferente
            }
        }
    
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]
        );
    }//insertarPedidosDetalles

    public function editarPedidosDetalles($f3) {
        $mensaje = "";
    
        // Obtener los datos del detalle del pedido desde el POST
        $detallePedidoId = $f3->get('POST.detalle_id');
        $productoId = $f3->get('POST.detalle_pedido_producto_id');
        $pedidoId = $f3->get('POST.detalle_pedido_id');
        
        $cantidad = $f3->get('POST.detalle_pedido_cantidad');
    
        // Cargar el detalle del pedido existente
        $detallePedido = new M_Pedidos_detalles();
        $detallePedido->load(['id=?', $detallePedidoId]);
    
        if ($detallePedido->loaded() == 0) {
            $mensaje = "El detalle del pedido con el id especificado no existe";
        } else {
            // Verificar si el producto existe
            $producto = new M_Productos();
            $producto->load(['id=?', $productoId]);
    
            if ($producto->loaded() == 0) {
                $mensaje = "El producto con el id especificado no existe";
            } else {
                // Calcular el precio total
                $precioUnitario = $producto->get('precio');
                $precioTotal = $precioUnitario * $cantidad;
    
                // Actualizar el detalle del pedido
                $detallePedido->set('producto_id', $productoId);
                $detallePedido->set('pedido_id', $pedidoId);
                $detallePedido->set('cantidad', $cantidad);
                $detallePedido->set('precio', $precioTotal);
                $detallePedido->save();
    
                $mensaje = "El detalle del pedido ha sido actualizado correctamente";
            }
        }
    
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $detallePedidoId
            ]
        );      
    }

    public function eliminarPedidosDetalles($f3) {
        $mensaje = "";
    
        // Obtener el id del detalle del pedido desde el POST
        $detallePedidoId = $f3->get('POST.detalle_id');
    
        // Cargar el detalle del pedido existente
        $detallePedido = new M_Pedidos_detalles();
        $detallePedido->load(['id=?', $detallePedidoId]);
    
        if ($detallePedido->loaded() == 0) {
            $mensaje = "El detalle del pedido con el id especificado no existe";
        } else {
            // Eliminar el detalle del pedido
            $detallePedido->erase();
            $mensaje = "El detalle del pedido ha sido eliminado correctamente";
        }
    
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $detallePedidoId
            ]
        );  
    }
    
    
        

    

  

    
}
