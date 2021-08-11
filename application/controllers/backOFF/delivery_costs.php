<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Delivery_Costs extends Admin_Controller
{
  public function index(){
    if(empty($this->data['setting']['store_id']))redirect('');
    $this->data['title']    = "Ongkos Kirim";
    $this->data['subtitle'] = "Ongkos Kirim";
    $this->data['add_url']  = base_url(SITE_ADMIN . '/delivery_costs/add');
    $this->data['data_url'] = base_url(SITE_ADMIN . '/delivery_costs/get_data');;
    $this->data['content'] .= $this->load->view('admin/delivery-cost-list', $this->data, true);
    $this->render('admin');
  }
  public function get_data()
  {
    $this->load->library(array('datatables'));
    $this->load->helper(array('datatables'));
    $this->datatables->select('id,delivery_cost_name,delivery_cost,is_percentage')->from('enum_delivery_cost')
    ->unset_column('delivery_cost')
    ->add_column('delivery_cost', '$1', 'check_is_percentage(delivery_cost, is_percentage)');
    echo $this->datatables->generate();
  }
}