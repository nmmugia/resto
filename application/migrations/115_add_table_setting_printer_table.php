<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_setting_printer_table extends CI_Migration{

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
      'printer_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      ),
      'table_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      ),
      'created_by' => array(
        'type' => 'INT',
        'NULL' => TRUE
      ),
      'created_at' => array(
        'type' => 'DATETIME',
        'NULL' => TRUE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('setting_printer_table',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('setting_printer_table');
  }
}
