<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_backoffice_expenses extends CI_Migration{

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
      'has_sync' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL' => FALSE,
        'default' => 0
      ),
      'expense_number' => array(
        'type' => 'VARCHAR',
        'constraint' => 25,
        'NULL' => FALSE
      ),
      'ge_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL' => FALSE
      ),
      'description' => array(
        'type' => 'text',
        'NULL' => TRUE
      ),
      'account_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL' => FALSE,
        'default' => 0
      ),
      'amount' => array(
        'type' => 'double',
        'NULL' => TRUE
      ),
      'created_at' => array(
        'type' => 'datetime',
        'null' => TRUE
      ),
      'created_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('backoffice_expenses',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('backoffice_expenses');
  }
}
