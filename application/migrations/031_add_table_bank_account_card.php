<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_bank_account_card extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => TRUE
      ),
      'bank_account_id' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'card_type_id' => array(
        'type' => 'INT',
        'constraint' => 11
      ), 
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('bank_account_card',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('bank_account_card');
  }
}
