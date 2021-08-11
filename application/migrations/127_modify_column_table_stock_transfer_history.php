<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_modify_column_table_stock_transfer_history extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'inventory_name'  => array(
        'name'        => 'uom_id',
        'type'        => 'INT',
        'constraint'  => 11,
        'null'        => false
      )
    );

    $this->dbforge->modify_column('stock_transfer_history', $fields);
  }

  public function down() {
    $fields = array(
      'inventory_name'  => array(
        'name'        => 'uom_id',
        'type'        => 'INT',
        'constraint'  => 11,
        'null'        => false
      )
    );

    $this->dbforge->modify_column('stock_transfer_history', $fields);
  }
}