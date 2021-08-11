<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_cash_on_hand_to_open_close_cashier extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'cash_on_hand' => array(
        'type' => 'float',
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("open_close_cashier",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('open_close_cashier', 'cash_on_hand');
  }
}
