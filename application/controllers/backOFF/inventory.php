<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Inventory extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model');
        $this->load->model('inventory_model');

    }
    public function compositions($id="")
    {
      $id = $this->uri->segment(4);
      if (empty($id))redirect(SITE_ADMIN . '/inventory');
      $this->load->model('inventory_model');
      $form_data = $this->inventory_model->get_by('inventory', $id, 'id');
      if (empty($form_data))redirect(SITE_ADMIN . '/inventory');
      $this->data['form_data'] = $form_data;
      $this->data['subtitle']  = "Turunan Inventory";
      if (isset($_POST) && ! empty($_POST)) {
        $data=$this->input->post();
        $this->inventory_model->delete_by_limit("inventory_compositions",array("parent_inventory_id"=>$data['parent_inventory_id']),0);
        if(isset($data['detail']['inventory_id'])){
          for($x=0;$x<sizeof($data['detail']['inventory_id']);$x++){
            $this->inventory_model->save("inventory_compositions",array(
              "parent_inventory_id"=>$data['parent_inventory_id'],
              "inventory_id"=>$data['detail']['inventory_id'][$x],
              "uom_id"=>$data['detail']['uom_id'][$x],
              "quantity"=>$data['detail']['quantity'][$x],
            ));
          }
        }
        $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
        $btnaction = $this->input->post('btnAction');
        if ($btnaction == 'save_exit') {
          redirect(SITE_ADMIN . '/inventory', 'refresh');
        }else {
          redirect(SITE_ADMIN . '/inventory/compositions/' . $id, 'refresh');
        }
      }
      $this->data['inventory_compositions']=$this->inventory_model->get_inventory_composition(array("parent_inventory_id"=>$id));
      $uoms=array();
      foreach($this->data['inventory_compositions'] as $i){
        $inventory_uoms=$this->inventory_model->get_inventory_uoms($i->inventory_id);
        $uoms[$i->inventory_id]=$inventory_uoms;
      }
      $this->data['inventory_lists']=$this->inventory_model->get_all_where("inventory",array("id !="=>$id));
      $this->data['uoms'] = $uoms;
      $this->data['csrf'] = $this->_get_csrf_nonce();
      $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
      $this->data['message_success'] = $this->session->flashdata('message_success');
      $this->data['cancel_url']      = base_url(SITE_ADMIN . '/inventory');
      $this->data['content'] .= $this->load->view('admin/inventory-composition', $this->data, true);
      $this->render('admin');
    }
    function add_inventory_composition()
    {
      $inventory_id=$this->input->post("inventory_id");
      $inventory_lists=$this->inventory_model->get_all_where("inventory",array("id !="=>$inventory_id));
      $content='
        <tr>
          <td>
            <select class="form-control select2 detail_inventory_id" name="detail[inventory_id][]">
              <option value="">Pilih Inventory</option>';
              foreach($inventory_lists as $i){
                $content.='<option value="'.$i->id.'">'.$i->name.'</option>';
              }
      $content.='</select>
          </td>
          <td>
            <select class="form-control select2 detail_uom_id" name="detail[uom_id][]">
              <option value="">Pilih Satuan</option>
            </select>
          </td>
          <td>
            <input type="text" class="form-control only_number" name="detail[quantity][]">
          </td>
          <td>
            <a href="javascript:void(0);" class="btn btn-danger remove_inventory_composition">Hapus</a>
          </td>
        </tr>
      ';
      echo json_encode(array(
        "content"=>$content
      ));
    }
    function get_inventory_uoms()
    {
      $inventory_id=$this->input->post("inventory_id");
      $inventory_uoms=$this->inventory_model->get_inventory_uoms($inventory_id);
      $content='<option value="">Pilih Satuan</option>';
      foreach($inventory_uoms as $i){
        $content.='<option value="'.$i->uom_id.'">'.$i->code.'</option>';
      }
      echo json_encode(array(
        "content"=>$content
      ));
    }
    public function index()
    {
        $this->data['title']    = "Daftar Inventaris";
        $this->data['subtitle'] = "Daftar Inventaris";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['data_url'] = base_url(SITE_ADMIN . '/inventory/get_inventory_data');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/inventory-list', $this->data, true);
        $this->render('admin');
    }
    public function get_inventory_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->datatables->select('id,name,price,unit,minimal_stock')->from('inventory')
        ->where('is_active', 1)
        ->order_by("name","asc")
        ->add_column('actions', "<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/inventory/compositions/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Turunan</a>
                                </div>", 'id');
        echo $this->datatables->generate();
    }

    public function stock()
    {

        $this->data['title']    = "Kelola Stok";
        $this->data['subtitle'] = "Kelola Stok";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/inventory/add_stock');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/inventory/get_stock_inventory_data');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/inventory-stock-list', $this->data, true);
        $this->render('admin');
    }

    
    public function get_stock_inventory_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('inventory_history.id,outlet_name,inventory.name,
                                  minimal_stock,stock,unit, inventory_history.date')
        ->from('inventory_history')
        ->join('inventory', 'inventory.id = inventory_history.inventory_id')
        ->join('outlet', 'outlet.id = inventory_history.outlet_id ');
        // ->add_column('actions', "<div class='btn-group'>
        //                             <a href='" . base_url(SITE_ADMIN . '/inventory/edit_stock/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i> Edit</a>
        //                             <a href='" . base_url(SITE_ADMIN . '/inventory/delete_stock/$1') . "' class='btn btn-danger deleteNow' rel='stok'><i class='fa fa-trash-o'></i> Hapus</a>
        //                         </div>", 'id');
        echo $this->datatables->generate();
    }

    public function add_stock()
    {
        $this->data['title']    = "Tambah Stok";
        $this->data['subtitle'] = "Tambah Stok";

        //validate form input
        $this->form_validation->set_rules('input_outlet_id', 'Outlet', 'required');
        $this->form_validation->set_rules('input_inventory_id', 'Bahan', 'required');
        $this->form_validation->set_rules('input_stock', 'Jumlah stok', 'required|numeric');
        $this->form_validation->set_rules('input_created_date', 'Tanggal', 'required');

        if ($this->form_validation->run() == true) {

          $outlet_id = $this->input->post('input_outlet_id');
          $inventory_id = $this->input->post('input_inventory_id');
          $stock =$this->input->post('input_stock');
          $note  = $this->input->post('input_note');
          $created_date    = $this->input->post('input_created_date');

          $data_array = array(
            'outlet_id' => $outlet_id,
            'inventory_id' => $inventory_id,
            'stock' => $stock,
            'notes' => $note,
            'date' => $created_date);

          $save = $this->inventory_model->save('inventory_history', $data_array);

            if ($save === false) {

                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            }
            else {
                // $this->inventory_model->save_stock_history($save,$this->data['user_profile_data']);
            
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');                  
            }         
          

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/inventory/stock', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/inventory/add_stock/', 'refresh');
            }


        }
        else {
           
            $this->data['ddl_outlet']    = $this->categories_model->get_outlet();
            $this->data['ddl_inventory']    = $this->inventory_model->get_inventory(); 

            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['input_stock']   = array('name' => 'input_stock',
                                                 'id' => 'input_stock',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField NumericOnly',
                                                 'field-name' => 'Jumlah stok',
                                                 'placeholder' => 'Masukan stok',
                                                 'value' => $this->form_validation->set_value('input_stock'));
            $this->data['input_created_date']   = array('name' => 'input_created_date',
                                                 'id' => 'input_created_date',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField datepicker',
                                                 'field-name' => 'Tanggal',
                                                 'placeholder' => 'Masukan tanggal',
                                                 'readonly'=>'true',
                                                 'style' => 'width:80%;float:left;',
                                                 'value' => $this->form_validation->set_value('input_created_date'));
            $this->data['input_note']   = array('name' => 'input_note',
                                                 'id' => 'input_note',
                                                 'type' => 'text',
                                                 'class' => 'form-control',
                                                 'field-name' => 'Catatan',
                                                 'placeholder' => 'Masukan catatan',
                                                 'value' => $this->form_validation->set_value('input_note'));

            //load content
            $this->data['content'] .= $this->load->view('admin/inventory-stock-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit_stock()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/inventory');
        }
        $this->load->model('inventory_model');
        $form_data = $this->inventory_model->get_one('inventory_history', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/inventory/stock');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Stok";

        //validate form input
        $this->form_validation->set_rules('input_outlet_id', 'Outlet', 'required');
        $this->form_validation->set_rules('input_inventory_id', 'Bahan', 'required');
        $this->form_validation->set_rules('input_created_date', 'Tanggal', 'required');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $outlet_id = $this->input->post('input_outlet_id');
                $inventory_id = $this->input->post('input_inventory_id');
                $note  = $this->input->post('input_note');
                $created_date    = $this->input->post('input_created_date');

                $data_array = array(
                    'outlet_id' => $outlet_id,
                    'inventory_id' => $inventory_id,
                    'notes' => $note,
                    'date' => $created_date);

                $save = $this->categories_model->save_by('inventory_history', $data_array,  $id,'id');

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                }
                else {
                  // $this->inventory_model->save_stock_history($save,$this->data['user_profile_data']);
                  $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
               

                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/inventory/stock', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/inventory/edit_stock/' . $id, 'refresh');
                }


            }
        }
        $this->data['ddl_outlet']    = $this->categories_model->get_outlet();
        $this->data['ddl_inventory']    = $this->inventory_model->get_inventory(); 

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url']      = base_url(SITE_ADMIN . '/inventory/stock');
        
        $this->data['input_stock']   = array('name' => 'input_stock',
                                                 'id' => 'input_stock',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField NumericOnly',
                                                 'field-name' => 'Jumlah stok',
                                                 'placeholder' => 'Masukan stok',
                                                 'readonly' => 'true',
                                                 'value' => $this->form_validation->set_value('input_stock', $form_data->stock));
        $this->data['input_created_date']   = array('name' => 'input_created_date',
                                                 'id' => 'input_created_date',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField datepicker',
                                                 'field-name' => 'Tanggal',
                                                 'placeholder' => 'Masukan tanggal',
                                                 'readonly'=>'true',
                                                 'style' => 'width:80%;float:left;',
                                                 'value' => $this->form_validation->set_value('input_created_date', $form_data->date));
        $this->data['input_note']   = array('name' => 'input_note',
                                                 'id' => 'input_note',
                                                 'type' => 'text',
                                                 'class' => 'form-control',
                                                 'field-name' => 'Catatan',
                                                 'placeholder' => 'Masukan catatan',
                                                 'value' => $this->form_validation->set_value('input_note', $form_data->notes));

        $this->data['content'] .= $this->load->view('admin/inventory-stock-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_stock()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/inventory/stock');
        }

        $form_data = $this->inventory_model->get_one('inventory_history', $id);

        if (empty($form_data)) {
          redirect(SITE_ADMIN . '/inventory/stock');
        }

        $result = $this->inventory_model->delete('inventory_history', $id);
        if ($result) {
          
          $this->session->set_flashdata('message_success', 'Berhasil menghapus data');

        }
        else {
          $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }


        redirect(SITE_ADMIN . '/inventory/stock', 'refresh');
    }



    public function history()
    {

        $this->load->model('store_model');

        $this->data['title']    = "Riwayat Stok";
        $this->data['subtitle'] = "Riwayat Stok";
        $this->data['all_store']  = $this->store_model->get_all_store();
        $this->data['all_outlet'] = $this->store_model->get_all_outlet();

        $this->data['content'] .= $this->load->view('admin/inventory-history-list', $this->data, true);
        $this->render('report');
    }

    public function get_history_data()
    {
       $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $month      = $this->input->post('month');
        $year       = $this->input->post('year');
        $store      = $this->input->post('store');
        $outlet     = $this->input->post('outlet');

        $this->load->model('order_model');
        $this->load->model('store_model');

        $ret_data            = array();
        $ret_data['data']    = null;
        $ret_data['status']  = false;
        $ret_data['message'] = "";
        $grouped_history = array();

        $data_history = '';
        if (empty($start_date) && empty($end_date) && empty($month) && empty($year)  && empty($outlet)) {
            $ret_data['message'] = "Harap isi filter tanggal/bulan/tahun";
          }
          else {

              if (!empty($month) || ! empty($year)) {
                if (! empty($month)) {
                    //monthly
                    $date_format_group = "Y-m-d";
                    if (empty($year)) {
                        $year       = date("Y");
                        $data_history = $this->inventory_model->get_history_by_date_range("2013-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59", $outlet);
                    }
                    else
                        $data_history = $this->inventory_model->get_history_by_date_range($year . "-" . $month . "-01 00:00:00", $year . "-" . $month . "-31 23:59:59", $outlet);
                }
                else {
                    //yearly
                    $date_format_group = "F";
                    $data_history        = $this->inventory_model->get_history_by_date_range($year . "-01-01 00:00:00", $year . "-12-31 23:59:59", $outlet);

                }
            }
            else if (! empty($start_date) || ! empty($end_date)) {
                if ($end_date > $start_date) {
                    //get berdasarkan start & end date
                    $data_history = $this->inventory_model->get_history_by_date_range($start_date, $end_date, $outlet);
                }
                else if ($end_date == $start_date) {
                    //get berdasarkan start & end date
                    $data_history = $this->inventory_model->get_history_by_date_range($start_date, $end_date, $outlet);
                }
                else {
                    $ret_data['message'] = "Tanggal Akhir harus lebih besar dari Tanggal Mulai";
                    $data_history          = '';
                }
            }else{
                $ret_data['message'] = "Harap isi filter tanggal/bulan/tahun";

              }
            
              $ret_data['data'] = $data_history;
              $ret_data['status']  = true;
              $ret_data['message'] = "success";
          }



        echo json_encode($ret_data);
      
        
    }


    public function stock_opname()
    {

        $this->data['title']    = "Stok Opname";
        $this->data['subtitle'] = "Stok Opname";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/inventory/add_stock_opname');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/inventory/get_stock_opname_data');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/inventory-stock-opname-list', $this->data, true);
        $this->render('admin');
    }

    
    public function get_stock_opname_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('inventory_opname.id,outlet_name,inventory.name,
                                  minimal_stock,stock,unit, inventory_opname.date')
        ->from('inventory_opname')
        ->join('inventory', 'inventory.id = inventory_opname.inventory_id')
        ->join('outlet', 'outlet.id = inventory_opname.outlet_id ');
        echo $this->datatables->generate();
    }

    public function add_stock_opname()
    {
        $this->data['title']    = "Tambah Stok Opname";
        $this->data['subtitle'] = "Tambah Stok Opname";

        //validate form input
        $this->form_validation->set_rules('input_outlet_id', 'Outlet', 'required');
        $this->form_validation->set_rules('input_inventory_id', 'Bahan', 'required');
        $this->form_validation->set_rules('input_stock', 'Jumlah stok', 'required|numeric');
        $this->form_validation->set_rules('input_created_date', 'Tanggal', 'required');

        if ($this->form_validation->run() == true) {

          $outlet_id = $this->input->post('input_outlet_id');
          $inventory_id = $this->input->post('input_inventory_id');
          $stock =$this->input->post('input_stock');
          $note  = $this->input->post('input_note');
          $created_date    = $this->input->post('input_created_date');

          $data_array = array(
            'outlet_id' => $outlet_id,
            'inventory_id' => $inventory_id,
            'stock' => $stock,
            'date' => $created_date);

          $save = $this->inventory_model->save('inventory_opname', $data_array);

            if ($save === false) {

                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            }
            else {
             $data_array = array(
              'outlet_id' => $outlet_id,
              'inventory_id' => $inventory_id,
              'stock' => $stock,
              'is_opname' => 1,
              'date' => $created_date);

             $save = $this->inventory_model->save('inventory_history', $data_array);

             $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');                  
            }         
          

            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/inventory/stock_opname', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/inventory/add_stock_opname/', 'refresh');
            }


        }
        else {
           
            $this->data['ddl_outlet']    = $this->categories_model->get_outlet();
            $this->data['ddl_inventory']    = $this->inventory_model->get_inventory(); 

            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['input_stock']   = array('name' => 'input_stock',
                                                 'id' => 'input_stock',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField NumericOnly',
                                                 'field-name' => 'Jumlah stok',
                                                 'placeholder' => 'Masukan stok',
                                                 'value' => $this->form_validation->set_value('input_stock'));
            $this->data['input_created_date']   = array('name' => 'input_created_date',
                                                 'id' => 'input_created_date',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField datepicker',
                                                 'field-name' => 'Tanggal',
                                                 'placeholder' => 'Masukan tanggal',
                                                 'readonly'=>'true',
                                                 'style' => 'width:80%;float:left;',
                                                 'value' => $this->form_validation->set_value('input_created_date'));
            $this->data['input_note']   = array('name' => 'input_note',
                                                 'id' => 'input_note',
                                                 'type' => 'text',
                                                 'class' => 'form-control',
                                                 'field-name' => 'Catatan',
                                                 'placeholder' => 'Masukan catatan',
                                                 'value' => $this->form_validation->set_value('input_note'));

            //load content
            $this->data['content'] .= $this->load->view('admin/inventory-stock-opname-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function edit_stock_opname()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/inventory/stock_opname');
        }
        $this->load->model('inventory_model');
        $form_data = $this->inventory_model->get_one('inventory_opname', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/inventory/stock_opname');
        }

        $this->data['form_data'] = $form_data;
        $this->data['subtitle']  = "Edit Stok";

        //validate form input
        $this->form_validation->set_rules('input_outlet_id', 'Outlet', 'required');
        $this->form_validation->set_rules('input_inventory_id', 'Bahan', 'required');
        $this->form_validation->set_rules('input_created_date', 'Tanggal', 'required');
        $this->form_validation->set_rules('input_stock', 'Jumlah stok', 'required|numeric');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $outlet_id = $this->input->post('input_outlet_id');
                $inventory_id = $this->input->post('input_inventory_id');
                $note  = $this->input->post('input_note');
                $created_date    = $this->input->post('input_created_date');
                $stock = $this->input->post('input_stock');
                $data_array = array(
                    'outlet_id' => $outlet_id,
                    'inventory_id' => $inventory_id,
                    'stock'=> $stock,
                    // 'notes' => $note,
                    'date' => $created_date);

                $save = $this->categories_model->save_by('inventory_opname', $data_array,  $id,'id');

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                }
                else {
                  // $this->inventory_model->save_stock_history($save,$this->data['user_profile_data']);
                  $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
               

                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/inventory/stock_opname', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/inventory/edit_stock_opname/' . $id, 'refresh');
                }


            }
        }
        $this->data['ddl_outlet']    = $this->categories_model->get_outlet();
        $this->data['ddl_inventory']    = $this->inventory_model->get_inventory(); 

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url']      = base_url(SITE_ADMIN . '/inventory/stock_opname');
        
        $this->data['input_stock']   = array('name' => 'input_stock',
                                                 'id' => 'input_stock',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField NumericOnly',
                                                 'field-name' => 'Jumlah stok',
                                                 'placeholder' => 'Masukan stok',
                                                 'value' => $this->form_validation->set_value('input_stock', $form_data->stock));
        $this->data['input_created_date']   = array('name' => 'input_created_date',
                                                 'id' => 'input_created_date',
                                                 'type' => 'text',
                                                 'class' => 'form-control requiredTextField datepicker',
                                                 'field-name' => 'Tanggal',
                                                 'placeholder' => 'Masukan tanggal',
                                                 'readonly'=>'true',
                                                 'style' => 'width:80%;float:left;',
                                                 'value' => $this->form_validation->set_value('input_created_date', $form_data->date));
        // $this->data['input_note']   = array('name' => 'input_note',
        //                                          'id' => 'input_note',
        //                                          'type' => 'text',
        //                                          'class' => 'form-control',
        //                                          'field-name' => 'Catatan',
        //                                          'placeholder' => 'Masukan catatan',
        //                                          'value' => $this->form_validation->set_value('input_note', $form_data->notes));

        $this->data['content'] .= $this->load->view('admin/inventory-stock-opname-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_stock_opname()
    {
        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/inventory/stock_opname');
        }

        $form_data = $this->inventory_model->get_one('inventory_opname', $id);

        if (empty($form_data)) {
          redirect(SITE_ADMIN . '/inventory/stock_opname');
        }

        $result = $this->inventory_model->delete('inventory_opname', $id);
        if ($result) {
          
          $this->session->set_flashdata('message_success', 'Berhasil menghapus data');

        }
        else {
          $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }


        redirect(SITE_ADMIN . '/inventory/stock_opname', 'refresh');
    }



    function inv(){
      $this->load->model('inventory_model');
      $all_outlet = $this->inventory_model->get('outlet')->result();
      foreach ($all_outlet as $key => $single_outlet) {
        $single_outlet->inventory_history = $this->inventory_model->get_sum_inventory('inventory_history', $single_outlet->id) ;
        $single_outlet->inventory_opname =$this->inventory_model->get_sum_inventory('inventory_opname', $single_outlet->id) ;
        $single_outlet->inventory_stock =$this->inventory_model->get_sum_inventory('inventory_stock', $single_outlet->id) ;
      }
      $data = json_encode($all_outlet);
      $data_insert = array(
        'report' => $data,
        'date' => date('Y-m-d') );
      // $this->inventory_model->save('inventory_report',$data_insert );
      echo "<pre>";
      var_dump(json_decode($data));
    }
    public function outlet_stock()
    {

        $this->data['title']    = "Outlet Stock";
        $this->data['subtitle'] = "Outlet Stock";
     
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        //$this->data['data_url'] = base_url(SITE_ADMIN . '/inventory/get_outlet_stock');;
        //load content
        $this->data['content'] .= $this->load->view('admin/outlet-stock-list', $this->data, true);
        $this->render('admin');
    }
    public function get_data_outlet_stock()
    {

        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

         $this->load->model('store_model');
        
        $user_data = $this->ion_auth->user()->row();
        $data_store = $this->store_model->get_store($user_data->store_id);
        

      $this->datatables->select('outlet.id, outlet_name, store_name, value')
                        ->from('outlet')
                        ->join('store', 'store.id = outlet.store_id')
                        ->join('enum_outlet_category eo', 'eo.id = outlet.is_warehouse')
                        ->where('store.id', $data_store[0]->id)
                        ->add_column('actions', "<div class='btn-group'>
                                <a href='" . base_url(SITE_ADMIN . '/inventory/list_stock/$1') . "'  class='btn btn-default'> Stok</a>
                                <a href='" . base_url(SITE_ADMIN . '/inventory/transfer/$1') . "' class='btn btn-default'  >  Transfer Stok</a>
                              <a href='" . base_url(SITE_ADMIN . '/inventory/pembelian/$1') . "' class='btn btn-default' > Pembelian</a>
                            </div>", 'id');
        
        echo $this->datatables->generate();
    }
     public function transfer()
    {
        $this->load->model('categories_model');
        $this->load->model('inventory_model');
         $this->load->model('store_model');
        $this->data['title']    = "Transfer Stock";
        $this->data['subtitle'] = "Transfer Stock";
     
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        
        $user_data = $this->ion_auth->user()->row();
        $data_store = $this->store_model->get_store($user_data->store_id);
        $this->data['data_store']    = $data_store[0];

        $data_outlet= $this->store_model->get_outlet_by_store_id($data_store[0]->id);
        $this->data['outlets'] = $data_outlet;
         $outlet_id = $this->uri->segment(4);
        $this->data['transfers'] = array('name' => 'transfers',
                                           'id' => 'transfers',
                                           'type' => 'hidden',
                                           'class' => 'form-control requiredTextField',
                                           'field-name' => 'transfers'
                                           );
        $data_stocks                     = $this->inventory_model->get_all_stock_by_outlet_id($outlet_id);
        $this->data['data_stocks']    = $data_stocks;

        $this->data['content'] .= $this->load->view('admin/transfer-stock', $this->data, true);
        $this->render('admin');
    }
    public function transfer_add(){
      $this->load->model('inventory_model');
      $this->load->model('store_model');
      $user_data = $this->ion_auth->user()->row();
      $data_store = $this->store_model->get_store($user_data->store_id);
     
        
      $destination_store_id = $data_store[0]->id;
      $destination_outlet_id = $this->input->post("outlet_id");
      
      $transfers = $this->input->post("transfers");
      $data_items = json_decode($transfers,true);
     
      $transfer_remaining = 0;
      $transfered = 0;
      foreach ($data_items as $item) {
            $transfer_remaining = $item['product_amount'];
           
            $data_stocks = $this->inventory_model->get_stock_by_inventory_id($item['product_id']);
            foreach ($data_stocks as $stock) {
                $data_stock_history = array(
                  "store_id"=>$stock->store_id,
                  "outlet_id"=>$stock->outlet_id,
                  "quantity"=>$stock->quantity,
                  "inventory_id"=>$stock->inventory_id,
                  "price"=>$stock->price,
                  "status"=>2
                );
              if($transfer_remaining > 0){
                  
                  if($stock->quantity > $transfer_remaining){
                    $data_update_stock = array( "quantity" => $stock->quantity - $transfer_remaining);
                    //update origin
                    $this->inventory_model->update_stock_by_id($stock->id, $data_update_stock);
                    //history
                    $this->inventory_model->insert_stock_history($data_stock_history);                      

                    //insert destination
                    $data_destination_stock = array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "quantity"=>$transfer_remaining,
                      "inventory_id"=>$item['product_id'],
                      "price"=>$item['product_price'],
                      "modified_at"=>date("Y-m-d h:i:s"),
                      "purchase_date"=>date("Y-m-d h:i:s")
                    );
                    $this->inventory_model->insert_stock($data_destination_stock);      
                    $data_destination_stock['status'] = 5;
                    //history
                    unset($data_destination_stock['modified_at']);
                    unset($data_destination_stock['purchase_date']);
                    $this->inventory_model->insert_stock_history($data_destination_stock);


                    $transfer_remaining = 0;
                  }elseif($stock->quantity == $transfer_remaining){
                    //delete origin
                    $this->inventory_model->delete_stock_by_id(array("id" => $stock->id));
                    //history
                    $this->inventory_model->insert_stock_history($data_stock_history);                      
                    

                    //insert destination
                    $data_destination_stock = array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "quantity"=>$transfer_remaining,
                      "inventory_id"=>$item['product_id'],
                      "price"=>$item['product_price'],
                      "modified_at"=>date("Y-m-d h:i:s"),
                       "purchase_date"=>date("Y-m-d h:i:s")
                    );
                    $this->inventory_model->insert_stock($data_destination_stock);      
                    $data_destination_stock['status'] = 5;
                    //history
                    unset($data_destination_stock['modified_at']);
                    unset($data_destination_stock['purchase_date']);
                    $this->inventory_model->insert_stock_history($data_destination_stock);


                    $transfer_remaining = 0;
                  }else{
                    $transfered = $stock->quantity;
                    //delete stock
                    $this->inventory_model->delete_stock_by_id(array("id" => $stock->id));
                     //history
                    $this->inventory_model->insert_stock_history($data_stock_history);


                     //insert destination
                    $data_destination_stock = array(
                      "store_id"=>$destination_store_id,
                      "outlet_id"=>$destination_outlet_id,
                      "quantity"=>$transfered,
                      "inventory_id"=>$item['product_id'],
                      "price"=>$item['product_price'],
                      "modified_at"=>date("Y-m-d h:i:s"),
                       "purchase_date"=>date("Y-m-d h:i:s")
                    );
                    $this->inventory_model->insert_stock($data_destination_stock);      
                    $data_destination_stock['status'] = 5;
                    //history
                    unset($data_destination_stock['modified_at']);
                    unset($data_destination_stock['purchase_date']);
                    $this->inventory_model->insert_stock_history($data_destination_stock);
                   
                    $transfer_remaining -= $transfered;
                  }
              }else break;
            }  
      }


      $this->session->set_flashdata('message_success', "Stock Transfered");
      redirect(SITE_ADMIN . '/inventory/transfer', 'refresh');
    }
}