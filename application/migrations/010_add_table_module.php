<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_module extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'auto_increment' => TRUE
      ),
      'created_at' => array(
        'type' => 'DATETIME',
        'NULL' => TRUE
      ),
      'name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50
      ),
      'is_installed' => array(
        'type' => 'TINYINT',
        'constraint' => 1,
        'NULL' => TRUE,
        'DEFAULT'=>1
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('module',TRUE);
    
  }

  public function down(){
    $this->dbforge->drop_table('module'); 
  }
}
