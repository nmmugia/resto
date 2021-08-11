<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_taxes_table extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {

    $fields = array(
      'is_service'  => array(
        'type'        => 'INT',
        'constraint'  => 11,
        'null'        => false
      )
    );
    $this->dbforge->add_column('taxes', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('taxes', 'is_service');
  }

}