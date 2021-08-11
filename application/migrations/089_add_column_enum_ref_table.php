<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_enum_ref_table extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'enum_ref_table' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'enum_ref_id' => array(
        'type' => 'INT',
        'constraint' => 11
      )
    );
    $this->dbforge->add_column("bill_information",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('bill_information', 'enum_ref_table');
    $this->dbforge->drop_column('bill_information', 'enum_ref_id');
  }
}
