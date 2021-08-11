<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_column_position_to_menu extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'position' => array(
        'type' => 'int',
        'constraint' => 11,
        'NULL'=>true
      )
    );
    $this->dbforge->add_column("menu",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('menu', 'position');
  }
}
