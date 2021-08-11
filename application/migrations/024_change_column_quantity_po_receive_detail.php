<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_change_column_quantity_po_receive_detail extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'received_quantity' => array(
        'name' => 'received_quantity',
        'type' => 'DOUBLE'
      ),
    );
    $this->dbforge->modify_column('purchase_order_receive_detail', $fields);
  }
   public function down(){
    $fields = array(
      'received_quantity' => array(
        'name' => 'received_quantity',
        'type' => 'INT',
        'constraint' => 11
      ),
    );
    $this->dbforge->modify_column('purchase_order_receive_detail', $fields);
  }
}
