<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_modify_column_table_order_menu_inventory_cogs extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
	public function up(){
    $fields = array(
      'quantity'  => array(
        'name'        => 'quantity',
        'type'        => 'double',
        'null'        => false
      )
    );
    $this->dbforge->modify_column('order_menu_inventory_cogs', $fields);
  }

  public function down(){    

  }
}
