<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Module_model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'module';

    function __construct()
    {
        parent::__construct();
    }

}
