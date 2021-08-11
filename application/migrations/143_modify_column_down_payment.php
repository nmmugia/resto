<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_modify_column_down_payment extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {

    $fields = array(
      'down_payment'  => array(
        'name'        => 'down_payment',
        'type'        => 'bigint',
        'null'        => false
      )
    );
    $this->dbforge->modify_column('reservation', $fields);
  }

  public function down() {
   
  }

}