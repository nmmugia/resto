<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_template_global extends CI_Migration{

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
      'created_at' => array(
        'type' => 'DATETIME',
        'NULL' => TRUE
      ),
      'created_by' => array(
        'type' => 'INT',
        'constraint' => 12,
        'NULL' => TRUE
      ),
      'reff' => array(
        'type' => 'VARCHAR',
        'constraint' => 20,
        'NULL' => TRUE
      ),
      'template_name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => TRUE
      ),
      'description' => array(
        'type' => 'TEXT',
        'NULL' => TRUE
      ),
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('template_global',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('template_global');
  }
}
