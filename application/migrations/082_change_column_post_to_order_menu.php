<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_change_column_post_to_order_menu extends CI_Migration{

    public function __construct(){
        $this->load->dbforge();
    }
    public function up(){

        $fields = array(
            'post_to' => array(
                'name' => 'post_to',
                'type' => 'VARCHAR',
                'constraint' => 30
            ),
        );
        $this->dbforge->modify_column('order_menu', $fields);
    }

    public function down(){
        $fields = array(
            'post_to' => array(
                'name' => 'post_to',
                'type' => 'INT',
                'constraint' => 11
            ),
        );
        $this->dbforge->modify_column('order_menu', $fields);
    }
}
