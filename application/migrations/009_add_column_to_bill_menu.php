<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_to_bill_menu extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'finished_at' => array(
        'type' => 'DATETIME',
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("bill_menu",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('bill_menu', 'finished_at');
  }
}
