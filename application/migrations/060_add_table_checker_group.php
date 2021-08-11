<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_checker_group extends CI_Migration{

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
			'user_id' => array(
        'type' => 'INT',
				'constraint'=>11,
				'null'=>true
      ),
      'checker_number' => array(
        'type' => 'INT',
				'constraint'=>11,
				'default'=>1
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('checker_group',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('checker_group');
  }
}
