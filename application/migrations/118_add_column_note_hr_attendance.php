<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_note_hr_attendance extends CI_Migration {

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'note' => array(
        'type' => 'text',
        'NULL' => TRUE
      )
    );
    $this->dbforge->add_column('hr_attendances', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('hr_attendances', 'note');
  }

}