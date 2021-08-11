<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Created by BOSRESTO.
* User: Azis
* Date: 09/01/2016
* Time: 16:30 PM
*/
class Hrd_Overtime extends Hrd_Controller
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
        $this->data['title']    = "Daftar Lembur";
        $this->data['subtitle'] ="Daftar Lembur";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_overtime/get_data_overtime');
        $this->data['add_url'] = base_url(SITE_ADMIN . '/hrd_overtime/add_overtime');
        //load content
        $this->data['use_username'] = TRUE;
        $this->data['content'] .= $this->load->view('admin/hrd/overtime_list_view', $this->data, true);
        $this->render('hrd');
    } 

    public function get_data_overtime(){
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));  

        $this->datatables->select('ho.id as id,u.name as uname, ho.created_at, ho.start_time, ho.end_time, ho.note')
        ->from('hr_overtime ho')
        ->join('users u','u.id = ho.user_id')
        ->add_column('actions', "<div class='btn-group'>
            <a href='" . base_url(SITE_ADMIN . '/hrd_overtime/edit_overtime/$1')."' class='btn btn-default'>Edit</a>
            <a href='" . base_url(SITE_ADMIN . '/hrd_overtime/delete_overtime/$1')."' class='btn btn-danger deleteNow' rel='Data'>Delete</a>
        </div>", 'id'); 
        echo $this->datatables->generate();
    }

    public function add_overtime(){
        $this->data['title']    = "Tambah Daftar Lembur";
        $this->data['subtitle'] ="Tambah Daftar Lembur";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 


        if (isset($_POST) && !empty($_POST)) { 

            $user_id =  $this->input->post('loan_total');
            $start_time =  $this->input->post('start_time');
            $end_time =  $this->input->post('end_time');
            $note = $this->input->post('note');



            $save_data = array(
                'user_id'       => $this->input->post('user_id'),
                'start_time'    => $this->input->post('start_time'),
                'end_time'      => $this->input->post('end_time'),
                'created_at'    => date('Y-m-d'),
                'note'          => $this->input->post('note'),

                );

            $save_data = $this->hrd_model->save('hr_overtime', $save_data); 
            if($save_data){
                $this->session->set_flashdata('message_success', "Berhasil Menyimpan Data");
            }else{ 
                $this->session->set_flashdata('message', "Gagal Menyimpan Data");
            }


            redirect(SITE_ADMIN . '/hrd_overtime', 'refresh');
        }


        $this->data['data_users']      = $this->hrd_model->get_user_dropdown(array("store_id"=>$this->data['setting']['store_id'],"active"=>1));

        $this->data['note'] = array(
            'name' => 'note',
            'id' => 'note',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Notes',
            'placeholder' => 'Masukan Catatan'
            );

        $this->data['content'] .= $this->load->view('admin/hrd/overtime_add_view', $this->data, true);
        $this->render('hrd');
    }
    public function edit_overtime(){
        $id = $this->uri->segment(4);

        $this->data['title']    = "Ubah Daftar Lembur";
        $this->data['subtitle'] ="Ubah Daftar Lembur";

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success'); 

        if (isset($_POST) && !empty($_POST)) { 

            $overtime_id =  $this->input->post('overtime_id');
            $start_time =  $this->input->post('start_time');
            $end_time =  $this->input->post('end_time');
            $note = $this->input->post('note');


            $save_data2 = array( 

                'start_time'    => $this->input->post('start_time'),
                'end_time'      => $this->input->post('end_time'),
                'created_at'    => date('Y-m-d'),
                'note'          => $this->input->post('note'),
                ); 

            $save_data = $this->hrd_model->update_where('hr_overtime', $save_data2, array('id' => $overtime_id));

            if($save_data){
                $this->session->set_flashdata('message_success', "Berhasil Menyimpan Data tters");
            }else{ 
                $this->session->set_flashdata('message', "Gagal Menyimpan Data");
            }
            redirect(SITE_ADMIN . '/hrd_overtime', 'refresh');
        }else{
            if(empty($id)){
                redirect(SITE_ADMIN . '/hrd_overtime', 'refresh');
            }
        }
        $this->data['data_overtime']  = $this->hrd_model->get_one('hr_overtime', $id);
        $user_id = $this->data['data_overtime']->user_id;
        $note = $this->data['data_overtime']->note;
        $this->data['data_users']      = $this->hrd_model->get_user_dropdown(array("active"=>1));
        $this->data['note'] = array(
            'name' => 'note',
            'id' => 'note',
            'type' => 'text',
            'class' => 'form-control requiredTextField',
            'field-name' => 'Notes',
            'placeholder' => 'Masukan Catatan',
            'value'     => $note
            );
        $this->data['content'] .= $this->load->view('admin/hrd/overtime_edit_view', $this->data, true);
        $this->render('hrd');
    }
    public function delete_overtime(){
        $id = $this->uri->segment(4);
        if(empty($id)){
            redirect(SITE_ADMIN . '/hrd_overtime', 'refresh');
        }
        $result = $this->hrd_model->delete('hr_overtime',$id);
        if($result){

            $this->session->set_flashdata('message_success', 'Data Berhasil Di Hapus');
        }else{
            $this->session->set_flashdata('message', 'Data Gagal Di Hapus');
        }
        redirect(SITE_ADMIN . '/hrd_overtime', 'refresh');
    }


}