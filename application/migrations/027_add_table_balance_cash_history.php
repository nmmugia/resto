<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_balance_cash_history extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'auto_increment' => TRUE
      ),
      'date' => array(
        'type' => 'DATETIME',
        'NULL' => TRUE
      ),
      'user_id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'description' => array(
        'type' => 'TEXT',
        'NULL' => TRUE
      ),
      'amount' => array(
        'type' => 'DOUBLE',
        'NULL' => TRUE
      ),
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('balance_cash_history',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('balance_cash_history');
  }
}
