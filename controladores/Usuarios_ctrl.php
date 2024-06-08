<?php
class Usuarios_ctrl
{
    public $M_Usuario = null;
    public function __construct()
    {
        $this->M_Usuario = new M_Usuarios();
    }

    public function listarUsuarios($f3)
    {
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from usuarios ";

        //echo $cadenaSql
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    } //listarUsuariosSql

    public function listarusuariosxid($f3)
    {
        $usuario_id = $f3->get('POST.usuario_id'); //debe tener este nombre al momento de enviar desde el cliente
        $cadenaSql = "";
        $cadenaSql = $cadenaSql . " select * ";
        $cadenaSql = $cadenaSql . " from usuarios ";
        $cadenaSql = $cadenaSql . " where id = " . $usuario_id;

        //echo $cadenaSql
        $items = $f3->DB->exec($cadenaSql);
        echo json_encode(
            [
                'mensaje' => count($items) > 0 ? 'Operacion exitosa' : 'No hay registro para esa consulta',
                'cantidad' => count($items),
                'data' => $items
            ]

        );
    } //listarUsuariosxidSql

    public function insertarUsuarios($f3)
    {
        $usuario = new M_Usuarios();
        $mensaje = "";
        $newId = 0;
        $usuario->load(['usuario=?', $f3->get('POST.usuario_usuario')]);
        if ($usuario->loaded() > 0) {
            $mensaje = "La Persona con ese usuario ya existe";
        } else {
            /* {
            "id": "1",
            "usuario": "usuario1",
            "clave": "14e1b600b1fd579f47433b88e8d85291",
            "nombre": "Jimmy Castellanos",
            "telefono": "444444",
            "correo": "micorreo@dominio.com",
            "activo": "1"
            }*/
            $this->M_Usuario->set('usuario', $f3->get('POST.usuario_usuario'));
            $this->M_Usuario->set('clave', $f3->get('POST.usuario_clave'));
            $this->M_Usuario->set('nombre', $f3->get('POST.usuario_nombre'));
            $this->M_Usuario->set('telefono', $f3->get('POST.usuario_telefono'));
            $this->M_Usuario->set('correo', $f3->get('POST.usuario_correo'));
            $this->M_Usuario->set('activo', $f3->get('POST.usuario_activo'));
            $this->M_Usuario->save();




            $mensaje = "Se ha registrado correctamente";
            $newId = $this->M_Usuario->get('id');
        }
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId
            ]
        );
    } //fin insertarUsuarios


    public function editarUsuarios($f3)
    {
        $usuario = new M_Usuarios();
        $mensaje = "";
        $newId = 0;

        // Cargar los datos del usuario existente con la identificación proporcionada
        $usuario->load(['usuario=?', $f3->get('POST.usuario_usuario')]);

        if ($usuario->loaded() > 0) {
            // Actualizar los datos del usuario
            $usuario->set('usuario', $f3->get('POST.usuario_usuario'));
            $usuario->set('clave', $f3->get('POST.usuario_clave'));
            $usuario->set('nombre', $f3->get('POST.usuario_nombre'));
            $usuario->set('telefono', $f3->get('POST.usuario_telefono'));
            $usuario->set('correo', $f3->get('POST.usuario_correo'));
            $usuario->set('activo', $f3->get('POST.usuario_activo'));

            // Guardar los cambios en la base de datos
            $usuario->save();

            $mensaje = "Se ha editado correctamente";
            $newId = $usuario->get('id');
        } else {

            $mensaje = "El Usuario con el id que intentas editar no existe";
        }

        // Devolver la respuesta en formato JSON
        echo json_encode(
            [
                'mensaje' => $mensaje,
                'id' => $newId,


            ]
        );
    } //fin editarUsuarios


    public function eliminarUsuarios($f3)
    {

        $mensaje = "";
        $newId = 0;
        $usuario_id = $f3->get('POST.usuario_id');

        $this->M_Usuario->load(['id=?',  $usuario_id]);
        if ($this->M_Usuario->loaded() > 0) {
            $this->M_Usuario->erase();
            $mensaje = "Se ha eliminado correctamente";
            $newId = 1;
        } else {
            $mensaje = "El Usuario con el id que intentas eliminar no existe";
        }
        echo json_encode([
            'mensaje' => $mensaje,
            'id' => $newId
        ]);
    }


    //Ingreso con usuario y clave
    public function login($f3)
    {
        $usuario = new M_Usuarios();
        $mensaje = "";
        $newId = 0;


        $usuario->load(['usuario=?', $f3->get('POST.usuario_usuario')]);
        if ($usuario->loaded() > 0) {
            $usuario->load(['clave=?', $f3->get('POST.usuario_clave')]);
            if ($usuario->loaded() > 0) {
                //verificar si esta activo
                if ($usuario->get('activo') == 1) {
                    $mensaje = "Se ha ingresado correctamente";
                    $newId = $usuario->get('id');
                    $retorno = 1;
                } else {
                    $mensaje = "El usuario no esta activo";
                    $retorno = 0;
                }
            } else {
                $mensaje = "Clave incorrecta";
                $retorno = 0;
            }
        } else {
            $mensaje = "El usuario no existe";
            $retorno = 0;
        }
        echo json_encode([
            'mensaje' => $mensaje,
            'id' => $newId,
            'retorno' => $retorno
        ]);
    }

    //Cambio de clave
    public function cambiarClave($f3)
    {
        //Ingresar nombre de usuario, si el usuario existe cambiar la clave, si no existe devolver mensaje de error
        $usuario = new M_Usuarios();
        $mensaje = "";
        $newId = 0;
        $usuario->load(['usuario=?', $f3->get('POST.usuario_usuario')]);
        if ($usuario->loaded() > 0) {
            $usuario->set('clave', $f3->get('POST.usuario_clave'));
            $usuario->save();
            $mensaje = "Se ha cambiado correctamente";
            $newId = $usuario->get('id');
            $retorno = 1;
        } else {
            $mensaje = "El usuario no existe";
            $retorno = 0;
        }
        echo json_encode([
            'mensaje' => $mensaje,
            'id' => $newId,
            'retorno' => $retorno
        ]);
    }
}
