<?php
class Pedidos_ctrl
{
    public $M_Pedido = null;
    public $M_Cliente = null;
    public $M_Usuario = null;
    public $M_Pedido_detalle = null;
    public function __construct()
    {
        $this->M_Pedido = new M_Pedidos();
        $this->M_Cliente = new M_Clientes();
        $this->M_Usuario = new M_Usuarios();
        $this->M_Pedido_detalle = new M_Pedidos_detalles();
    }

    public function listarPedidosSql($f3)
    {
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from pedidos ";

        //echo $cadenaSql
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    } //listarPedidosSql

    public function listarPedidosxidSql($f3)
    {
        $pedido_id = $f3->get('POST.pedido_id'); //debe tener este nombre al momento de enviar desde el cliente
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from pedidos ";
        $cadenaSql = $cadenaSql . " where id = " . $pedido_id;

        //echo $cadenaSql
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje' => count($items) > 0 ? 'Operacion exitosa' : 'No hay registro para esa consulta',
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    } //listarPedidosxidSql

    public function insertarPedidos($f3)
    {

        $mensaje = "";
        $newId = 0;

        // Obtener los datos del pedido desde el POST
        $clienteId = $f3->get('POST.pedido_cliente_id');
        $fecha = $f3->get('POST.pedido_fecha');
        $usuarioId = $f3->get('POST.pedido_usuario_id');
        $estado = $f3->get('POST.pedido_estado');

        // Verificar si el cliente existe
        $cliente = new M_Clientes();
        $cliente->load(['id=?', $clienteId]);

        if ($cliente->loaded() == 0) {
            $mensaje = "El cliente con el id especificado no existe";
        } else {
            // Verificar si el usuario existe
            $usuario = new M_Usuarios();
            $usuario->load(['id=?', $usuarioId]);

            if ($usuario->loaded() == 0) {
                $mensaje = "El usuario con el id especificado no existe";
            } else {
                // Registrar el pedido
                $pedido = new M_Pedidos();
                $pedido->set('cliente_id', $clienteId);
                $pedido->set('fecha', $fecha);
                $pedido->set('usuario_id', $usuarioId);
                $pedido->set('estado', $estado);
                $pedido->save();

                $mensaje = "Se ha registrado correctamente";
                $newId = $pedido->get('id');
            }
        }

        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]
        );
    }


    public function editarPedidos($f3)
    {

        $mensaje = "";

        // Obtener los datos del pedido desde el POST
        $pedidoId = $f3->get('POST.pedido_id');
        $clienteId = $f3->get('POST.pedido_cliente_id');
        $fecha = $f3->get('POST.pedido_fecha');
        $usuarioId = $f3->get('POST.pedido_usuario_id');
        $estado = $f3->get('POST.pedido_estado');

        // Cargar el pedido existente
        $pedido = new M_Pedidos();
        $pedido->load(['id=?', $pedidoId]);

        if ($pedido->loaded() == 0) {
            $mensaje = "El pedido con el id especificado no existe";
        } else {
            // Verificar si el cliente existe
            $cliente = new M_Clientes();
            $cliente->load(['id=?', $clienteId]);

            if ($cliente->loaded() == 0) {
                $mensaje = "El cliente con el id especificado no existe";
            } else {
                // Verificar si el usuario existe
                $usuario = new M_Usuarios();
                $usuario->load(['id=?', $usuarioId]);

                if ($usuario->loaded() == 0) {
                    $mensaje = "El usuario con el id especificado no existe";
                } else {
                    // Actualizar el pedido
                    $pedido->set('cliente_id', $clienteId);
                    $pedido->set('fecha', $fecha);
                    $pedido->set('usuario_id', $usuarioId);
                    $pedido->set('estado', $estado);
                    $pedido->save();

                    $mensaje = "El pedido ha sido actualizado correctamente";
                }
            }
        }

        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $pedidoId
            ]
        );
    }

    public function eliminarPedidos($f3)
    {
        /*{
            "pedido_id": "3"
        }*/
        $mensaje = "";

        // Obtener el id del pedido desde el POST
        $pedidoId = $f3->get('POST.pedido_id');

        // Cargar el pedido existente
        $pedido = new M_Pedidos();
        $pedido->load(['id=?', $pedidoId]);

        if ($pedido->loaded() == 0) {
            $mensaje = "El pedido con el id especificado no existe";
        } else {
            // Verificar si hay detalles del pedido
            $detallePedido = new M_Pedidos_detalles();
            $detallePedido->load(['pedido_id=?', $pedidoId]);

            if ($detallePedido->loaded() > 0) {
                $mensaje = "El pedido no puede ser eliminado porque tiene detalles asociados";
            } else {
                // Eliminar el pedido
                $pedido->erase();
                $mensaje = "El pedido ha sido eliminado correctamente";
            }
        }

        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $pedidoId
            ]
        );
    }


    

}
