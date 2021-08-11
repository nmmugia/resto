<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_inventory_account extends CI_Migration{

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
      'inventory_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      ),
      'account_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      ),
      'coa_type' => array(
        'type' => 'TINYINT',
        'constraint' => 1,
        'null'=>true
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('inventory_account',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('inventory_account');
  }
}
