<?php
class M_Pedidos_detalles extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), 'pedidos_detalle');
    }
}
?>