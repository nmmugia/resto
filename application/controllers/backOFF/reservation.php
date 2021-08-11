<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Reservation extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("order_model");
        $all_cooking_status=array();
        foreach($this->order_model->get("enum_cooking_status")->result() as $a){
          $all_cooking_status[$a->id]=$a->status_name;
        }
        $this->data['all_cooking_status']=json_encode($all_cooking_status);
    }

    public function index(){
        //load content
        $this->data['title']           = "Reservasi";
        $this->data['subtitle']        = "Reservasi";
        $this->data['theme']           = 'floor-theme';
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['add_url']  = base_url(SITE_ADMIN . '/reservation/add');
        $this->data['data_url'] = base_url(SITE_ADMIN . '/reservation/get_data');;
        //load content
        $this->data['content'] .= $this->load->view('admin/reservation-list', $this->data, true);
        $this->render('admin');
    }

    public function get_data()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $this->datatables->select('reservation.id, customer_name,reservation.customer_count,
            phone, book_date, book_note, table.table_name, enum_reservation_status.value, failed_note,reservation.down_payment')
        ->from('reservation')
        ->join('enum_reservation_status', 'enum_reservation_status.id = reservation.status')
        ->join('table', 'table.id = reservation.table_id')
        ->unset_column("down_payment")
        ->add_column("down_payment","$1","convert_rupiah(down_payment)")
        ->add_column('actions', "
                                    <a rel='tooltip' title='Lihat order'
                                    href='" . base_url(SITE_ADMIN.'/reservation/detail/$1') . "'  class='btn btn-default'>
                                    <i class='fa fa-search'></i>Lihat Order</a>
                                   
                                </div>", 'id');
        echo $this->datatables->generate();
    }
    public function detail()
    {
      $id=$this->uri->segment(4);
      if(empty($id))redirect(SITE_ADMIN."/reservation");
      $this->load->model('reservation_model');
      $this->data['reservation'] = $this->reservation_model->get_by_id($id);
      $this->data['title']           = "Detail Reservasi";
      $this->data['theme']           = 'floor-theme';
      $this->data['content'] .= $this->load->view('admin/reservation-detail', $this->data, true);
      $this->render('admin');
    }
/*
    public function _book_date_check($value){
    	 // $id = $this->uri->segment(3);

      //   if (!empty($id)) {
      //       $id_old = $this->db->where("id", $id)->get('reservation')->row()->id;
      //       $this->db->where("id !=", $id_old);
      //   }

      //   $num_row = $this->db->where('id', $id)->get('reservation')->num_rows();
      //   // var_dump($id_old);die();
      //   if ($num_row > 0) {
      //       $this->form_validation->set_message('_book_date_check', 'Waktu reservation tidak dapat dipesan.');
      //       return FALSE;
      //   } else {
      //       return TRUE;
      //   }

    	return TRUE;
    }
   

     public function edit()
    {

        $id = $this->uri->segment(3);

        if (empty($id)) {
            redirect('reservation');
        }
        $this->load->model('reservation_model');

        $form_data = $this->reservation_model->get_one('reservation', $id);

        $this->load->model('reservation_model');

        if (empty($form_data)) {
            redirect('reservation');
        }

        $this->data['form_data'] = $form_data;
        $this->data['title']  = "Edit reservation";
        $this->data['subtitle']  = "Edit reservation";

        //validate form input
        $this->form_validation->set_rules('customer_name', 'nama', 'required|max_length[100]');
        $this->form_validation->set_rules('customer_count', 'jumlah tamu', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('phone', 'nomor kontak', 'required|numeric|max_length[20]');
        $this->form_validation->set_rules('book_date', 'waktu reservasi', 'required|callback__book_date_check');
        $this->form_validation->set_rules('table_id', 'meja', 'required');
        $this->form_validation->set_rules('book_note', 'meja', 'max_length[500]');

        if (isset($_POST) && ! empty($_POST)) {

            if ($this->form_validation->run() === TRUE) {
                $created_at = date("Y-m-d H:i:s") ;
                $created_by =  $this->data['user_profile_data']->id;
                $book_date = $this->input->post('book_date') ;

                $data_array = array('customer_name' => $this->input->post('customer_name'),
                	'customer_count' => $this->input->post('customer_count'),
                	'table_id' => $this->input->post('table_id'),
                	'book_date' => date('Y-m-d H:i:s',strtotime($book_date)),
                	'phone' => $this->input->post('phone'),
                	'book_note' => $this->input->post('book_note'),
                	'status' => 1,
                	'modified_at' => $created_at,
                	'modified_by' => $created_by,

                	);

                $save = $this->reservation_model->save('reservation', $data_array, $id);

                if ($save === false) {
                    $this->session->set_flashdata('message', 'Gagal menyimpan data');
                }
                else {
                    $this->session->set_flashdata('message_success', 'Berhasil menyimpan data');
                }
                $btnaction = $this->input->post('btnAction');
                if ($btnaction == 'save_exit') {
                    redirect('reservation', 'refresh');
                }
                else {
                    redirect('reservation/edit/' . $id, 'refresh');
                }


            }
        }

        //display the edit user form

        //set the flash data error message if there is one
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');
        $this->data['cancel_url']      = base_url('reservation/reservation');

        $this->data['customer_name'] = array('name' => 'customer_name',
        	'id' => 'customer_name',
        	'type' => 'text',
        	'class' => 'form-control requiredTextField',
        	'field-name' => 'nama',
        	'placeholder' => 'Masukan nama',
        	'value' => $this->form_validation->set_value('customer_name', $form_data->customer_name));

        $this->data['phone'] = array('name' => 'phone',
        	'id' => 'phone',
        	'type' => 'text',
        	'class' => 'form-control requiredTextField NumericWithZero',
        	'field-name' => 'kontak',
        	'placeholder' => 'Masukan nomor kontak',
        	'value' => $this->form_validation->set_value('phone', $form_data->phone));

        $this->data['book_date'] = array('name' => 'book_date',
        	'id' => 'book_date',
        	'type' => 'text',
        	'class' => 'form-control requiredTextField',
        	'field-name' => 'waktu reservasi',
        	'placeholder' => 'Masukan waktu reservasi',
        	'onkeydown'=>'return false',
                                                // 'readonly'=> true,
        	'value' => $this->form_validation->set_value('book_date', $form_data->book_date));

        $this->data['customer_count'] = array('name' => 'customer_count',
        	'id' => 'customer_count',
        	'type' => 'text',
        	'class' => 'form-control requiredTextField ',
        	'field-name' => 'jumlah tamu',
        	'placeholder' => 'Masukan jumlah tamu',
        	'value' => $this->form_validation->set_value('customer_count', $form_data->customer_count));



        $this->data['book_note'] = array('name' => 'book_note',
        	'id' => 'book_note',
        	'type' => 'text',
        	'class' => 'form-control',
        	'field-name' => 'catatan',
        	'placeholder' => 'Masukan catatan',
        	'value' => $this->form_validation->set_value('book_note', $form_data->book_note));


        $this->data['table'] =$this->reservation_model->get_table_dropdown();
        
        $this->data['content'] .= $this->load->view('reservation_edit_v.php', $this->data, true);

        $this->render('cashier');
    }

    public function delete()
    {

        $id = $this->uri->segment(3);

        if (empty($id)) {
            redirect('reservation');
        }

        $form_data = $this->reservation_model->get_one('reservation', $id);

        if (empty($form_data)) {
            redirect('reservation');
        }

        $result = $this->reservation_model->delete('reservation', $id);
        if ($result) {
            $this->session->set_flashdata('message_success', 'Berhasil menghapus data');
        }
        else {
            $this->session->set_flashdata('message', 'Error. Gagal menghapus data');
        }

        redirect('reservation', 'refresh');
    }


    public function get_table_reservation(){
    	$book_date = $this->input->post('book_date') ;


    	$book_date = '2015-09-18 15:54';
    	// $book_date = date('Y-m-d H:i:s',strtotime($book_date));
    	$book_time = strtotime($book_date);
    	$to_time = date('Y-m-d',$book_time );    	
    	$now = now();
    	$booking_start_lock = 60;
    	// $from_time = strtotime("+".$booking_start_lock." minutes", strtotime($now)) ;
    	$from_time = strtotime("+".$booking_start_lock." minutes", $now) ;

    	// $diff =  round(abs($book_time - $from_time) / 60,2);
    	if($book_time <= $from_time){
    		// $table = $this->reservation_model->get_table_bydate($)
    	}else{

    	}

    	echo $to_time;
    }*/

    
  }