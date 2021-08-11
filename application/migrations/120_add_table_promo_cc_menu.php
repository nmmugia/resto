<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_table_promo_cc_menu extends CI_Migration{

  public function __construct() {
    $this->load->dbforge();
  }

  public function up() {
    $fields = array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => TRUE
      ),
      'promo_cc_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => FALSE
      ),
      'menu_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => FALSE
      ),
      'created_at' => array(
        'type' => 'datetime',
        'null'=> TRUE
      ),
      'created_by' => array(
        'type' => 'INT',
        'constraint' => 11,
        'NULL' => TRUE
      )
    );

    $this->dbforge->add_field($fields);
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('promo_cc_menu',TRUE);

  }

  public function down() {
    $this->dbforge->drop_table('promo_cc_menu');
  }
  
}
