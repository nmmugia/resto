<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_due_date_module extends CI_Migration{

	public function __construct(){
		$this->load->dbforge();
	}
    public function up(){
		$fields = array(
			'due_date' => array(
				'type' => 'DATE',
				'NULL'=>true
			),
			'reminder' => array(
				'type' => 'Integer',
				'NULL'=>true,
				'default'=>0
			)
		);
		$this->dbforge->add_column("module", $fields);
	}

	public function down(){
		$this->dbforge->drop_column('module', 'due_date');
		$this->dbforge->drop_column('module', 'reminder');
	}
}
