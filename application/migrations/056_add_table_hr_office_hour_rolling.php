<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_hr_office_hour_rolling extends CI_Migration{

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
			'office_hour_id' => array(
        'type' => 'INT',
				'constraint'=>11,
				'null'=>true
      ),
      'office_hour_target_id' => array(
        'type' => 'INT',
				'constraint'=>11,
				'null'=>true
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_office_hour_rolling',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('hr_office_hour_rolling');
  }
}
