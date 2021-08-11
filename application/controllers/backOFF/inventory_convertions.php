<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_Convertions extends Admin_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('inventory_model');
    $this->load->model('categories_model');
  }

  public function index()
  {
    $this->data['title']    = "Konversi Inventori";
    $this->data['subtitle'] = "Konversi Inventori";
    $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    $this->data['message_success'] = $this->session->flashdata('message_success');
    $this->data['add_url']  = base_url(SITE_ADMIN . '/inventory_convertions/add');
    $this->data['data_url'] = base_url(SITE_ADMIN . '/inventory_convertions/get_data');
		$this->data['store_lists']      = $this->categories_model->get_store();
		$this->data['outlets']     = $this->categories_model->get_outlet_dropdown2();
		$this->data['inventories']=$this->inventory_model->get_inventory_process_drop_down();
    $this->data['content'] .= $this->load->view('admin/inventory-convertion-list', $this->data, true);
    $this->render('admin');
  }
  public function get_data()
  {
    $this->load->library(array('datatables'));
    $this->load->helper(array('datatables'));
    $this->datatables->select('ic.inventory_id,i.name as inventory_name')
    ->from('inventory_convertion ic')
    ->join('inventory i', 'i.id = ic.inventory_id')
    ->join('uoms u','i.uom_id=u.id')
    ->join('uoms u2','ic.uom_id=u2.id')
    ->group_by("ic.inventory_id")
    ->add_column('actions', "<div class='btn-group'>
      <a href='javascript:void(0);' inventory_id='$1' class='btn btn-success process_inventory'>Proses</a>
      <a href='" . base_url(SITE_ADMIN . '/inventory_process/index/$1') . "' target='_blank' class='btn btn-info history_process_inventory'>History</a>
    </div>", 'inventory_id');
    echo $this->datatables->generate();
  }
  function add_detail_convertion()
  {
    $inventory_id=$this->input->post("inventory_id");
    $this->load->model("inventory_model");
    $inventory_uoms=$this->inventory_model->get_inventory_uoms($inventory_id);
    $inventory=$this->inventory_model->get_one("inventory",$inventory_id);
    $uom=$this->inventory_model->get_one("uoms",$inventory->uom_id);
    $html="";
    foreach($inventory_uoms as $i){
      $html.='
        <tr>
          <td>'.$i->code.'</td>
          <td>
            <input type="hidden" name="detail[UOM_ID][]" value="'.$i->uom_id.'">
            <input type="text" name="detail[CONVERTION][]" class="form-control" '.($inventory->uom_id==$i->uom_id ? "value='1' readonly=''" : "").'>
          </td>
          <td>'.$uom->code.'</td>
        </tr>
      ';
    }
    echo json_encode(array(
      "content"=>$html
    ));
  }
  public function add()
  {
    $this->data['title']    = "Tambah Konversi";
    $this->data['subtitle'] = "Tambah Konversi";
    $this->form_validation->set_rules('store_id', 'Resto', 'required');
    $this->form_validation->set_rules('inventory_id', 'Inventori', 'required');
    $this->load->model("inventory_model");
    if ($this->form_validation->run() == true) {

      $data = $this->input->post();
      $save=false;
      for($x=0;$x<sizeof($data['detail']['UOM_ID']);$x++){
        if($data['detail']['CONVERTION'][$x]>0){
          $this->inventory_model->save('inventory_convertion',array(
            'store_id' => $data['store_id'],
            'inventory_id' => $data['inventory_id'],
            'uom_id' => $data['detail']['UOM_ID'][$x],
            'convertion' => $data['detail']['CONVERTION'][$x],
          ));
          $save=true;
        }
      }

      if ($save === false) {
        $this->session->set_flashdata('message', 'Gagal menyimpan data');
      }else {
        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');                  
      }         
      $btnaction = $this->input->post('btnAction');
      if ($btnaction == 'save_exit') {
        redirect(SITE_ADMIN . '/inventory_convertions', 'refresh');
      }
      else {
        redirect(SITE_ADMIN . '/inventory_convertions/add/', 'refresh');
      }
    }
    else {
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['store_lists']=$this->store_model->get_store_dropdown();
      unset($this->data['store_lists'][0]);
      $this->data['inventories']=$this->inventory_model->get_inventory_convertion_drop_down();
      $this->data['content'] .= $this->load->view('admin/inventory-convertion-add', $this->data, true);
      $this->render('admin');
    }
  }
  public function edit()
  {
      $id = $this->uri->segment(4);
      if (empty($id))redirect(SITE_ADMIN . '/inventory_convertions');
      $detail = $this->inventory_model->get_inventory_convertions($id);
      if (empty($detail))redirect(SITE_ADMIN . '/inventory_convertions');
      $this->data['form_data'] = $detail[0];
      $this->data['detail'] = $detail;
      $this->data['subtitle']  = "Edit Konversi";
      $this->form_validation->set_rules('store_id', 'Resto', 'required');
      $this->form_validation->set_rules('inventory_id', 'Inventori', 'required');
      if (isset($_POST) && ! empty($_POST)) {
        if ($this->form_validation->run() === TRUE) {
          $data = $this->input->post();
          $this->inventory_model->delete_by_limit("inventory_convertion",array("inventory_id"=>$data['inventory_id']),0);
          $save=false;
          for($x=0;$x<sizeof($data['detail']['UOM_ID']);$x++){
            if($data['detail']['CONVERTION'][$x]>0){
              $this->inventory_model->save('inventory_convertion',array(
                'store_id' => $data['store_id'],
                'inventory_id' => $data['inventory_id'],
                'uom_id' => $data['detail']['UOM_ID'][$x],
                'convertion' => $data['detail']['CONVERTION'][$x],
              ));
              $save=true;
            }
          }
          if ($save === false) {
            $this->session->set_flashdata('message', 'Gagal menyimpan data');
          }else {
            $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
          }
          $btnaction = $this->input->post('btnAction');
          if ($btnaction == 'save_exit') {
            redirect(SITE_ADMIN . '/inventory_convertions', 'refresh');
          }else {
            redirect(SITE_ADMIN . '/inventory_convertions/edit/' . $id, 'refresh');
          }
        }
      }
      $this->data['csrf'] = $this->_get_csrf_nonce();
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['cancel_url']      = base_url(SITE_ADMIN . '/inventory_convertions');        
    $this->data['store_lists']=$this->store_model->get_store_dropdown();
    unset($this->data['store_lists'][0]);
    $this->data['inventories']=$this->inventory_model->get_inventory_convertion_drop_down();
    $this->data['content'] .= $this->load->view('admin/inventory-convertion-edit', $this->data, true);
    $this->render('admin');
  }

  public function delete()
  {
    $id = $this->uri->segment(4);
    if (empty($id))redirect(SITE_ADMIN . '/inventory_convertions');
    $form_data = $this->inventory_model->get_by('inventory_convertion', $id,"inventory_id");
    if (empty($form_data))redirect(SITE_ADMIN . '/inventory_convertions');
    $result = $this->inventory_model->delete_by_limit('inventory_convertion', array("inventory_id"=>$id),0);
    if ($result) {
      $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
    }else {
      $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
    }
    redirect(SITE_ADMIN . '/inventory_convertions', 'refresh');
  }
}