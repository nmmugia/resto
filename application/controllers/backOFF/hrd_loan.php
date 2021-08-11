<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_loan extends Hrd_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) { 
            redirect(SITE_ADMIN . '/login', 'refresh');
        }else{
            $this->load->model('hrd_model');
            $this->load->model('categories_model');
            $this->load->library('encrypt');
              $this->_store_data = $this->ion_auth->user()->row();
        } 
    }

    public function index()
    {
        $this->data['title']    = "Kelola Pinjaman";
        $this->data['subtitle'] ="Kelola Pinjaman";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_loan/get_data_loan');
        $this->data['add_url'] = base_url(SITE_ADMIN . '/hrd_loan/add_loan');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/loan_list_view', $this->data, true);
        $this->render('hrd');
    } 

    public function get_data_loan(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  

         $this->datatables->select('hr_loan.id,users.name,store.store_name,hr_loan.instalment,hr_jobs.jobs_name,
                (hr_loan.loan_total/hr_loan.instalment) as instalment_total,sum(hr_repayments.repayment_total)  as repayment_total,
                (hr_loan.loan_total - sum(hr_repayments.repayment_total)) as outstanding_total,
                hr_loan.loan_date,hr_loan.loan_total')
            ->from('hr_loan')
            ->join('hr_repayments','hr_loan.id = hr_repayments.loan_id','LEFT')
            ->join('users','users.id = hr_loan.user_id')
            ->join('store','users.store_id = store.id') 
            ->join('hr_jobs_history hjh','users.id = hjh.employee_id and curdate() between hjh.start_date and hjh.end_date','LEFT') 
            ->join('hr_jobs','hr_jobs.id = hjh.jobs_id','LEFT') 
            ->group_by("hr_loan.id")
            ->add_column('view', "<div class='btn-group'>
              <a href='" . base_url(SITE_ADMIN . '/hrd_loan/detail_loan/$1')."' class='btn btn-default' >View</a>
             
                </div>", 'id')
            ->add_column('actions', "<div class='btn-group'>
              <a href='" . base_url(SITE_ADMIN . '/hrd_loan/edit_loan/$1')."' class='btn btn-default'>Edit</a>
              <a href='" . base_url(SITE_ADMIN . '/hrd_loan/delete_loan/$1')."' class='btn btn-danger deleteNow' rel='Pinjaman'>Delete</a>
                </div>", 'id');  
        echo $this->datatables->generate();
    }

    public function add_loan(){
        $this->data['title']    = "Tambah Pinjaman";
        $this->data['subtitle'] ="Tambah Pinjaman";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

         if (isset($_POST) && !empty($_POST)) { 
            $total_pinjaman =  $this->input->post('loan_total');
            $instalment =  $this->input->post('instalment');
            $total_take_home =  $this->input->post('total_take_home');
            if($total_take_home == 0){
                 $this->session->set_flashdata('message', "Maaf, Pendapatan anda tidak mencukupi");
                redirect(SITE_ADMIN . '/hrd_loan/add_loan', 'refresh');
            }

            if(empty($instalment)) $instalment = 1;
            
            $rata_pinjaman  = $total_pinjaman/$instalment; 
            
           
            if($rata_pinjaman > $total_take_home){ 
                $this->session->set_flashdata('message', "Maaf, Jumlah Cicilan Harus Lebih besar dari Total pendapatan");
                  redirect(SITE_ADMIN . '/hrd_loan/add_loan', 'refresh');
            }else{    
                $save_data = array(
                    'user_id'      => $this->input->post('user_id'),
                    'loan_total'      => $this->input->post('loan_total'),
                    'instalment'      => $instalment, 
                     'loan_date'      => date('Y-m-d'),
                    'created_at'      => date('Y-m-d H:i:s'),
                    'created_by'    => $this->_store_data->user_id,
                    'payment_option'    => $this->input->post('payment_option'),
                );
                $save_data = $this->hrd_model->save('hr_loan', $save_data); 
                  if($save_data){
                   $this->session->set_flashdata('message_success', "Berhasil Menyimpan Pinjaman");
                }else{ 
                    $this->session->set_flashdata('message', "Gagal Menyimpan Pinjaman");
                }
            } 
          
             redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
        }


        $this->data['data_users']      = $this->hrd_model->get_user_dropdown(array("store_id"=>$this->data['setting']['store_id'],"active"=>1));
        $this->data['data_enum_loan_payments']      = $this->hrd_model->get_enum_loan_payment_dropdown();

        $this->data['content'] .= $this->load->view('admin/hrd/loan_add_view', $this->data, true);
        $this->render('hrd');
    }
    public function edit_loan(){
        $id = $this->uri->segment(4);

        

        $this->data['title']    = "Ubah Pinjaman";
        $this->data['subtitle'] ="Ubah Pinjaman";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

         if (isset($_POST) && !empty($_POST)) { 
            $total_pinjaman =  $this->input->post('loan_total');
            $instalment =  $this->input->post('instalment');
            $total_take_home =  $this->input->post('total_take_home');
         
            $rata_pinjaman  = $total_pinjaman/$instalment; 
            
             $loan_id =  $this->input->post('loan_id');
            if($rata_pinjaman > $total_take_home){ 
                $this->session->set_flashdata('message', "Maaf, Jumlah Cicilan Harus Lebih besar dari Total pendapatan");
                redirect(SITE_ADMIN . '/hrd_loan/edit_loan/'.$loan_id, 'refresh');
            }else{ 
                $save_data2 = array( 
                    'loan_total'      => $this->input->post('loan_total'),
                    'instalment'      => $this->input->post('instalment'),  
                    'modified_at'      => date('Y-m-d H:i:s'),
                    'modified_by'    => $this->_store_data->user_id,
                    'payment_option'    => $this->input->post('payment_option')
                ); 
                $save_data = $this->hrd_model->update_where('hr_loan', $save_data2, array('id' => $loan_id));
               
                if($save_data){
                   $this->session->set_flashdata('message_success', "Berhasil Menyimpan Pinjaman");
                }else{ 
                    $this->session->set_flashdata('message', "Gagal Menyimpan Pinjaman");
                }
                redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
            }
        }else{
            if(empty($id)){
                redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
            }
        }
        $this->data['data_loan']  = $this->hrd_model->get_one('hr_loan', $id);
        $user_id = $this->data['data_loan']->user_id;
        $this->data['data_last_job'] = $this->hrd_model->get_detail_payroll_history_byuser($user_id);  
        $this->data['data_users']      = $this->hrd_model->get_user_dropdown(array("active"=>1));
        $this->data['data_enum_loan_payments']      = $this->hrd_model->get_enum_loan_payment_dropdown();

        $this->data['content'] .= $this->load->view('admin/hrd/loan_edit_view', $this->data, true);
        $this->render('hrd');
    }
    public function delete_loan(){
         $id = $this->uri->segment(4);
         if(empty($id)){
             redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
         }
        $result = $this->hrd_model->delete_by_where('hr_repayments',array("loan_id"=>$id));
        if($result){
               $result = $this->hrd_model->delete('hr_loan', $id);
             $this->session->set_flashdata('message_success', 'Data Pinjaman Berhasil Di Hapus');
        }else{
             $this->session->set_flashdata('message', 'Data Pinjaman Gagal Di Hapus');
        }
        redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
    }

    public function detail_loan(){
        $id = $this->uri->segment(4);

        if(empty($id)){
            redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
        }

        $this->data['title']    = "Detail Pinjaman";
        $this->data['subtitle'] ="Detail Pinjaman";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
 
         $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_loan/get_detail_repayments/'.$id);
        $this->data['data_loan']  = $this->hrd_model->get_one('hr_loan', $id); 

        $this->data['data_users']      = $this->hrd_model->get_one('users', $this->data['data_loan']->user_id);
         
        $this->data['sisa_hutang'] = $this->hrd_model->get_sisa_hutang(array("hr_loan.id"=>$id)); 


        $this->data['content'] .= $this->load->view('admin/hrd/loan_detail_view', $this->data, true);
        $this->render('hrd');
    }

    public function get_detail_repayments(){
        $id = $this->uri->segment(4);
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  
        $this->load->helper(array('hrd'));  

         $this->datatables->select('hr_loan.id,hr_repayments.id as repayment_id,repayment_date,repayment_total, (hr_loan.loan_total - sum(hr_repayments.repayment_total)) as outstanding_total,repayment_method')
            ->from('hr_repayments')
             ->join('hr_loan','hr_loan.id = hr_repayments.loan_id')
              ->unset_column('repayment_method')
                ->add_column('repayment_method', '$1', 'set_status_repayment_method(repayment_method)')
            ->where('loan_id',$id)
            ->group_by('hr_repayments.id')
             ->add_column('actions', "<div class='btn-group'>
             
              <a href='" . base_url(SITE_ADMIN . '/hrd_loan/delete_repayments/$1/$2')."' class='btn btn-danger deleteNow' rel='Pinjaman'>Delete</a>
                </div>", 'repayment_id,id');
        echo $this->datatables->generate();
    }

    public function save_repayment(){
        $loan_id =  $this->input->post('loan_id');
        $repayment_date =  $this->input->post('repayment_date');
        $repayment_total =  $this->input->post('repayment_total');

        $data_loan = $this->hrd_model->get_sisa_hutang(array("hr_loan.id"=>$loan_id)); 
        $sisa_hutang = 0;
        if($data_loan){
            $sisa_hutang = $data_loan->loan_total- $data_loan->repayment_total; 
        }
      
        $return_data = array();
        $return_data['status'] = true;
        $return_data['data'] = array();
        $return_data['message'] = ""; 


        if($repayment_total > $sisa_hutang){
            $return_data['status'] = false;
            $sisa_hutang  = "Rp. ".number_format(round($sisa_hutang,2),0,"",".");
            $return_data['message'] = "Total Pembayaran Lebih Dari Sisa Hutang, Sisa Hutang :".$sisa_hutang;  
        }
        

        if(empty($loan_id)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter ID  Kosong";  
        }

        if(empty($repayment_date)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Tanggal Kosong";  
        }

        if(empty($repayment_total)){
            $return_data['status'] = false;
            $return_data['message'] = "Parameter Total Pembayaran Kosong";  
        }

         

        $outlet_id = 0;
        if($return_data['status']){
            $data = array(
                'loan_id' =>$loan_id,
                'repayment_date' =>$repayment_date,
                'repayment_total' =>$repayment_total,
                'repayment_method'=>1,
                'created_at'=> date('Y-m-d H:i:s')
            );
           
            $save = $this->hrd_model->save('hr_repayments', $data);
            if($save){
                $sisa = $sisa_hutang + $repayment_total;
                $return_data['data'] = array("sisa"=>$sisa);
                $return_data['status'] = true;
            }else{
                $return_data['message'] = "Maaf, Data Gagal Di simpan. Silahkan Hubungi Administrator";  
            } 
        }

        echo json_encode($return_data);
       
    }

    public function delete_repayments(){
        $id = $this->uri->segment(4);
        $loan_id = $this->uri->segment(5);
        if(empty($id)){
            redirect(SITE_ADMIN . '/hrd_loan', 'refresh');
        }
        $result = $this->hrd_model->delete_by_where('hr_repayments',array("id"=>$id));
        if($result){ 
             $this->session->set_flashdata('message_success', 'Data Pinjaman Berhasil Di Hapus');
        }else{
             $this->session->set_flashdata('message', 'Data Pinjaman Gagal Di Hapus');
        }
        redirect(SITE_ADMIN . '/hrd_loan/detail_loan/'.$loan_id, 'refresh');
    }

    public function get_last_payroll(){
        $return_data['status'] = false;
        $return_data['data'] = array();
        $return_data['message'] = ""; 
        $user_id = $this->input->post('user_id'); 
        if(!empty($user_id)){
            $return_data['data'] = $this->hrd_model->get_last_jobs_user($user_id);  
            if(sizeof($return_data['data'])>0){
              $total_take_home_pay = $this->hrd_model->sum_total_gaji(array("job_id"=>$return_data['data']->jobs_id));  
              $return_data['data']->total_take_home_pay = $total_take_home_pay->total_gaji;
            }
            $return_data['status'] = true;
        }else{
            $return_data['message'] = "Empty Parameter";
        }
        
        echo json_encode($return_data);
    }
}