<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_quantity extends CI_Migration{
public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'menu_quantity' => array(
        'type'        => 'INT', 
        'constraint'  => 11,
        'null'        => true,
        'default'     => 0
      )
    );
    $this->dbforge->add_column('menu', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('menu', 'menu_quantity');
  }
  
}
