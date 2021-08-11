<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Table extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model');

        $this->data['default_table_width']  = ($this->data['setting']['default_table_width']) ? $this->data['setting']['default_table_width'] . 'px' : '1000px';
        $this->data['default_table_height'] = ($this->data['setting']['default_table_height']) ? $this->data['setting']['default_table_height'] . 'px' : '500px';

        // disable for client
        redirect(SITE_ADMIN);
    }

    public function index()
    {
        $this->data['title']    = "Table Management";
        $this->data['subtitle'] = "Table Management";
        $this->data['is_draw']  = true;

        $this->data['message']         = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['floor_id']           = $this->categories_model->get_floor();
        $this->data['table_name']         = array('name' => 'table_name',
                                                  'id' => 'table_name',
                                                  'type' => 'text',
                                                  'value' => '',
                                                  'placeholder' => 'Enter Table Name',
                                                  'class' => 'form-control');
        $this->data['loadDataTable']      = array('name' => 'loadDataTable',
                                                  'id' => 'loadDataTable',
                                                  'type' => 'hidden',
                                                  'value' => base_url(SITE_ADMIN . '/table/load_object'));
        $this->data['saveDataTable']      = array('name' => 'saveDataTable',
                                                  'id' => 'saveDataTable',
                                                  'type' => 'hidden',
                                                  'value' => base_url(SITE_ADMIN . '/table/save_object'));
        $this->data['getUniqueID']        = array('name' => 'getUniqueID',
                                                  'id' => 'getUniqueID',
                                                  'type' => 'hidden',
                                                  'value' => base_url(SITE_ADMIN . '/table/get_unique_id'));
        $this->data['deleteDataID']       = array('name' => 'deleteDataID',
                                                  'id' => 'deleteDataID',
                                                  'type' => 'hidden',
                                                  'value' => base_url(SITE_ADMIN . '/table/delete_data_id'));
        $this->data['clearDataTable']     = array('name' => 'clearDataTable',
                                                  'id' => 'clearDataTable',
                                                  'type' => 'hidden',
                                                  'value' => base_url(SITE_ADMIN . '/table/clear_data_table'));
        $this->data['defaultTableWidth']  = array('name' => 'defaultTableWidth',
                                                  'id' => 'defaultTableWidth',
                                                  'type' => 'hidden',
                                                  'value' => $this->data['setting']['default_table_width']);
        $this->data['defaultTableHeight'] = array('name' => 'defaultTableHeight',
                                                  'id' => 'defaultTableHeight',
                                                  'type' => 'hidden',
                                                  'value' => $this->data['setting']['default_table_height']);

        //load content
        $this->data['content'] .= $this->load->view('admin/table-list', $this->data, true);
        $this->render('admin');
    }

    public function get_unique_id()
    {
        if ($this->input->is_ajax_request()) {

            $return_data['random_id'] = $this->categories_model->get_random_unique_id('table', 'id');
            echo json_encode($return_data);
        }
    }

    public function load_object()
    {
        if ($this->input->is_ajax_request()) {
            $floor_id                = $this->input->post('floor_id');
            $return_data['floor_id'] = $floor_id;
            $return_data['status']   = false;

            $dataObject = $this->categories_model->paged('table', 0, 0, array('floor_id' => $floor_id));
            if ($dataObject->num_rows > 0) {
                $return_data['status'] = true;

                $return_data['items'] = array();
                foreach ($dataObject->result() as $res) {
                    $return_data['items']['objects'][] = json_decode($res->json_data);
                }
                $return_data['items']['background'] = '#FFF';
            }

            echo json_encode($return_data);
        }
    }

    public function save_object()
    {
        if ($this->input->is_ajax_request()) {
            $floor_id = $this->input->post('floor_id');
            $data     = $this->input->post('data');
            $data     = json_decode($data, true);
            $objects  = $data['objects'];


            if (! empty($objects)) {
                foreach ($objects as $key => $obj) {
                    $form_data = $this->categories_model->get_one('table', $obj['id']);
                    if (empty($form_data)) {
                        $array = array('id' => $obj['id'],
                                       'table_name' => $obj['label'],
                                       'floor_id' => $floor_id,
                                       'pos_x' => $obj['left'],
                                       'pos_y' => $obj['top'],
                                       'width' => $obj['width'] * $obj['scaleX'],
                                       'height' => $obj['height'] * $obj['scaleY'],
                                       'rotate' => $obj['angle'],
                                       'table_shape' => $obj['type'],
                                       'status' => '1',
                                       'table_status' => '1',
                                       'json_data' => json_encode($objects[$key]));

                        $save = $this->categories_model->save('table', $array);
                    }
                    else {
                        $array = array('pos_x' => $obj['left'],
                                       'pos_y' => $obj['top'],
                                       'width' => $obj['width'] * $obj['scaleX'],
                                       'height' => $obj['height'] * $obj['scaleY'],
                                       'rotate' => $obj['angle'],
                                       'json_data' => json_encode($objects[$key]));
                        $save  = $this->categories_model->save('table', $array, $obj['id']);
                    }
                }
            }
        }
    }

    public function delete_data_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            // get status table
            $status_table = $this->categories_model->get_status_table($id);
            $return       = '';
            if (!empty($status_table) && $status_table[0]->table_status == 1) {
                $this->categories_model->delete('table', $id);
                $return = 1;
            }

            echo json_encode($return);
            // print_r($status_table[0]->table_status);
        }
    }

    public function clear_data_table()
    {
        if ($this->input->is_ajax_request()) {
            $id           = $this->input->post('id');
            $is_not_empty = 0;
            $status_table = $this->categories_model->get_status_table_by_floor($id);
            foreach ($status_table as $stat_table) {
                if ($stat_table->table_status != 1) {
                    $is_not_empty = 1;
                    break;
                }
            }
            $return = '';
            // print_r($is_not_empty);
            if ($is_not_empty == 0) {
                $this->categories_model->delete_by_limit('table', array('floor_id' => $id), 0);
                $return = 1;
            }

            echo json_encode($return);
        }
    }
}