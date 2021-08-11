<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_is_package_to_category extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'is_package' => array(
        'type' => 'tinyint',
        'constraint' => 11,
        'NULL'=>true,
				'default'=>0
      )
    );
    $this->dbforge->add_column("category",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('category', 'is_package');
  }
}
