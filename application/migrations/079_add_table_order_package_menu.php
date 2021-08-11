<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_order_package_menu extends CI_Migration{

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
			'order_menu_id' => array(
        'type' => 'INT',
				'constraint'=>11
      ),
			'menu_id' => array(
        'type' => 'INT',
				'constraint'=>11
      ),
			'quantity' => array(
        'type' => 'INT',
				'constraint'=>11
      ),
      'cooking_status' => array(
        'type' => 'TINYINT',
				'constraint'=>1,
				'null'=>true,
				'default'=>0
      ),
			'process_status' => array(
        'type' => 'TINYINT',
				'constraint'=>1,
				'null'=>true,
				'default'=>0
      ),
			'is_check' => array(
        'type' => 'TINYINT',
				'constraint'=>1,
				'null'=>true,
				'default'=>0
      )
    );
    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('order_package_menu',TRUE);
  }

  public function down(){
    $this->dbforge->drop_table('order_package_menu');
  }
}
