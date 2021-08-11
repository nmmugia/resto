<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_enum_printer_type extends CI_Migration{

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
      'name_type' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null'=>true
      ),
      'value' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null'=>true
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('enum_printer_type',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('enum_printer_type');
  }
}
