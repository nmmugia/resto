<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_card_type_id_to_bill_payment extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'card_type_id' => array(
        'type' => 'INT',
        'constraint'=>11,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("bill_payment",$fields);
  }
  public function down(){
    $this->dbforge->drop_column('bill_payment', 'card_type_id');
  }
}
