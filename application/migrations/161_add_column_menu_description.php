<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_menu_description extends CI_Migration{
public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'menu_description' => array(
        'type'        => 'varchar', 
        'constraint' => 255,
        'null'        => true
      )
    );
    $this->dbforge->add_column('menu', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('menu', 'menu_description');
  }
  
}
