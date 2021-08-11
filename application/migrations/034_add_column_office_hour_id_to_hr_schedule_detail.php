<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_office_hour_id_to_hr_schedule_detail extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'office_hour_id' => array(
        'type' => 'INT',
        'constraint'=>11,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("hr_schedule_detail",$fields);
  }
  public function down(){
    $this->dbforge->drop_column('hr_schedule_detail', 'office_hour_id');
  }
}
