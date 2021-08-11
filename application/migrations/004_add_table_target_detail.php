<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_target_detail extends CI_Migration{

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
      'target_id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'menu_id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'target_qty' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('target_detail',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('target_detail');
  }
}
