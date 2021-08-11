<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Change_column_discount_promo_discount extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    
    $fields = array(
      'discount' => array(
        'name' => 'discount',
        'type' => 'FLOAT'
      ),
    );
    $this->dbforge->modify_column('promo_discount', $fields);
  }

  public function down(){
    $fields = array(
      'discount' => array(
        'name' => 'discount',
        'type' => 'INT',
        'constraint' => 11
      ),
    );
    $this->dbforge->modify_column('promo_discount', $fields);
  }
}
