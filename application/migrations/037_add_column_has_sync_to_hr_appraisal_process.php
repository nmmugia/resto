<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_has_sync_to_hr_appraisal_process extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'has_sync' => array(
        'type' => 'TINYINT',
        'constraint'=>1,
        'DEFAULT'=>0,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("hr_appraisal_process",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('hr_appraisal_process', 'has_sync');
  }
}
