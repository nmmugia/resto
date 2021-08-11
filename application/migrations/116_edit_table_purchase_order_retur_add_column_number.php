<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_edit_table_purchase_order_retur_add_column_number extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){

    $fields = array(
      'number' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'null'=>true
      )
    );
    $this->dbforge->add_column('purchase_order_retur', $fields);
  }

  public function down(){
    $this->dbforge->drop_column('purchase_order_retur', 'number');
  }
}
