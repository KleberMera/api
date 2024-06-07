<?php
class Clientes_ctrl
{
    public $M_Cliente = null;

    public function __construct()
    {
        $this->M_Cliente = new M_Clientes();
    }


    public function listarClientesSql($f3)
    {
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from clientes ";

        //echo $cadenaSql
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    } //listarClientesSql

    public function listarClientesxidSql($f3)
    {
        $cliente_id = $f3->get('POST.cliente_id'); //debe tener este nombre al momento de enviar desde el cliente
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from clientes ";
        $cadenaSql = $cadenaSql . " where id = " . $cliente_id;

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


    //Listar mediante secuencias de SQL
    public function insertarClientesSql($f3)
    {

        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " insert into clientes ";
        $cadenaSql = $cadenaSql . " (identificacion, nombre, telefono, correo, direccion, pais, ciudad) ";
        $cadenaSql = $cadenaSql . " values (";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_cedula') . "',";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_nombre') . "',";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_telefono') . "',";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_correo') . "',";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_direccion') . "',";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_pais') . "',";
        $cadenaSql = $cadenaSql . " '" . $f3->get('POST.cliente_ciudad') . "')";

        echo $cadenaSql;
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje' => "Operacion exitosa",

                'data' => $items
            ]

        );
    } //FIN insertarClientes

    public function insertarClientes($f3)
    {
        $cliente = new M_Clientes();
        $mensaje = "";
        $newId = 0;
        $cliente->load(['identificacion=?',  $f3->get('POST.cliente_cedula')]); //se cargan los datos de la tabla clientes

        if ($cliente->loaded() > 0) {
            $mensaje = "La Persona con la cedula que intentas registrar ya existe";
        } else {
            $this->M_Cliente->set('identificacion', $f3->get('POST.cliente_cedula'));
            $this->M_Cliente->set('nombre', $f3->get('POST.cliente_nombre'));
            $this->M_Cliente->set('telefono', $f3->get('POST.cliente_telefono'));
            $this->M_Cliente->set('correo', $f3->get('POST.cliente_correo'));
            $this->M_Cliente->set('direccion', $f3->get('POST.cliente_direccion'));
            $this->M_Cliente->set('pais', $f3->get('POST.cliente_pais'));
            $this->M_Cliente->set('ciudad', $f3->get('POST.cliente_ciudad'));
            $this->M_Cliente->save();

            $mensaje = "Se ha registrado correctamente";
            $newId = $this->M_Cliente->get('id');
        }
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]

        );
    } //Fin insertarClientes


    public function editarClientes($f3)
    {
        $cliente = new M_Clientes();
        $mensaje = "";
        $newId = 0;

        // Cargar los datos del cliente existente con la identificación proporcionada
        $cliente->load(['identificacion=?', $f3->get('POST.cliente_cedula')]);

        if ($cliente->loaded() > 0) {
            // Actualizar los datos del cliente
            $cliente->set('nombre', $f3->get('POST.cliente_nombre'));
            $cliente->set('telefono', $f3->get('POST.cliente_telefono'));
            $cliente->set('correo', $f3->get('POST.cliente_correo'));
            $cliente->set('direccion', $f3->get('POST.cliente_direccion'));
            $cliente->set('pais', $f3->get('POST.cliente_pais'));
            $cliente->set('ciudad', $f3->get('POST.cliente_ciudad'));

            // Guardar los cambios en la base de datos
            $cliente->save();

            $mensaje = "Se ha editado correctamente";
            $newId = $cliente->get('id');
        } else {
            $mensaje = "La Persona con la cedula que intentas editar no existe";
        }

        // Devolver la respuesta en formato JSON
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]
        );
    }


    public function eliminarClientes($f3)
    {

        $mensaje = "";
        $newId = 0;
        $cliente_id = $f3->get('POST.cliente_id');

        $this->M_Cliente->load(['id=?',  $cliente_id]);
        if ($this->M_Cliente->loaded() > 0) {
            $this->M_Cliente->erase();
            $mensaje = "Se ha eliminado correctamente";
            $newId = 1;
        } else {
            $mensaje = "La Persona con la cedula que intentas eliminar no existe";
        }
        echo json_encode([
            'mensaje' => $mensaje,
            'id' => $newId
        ]);
    } //Fin eliminarClientes 


}
