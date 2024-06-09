<?php
class Pedidos_detalles_ctrl
{
    public $M_Pedido_detalle = null;
    public $M_Producto = null;
    public $M_Pedido = null;
    public $M_Cliente = null;
    public $M_Usuario = null;
    public function __construct()
    {
        $this->M_Pedido_detalle = new M_Pedidos_detalles();
        $this->M_Producto = new M_Productos();
        $this->M_Pedido = new M_Pedidos();
        $this->M_Cliente = new M_Clientes();
        $this->M_Usuario = new M_Usuarios();
    }

    public function listarPedidos_detallesSql($f3)
    {
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

    public function listarPedidos_detallesxidSql($f3)
    {
        $pedido_detalle_id = $f3->get('POST.pedido_detalle_id');
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from pedidos_detalle ";
        $cadenaSql = $cadenaSql . " where id = " . $pedido_detalle_id;

        //echo $cadenaSql
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje' => count($items) > 0 ? 'Operacion exitosa' : 'No hay registro para esa consulta',
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    }

    public function insertarPedidosDetalles($f3)
    {
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

                // Actualizar el stock del producto
                $nuevoStock = $producto->get('stock') - $cantidad;
                $producto->set('stock', $nuevoStock);
                $producto->save();

                $mensaje = "Registro Exitoso";
                $newId = $detallePedido->get('id'); // Cambia 'id' por el nombre real de la columna si es diferente
            }
        }

        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]
        );
    } //insertarPedidosDetalles

    public function editarPedidosDetalles($f3)
    {
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

    public function eliminarPedidosDetalles($f3)
    {
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


    public function listarPedidos_detallesPorUsuario($f3)
    {
        $usuario_id = $f3->get('POST.usuario_id'); // Obtener el ID del usuario desde el POST
        $nombre_cliente = $f3->get('POST.nombre_cliente'); // Obtener el nombre del cliente desde el POST

        $cadenaSql = "SELECT 
        pd.id AS PedidoDetalleID,
        p.id AS PedidoID,
        u.nombre AS Usuario,
        c.nombre AS Cliente,
        p.fecha AS Fecha,
        pr.nombre AS Codigo_Producto,
        pr.precio AS Precio_Producto,
        pd.cantidad AS Cantidad,
        (pd.cantidad * pr.precio) AS PrecioTotalDetalle,
        SUM(pd.cantidad * pr.precio) OVER (PARTITION BY u.id, c.nombre) AS SumaPrecioTotalPorUsuarioCliente
    FROM 
        pedidos_detalle pd
    JOIN 
        pedidos p ON pd.pedido_id = p.id
    JOIN 
        productos pr ON pd.producto_id = pr.id
    JOIN 
        usuarios u ON p.usuario_id = u.id
    JOIN 
        clientes c ON p.cliente_id = c.id
    WHERE 
        u.id = :usuario_id AND c.nombre = :nombre_cliente
    ORDER BY 
        u.nombre, p.fecha";

        // Ejecutar la consulta con el ID de usuario y nombre del cliente proporcionados
        $items = $f3->DB->exec($cadenaSql, [
            ':usuario_id' => $usuario_id,
            ':nombre_cliente' => $nombre_cliente
        ]);

        // Devolver los resultados como JSON
        echo json_encode([
            'cantidad' => count($items),
            'data' => $items
        ]);
    }


    public function obtenerResumenPorUsuario($f3)
    {
        $usuario_id = $f3->get('POST.usuario_id'); // Obtener el ID del usuario desde el POST

        $cadenaSql = "SELECT
        pd.pedido_id AS PedidoDetalleID,
        p.id AS PedidoID,
        u.id AS Usuario_ID,
        u.nombre AS Usuario,
        c.nombre AS Cliente,
        COUNT(pd.id) AS Cantidad_Productos,
        SUM(pd.cantidad * pr.precio) AS Total_Productos
    FROM
        usuarios u
    JOIN
        pedidos p ON u.id = p.usuario_id
    JOIN
        clientes c ON p.cliente_id = c.id
    JOIN
        pedidos_detalle pd ON p.id = pd.pedido_id
    JOIN
        productos pr ON pd.producto_id = pr.id
    WHERE
        u.id = :usuario_id
    GROUP BY
        u.id, u.nombre, c.nombre";

        // Ejecutar la consulta con el ID de usuario proporcionado
        $items = $f3->DB->exec($cadenaSql, [':usuario_id' => $usuario_id]);

        // Devolver los resultados como JSON
        echo json_encode([
            'cantidad' => count($items),
            'data' => $items
        ]);
    }


    public function listarPedidos_detallesPorUsuarioPorIDPorNombre($f3)
    {
        $pedido_id = $f3->get('POST.pedido_id'); // Obtener el ID del pedido desde el POST
        $usuario_id = $f3->get('POST.usuario_id'); // Obtener el ID del usuario desde el POST
        $nombre_cliente = $f3->get('POST.nombre_cliente'); // Obtener el nombre del cliente desde el POST

        $cadenaSql = "SELECT 
        pd.id AS PedidoDetalleID,
        p.id AS PedidoID,
        u.nombre AS Usuario,
        c.nombre AS Cliente,
        p.fecha AS Fecha,
        pr.nombre AS Codigo_Producto,
        pr.precio AS Precio_Producto,
        pd.cantidad AS Cantidad,
        (pd.cantidad * pr.precio) AS PrecioTotalDetalle,
        SUM(pd.cantidad * pr.precio) OVER (PARTITION BY u.id, c.nombre) AS SumaPrecioTotalPorUsuarioCliente
    FROM 
        pedidos_detalle pd
    JOIN 
        pedidos p ON pd.pedido_id = p.id
    JOIN 
        productos pr ON pd.producto_id = pr.id
    JOIN 
        usuarios u ON p.usuario_id = u.id
    JOIN 
        clientes c ON p.cliente_id = c.id
    WHERE 
        p.id = :pedido_id AND u.id = :usuario_id AND c.nombre = :nombre_cliente
    ORDER BY 
        u.nombre, p.fecha";

        // Ejecutar la consulta con el ID del pedido, ID del usuario y nombre del cliente proporcionados
        $items = $f3->DB->exec($cadenaSql, [
            ':pedido_id' => $pedido_id,
            ':usuario_id' => $usuario_id,
            ':nombre_cliente' => $nombre_cliente
        ]);

        // Devolver los resultados como JSON
        echo json_encode([
            'cantidad' => count($items),
            'data' => $items
        ]);
    }

    public function listarPedidosPorUsuarioYNombre($f3)
    {
        $usuario_id = $f3->get('POST.usuario_id');
        $nombre_cliente = $f3->get('POST.nombre_cliente');

        $cadenaSql = "SELECT 
        p.id AS id,
        p.fecha AS fecha
    FROM 
        pedidos p
    JOIN 
        usuarios u ON p.usuario_id = u.id
    JOIN 
        clientes c ON p.cliente_id = c.id
    WHERE 
        u.id = :usuario_id AND c.nombre = :nombre_cliente
    ORDER BY 
        p.fecha";

        $items = $f3->DB->exec($cadenaSql, [
            ':usuario_id' => $usuario_id,
            ':nombre_cliente' => $nombre_cliente
        ]);

        echo json_encode([
            'cantidad' => count($items),
            'data' => $items
        ]);
    }


    public function eliminarPedidosDetallesxPedidoID($f3)
    {
        $mensaje = "";

        // Obtener el id del pedido del pedido desde el POST
        $detallePedidoId = $f3->get('POST.pedido_id');

        // Cargar el detalle del pedido existente
        $detallePedido = new M_Pedidos_detalles();
        $detallePedido->load(['pedido_id=?', $detallePedidoId]);

        if ($detallePedido->loaded() == 0) {
            $mensaje = "El detalle del pedido con el id especificado no existe";
        } else {
           // Eliminar todos los detalles del pedido que se encuentran asociados
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
