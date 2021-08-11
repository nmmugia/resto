<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_image_to_promo_discount extends CI_Migration {

	public function __construct() {
		$this->load->dbforge();
	}

	public function up() {
		$fields = array(
			'image' => array(
				'type'        => 'varchar',
		        'constraint'  => 255,
		        'null'        => true
			)
		);
		$this->dbforge->add_column('promo_discount', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promo_discount', 'image');
	}

}