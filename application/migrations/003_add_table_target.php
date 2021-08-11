<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_target extends CI_Migration{

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
      'user_id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'target_type' => array(
        'type' => 'TINYINT',
        'constraint' => 1,
        'NULL' => TRUE
      ),
      'target_by_total' => array(
        'type' => 'DOUBLE',
        'NULL' => TRUE
      ),
      'reward' => array(
        'type' => 'DOUBLE',
        'NULL' => TRUE
      ),
      'calculate_to_payroll' => array(
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1,
        'NULL' => TRUE
      )
      
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('target',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('target');
  }
}
