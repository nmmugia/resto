<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_purchase_order_payment_history extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    if ($this->db->table_exists('purchase_order_payment_history') == FALSE) {
      $fields = array(
        'id'  => array(
          'type'            => 'INT',
          'constraint'      => 11,
          'auto_increment'  => TRUE
        ),
        'purchase_order_receive_id'  => array(
          'type'        => 'INT',
          'null'        => TRUE
        ),
        'amount' => array(
          'type'        => 'DOUBLE',
          'null'        => TRUE
        ),
        'payment_date'  => array(
          'type'  => 'DATETIME',
          'null'  => TRUE
        ),
        'has_sync'  => array(
          'type'        => 'TINYINT',
          'constraint'  => 4,
          'null'        => TRUE,
          'DEFAULT'     => 0
        )
      );

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', TRUE);
      $this->dbforge->create_table('purchase_order_payment_history', TRUE);
    }
  }

  public function down() {
    $this->dbforge->drop_table('purchase_order_payment_history');
  }
}