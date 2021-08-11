<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_has_sync_to_order_history extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
			'has_sync' => array(
        'type' => 'tinyint',
        'constraint' => 1,
        'null'=>true,
        'default'=>0
      ),
    );
    $this->dbforge->add_column("order_history",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('order_history', 'has_sync');
  }
}
