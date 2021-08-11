<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_member extends CI_Migration {

	public function __construct() {
		$this->load->dbforge();
	}

	public function up() {
		$fields = array(
			'birth_date' => array(
				'type' => 'date',
				'NULL' => TRUE
			),
			'email' => array(
				'type' => 'varchar',
				'constraint' => 200,
				'NULL' => TRUE
			),
		);
		$this->dbforge->add_column('member', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('member', 'birth_date');
		$this->dbforge->drop_column('member', 'email');
	}

}