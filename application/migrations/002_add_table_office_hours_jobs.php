<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_office_hours_jobs extends CI_Migration{

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
        'constraint' => 50
      ),
      'checkin_time' => array(
        'type' => 'TIME'
      ) ,
      'checkout_time' => array(
        'type' => 'TIME'
      )  
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_office_hours',TRUE);

    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'auto_increment' => TRUE
      ),
      'jobs_name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50
      ),
      'note' => array(
        'type' => 'TEXT'
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('hr_jobs',TRUE);


     
  }

  public function down(){
    $this->dbforge->drop_table('hr_office_hours');
    $this->dbforge->drop_table('hr_jobs'); 
  }
}
