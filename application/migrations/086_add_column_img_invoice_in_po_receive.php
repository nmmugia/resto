<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_img_invoice_in_po_receive extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'invoice_logo' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'NULL'=>true
      )
    );
    $this->dbforge->add_column("purchase_order_receive",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('purchase_order_receive', 'invoice_logo');
  }
}
