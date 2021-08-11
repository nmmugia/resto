<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_change_column_quantity_po_detail extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'quantity' => array(
        'name' => 'quantity',
        'type' => 'DOUBLE'
      ),
    );
    $this->dbforge->modify_column('purchase_order_detail', $fields);
  }
   public function down(){
    $fields = array(
      'quantity' => array(
        'name' => 'quantity',
        'type' => 'INT',
        'constraint' => 11
      ),
    );
    $this->dbforge->modify_column('purchase_order_detail', $fields);
  }
}
