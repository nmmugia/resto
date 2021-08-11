<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_edit_table_purchase_order_retur_detail_add_column_notes extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $fields = array(
      'notes' => array(
        'type' => 'TEXT',
        'null' => true
      ),
      'purchase_order_receive_detail_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => true
      )
    );
    $this->dbforge->add_column('purchase_order_retur_detail', $fields);
  }

  public function down(){
    $this->dbforge->drop_column('purchase_order_retur_detail', 'notes');
    $this->dbforge->drop_column('purchase_order_retur_detail', 'purchase_order_receive_detail_id');
  }
}
