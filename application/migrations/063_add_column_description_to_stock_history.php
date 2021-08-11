<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_description_to_stock_history extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'description' => array(
        'type' => 'text',
        'NULL'=>true
      )
    );
    $this->dbforge->add_column("stock_history",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('stock_history', 'description');
  }
}
