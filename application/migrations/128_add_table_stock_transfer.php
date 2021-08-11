<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_stock_transfer extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    if ($this->db->table_exists('stock_transfer') == FALSE) {
      $fields = array(
        'id'  => array(
          'type'            => 'INT',
          'constraint'      => 11,
          'auto_increment'  => TRUE
        ),
        'origin_outlet_id'  => array(
          'type'        => 'INT',
          'constraint'  => 11,
          'null'        => false
        ),
        'destination_outlet_id' => array(
          'type'        => 'INT',
          'constraint'  => 11,
          'null'        => false
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
      $this->dbforge->create_table('stock_transfer', TRUE);
    }
  }

  public function down() {
    $this->dbforge->drop_table('stock_transfer');
  }
}