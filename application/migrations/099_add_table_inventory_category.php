<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_inventory_category extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => TRUE
      ),
      'category_name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'null'=>true
      ),
      'is_active' => array(
        'type' => 'TINYINT',
        'constraint' => 4,
        'default' => 1,
        'NULL' => FALSE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('inventory_category',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('inventory_category');
  }
}
