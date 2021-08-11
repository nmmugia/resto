<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_has_sync_hr_office_hours extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'has_sync' => array(
        'type' => 'TINYINT',
        'constraint' => 4,
        'default' => 0,
        'null' => FALSE
        )
    );
    $this->dbforge->add_column('hr_office_hours', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('hr_office_hours', 'has_sync');
  }

}