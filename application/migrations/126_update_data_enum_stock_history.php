<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_data_enum_stock_history extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  
  public function up(){
    $result = $this->db->truncate("enum_stock_history_status");
    if ($result){
      $this->db->insert("enum_stock_history_status", array(
        "value" => "Dipakai di kitchen"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Dipindahkan ke gudang/outlet lain"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Pembelian barang"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Selisih stock opname"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Datang dari gudang/outlet lain"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Proses Konversi Inventory"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Spoiled"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Bertambah karena void"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Retur Pembelian"
      ));

      $this->db->insert("enum_stock_history_status", array(
        "value" => "Bertambah karena refund"
      ));
    }
  }

  public function down(){
    $this->db->truncate("enum_stock_history_status");
  }
}
