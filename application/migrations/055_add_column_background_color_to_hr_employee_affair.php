<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_column_background_color_to_hr_employee_affair extends CI_Migration{

  public function __construct(){
    $this->load->dbforge();
  }
    public function up(){
    $fields = array(
      'background_color' => array(
        'type' => 'VARCHAR',
				'constraint'=>50,
        'NULL'=>true,
      )
    );
    $this->dbforge->add_column("hr_enum_employee_affair",$fields);
  }

  public function down(){
    $this->dbforge->drop_column('hr_enum_employee_affair', 'background_color');
  }
}
