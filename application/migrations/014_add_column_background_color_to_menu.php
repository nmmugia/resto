<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_background_color_to_menu extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'menu_short_name' => array(
        'name' => 'menu_short_name',
        'type' => 'VARCHAR',
        'constraint' => 30
      ),
    );
    $this->dbforge->modify_column('menu', $fields);
    $fields = array(
      'background_color' => array(
        'type' => 'VARCHAR',
        'constraint'=>20,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("menu",$fields);
  }

  public function down(){
    $fields = array(
      'menu_short_name' => array(
        'name' => 'menu_short_name',
        'type' => 'VARCHAR',
        'constraint' => 14
      ),
    );
    $this->dbforge->modify_column('menu', $fields);
    $this->dbforge->drop_column('menu', 'background_color');
  }
}
