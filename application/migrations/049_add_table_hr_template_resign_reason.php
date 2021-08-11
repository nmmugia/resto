<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_hr_template_resign_reason extends CI_Migration{

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
			'name' => array(
        'type' => 'VARCHAR',
				'constraint'=>50,
				'null'=>true
      ),
      'description' => array(
        'type' => 'TEXT',
				'null'=>true
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_template_resign_reason',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('hr_template_resign_reason');
  }
}
