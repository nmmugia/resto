<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_category_to_inventory extends CI_Migration {

	public function __construct() {
		$this->load->dbforge();
	}

	public function up() {
		$fields = array(
			'category_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'NULL' => TRUE
			)
		);
		$this->dbforge->add_column('inventory', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('inventory', 'category_id');
	}

}