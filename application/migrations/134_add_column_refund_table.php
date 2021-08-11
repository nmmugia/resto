<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_refund_table extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {

    $fields = array(
      'refund_key'  => array(
        'type'        => 'VARCHAR',
        'constraint'  => 25,
        'default'     => NULL,
        'null'        => TRUE
      )
    );
    $this->dbforge->add_column('refund', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('refund', 'refund_key');
  }

}