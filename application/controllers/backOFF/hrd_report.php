<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:27 PM
 */
class Hrd_report extends Hrd_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect(SITE_ADMIN . '/login', 'refresh');
        } else {
            $this->load->model('hrd_model');
            $this->load->model('categories_model');
            $this->load->library('encrypt');
            $this->_store_data = $this->ion_auth->user()->row();
        }
    }

    public function report_attendance()
    {
        $this->data['title'] = "Laporan Absensi";
        $this->data['subtitle'] = "Laporan Absensi";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_report/get_report_attendance');
        $this->data['data_users'] = $this->hrd_model->get_user_dropdown();
        $this->data['content'] .= $this->load->view('admin/hrd/report_attendance_view', $this->data, true);
        $this->render('hrd');
    }

    public function get_report_attendance()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->load->helper(array('hrd'));
        $post_array = array();
        parse_str($this->input->post('param'), $post_array);
        $star_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];
        if ($start_date == "") $start_date = date("Y-m-d");
        if ($end_date == "") $end_date = date("Y-m-d");
        $user_id = $post_array['user_id'];

        $where = array();
        if (!empty($user_id)) {
            $where = array(
                "u.id" => $user_id
            );
        }
        if (!empty($start_date)) {
            $where = array(
                'ha.created_at >=' => $start_date
            );
        }
        if (!empty($end_date)) {
            $where = array(
                'ha.created_at <=' => $end_date
            );
        }

        $this->datatables->select('u.id as userid, s.store_name as sname,u.name as name,
                                    date(ha.created_at) as curdate,
                                    hsd.start_time,
                                    hsd.end_time,
                                    ha.checkin_time,
                                    ha.checkout_time,
                                    FLOOR(sum(TIME_TO_SEC(IFNULL(ha.checkout_time,hsd.end_time)) - TIME_TO_SEC(ha.checkin_time))/3600) spent_hour, 
                                    FLOOR(sum(TIME_TO_SEC(IFNULL(ha.checkin_time,0)) - TIME_TO_SEC(hsd.start_time))/60) late_total,
                                    FLOOR(sum(TIME_TO_SEC(IFNULL(ha.checkout_time,ha.over_checkout_time)) - TIME_TO_SEC(hsd.end_time))/60) overtime_total
                
                                    ', false)
            ->from('users u')
            ->join('store s', 's.id = u.store_id')
            ->join('hr_schedules hs', 'hs.user_id = u.id', 'left')
            ->join('hr_schedule_detail hsd', 'hsd.schedule_id = hs.id', 'left')
            ->join('hr_attendances ha', 'ha.user_id = u.id')
            ->join('hr_enum_status_attendance hesa', 'hesa.id=ha.enum_status_attendance', 'left')

            ->unset_column('userid')
            ->add_column('hadir','$1','get_hadir('.$star_date.','.$end_date.', userid)')
            ->add_column('sakit','$1','get_sakit('.$star_date.','.$end_date.',  userid)')
            ->add_column('cuti','$1','get_cuti('.$star_date.','.$end_date.', userid)')
            ->add_column('ijin','$1','get_ijin('.$star_date.','.$end_date.',  userid)');

            // ->unset_column('start_time')
            // ->unset_column('end_time')
            // ->unset_column('checkin_time')
            // ->unset_column('checkout_time')

            // // ->add_column('spent_hour', '$1', 'interval(checkout_time,checkin_time,end_time)')
            // ->unset_column('end_time')
            // ->unset_column('checkin_time')
            // ->unset_column('checkout_time')

            // // ->add_column('late_total', '$1', 'interval_menit(checkin_time,start_time,start_time)')
            // ->unset_column('start_time')
            // ->unset_column('checkin_time');

            // // ->add_column('overtime_total', '$1', 'interval_menit(checkout_time,end_time,end_time)');
 
        if($user_id){
            $this->datatables->where('u.id', $user_id);
        }
        if($start_date){
            $this->datatables->where('date(ha.created_at) >= ', $start_date);
        }

        if($end_date){
            $this->datatables->where('date(ha.created_at) <= ', $end_date);
        }

        $this->datatables->group_by('u.id', 'ha.created_at');
        $this->datatables->order_by('ha.created_at','u.name', "asc");

        echo $this->datatables->generate();
       
    }

    public function report_attendance_detail()
    {
        $this->data['title'] = "Laporan Detail Absensi";
        $this->data['subtitle'] = "Laporan Detail Absensi";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');


        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_report/get_report_attendance_detail');
        $this->data['data_users'] = $this->hrd_model->get_user_dropdown();
        $this->data['content'] .= $this->load->view('admin/hrd/report_attendance_detail_view', $this->data, true);
        $this->render('hrd');
    }

    public function get_report_attendance_detail()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->load->helper(array('hrd'));
        $post_array = array();
        parse_str($this->input->post('param'), $post_array);
        $start_date = $post_array['start_date'];
        $end_date = $post_array['end_date'];
        $user_id = $post_array['user_id'];
        
        $this->datatables->select('u.name,
            ha.created_at,
            hsd.start_time,
            hsd.end_time,

            IFNULL(ha.checkin_time,ha.over_checkin_time) AS checkin, 
            IFNULL(ha.checkout_time,ha.over_checkout_time) AS checkout,

            hesa.name as status,ha.note', false)
            ->from('users u')
            ->join('hr_schedules hs', 'hs.user_id = u.id', 'left')
            ->join('hr_schedule_detail hsd', 'hsd.schedule_id = hs.id', 'left')
            ->join('hr_attendances ha', 'ha.user_id = u.id')
            ->join('hr_enum_status_attendance hesa', 'hesa.id=ha.enum_status_attendance', 'left');
        
        if($user_id){
            $this->datatables->where('u.id', $user_id);
        }
        if($start_date){
            $this->datatables->where('date(ha.created_at) >= ', $start_date);
        }

        if($end_date){
            $this->datatables->where('date(ha.created_at) <= ', $end_date);
        }

        $this->datatables->order_by('ha.created_at','u.name', "asc");
        echo $this->datatables->generate();
    }

    public function get_report_payroll()
    {
        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));

        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $start_date = $post_array['i_date'];
        $end_date = $post_array['e_date'];

        if ($start_date == "") $start_date = date("Y-m-d");
        if ($end_date == "") $end_date = date("Y-m-d");

        $user_id = $post_array['user_id'];

        $this->datatables->select("u.`name` as pname,
    hph.id,
    hj.jobs_name,
    RIGHT (hph.period, 4) AS years,
    LEFT (hph.period, 2) AS months,
(
SELECT
sum(q.value)
FROM
    hr_detail_payroll_history q
JOIN hr_salary_component w on w.id = q.component_id
JOIN hr_payroll_history r on r.id=q.payroll_history_id
WHERE
    w.is_enhancer = '1' and r.user_id = u.id    
) as total_penerimaan,
(
SELECT
sum(q.value)
FROM
    hr_detail_payroll_history q
JOIN hr_salary_component w on w.id = q.component_id
JOIN hr_payroll_history r on r.id=q.payroll_history_id
WHERE
    w.is_enhancer = '-1' and r.user_id = u.id   
) as total_potongan,
(
(
SELECT
sum(q.value)
FROM
    hr_detail_payroll_history q
JOIN hr_salary_component w on w.id = q.component_id
JOIN hr_payroll_history r on r.id=q.payroll_history_id
WHERE
    w.is_enhancer = '1' and r.user_id = u.id    
) - 
(
SELECT
sum(q.value)
FROM
    hr_detail_payroll_history q
JOIN hr_salary_component w on w.id = q.component_id
JOIN hr_payroll_history r on r.id=q.payroll_history_id
WHERE
    w.is_enhancer = '-1' and r.user_id = u.id   
)) as total", false)
            ->from('hr_payroll_history hph')
            ->join('hr_jobs hj ', 'hj.id = hph.jobs_id')
            ->join('users u ', 'hph.user_id = u.id');


        if($user_id){
            $this->datatables->where('u.id', $user_id);
        }
        if($start_date){
            $this->datatables->where('hph.period >= ', $start_date);
        }

        if($end_date){
            $this->datatables->where('hph.period <= ', $end_date);
        }

        $this->datatables->group_by('hph.user_id');

        echo $this->datatables->generate();
    }

    public function get_report_appraisal()
    {
        $post_array = array();
        parse_str($this->input->post('param'), $post_array);

        $start_period = $post_array['start_period'];

        $user_id = $post_array['user_id'];

        $where = array();
        if (!empty($user_id)) {
            $where['users.id'] = $user_id;
        }

        if (!empty($start_period)) {
            $where['hap.period'] = $start_period;

        }

        $this->load->library(array('datatables'));
        $this->load->helper(array('datatables'));
        $this->load->helper(array('hrd'));
        $this->datatables->select('hap.id as appraisal_process_id,
                                users.name,hap.created_at, hat.name as template_name,hap.description,
                                hap.period,sum(hc.value) as total_nilai, 
                                 sum(hadc.point)  as max_nilai 
                                ')
            ->from('hr_appraisal_process hap')
            ->join('hr_appraisal_process_detail hd', 'hap.id = hd.appraisal_process_id')
            ->join('hr_appraisal_process_detail_category hc', 'hd.id = hc.appraisal_process_detail_id')
            ->join('hr_appraisal_detail_category hadc', 'hadc.id = hc.detail_category_id')
            ->join('users', 'hap.user_id = users.id')
            ->join('hr_appraisal_template hat', 'hap.template_id = hat.id')
            ->where($where)
            ->group_by("users.id")
            ->add_column('percentage', '$1', 'set_percentage_appraisal(total_nilai)');

        echo $this->datatables->generate();
    }

    public function report_payroll()
    {
        $this->data['title'] = "Laporan Gaji";
        $this->data['subtitle'] = "Laporan Gaji";
        $this->data['data_users'] = $this->hrd_model->get_user_dropdown();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_report/get_report_payroll');
        $this->data['content'] .= $this->load->view('admin/hrd/report_payroll_view', $this->data, true);
        $this->render('hrd');
    }

    public function report_appraisal()
    {
        $this->data['title'] = "Laporan Appraisal";
        $this->data['subtitle'] = "Laporan Appraisal";

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['data_users'] = $this->hrd_model->get_user_dropdown();
        $this->data['data_url'] = base_url(SITE_ADMIN . '/hrd_report/get_report_appraisal');

        $this->data['content'] .= $this->load->view('admin/hrd/report_appraisal_view', $this->data, true);
        $this->render('hrd');
    }

    public function export_report_to_pdf()
    {
        $this->load->helper(array('datatables'));
        $this->load->model('order_model');
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $user_id = $this->input->post('user_id');
        $type = $this->input->post('type');

        $start_period = $this->input->post('start_date');
        $end_period = $this->input->post('end_date');
        $office_hour = $this->input->post('office_hour');

        $i_date = $this->input->post('i_date');
        $e_date = $this->input->post('e_date');


        $data['is_print'] = TRUE;
        $pdf_orientation = "landscape";
        $report_name = "";
        if ($type == "attendance") {
            


            $this->load->model("report_model");
           $pdf_orientation= "portrait";
           $data['data'] =$this->order_model->getattendances(array('store_id' => $store_id , 'user_id' => $user_id ,'start_date' => $start_date,'end_date' => $end_date));
           $report = 'attendance';

           
            $date     = new DateTime();
            $perpage=38;
            $offset=0;
            $total_page=ceil(sizeof($data['data'])/$perpage);
            $filenames=array();
            
            $data['from']=-1;
            $html = $this->load->view('admin/report/report_hrd_attendance_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false,  $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_0' . '.pdf'; 
            array_push($filenames,$filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for($x=0;$x<$total_page;$x++){
              $data['from']=$x*$perpage;
              $data['to']=($x*$perpage)+$perpage;
              if($data['to']>sizeof($data['data']))$data['to']=sizeof($data['data']);
              $html = $this->load->view('admin/report/report_hrd_attendance_pdf_v', $data, true);
              $data_pdf = pdf_create($html, '', false, $pdf_orientation);
              $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_'.($x+1) . '.pdf'; 
              array_push($filenames,$filename);
              write_file($filename, $data_pdf);
              echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach($filenames as $file){
              $pdf->addPDF($file, 'all');         
            }
            

            $merge='assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis'). '.pdf';
            $pdf->merge('file', $merge);
            
            foreach($filenames as $file){
              unlink($file);
            }
             // redirect($merge);

            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);
        

        } elseif ($type == "attendance_detail") {
            
          $pdf_orientation= "portrait";
          
             $data['all_attendance_detail'] =$this->order_model->get_data_attendance_detail($start_date,$end_date,$user_id);
             
            $report = 'attendance_detail';
            // $html = $this->load->view('admin/report/report_open_close_to_pdf_v', $data, true);

            $date     = new DateTime();
            $perpage=40;
            $offset=0;
            $total_page=ceil(sizeof($data['all_attendance_detail'])/$perpage);

            $filenames=array();
            
            $data['from']=-1;
            $html = $this->load->view('admin/report/report_hrd_attendance_detail_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false, $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_0' . '.pdf'; 
            array_push($filenames,$filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for($x=0;$x<$total_page;$x++){
              $data['from']=$x*$perpage;
              $data['to']=($x*$perpage)+$perpage;
              if($data['to']>sizeof($data['all_attendance_detail']))$data['to']=sizeof($data['all_attendance_detail']);
              $html = $this->load->view('admin/report/report_hrd_attendance_detail_pdf_v', $data, true);
              $data_pdf = pdf_create($html, '', false, $pdf_orientation);
              $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_'.($x+1) . '.pdf'; 
              array_push($filenames,$filename);
              write_file($filename, $data_pdf);
              echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach($filenames as $file){
              $pdf->addPDF($file, 'all');         
            }
             $merge='assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis'). '.pdf';
             $pdf->merge('file', $merge);
            foreach($filenames as $file){
              @unlink($file);
             }
            // redirect($merge);
            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);
        } else if ($type == "payroll") {

           $pdf_orientation= "portrait";
           $data['data'] =$this->order_model->getpayroll(array('user_id' => $user_id ,'start_date' => $i_date,'end_date' => $e_date));
           $report = 'payroll';

           
            $date     = new DateTime();
            $perpage=38;
            $offset=0;
            $total_page=ceil(sizeof($data['data'])/$perpage);
            $filenames=array();
            
            $data['from']=-1;
            $html = $this->load->view('admin/report/report_hrd_payroll_pdf_v', $data, true);
            $data_pdf = pdf_create($html, '', false,  $pdf_orientation);
            $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_0' . '.pdf'; 
            array_push($filenames,$filename);
            write_file($filename, $data_pdf);
            echo json_encode($filename);
            for($x=0;$x<$total_page;$x++){
              $data['from']=$x*$perpage;
              $data['to']=($x*$perpage)+$perpage;
              if($data['to']>sizeof($data['data']))$data['to']=sizeof($data['data']);
              $html = $this->load->view('admin/report/report_hrd_payroll_pdf_v', $data, true);
              $data_pdf = pdf_create($html, '', false, $pdf_orientation);
              $filename = 'assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis').'_'.($x+1) . '.pdf'; 
              array_push($filenames,$filename);
              write_file($filename, $data_pdf);
              echo json_encode($filename);
            }
            $this->load->library("PDFMerger/PDFMerger");
            $pdf = new PDFMerger;
            foreach($filenames as $file){
              $pdf->addPDF($file, 'all');         
            }
            

            $merge='assets/report/report_' . $report . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis'). '.pdf';
            $pdf->merge('file', $merge);
            
            foreach($filenames as $file){
              unlink($file);
            }
             // redirect($merge);

            $file_url = $merge;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url);



        } else if ($type == "appraisal") {
            $report_name = "Laporan Appraisal";
            $where = array();
            $where = array();
            if (!empty($user_id)) {
                $where['users.id'] = $user_id;
            }

            if (!empty($start_period)) {
                $where['hap.period >='] = $start_period;

            }

            $data['all_data'] = $this->hrd_model->get_report_appraisal($where);
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['users'] = $this->hrd_model->get_one("users", $user_id);
            $report = 'Laporan Appraisal';
            $html = $this->load->view('admin/report/report_hrd_apparisal_pdf_v', $data, true);
        } else if ($type == "employee_schedule") {

            $this->load->model("report_model");
            $pdf_orientation = "portrait";
            $report_name = "Laporan Jadwal Kerja";

            $user_id = $this->input->post("user_id");
            $status = $this->input->post("status");
            $input_date = $this->input->post("start_date");
            $report_end_date = $this->input->post("end_date");
            $user = $this->hrd_model->get_one("users", $user_id);

            $this->data['is_print'] = TRUE;

            $this->data['user'] = $user;
            $this->data['store'] = $store_id;
            $this->data['start_date'] = $input_date;
            $this->data['end_date'] = $report_end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $this->data['results'] = $this->report_model->employee_schedule(array("start_date" => $input_date, "end_date" => $report_end_date, "user_id" => $user_id, "status" => $status));



            $html = $this->load->view('admin/report/report-employee-schedule-pdf', $this->data, true);



        }else if ($type == "attendance_periode") {
            $this->load->model("report_model");
            $report_name = "Laporan Absensi Periode";
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $lists = $this->report_model->report_attendance_periode(array("start_date" => $start_date, "end_date" => $end_date));
            $results = array();
            foreach ($lists as $l) {
                if (!isset($results[$l->id])) {
                    $results[$l->id] = array(
                        "data" => $l,
                        "detail" => array()
                    );
                }
                if (!isset($results[$l->id]['detail'][$l->created_at])) $results[$l->id]['detail'][$l->created_at] = array();
                $results[$l->id]['detail'][$l->created_at] = $l;
            }
            $this->data['results'] = $results;
            $html = $this->load->view('admin/report/report-attendance-periode-pdf', $this->data, true);
        } else if ($type == "attendance_overdue") {
            $this->load->model("report_model");
            $report_name = "Laporan Absensi Keterlambatan";
            $this->data['is_print'] = TRUE;
            $this->data['start_date'] = $start_date;
            $this->data['end_date'] = $end_date;
            $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
            $lists = $this->report_model->report_attendance_overdue(array("start_date" => $start_date, "end_date" => $end_date));
            $results = array();
            foreach($lists as $l){ 
                if (!isset($results[$l->user_id]) ){
                    $results[$l->user_id] = array("name"=>$l->name);
                } 
                if(!isset($results[$l->user_id]['detail'][$l->created_at]))$results[$l->user_id]['detail'][$l->created_at]=array();
                $results[$l->user_id]['detail'][$l->created_at]=$l; 
            }   
            $this->data['results'] = $results;
            $html = $this->load->view('admin/report/report-attendance-overdue-pdf', $this->data, true);  
        }
        $date = new DateTime();
        $data = pdf_create($html, '', false, $pdf_orientation);
        if (in_array($type, array("appraisal","employee_schedule", "attendance_periode","attendance_overdue"))) {
            $filename = 'report_' . $report_name . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            header("Content-type:application/pdf");
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            echo $data;
        } else {
            $filename = 'assets/report/report_' . $report_name . '_' . $date->format('Y-m-d') . '_' . $date->format('Gis') . '.pdf';
            write_file($filename, $data); 
            echo json_encode($filename);
        }

    }

    public function report_schedule()
    {
        $this->load->model("report_model");
        $this->data['title'] = "Laporan Jadwal Kerja";
        $this->data['subtitle'] = "Laporan Jadwal Kerja"; 
        $this->data['users']=$this->hrd_model->get_all_where("users",array("active"=>1,"name !="=>$this->config->config['sync_user_username']));
        $store_id = $this->data['setting']['store_id'];
        $user_id = $this->input->post("user_id");
        $input_date = date('Y-m-d');
        //$report_end_date = date('Y-m-d');
        $user = $this->hrd_model->get_one("users", $user_id);

        $this->data['is_print'] = FALSE;

        $this->data['user'] = $user;
        $this->data['store'] = $store_id;
        $this->data['start_date'] = $input_date;
        //$this->data['end_date'] = $report_end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->employee_schedule(array("start_date" => $input_date, "store_id" => $store_id, "user_id" => $user_id));
        $this->data['content'] .= $this->load->view('admin/report/report-employee-schedule', $this->data, true);
        $this->render('hrd');
    }

    public function get_report_schedule()
    {
        $this->load->model("report_model");

        $store_id = $this->data['setting']['store_id'];
        $user_id = $this->input->post("user_id");
        $status = $this->input->post("status");
        $input_date = $this->input->post("start_date");
        //$report_end_date = $this->input->post("end_date");
        $user = $this->hrd_model->get_one("users", $user_id);


        $this->data['is_print'] = FALSE;

        $this->data['user'] = $user;
        $this->data['store'] = $store_id;
        $this->data['start_date'] = $input_date;
        //$this->data['end_date'] = $report_end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $this->data['results'] = $this->report_model->employee_schedule(array("start_date" => $input_date, "store_id" => $store_id, "user_id" => $user_id, "status" => $status));
        $this->load->view('admin/report/report-employee-schedule-pdf', $this->data);
    }

    public function report_attendance_periode()
    {
        $this->load->model("report_model");
        $this->data['title'] = "Laporan Absensi Periode";
        $this->data['subtitle'] = "Laporan Absensi Periode";

        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $this->load->model("report_model");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $lists = $this->report_model->report_attendance_periode(array("start_date" => $start_date, "end_date" => $end_date));
        $results = array();
        foreach ($lists as $l) {
            if (!isset($results[$l->id])) {
                $results[$l->id] = array(
                    "data" => $l,
                    "detail" => array()
                );
            }
            if (!isset($results[$l->id]['detail'][$l->created_at])) $results[$l->id]['detail'][$l->created_at] = array();
            $results[$l->id]['detail'][$l->created_at] = $l;
        }
        $this->data['results'] = $results;


        $this->data['content'] .= $this->load->view('admin/report/report-attendance-periode', $this->data, true);
        $this->render('hrd');
    }

    public function get_report_attendance_periode()
    {
        $this->load->model("report_model");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->data['is_print'] = FALSE;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['data_store'] = $this->store_model->get_by('store', $this->data['setting']['store_id']);
        $lists = $this->report_model->report_attendance_periode(array("start_date" => $start_date, "end_date" => $end_date));
        $results = array();
        foreach ($lists as $l) {
            if (!isset($results[$l->id])) {
                $results[$l->id] = array(
                    "data" => $l,
                    "detail" => array()
                );
            }
            if (!isset($results[$l->id]['detail'][$l->created_at])) $results[$l->id]['detail'][$l->created_at] = array();
            $results[$l->id]['detail'][$l->created_at] = $l;
        }
        $this->data['results'] = $results;
        $this->load->view('admin/report/report-attendance-periode-pdf', $this->data);
    }
	public function report_attendance_overdue(){
    $this->load->model("report_model");
    $this->data['title']    = "Laporan Absensi Keterlambatan";
    $this->data['subtitle'] = "Laporan Absensi Keterlambatan";

    $start_date=date("Y-m-d");
    $end_date=date("Y-m-d");
    $this->load->model("report_model");

    $this->data['is_print'] = FALSE;
    $this->data['start_date'] =  $start_date;
    $this->data['end_date'] =  $end_date;
    $this->data['data_store'] = $this->store_model->get_by('store',$this->data['setting']['store_id']);
    $lists=$this->report_model->report_attendance_overdue(array("start_date"=>$start_date,"end_date"=>$end_date));
    $results=array();


    foreach($lists as $l){ 
        if (!isset($results[$l->user_id]) ){
             $results[$l->user_id] = array("name"=>$l->name);
          } 
          if(!isset($results[$l->user_id]['detail'][$l->created_at]))$results[$l->user_id]['detail'][$l->created_at]=array();
            $results[$l->user_id]['detail'][$l->created_at]=$l;
           
    }   
     
   
    $this->data['results']=$results;



    $this->data['content'] .= $this->load->view('admin/report/report-attendance-overdue', $this->data, true);
    $this->render('hrd');
  } 
    public function get_report_attendance_overdue(){
    $this->load->model("report_model");
    $start_date=$this->input->post("start_date");
    $end_date=$this->input->post("end_date");
    $this->data['is_print'] = FALSE;
    $this->data['start_date'] =  $start_date;
    $this->data['end_date'] =  $end_date;
    $this->data['data_store'] = $this->store_model->get_by('store',$this->data['setting']['store_id']);
    $lists=$this->report_model->report_attendance_overdue(array("start_date"=>$start_date,"end_date"=>$end_date));
    $results=array();


    foreach($lists as $l){ 
        if (!isset($results[$l->user_id]) ){
             $results[$l->user_id] = array("name"=>$l->name);
          } 
          if(!isset($results[$l->user_id]['detail'][$l->created_at]))$results[$l->user_id]['detail'][$l->created_at]=array();
            $results[$l->user_id]['detail'][$l->created_at]=$l;
           
    }   
     
   
    $this->data['results']=$results;
    $this->load->view('admin/report/report-attendance-overdue-pdf', $this->data);
  }
}