<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_petty_cash extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'ge_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL'=>true
      )
    );
    $this->dbforge->add_column("petty_cash",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('petty_cash', 'ge_id');
  }
}
