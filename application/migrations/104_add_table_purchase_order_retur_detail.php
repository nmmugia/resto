<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_purchase_order_retur_detail extends CI_Migration{

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
      'purchase_order_detail_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
      'retur_quantity' => array(
        'type' => 'DOUBLE',
        'null' => FALSE
      ),
      'price' => array(
        'type' => 'DOUBLE',
        'null' => TRUE
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
      'purchase_order_retur_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key(array('id', 'store_id'), TRUE);
    $this->dbforge->create_table('purchase_order_retur_detail',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('purchase_order_retur_detail');
  }
}
