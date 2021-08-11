<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Order_company extends Admin_Controller
{

    public function index(){


        $this->data['title']    = "Order Perusahaan";
        $this->data['subtitle'] = "Order Perusahaan";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/order_company/add');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/order_company/get_data');;
        //load content
        $this->data['content'] .= $this->load->view('admin/company/company-list', $this->data, true);
        $this->render('admin');
    }

    public function get_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('*,is_use_banquet,order_status,order_company.id')
        ->from('order_company')
        ->join('store', 'store.id = order_company.store_id')
        ->unset_column('is_use_banquet')
        ->add_column('is_use_banquet', '$1', 'set_banquet(is_use_banquet)')
        ->add_column('actions', '$1','generate_link_order(order_status,id)' )

       ;
       
        echo $this->datatables->generate();
    }

 
    public function add()
    {

        $this->data['title']    = "Order Perusahaan";
        $this->data['subtitle'] = "Order Perusahaan";

        $this->load->model('store_model');
        $this->load->model('order_model');
        $this->load->model('member_category_model');

        //validate form input
        $this->form_validation->set_rules('company_name', 'Nama Perusahaan', 'required');
        $this->form_validation->set_rules('pic_name', 'Penanggung Jawab ', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('address', 'Alamat', 'required');
        $this->form_validation->set_rules('mobile_phone', 'nomor handphone', 'required');
        $this->form_validation->set_rules('land_phone', 'nomor telepon', 'required');
        $this->form_validation->set_rules('no_rec', 'Nomor Rekening', 'required');
        $this->form_validation->set_rules('beneficary', 'Atas Nama Rekening', 'required');

        if ($this->form_validation->run() == true) {
            $max_id = $this->order_model->get_max_order_company();
            $order_id = str_pad($max_id + 1, 6, '0', STR_PAD_LEFT);

            $created_at = date("Y-m-d H:i:s") ;
            $created_by =  $this->data['user_profile_data']->id;

            $data_array = array('company_name' => $this->input->post('company_name'),
                                'pic_name' => $this->input->post('pic_name'),
                                'order_id' => $order_id,
                                'email' => $this->input->post('email'),
                                'address' => $this->input->post('address'),
                                'down_payment' => $this->input->post('down_payment'),
                                'land_phone' => $this->input->post('land_phone'),
                                'mobile_phone' => $this->input->post('mobile_phone'),
                                'beneficary' => $this->input->post('beneficary'),
                                'no_rec' => $this->input->post('no_rec'),
                                'created_at' => $created_at,
                                'created_by' => $created_by,
                                'store_id' => $this->input->post('store_id'),
                                'is_use_banquet' => $this->input->post('is_use_banquet'),
                                );

            $save = $this->member_category_model->save('order_company', $data_array);
         
            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/order_company', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/order_company/add/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['company_name'] = array('name' => 'company_name',
                                                'id' => 'company_name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Nama Perusahaan',
                                                'placeholder' => 'Masukan Nama Perusahaan',
                                                'value' => $this->form_validation->set_value('company_name'));
            $this->data['pic_name'] = array('name' => 'pic_name',
                                                'id' => 'pic_name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Nama Penanggung Jawab',
                                                'placeholder' => 'Masukan Nama Penanggung Jawab',
                                                'value' => $this->form_validation->set_value('pic_name'));

            $this->data['email'] = array('name' => 'email',
                                                'id' => 'email',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'Email',
                                                'placeholder' => 'Masukan Email',
                                                'value' => $this->form_validation->set_value('email'));

         
            $this->data['address'] = array('name' => 'address',
                                                'id' => 'address',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'alamat',
                                                'placeholder' => 'Masukan alamat',
                                                'value' => $this->form_validation->set_value('address', $this->input->post('address')));

           

            $this->data['mobile_phone'] = array('name' => 'mobile_phone',
                                                'id' => 'mobile_phone',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField NumericDecimal',
                                                'field-name' => 'nomor handphone',
                                                'placeholder' => 'Masukan no handphone',
                                                'value' => $this->form_validation->set_value('mobile_phone', $this->input->post('mobile_phone')));

        
            $this->data['land_phone'] = array('name' => 'land_phone',
                                                'id' => 'land_phone',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'nomor telepon',
                                                'placeholder' => 'Masukan nomor telepon',
                                                'value' => $this->form_validation->set_value('land_phone'));
          
            $this->data['down_payment'] = array('name' => 'down_payment',
                                                'id' => 'down_payment',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'Uang Muka',
                                                'placeholder' => 'Masukan Uang Muka',
                                                'value' => 0);


             $this->data['no_rec'] = array('name' => 'no_rec',
                                                'id' => 'no_rec',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'No Rekening',
                                                'placeholder' => 'Masukan No Rekening',
                                                'value' => $this->form_validation->set_value('no_rec'));
          
            $this->data['beneficary'] = array('name' => 'beneficary',
                                                'id' => 'beneficary',
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'field-name' => 'Atas Nama',
                                                'placeholder' => 'Masukan Atas Nama Rekening',
                                                'value' => $this->form_validation->set_value('beneficary'));
            $this->data['is_banquet'] = array('name' => 'is_banquet',
                                                'id' => 'is_banquet',
                                                'type' => 'checkbox',
                                                'class' => 'form-control',
                                                'field-name' => 'Banquet',
                                                'placeholder' => 'Pilih Banquet',
                                                'value' => $this->form_validation->set_value('is_banquet'));
          
            $this->data['store'] = $this->store_model->get_store_dropdown();
          

            $this->data['content'] .= $this->load->view('admin/company/company-add', $this->data, true);
            $this->render('admin');
        }

    }

  
    public function edit()
    {
        $id = $this->uri->segment(4);
        if (empty($id)) {
            redirect(SITE_ADMIN . '/member');
        }
        $this->load->model('member_model');

        $form_data = $this->member_model->get_one('order_company', $id);

        $this->load->model('store_model');
         

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/order_company');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title']  = "Edit Order Company";
        $this->data['subtitle']  = "Edit Order Company";

        $this->form_validation->set_rules('company_name', 'Nama Perusahaan', 'required');
        $this->form_validation->set_rules('pic_name', 'Penanggung Jawab ', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('address', 'Alamat', 'required');
        $this->form_validation->set_rules('mobile_phone', 'nomor handphone', 'required');
        $this->form_validation->set_rules('land_phone', 'nomor telepon', 'required');
        $this->form_validation->set_rules('no_rec', 'Nomor Rekening', 'required|exact_length[16]');
        $this->form_validation->set_rules('beneficary', 'Atas Nama Rekening', 'required');

         if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $created_at = date("Y-m-d H:i:s") ;
                $created_by =  $this->data['user_profile_data']->id;

                 $data_array = array(
                        'company_name' => $this->input->post('company_name'),
                        'pic_name' => $this->input->post('pic_name'),
                        'email' => $this->input->post('email'),
                        'address' => $this->input->post('address'),
                        'down_payment' => $this->input->post('down_payment'),
                        'land_phone' => $this->input->post('land_phone'),
                        'mobile_phone' => $this->input->post('mobile_phone'),
                        'beneficary' => $this->input->post('beneficary'),
                        'no_rec' => $this->input->post('no_rec'),
                        'created_at' => $created_at,
                        'created_by' => $created_by,
                        'is_use_banquet' => $this->input->post('is_use_banquet'),
                );
                $save = $this->member_model->save('order_company', $data_array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/order_company', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/order_company/edit/' . $id, 'refresh');
                } 
            }
        }


        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url']      = base_url(SITE_ADMIN . '/member/member');

        $this->data['company_name'] = array('name' => 'company_name',
                                            'id' => 'company_name',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField',
                                            'field-name' => 'Nama Perusahaan',
                                            'placeholder' => 'Masukan Nama Perusahaan',
                                            'value' => $this->form_validation->set_value('company_name',$form_data->company_name));
        $this->data['pic_name'] = array('name' => 'pic_name',
                                            'id' => 'pic_name',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField',
                                            'field-name' => 'Nama Penanggung Jawab',
                                            'placeholder' => 'Masukan Nama Penanggung Jawab',
                                            'value' => $this->form_validation->set_value('pic_name',$form_data->pic_name));

        $this->data['email'] = array('name' => 'email',
                                            'id' => 'email',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField',
                                            'field-name' => 'Email',
                                            'placeholder' => 'Masukan Email',
                                            'value' => $this->form_validation->set_value('email',$form_data->email));

     
        $this->data['address'] = array('name' => 'address',
                                            'id' => 'address',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField ',
                                            'field-name' => 'alamat',
                                            'placeholder' => 'Masukan alamat',
                                            'value' => $this->form_validation->set_value('address', $form_data->address));

       

        $this->data['mobile_phone'] = array('name' => 'mobile_phone',
                                            'id' => 'mobile_phone',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField NumericDecimal',
                                            'field-name' => 'nomor handphone',
                                            'placeholder' => 'Masukan no handphone',
                                            'value' => $this->form_validation->set_value('mobile_phone', $form_data->mobile_phone));

    
        $this->data['land_phone'] = array('name' => 'land_phone',
                                            'id' => 'land_phone',
                                            'type' => 'text',
                                            'class' => 'form-control requiredTextField ',
                                            'field-name' => 'nomor telepon',
                                            'placeholder' => 'Masukan nomor telepon',
                                            'value' => $this->form_validation->set_value('land_phone', $form_data->land_phone));
      
        $this->data['down_payment'] = array('name' => 'down_payment',
                                            'id' => 'down_payment',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Uang Muka',
                                            'placeholder' => 'Masukan Uang Muka',
                                            'value' => $this->form_validation->set_value('land_phone', $form_data->down_payment));


         $this->data['no_rec'] = array('name' => 'no_rec',
                                            'id' => 'no_rec',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'No Rekening',
                                            'placeholder' => 'Masukan No Rekening',
                                            'value' => $this->form_validation->set_value('no_rec',$form_data->no_rec));
      
        $this->data['beneficary'] = array('name' => 'beneficary',
                                            'id' => 'beneficary',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Atas Nama',
                                            'placeholder' => 'Masukan Atas Nama Rekening',
                                            'value' => $this->form_validation->set_value('beneficary',$form_data->beneficary));

        $this->data['is_use_banquet'] = $form_data->is_use_banquet;
        $this->data['store'] = $this->store_model->get_store_dropdown();
      
        
        $this->data['content'] .= $this->load->view('admin/company/company-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/order_company');
        }

        $this->load->model('member_model');
        $form_data = $this->member_model->get_one('order_company', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/order_company');
        }

        $result = $this->member_model->delete('order_company', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect(SITE_ADMIN . '/order_company', 'refresh');
    }
    public function order_done()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/order_company');
        }

        $this->load->model('member_model');
        $form_data = $this->member_model->get_one('order_company', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/order_company');
        }

       $data_array = array( 
                'order_status' => 1,
        );
        $result = $this->member_model->save('order_company', $data_array, $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil Proses Data');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Gagal Proses data');
        }

        redirect(SITE_ADMIN . '/order_company', 'refresh');
    }

    public function detail_order(){
        $id = $this->uri->segment(4);
        if (empty($id)) {
            redirect(SITE_ADMIN . '/member');
        }
        $this->load->model('member_model');

        $this->data['order_company'] = $this->member_model->get_one('order_company', $id);
        $this->data['content'] .= $this->load->view('admin/company/company-detail-order.php', $this->data, true);

        $this->render('admin');
    }

}