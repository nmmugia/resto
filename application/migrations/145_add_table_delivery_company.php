<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_delivery_company extends CI_Migration{

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
      'company_name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => TRUE
      ),
      'company_address' => array(
        'type' => 'text',
        'NULL' => TRUE
      ),
      'email' => array(
        'type' => 'varchar',
        'constraint' => 200,
        'NULL' => TRUE
      ),
      'phone' => array(
        'type' => 'varchar',
        'constraint' => 20,
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
    $this->dbforge->create_table('delivery_company',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('delivery_company');
  }
}
