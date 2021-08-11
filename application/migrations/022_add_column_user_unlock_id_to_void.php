<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_user_unlock_id_to_void extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'user_unlock_id' => array(
        'type' => 'INT',
        'constraint'=>11,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("void",$fields);
  }
  public function down(){
    $this->dbforge->drop_column('void', 'user_unlock_id');
  }
}
