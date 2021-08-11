<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_enum_transaction_type_and_resto_transaction_configuration extends CI_Migration{

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
        'constraint' => 100,
      ),
      'description'=>array(
        'type'=>'TEXT'
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('enum_transaction_type',TRUE);

    $data = array(
      array(
        "name" => "POS",
        "description" => "POS"
      ),
      array(
        "name" => "Transafer Inventory",
        "description" => "Transafer Inventory"
      ),
      array(
        "name" => "Purchase Inventory",
        "description" => "Purchase Inventory"
      ),
      array(
        "name" => "Auto Reduce Inventory",
        "description" => "Auto Reduce Inventory"
      ),
      array(
        "name" => "Jurnal Akunting",
        "description" => "Jurnal Akunting"
      ),
    );

    $this->db->insert_batch('enum_transaction_type', $data); 

    $fields_resto = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => TRUE
      ),
			'resto_id' => array(
        'type' => 'INT',
				'constraint'=>11
      ),
      'transaction_type' => array(
        'type' => 'INT',
        'constraint'=>11
      ),
      'periode_cost' => array(
        'type' => "ENUM('harian','mingguan','bulanan')",
        'null'=>true
      ),
      'max_cost' => array(
        'type' => 'FLOAT',
        'null'=>true
      ),
			'max_day_last_update' => array(
        'type' => 'INT',
				'null'=>true
      )
    );
    $this->dbforge->add_field($fields_resto);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('resto_transaction_configuration',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('enum_transaction_type');
    $this->dbforge->drop_table('resto_transaction_configuration');
  }
}
