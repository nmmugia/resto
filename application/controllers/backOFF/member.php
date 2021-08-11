<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Member extends Admin_Controller
{

    public function index(){
        if(empty($this->data['setting']['store_id'])){
            redirect('');
        }
        $this->data['title']    = "Member";
        $this->data['subtitle'] = "Daftar Member";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/member/add');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/member/get_data');;
        //load content
        $this->data['content'] .= $this->load->view('admin/member-list', $this->data, true);
        $this->render('admin');
    }

    public function get_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('member.id, member_id, member_category_id, 
          member_category.name as category_name, member_category.discount, 
          member.name, discount, join_date,point, last_transaction_date,
          (
            select sum(b.total_price) as total_price
            from bill b
            where b.id in (
              select bill_id from bill_information where info=concat("Diskon Member(",member.member_id,")")
            ) 
          ) as total_spending
        ',false)
        ->from('member')
        ->join('member_category', 'member_category.id = member.member_category_id', 'left')
        
        // ->unset_column('total_spending')
        ->unset_column('discount')
        ->unset_column('join_date')
        ->unset_column('last_transaction_date')

        // ->add_column('total_spending', '$1', 'convert_rupiah(total_spending)')
        ->add_column('discount', '$1', 'convert_member_benefit(discount)')
        ->add_column('join_date', '$1', 'convert_date(join_date)')
        ->add_column('last_transaction_date', '$1', 'convert_date(last_transaction_date)')
        ->add_column('actions',"<div class='btn-group'>
                                    <a href='" . base_url(SITE_ADMIN . '/member/detail/$1') . "'  
                                    class='btn btn-default'
                                    rel='tooltip' data-tooltip='tooltip'  title='Detail'
                                    >
                                    <i class='fa fa-search'></i></a>,
                                    <a rel='tooltip' data-tooltip='tooltip'  title='Edit'
                                    href='" . base_url(SITE_ADMIN . '/member/edit_member/$1') . "'  class='btn btn-default'><i class='fa fa-pencil'></i></a>
                                    ",'id');
        echo $this->datatables->generate();
    }


    public function _id_check($id)
    {
        $member_id = $this->uri->segment(4);

        if (!empty($member_id)) {
            $member_id_old = $this->db->where("id", $member_id)->get('member')->row()->id;
            $this->db->where("id !=", $member_id_old);
        }

        $num_row = $this->db->where('member_id', $id)->get('member')->num_rows();
        // var_dump($member_id_old);die();
        if ($num_row > 0) {
            $this->form_validation->set_message('_id_check', 'ID member sudah terdaftar');
            return FALSE;
        } else {
            return TRUE;
        }
    }


    public function add()
    {

        $this->data['title']    = "Tambah Member";
        $this->data['subtitle'] = "Tambah Member";

        $this->load->model('store_model');
        $this->load->model('member_category_model');

        //validate form input
        $this->form_validation->set_rules('name', 'nama', 'required');
        $this->form_validation->set_rules('member_id', 'ID member', 'required|callback__id_check');
        $this->form_validation->set_rules('mobile_phone', 'nomor handphone', 'required|numeric');
        $this->form_validation->set_rules('land_phone', 'nomor telepon', 'required|numeric');

        if ($this->form_validation->run() == true) {

            $created_at = date("Y-m-d H:i:s") ;
            $created_by =  $this->data['user_profile_data']->id;

            $data_array = array('name' => $this->input->post('name'),
                                'member_id' => $this->input->post('member_id'),
                                'member_category_id' => $this->input->post('member_category_id'),
                                'address' => $this->input->post('address'),
                                'city_id' => $this->input->post('city_id'),
                                'province_id' => $this->input->post('province_id'),
                                'country_id' => $this->input->post('country_id'),
                                'postal_code' => $this->input->post('postal_code'),
                                'land_phone' => $this->input->post('land_phone'),
                                'mobile_phone' => $this->input->post('mobile_phone'),
                                'join_date' =>$this->input->post('join_date'),
                                'join_store_id' => $this->data['setting']['store_id'],
                                'created_at' => $created_at,
                                'created_by' => $created_by,
                               
                                );

            $save = $this->member_category_model->save('member', $data_array);
         
            if ($save === false) {
                $this->session->set_flashdata('message', 'Gagal menyimpan data');
            }
            else {
                $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
            }
            $btnaction = $this->input->post('btnAction');
            if ($btnaction == 'save_exit') {
                redirect(SITE_ADMIN . '/member', 'refresh');
            }
            else {
                redirect(SITE_ADMIN . '/member/add/', 'refresh');
            }


        }
        else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['message_success'] = $this->session->flashdata('message_success');

            $this->data['name'] = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'nama',
                                                'placeholder' => 'Masukan nama',
                                                'value' => $this->form_validation->set_value('name'));

            $this->data['member_id'] = array('name' => 'member_id',
                                                'id' => 'member_id',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'id member',
                                                'placeholder' => 'Masukan ID member',
                                                'maxlength' => 20,
                                                'value' => $this->form_validation->set_value('member_id'));

            $this->data['postal_code'] = array('name' => 'postal_code',
                                                'id' => 'postal_code',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'kode pos',
                                                'placeholder' => 'Masukan kode pos',
                                                'value' => $this->form_validation->set_value('postal_code', $this->input->post('postal_code')));

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

            $this->data['member_category_id'] = array('name' => 'member_category_id',
                                                'id' => 'member_category_id',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'member kategori',
                                                'placeholder' => 'Masukan value',
                                                'value' => $this->form_validation->set_value('member_category_id'));

            $this->data['land_phone'] = array('name' => 'land_phone',
                                                'id' => 'land_phone',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'nomor telepon',
                                                'placeholder' => 'Masukan nomor telepon',
                                                'value' => $this->form_validation->set_value('land_phone'));
            $this->data['join_date'] = array('name' => 'join_date',
                                                'id' => 'join_date',
                                                'type' => 'text',
                                                'class' => 'form-control datepicker',
                                                'field-name' => 'tanggal bergabng',
                                                'readonly'=> true,
                                                 'style' => 'width:80%;float:left;',
                                                'placeholder' => 'tanggal bergabung',
                                                'value' => $this->form_validation->set_value('join_date', date('Y-m-d')));

            //load content
            $this->data['city'] = $this->store_model->get_city_dropdown();
            $this->data['country'] = $this->store_model->get_country_dropdown();
            $this->data['province'] = $this->store_model->get_province_dropdown();

            $this->data['store'] = $this->store_model->get_store_dropdown();
            $this->data['member_category'] = $this->member_category_model->get_category_dropdown();

            $this->data['content'] .= $this->load->view('admin/member-add', $this->data, true);
            $this->render('admin');
        }

    }

    public function detail($id){
         if (empty($id)) {
            redirect(SITE_ADMIN . '/member');
        }
        $this->load->model('member_model');

        $form_data = $this->member_model->get_detail_member($id);
        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/member');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title']  = "Detail Member";
        $this->data['subtitle']  = "Detail Member";

        $this->load->model('store_model');
        $this->load->model('member_category_model');

        $this->data['content'] .= $this->load->view('admin/member-detail.php', $this->data, true);

        $this->render('admin');


    }
    public function get_transaction_data($member_id=0){
      $this->load->library(array('datatables'));
      $this->load->helper(array('datatables'));
      $bill_members=$this->db->query("select bill_id from bill_information where info=CONCAT('Diskon Member(',".$member_id.",')')")->result();
      $bills=array();
      foreach($bill_members as $b){
        array_push($bills,$b->bill_id);
      }
      if(sizeof($bills)==0)$bills='';
      $this->datatables->select('
        bill.payment_date,bill.total_price,bill.receipt_number,bill.customer_count,bill.order_id,
        (SELECT sum(quantity) from bill_menu where bill_id = bill.id) as quantity_order 
      ',false)
      ->from('bill')
      ->where_in("id",$bills)
      ->unset_column('payment_date')
      ->add_column('order_type', '$1', 'convert_order_type(order_type,is_delivery)')
      ->add_column('total_price_rp', '$1', 'convert_rupiah(total_price)')
      ->add_column('payment_date', '$1', 'convert_date_with_time(payment_date)')
      ->add_column('actions', "<div class='btn-group'>
        <a href='" . base_url(SITE_ADMIN . '/reports/detail_transaction/$1') . "' target='_blank' class='btn btn-default'
        rel='tooltip' data-tooltip='tooltip' target='_blank' title='Detail'><i class='fa fa-search'></i></a>", 'receipt_number');
      echo $this->datatables->generate();
    }

    public function edit_member()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/member');
        }
        $this->load->model('member_model');

        $form_data = $this->member_model->get_one('member', $id);

        $this->load->model('store_model');
        $this->load->model('member_category_model');

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/member');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title']  = "Edit Member";
        $this->data['subtitle']  = "Edit Member";

        //validate form input
        $this->form_validation->set_rules('name', 'nama', 'required');
        $this->form_validation->set_rules('member_id', 'ID member', 'required|callback__id_check');
        $this->form_validation->set_rules('mobile_phone', 'nomor handphone', 'required|numeric');
        $this->form_validation->set_rules('land_phone', 'nomor telepon', 'required|numeric');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $created_at = date("Y-m-d H:i:s") ;
                $created_by =  $this->data['user_profile_data']->id;

                $data_array = array('name' => $this->input->post('name'),
                    'member_id' => $this->input->post('member_id'),
                    'member_category_id' => $this->input->post('member_category_id'),
                    'address' => $this->input->post('address'),
                    'city_id' => $this->input->post('city_id'),
                    'province_id' => $this->input->post('province_id'),
                    'country_id' => $this->input->post('country_id'),
                    'postal_code' => $this->input->post('postal_code'),
                    'land_phone' => $this->input->post('land_phone'),
                    'mobile_phone' => $this->input->post('mobile_phone'),
                    'join_date' =>$this->input->post('join_date'),
                    'join_store_id' => $this->data['setting']['store_id'],
                    'modified_at' => $created_at,
                    'modified_by' => $created_by,

                    );

                $save = $this->member_model->save('member', $data_array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect(SITE_ADMIN . '/member', 'refresh');
                }
                else {
                    redirect(SITE_ADMIN . '/member/edit_member/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url']      = base_url(SITE_ADMIN . '/member/member');

            $this->data['name'] = array('name' => 'name',
                                                'id' => 'name',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'nama',
                                                'placeholder' => 'Masukan nama',
                                                'value' => $this->form_validation->set_value('name', $form_data->name));

            $this->data['member_id'] = array('name' => 'member_id',
                                                'id' => 'member_id',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField',
                                                'field-name' => 'id member',
                                                'placeholder' => 'Masukan ID member',
                                                'maxlength' => 20,
                                                'value' => $this->form_validation->set_value('member_id', $form_data->member_id));

            $this->data['postal_code'] = array('name' => 'postal_code',
                                                'id' => 'postal_code',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'kode pos',
                                                'placeholder' => 'Masukan kode pos',
                                                'value' => $this->form_validation->set_value('postal_code', $form_data->postal_code));

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

            $this->data['member_category_id'] = array('name' => 'member_category_id',
                                                'id' => 'member_category_id',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'member kategori',
                                                'placeholder' => 'Masukan value',
                                                'value' => $this->form_validation->set_value('member_category_id', $form_data->member_category_id));

            $this->data['land_phone'] = array('name' => 'land_phone',
                                                'id' => 'land_phone',
                                                'type' => 'text',
                                                'class' => 'form-control requiredTextField ',
                                                'field-name' => 'nomor telepon',
                                                'placeholder' => 'Masukan nomor telepon',
                                                'value' => $this->form_validation->set_value('land_phone', $form_data->land_phone));
            
            if($form_data->join_date != 0){
                $join_date= date_format(date_create($form_data->join_date),'Y-m-d') ;
            }
            $this->data['join_date'] = array('name' => 'join_date',
                                                'id' => 'join_date',
                                                'type' => 'text',
                                                'class' => 'form-control datepicker',
                                                'field-name' => 'tanggal bergabng',
                                                'readonly'=> true,
                                                 'style' => 'width:80%;float:left;',
                                                'placeholder' => 'tanggal bergabung',
                                                'value' => $this->form_validation->set_value('join_date', $join_date));

        $this->data['city'] = $this->store_model->get_city_dropdown();
        $this->data['country'] = $this->store_model->get_country_dropdown();
        $this->data['province'] = $this->store_model->get_province_dropdown();
        $this->data['store'] = $this->store_model->get_store_dropdown();
        $this->data['member_category'] = $this->member_category_model->get_category_dropdown();
        
        $this->data['content'] .= $this->load->view('admin/member-edit.php', $this->data, true);

        $this->render('admin');
    }

    public function delete_member()
    {

        $id = $this->uri->segment(4);

        if (empty($id)) {
            redirect(SITE_ADMIN . '/member');
        }

        $this->load->model('member_model');
        $form_data = $this->member_model->get_one('member', $id);

        if (empty($form_data)) {
            redirect(SITE_ADMIN . '/member');
        }

        $result = $this->member_model->delete('member', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect(SITE_ADMIN . '/member', 'refresh');
    }

}