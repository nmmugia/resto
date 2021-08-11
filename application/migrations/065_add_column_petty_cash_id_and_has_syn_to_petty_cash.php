<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_petty_cash_id_and_has_syn_to_petty_cash extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'petty_cash_id' => array(
        'type' => 'varchar',
        'constraint' => 15,
        'NULL'=>true
      ),
      'has_sync' => array(
        'type' => 'tinyint',
        'constraint' => 1,
        'null'=>true,
        'default'=>0
      ),
    );
    $this->dbforge->add_column("petty_cash",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('petty_cash', 'petty_cash_id');
    $this->dbforge->drop_column('petty_cash', 'has_sync');
  }
}
