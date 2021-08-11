<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_drop_column_table_stock_transfer_history extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $this->dbforge->drop_column('stock_transfer_history', 'origin_outlet_id');
    $this->dbforge->drop_column('stock_transfer_history', 'destination_outlet_id');
    $this->dbforge->drop_column('stock_transfer_history', 'created_at');
    $this->dbforge->drop_column('stock_transfer_history', 'created_by');
  }

  public function down() {

  }
}