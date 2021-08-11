<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_hr_schedule_change extends CI_Migration{

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
      ),
      'ex_user_id' => array(
        'type' => 'INT',
        'constraint' => 11,
      ),
      'created_at' => array(
        'type' => 'date',
      ),
      'note' => array(
        'type' => 'text',
      )
    );

    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_schedule_change',TRUE);

  }

  public function down() {
    $this->dbforge->drop_table('hr_schedule_change');
  }
  
}
