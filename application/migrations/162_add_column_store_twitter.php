<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_store_twitter extends CI_Migration{
public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'store_twitter' => array(
        'type'        => 'varchar', 
        'constraint' => 255,
        'null'        => true
      )
    );
    $this->dbforge->add_column('store', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('store', 'store_twitter');
  }
  
}
