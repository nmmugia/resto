<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_hr_employee_holidays extends CI_Migration{

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
        'constraint'=>11,
        'NULL' => TRUE
      ),
      'user_id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'day' => array(
        'type' => 'VARCHAR',
        'constraint'=>100,
        'NULL' => TRUE
      ),
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_employee_holidays',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('hr_employee_holidays');
  }
}
