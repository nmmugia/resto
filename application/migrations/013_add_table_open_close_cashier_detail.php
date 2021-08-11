<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_open_close_cashier_detail extends CI_Migration{

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
      'open_close_cashier_id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'is_enhancher' => array(
        'type' => 'TINYINT',
        'DEFAULT' => 1
      ),
      'name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => TRUE
      ),
      'value' => array(
        'type' => 'DOUBLE',
        'NULL' => TRUE
      ),
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('open_close_cashier_detail',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('open_close_cashier_detail');
  }
}
