<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_enum_coa_type extends CI_Migration{

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
			'name' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => TRUE
      ),
      'description' => array(
        'type' => 'VARCHAR',
        'constraint' => 50,
        'NULL' => TRUE
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('enum_coa_type',TRUE);
		$this->db->insert("enum_coa_type", array("name" => "sell", "description" => "Sell Item"));
		$this->db->insert("enum_coa_type", array("name" => "buy", "description" => "Buy Item"));
		$this->db->insert("enum_coa_type", array("name" => "cogs", "description" => "Cogs Item"));
  }

  public function down(){
    $this->dbforge->drop_table('enum_coa_type');
  }
}
