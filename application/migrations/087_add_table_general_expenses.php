<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_general_expenses extends CI_Migration{

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
			'name' => array(
        'type' => 'varchar',
        'constraint' => 255,
        'null' => TRUE
      ),
			'description' => array(
        'type' => 'text',
        'NULL' => TRUE
      ),
      'account_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
        'store_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('general_expenses',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('general_expenses');
  }
}
