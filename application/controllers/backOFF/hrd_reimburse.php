<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_reimburse extends Hrd_Controller
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
        } 
    }

    public function index()
    {
        $this->data['title']    = "Kelola Reimburse";
        $this->data['subtitle'] ="Kelola Reimburse";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

       
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_reimburse/get_data_reimburse');
        $this->data['add_url'] = base_url(SITE_ADMIN . '/hrd_reimburse/add_reimburse');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/reimburse_list_view', $this->data, true);
        $this->render('hrd');
    }

    public function add_reimburse(){
        $this->data['title']    = "Tambah Reimburse";
        $this->data['subtitle'] ="Tambah Reimburse";

        
        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 
         if (isset($_POST) && !empty($_POST)) {  
            $user_id = $this->input->post('user_id');
            $max_reimburse = 0;
            $data_user =  $this->hrd_model->get_last_jobs_user($user_id); 
            $data_reimmburse = $this->hrd_model->get_taken_total_reimburse_byuser(array("user_id"=>$user_id));
            if(!empty($data_reimmburse) && !empty($data_user)){
                $max_reimburse = $data_user->reimburse - $data_reimmburse->total;
            } 
            $total_reimburse = $this->input->post('total_reimburse');

            if($max_reimburse > 0 && $total_reimburse <= $max_reimburse){

                $save_data = array(
                    'user_id'      => $this->input->post('user_id'),
                    'total'      => $this->input->post('total_reimburse'),
                    'note'      => $this->input->post('note'),
                    'attachment'      => $this->input->post('attachment'),
                   'created_at'      => date('Y-m-d H:i:s')
                );
                $save_data = $this->hrd_model->save('hr_reimburse', $save_data); 
                 if($save_data){
                   $this->session->set_flashdata('message_success', "Berhasil Menyimpan Reimburse");
                }else{ 
                    $this->session->set_flashdata('message', "Gagal Menyimpan Reimburse");
                }
                redirect(SITE_ADMIN . '/hrd_reimburse', 'refresh');
            }else{
             $this->data['message']="Gagal Menyimpan Reimburse, Jumlah Reimburse Tidak boleh melebihi jatah reimburse. Sisa reimburse yaitu : Rp. ".number_format($max_reimburse,0);
            }
        }

       
        $this->data['data_users']      = $this->hrd_model->get_user_dropdown(array("active"=>1));

        $this->data['content'] .= $this->load->view('admin/hrd/reimburse_add_view', $this->data, true);
        $this->render('hrd');
    }

    public function get_data_reimburse(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  

         $this->datatables->select('hr_reimburse.*,hr_reimburse.id, users.name,store.store_name,hr_jobs.jobs_name,hr_reimburse.created_at
            ,hr_reimburse.total,hr_reimburse.note
                ')
            ->from('hr_reimburse')
            ->join('users','users.id = hr_reimburse.user_id')
            ->join('store','users.store_id = store.id') 
            ->join('hr_jobs_history hjh','users.id = hjh.employee_id and curdate() between hjh.start_date and hjh.end_date','LEFT') 
            ->join('hr_jobs','hr_jobs.id = hjh.jobs_id','LEFT') 
            ->add_column('actions', "<div class='btn-group'>
              <a href='" . base_url(SITE_ADMIN . '/hrd_reimburse/delete_reimburse/$1')."' class='btn btn-default deleteNow' rel='Reimburse'>Delete</a>
                </div>", 'id');  
        echo $this->datatables->generate();
    }

    public function delete_reimburse(){
         $id = $this->uri->segment(4);
         if(empty($id)){
             redirect(SITE_ADMIN . '/hrd_reimburse', 'refresh');
         }
        $result = $this->hrd_model->delete('hr_reimburse', $id);
        if($result){
             $this->session->set_flashdata('message_success', 'Data Reimburse Berhasil Di Hapus');
        }else{
             $this->session->set_flashdata('message', 'Data Reimburse Gagal Di Hapus');
        }
        redirect(SITE_ADMIN . '/hrd_reimburse', 'refresh');
    }
}