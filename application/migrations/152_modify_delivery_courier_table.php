<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_modify_delivery_courier_table extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $this->dbforge->drop_column('delivery_courier', 'birthdate');

    $fields = array(
      'is_active'  => array(
        'type'        => 'INT',
        'constraint'  => 11,
        'default'     => 1,
        'null'        => TRUE
      )
    );
    $this->dbforge->add_column('delivery_courier', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('delivery_courier', 'is_active');
  }

}