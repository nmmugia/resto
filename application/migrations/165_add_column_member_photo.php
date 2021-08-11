<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_member_photo extends CI_Migration{
public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'member_photo' => array(
        'type'        => 'text',  
        'null'        => true
      )
    );
    $this->dbforge->add_column('member', $fields);
  }

  public function down() {
    $this->dbforge->drop_column('member', 'member_photo');
  }
  
}
