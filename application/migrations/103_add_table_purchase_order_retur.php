<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_purchase_order_retur extends CI_Migration{

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
      'store_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => FALSE
      ),
      'has_synchronized' => array(
        'type' => 'TINYINT',
        'constraint' => 4,
        'default' => 0,
        'null' => FALSE
      ),
      'purchase_order_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
      'retur_date' => array(
        'type' => 'DATETIME',
        'null' => TRUE
      ),
      'total' => array(
        'type' => 'DOUBLE',
        'null' => TRUE
      ),
      'payment_method' => array(
        'type' => 'TINYINT',
        'constraint' => 4,
        'default' => 1,
        'null' => TRUE
      ),
      'payment_date' => array(
        'type' => 'DATE',
        'null' => TRUE
      ),
      'payment_no' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'null' => TRUE
      ),
      'payment_status' => array(
        'type' => 'TINYINT',
        'constraint' => 4,
        'default' => 0,
        'null' => FALSE
      ),
      'has_journaled' => array(
        'type' => 'TINYINT',
        'constraint' => 4,
        'default' => 0,
        'null' => FALSE
      ),
      'created_at' => array(
        'type' => 'DATETIME',
        'null' => FALSE
      ),
      'created_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
      'modified_at' => array(
        'type' => 'DATETIME',
        'null' => TRUE
      ),
      'modified_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key(array('id', 'store_id'), TRUE);
    $this->dbforge->create_table('purchase_order_retur',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('purchase_order_retur');
  }
}
