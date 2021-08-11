<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_format_order_setting_printer extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'format_order' => array(
        'type' => 'INT',
        'constraint' => 11,
        'default' => 1,
        'null' => FALSE
        )
    );
    $this->dbforge->add_column('setting_printer', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('setting_printer', 'format_order');
  }

}