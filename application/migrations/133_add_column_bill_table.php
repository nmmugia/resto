<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_bill_table extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'is_refund'   => array(
        'type'        => 'INT',
        'constraint'  => 11,
        'default'     => 0,
        'null'        => FALSE
      ),
      'refund_key'  => array(
        'type'        => 'VARCHAR',
        'constraint'  => 25,
        'default'     => NULL,
        'null'        => TRUE
      )
    );
    $this->dbforge->add_column('bill', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('bill', 'is_refund');
    $this->dbforge->drop_column('bill', 'refund_key');
  }

}