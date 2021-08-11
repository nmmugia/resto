<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_to_side_bar_menu extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'module_id' => array(
        'type' => 'INT',
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("sidebar_menu",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('sidebar_menu', 'module_id');
  }
}
