<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_order_taxes extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    if ($this->db->table_exists('order_taxes') == FALSE) {
      $fields = array(
        'id'  => array(
          'type'            => 'INT',
          'constraint'      => 11,
          'auto_increment'  => TRUE
        ),
        'tax_id'  => array(
          'type'        => 'INT',
          'constraint'  => 11,
          'null'        => false
        ),
        'order_type' => array(
          'type'        => 'INT',
          'constraint'  => 11,
          'null'        => false
        ),
        'is_active' => array(
          'type'        => 'INT',
          'constraint'  => 11,
          'null'        => false,
          'default'     => 1
        ),
        'created_at'  => array(
          'type'  => 'DATETIME',
          'null'  => false
        ),
        'created_by'  => array(
          'type'        => 'INT',
          'constraint'  => 11,
          'null'        => false
        )
      );

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', TRUE);
      $this->dbforge->create_table('order_taxes', TRUE);
    }
  }

  public function down() {
    $this->dbforge->drop_table('order_taxes');
  }
}