<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_is_percentage_to_delivery_cost extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'is_percentage' => array(
        'type' => 'tinyint',
        'constraint'=>1,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("enum_delivery_cost",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('enum_delivery_cost', 'is_percentage');
  }
}
