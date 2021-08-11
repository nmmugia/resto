<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_hr_payroll_periode extends CI_Migration{

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
      'created_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL'=>TRUE
      ),
      'created_at' => array(
        'type' => 'DATETIME',
        'NULL'=>TRUE
      ),
      'modified_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL'=>TRUE
      ),
      'modified_at' => array(
        'type' => 'DATETIME',
        'NULL'=>TRUE
      ),
      'from' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'to' => array(
        'type' => 'INT',
        'constraint' => 11
      ), 
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_payroll_periode',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('hr_payroll_periode');
  }
}
