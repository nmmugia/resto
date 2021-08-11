<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_refund extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => TRUE
      ),
      'created_at' => array(
        'type' => 'datetime',
        'null' => TRUE
      ),
      'created_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
      'data'=>array(
        'type'=>'TEXT'
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('refund',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('refund');
  }
}
