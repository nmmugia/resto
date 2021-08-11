<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_has_sync_to_reservation extends CI_Migration{

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
    $this->dbforge->add_column("reservation",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('reservation', 'has_sync');
  }
}
