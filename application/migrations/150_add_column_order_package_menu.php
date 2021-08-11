<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_order_package_menu extends CI_Migration {

	public function __construct() {
		$this->load->dbforge();
	}

	public function up() {
		$fields = array(
			'quantity_process' => array(
				'type' => 'int',
				'constraint' => 11,
				'NULL' => TRUE,
				'default' => 0
			),
		);
		$this->dbforge->add_column('order_package_menu', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('order_package_menu', 'quantity_process');
	}

}
