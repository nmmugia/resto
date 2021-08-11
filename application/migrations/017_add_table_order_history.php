<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_order_history extends CI_Migration{

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
      'created_by' => array(
        'type' => 'INT',
        'NULL' => TRUE
      ),
      'data' => array(
        'type' => 'TEXT'
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('order_history');
  }

  public function down(){
    $this->dbforge->drop_table('order_history');
  }
}
