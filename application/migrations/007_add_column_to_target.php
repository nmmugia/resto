<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_to_target extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'is_percentage' => array(
        'type' => 'TINYINT',
        'constraint'=>1,
        'NULL'=>true,
        'DEFAULT'=>0
      )
    );
    $this->dbforge->add_column("target",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('target', 'duration');
  }
}
