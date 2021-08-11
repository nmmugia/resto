<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_delivery_courier extends CI_Migration{

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
      'courier_name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => TRUE
      ),
      'courier_code' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => FALSE
      ),
      'delivery_company_id' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'phone' => array(
        'type' => 'varchar',
        'constraint' => 20,
        'NULL' => TRUE
      ),
      'birthdate' => array(
        'type' => 'datetime',
        'NULL' => FALSE
      ),
      'commission' => array(
        'type' => 'double',
        'null' => FALSE,
      ),
      'account_id' => array(
        'type' => 'INT',
        'constraint' => 11
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
    $this->dbforge->create_table('delivery_courier',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('delivery_courier');
  }
}
