<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_setting_printer extends CI_Migration{

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
      'name_printer' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null'=>true
      ),
      'alias_name' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null'=>true
      ),
      'outlet_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      ),
      'printer_width' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null'=>true
      ),
      'font_size' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      ),
      'logo' => array(
        'type' => 'VARCHAR',
        'constraint' => 200,
        'null'=>true
      ),
      'type' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null'=>true
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('setting_printer',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('setting_printer');
  }
}
