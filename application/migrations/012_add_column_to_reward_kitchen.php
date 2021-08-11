<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_to_reward_kitchen extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'outlet_id' => array(
        'type' => 'INT',
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("reward_kitchen",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('reward_kitchen', 'outlet_id');
  }
}
