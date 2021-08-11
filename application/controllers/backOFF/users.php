<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/15/2014
 * Time: 10:38 AM
 */
class Users extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }
}