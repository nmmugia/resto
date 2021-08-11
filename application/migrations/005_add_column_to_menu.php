<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_to_menu extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'duration' => array(
        'type' => 'FLOAT'
      )
    );
    $this->dbforge->add_column("menu",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('menu', 'duration');
  }
}
