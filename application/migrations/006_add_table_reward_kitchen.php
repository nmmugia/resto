<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_reward_kitchen extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
  public function up(){
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 12,
        'auto_increment' => TRUE
      ),
      'created_at' => array(
        'type' => 'DATETIME',
        'NULL' => TRUE
      ),
      'reward' => array(
        'type' => 'DOUBLE',
        'NULL' => TRUE
      ),
      'calculate_to_payroll' => array(
        'type' => 'TINYINT',
        'constraint' => 1,
        'NULL' => TRUE,
        'DEFAULT'=>1
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('reward_kitchen',TRUE);
    $this->db->insert("hr_salary_component",array(
      "name"            => "Reward",
      "is_enhancer"     => 1,
      "key"             => "reward",
      "formula_default" => "reward",
      "is_static"       => 1
    ));
  }

  public function down(){
    $this->dbforge->drop_table('reward_kitchen');
    $this->db->where("key","reward");
    $this->db->delete("hr_salary_component");
  }
}
