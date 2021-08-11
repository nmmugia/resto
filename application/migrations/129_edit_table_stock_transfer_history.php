<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_edit_table_stock_transfer_history extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'stock_transfer_id' => array(
        'type'        => 'INT',
        'constraint'  => 11,
        'null'        => false
      )
    );
    $this->dbforge->add_column('stock_transfer_history', $fields);
  }

  public function down() {

  }
}