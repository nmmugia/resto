<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_bill_courier_service extends CI_Migration{

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
			'bill_id' => array(
        'type' => 'INT',
				'constraint'=>11
      ),
      'courier_service_percentage' => array(
        'type' => 'FLOAT',
				'null'=>true
      ),
			'courier_service_value' => array(
        'type' => 'FLOAT',
				'null'=>true
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('bill_courier_service',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('bill_courier_service');
  }
}
