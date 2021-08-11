<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_post_to_table_order_menu extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'post_to' => array(
        'type' => 'int',
        'constraint' => 11,
        'NULL'=>true
      )
    );
    $this->dbforge->add_column("order_menu",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('order_menu', 'post_to');
  }
}
