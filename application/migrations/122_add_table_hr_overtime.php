<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_hr_overtime extends CI_Migration{

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => TRUE
      ),
      'user_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => FALSE
      ),
      'start_time' => array(
        'type' => 'time',
        'null' => TRUE
      ),
      'end_time' => array(
        'type' => 'time',
        'null' => TRUE
      ),
      'created_at' => array(
        'type' => 'date',
        'null'=> TRUE
      ),
      'note' => array(
        'type' => 'text',
        'NULL' => TRUE
      )
    );

    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_overtime',TRUE);

  }

  public function down() {
    $this->dbforge->drop_table('hr_overtime');
  }
  
}
