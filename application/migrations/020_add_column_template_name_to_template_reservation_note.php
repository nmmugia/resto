<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_template_name_to_template_reservation_note extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'template_name' => array(
        'type' => 'VARCHAR',
        'constraint'=>60,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("template_reservation_note",$fields);
  }
  public function down(){
    $this->dbforge->drop_column('template_reservation_note', 'template_name');
  }
}
