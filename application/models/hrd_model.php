<?php

class Hrd_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function save_employee_affair($data){
        $this->db->insert('hr_enum_employee_affair', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    function delete_employee_affair($cond){
         $this->db->where($cond);
        $this->db->delete('hr_enum_employee_affair');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    }
    function get_job_history_by_date(){
          $this->db->select('   hjh.*,
                                `u`.`name`,
                                `hjh`.`employee_id`, 
                                `hem`.`during`,
                                `hem`.`next_job`, 
                              DATE_ADD(hjh.start_date,INTERVAL hem.during MONTH)  start_naik_jabatan,
                            DATE_ADD(hjh.start_date,INTERVAL h.during MONTH)  end_naik_jabatan
                                ,h.during as during_parent_id

                            ',false)
            ->from('hr_jobs_history hjh')
            ->join('hr_enum_employee_affair hem','hem.id = hjh.e_affair_id')
            ->join('hr_enum_employee_affair h','hem.next_job = h.id')
            ->join('users u','u.id = hjh.employee_id')
            ->where("hjh.start_date <= curdate()" )
            ->where("curdate() <= hjh.end_date")
            ->where("curdate() >= DATE_ADD(hjh.start_date,INTERVAL hem.during MONTH)")
            ->where("hem.next_job != 0");  
        $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }
    function check_job_history($cond){
          $this->db->select('*')
            ->from('hr_jobs_history hjh')
            ->join('hr_enum_employee_affair hem','hem.id = hjh.e_affair_id')
            ->join('users u','u.id = hjh.employee_id');  
             $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->row();
        return $data;
    } 

    function check_enum_employee_affair($cond){
          $this->db->select('*')
            ->from('hr_jobs_history') ;
             $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->row();
        return $data;
    }

    function get_sisa_hutang($cond){
        $this->db->select('(hr_loan.loan_total/hr_loan.instalment) as instalment_total,sum(hr_repayments.repayment_total)  as repayment_total,
                (hr_loan.loan_total - sum(hr_repayments.repayment_total)) as outstanding_total,
                hr_loan.loan_date,
                hr_loan.loan_total')
            ->from('hr_loan')
            ->join('hr_repayments','hr_loan.id = hr_repayments.loan_id','LEFT');
            $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->row();
        return $data;
    }
    function update_employee_affair($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('hr_enum_employee_affair', $data);
        // $sql = $this->db->last_query();
        // return $sql;
        return ($this->db->affected_rows() > 0);
    } 

    function save_memorandum($data){
        $this->db->insert('hr_memorandum', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    function delete_memorandum($cond){
         $this->db->where($cond);
        $this->db->delete('hr_memorandum');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    }

    function update_memorandum($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('hr_memorandum', $data);
        // $sql = $this->db->last_query();
        // return $sql;
        return ($this->db->affected_rows() > 0);
    }


    function save_salary_component($data){
        $this->db->insert('hr_salary_component', $data);
        $id = $this->db->insert_id();
        return $id;
    }


    function get_salary_component($cond){
        $this->db->select('*');
        $this->db->from('hr_salary_component');
       
        $this->db->where($cond)->order_by("is_static","asc");
        
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    function get_employee_schedule($cond = array() , $user_id){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where($cond);
        if (!empty($user_id)){
        $this->db->where_not_in('users.id', $user_id);
        }
        $this->db->order_by("nip","asc");


        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

     function sum_total_gaji($cond){
        $this->db->select('SUM(hr_jobs_components.value * hr_salary_component.is_enhancer) as total_gaji');
        $this->db->from('hr_jobs_components')
        ->join('hr_salary_component', 'hr_salary_component.id = hr_jobs_components.component_id');
        $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->row();
          return $data;
    }

    function get_jobs_component_by($cond){
        $this->db->select('hr_jobs_components.*,hr_salary_component.is_enhancer,hr_salary_component.id as salary_component_id,hr_salary_component.is_static,hr_salary_component.name as salary_component_name,hr_salary_component.name as component_name,hr_salary_component.key,hr_salary_component.formula_default');
        $this->db->from('hr_jobs_components')
        ->join('hr_salary_component', 'hr_salary_component.id = hr_jobs_components.component_id');
        $this->db->where($cond)->order_by("is_static","asc");
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    function delete_salary_component($cond){
         $this->db->where($cond);
        $this->db->delete('hr_salary_component');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    }  

    function delete_office_hours($cond){
         $this->db->where($cond);
        $this->db->delete('hr_office_hours');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    } 


    function delete_jobs($cond){
         $this->db->where($cond);
        $this->db->delete('hr_jobs');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    } 

    public function delete_jobs_components($cond){
         $this->db->where($cond);
        $this->db->delete('hr_jobs_components');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    } 
    public function get_salary_component_dropdown($cond)
    {
        $this->db->select('*');
        $this->db->from('hr_salary_component');
         $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = 'Pilih Komponen Gaji';
        foreach ($data as $row) {
            $results[$row->id] = str_replace("'","&#39;",$row->name);
        }

        return $results;
    }

     public function get_user_level_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('groups');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array(); 
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id] = $outlet->name;
            }
            else {
                $results[$outlet->id] = $outlet->name;
            }
        }

        return $results;
    }

     public function get_enum_loan_payment_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('hr_enum_loan_payment'); 
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array(); 
        $results[0] = "Pilih Tipe Pembayaran";
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id] = $outlet->name;
            }
            else {
                $results[$outlet->id] = $outlet->name;
            }
        }

        return $results;
    }
    public function get_bank_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('enum_bank');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array(); 
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id] = $outlet->bank_name;
            }
            else {
                $results[$outlet->id] = $outlet->bank_name;
            }
        }

        return $results;
    }


    public function get_outlet_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('outlet');
        $this->db->order_by('outlet_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['" class="chain_cl_0'] = $this->lang->line('ds_choose_outlet');
        foreach ($data as $outlet) {
            if ($id != 0 && $id == $outlet->id) {
                $results[$outlet->id . '" class="select_me chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
            else {
                $results[$outlet->id . '" class="chain_cl_' . $outlet->store_id] = $outlet->outlet_name;
            }
        }

        return $results;
    }

    
    public function get_store()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->order_by('store_name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = $this->lang->line('ds_choose_store');
        foreach ($data as $store) {
            $results[$store->id] = $store->store_name;
        }

        return $results;
    }

    public function get_template_dropdown()
    {
        $this->db->select('*');
        $this->db->from('hr_audit_template');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = "Pilih Template Audit";
        foreach ($data as $store) {
            $results[$store->id] = $store->name;
        }

        return $results;
    }


    public function get_jobs_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('hr_jobs');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array(); 
        foreach ($data as $job) {
            if ($id != 0 && $id == $job->id) {
                $results[$job->id] = $job->jobs_name;
            }
            else {
                $results[$job->id] = $job->jobs_name;
            }
        }

        return $results;
    }


     public function get_employee_affair_dropdown($id = 0)
    {
        $this->db->select('*');
        $this->db->from('hr_enum_employee_affair');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array(); 
        foreach ($data as $e_affair) {
            if ($id != 0 && $id == $e_affair->id) {
                $results[$e_affair->id] = $e_affair->name;
            }
            else {
                $results[$e_affair->id] = $e_affair->name;
            }
        }

        return $results;
    }

     function save_jobs_history($data){
        $this->db->insert('hr_jobs_history', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    function delete_jobs_history($cond){
         $this->db->where($cond);
        $this->db->delete('hr_jobs_history');
        // $sql = $this->db->last_query();
        // echo $sql;
        return ($this->db->affected_rows() > 0);
    }

     function update_jobs_history($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('hr_jobs_history', $data);
        // $sql = $this->db->last_query();
        // return $sql;
        return true;
    } 

    function get_last_jobs_user($id){ 

        $this->db->select('users.nip,hr_jobs.*,hjh.*,hjh.id as job_history_id,users.username,users.name,users.id as user_id');
        $this->db->from('users')
            ->join('hr_jobs_history hjh', 'hjh.employee_id = users.id')
            ->join('hr_jobs', 'hjh.jobs_id = hr_jobs.id'); 
        $where_date = "current_date()>=hjh.start_date AND IF(hjh.end_date is null,1,(current_date<=hjh.end_date)) and users.id = ".$id;
        $this->db->where($where_date); 
        $this->db->order_by("hjh.id","desc");
        $this->db->limit(1);

        $query = $this->db->get();
        $data  = $query->row();

        return $data;
    }


    function get_users_schedules($start_date,$end_date){ 

        $this->db->select('users.id as user_id,hr_schedules.*,hr_schedules.id as hr_schedule_id,hr_schedule_detail.*,hr_schedule_detail.id as hr_schedule_detail_id');
        $this->db->from('users')
            ->join('hr_schedules', 'hr_schedules.user_id = users.id')
            ->join('hr_schedule_detail', 'hr_schedule_detail.schedule_id = hr_schedules.id');  
        $this->db->where("'".$start_date."' BETWEEN hr_schedules.start_date AND hr_schedules.end_date",null,false); 
        $this->db->where("'".$end_date."' BETWEEN hr_schedules.start_date AND hr_schedules.end_date",null,false); 
        $query = $this->db->get();
        
        $data  = $query->result();

        return $data;
    }

    function checking_payroll($cond){
        $this->db->select('*');
        $this->db->from('hr_payroll_history');
           $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    public function get_where($table = '',$where = FALSE)
    {
        $this->db->select('*');
        $this->db->from($table);
        
        if ($where) {
            $this->db->where($where);
        } 
         $query = $this->db->get();
         $data  = $query->result();
        return $data;
    }

     function delete_by($table = '',$where  = false){
         $this->db->where($where);
        $this->db->delete($table); 
        return ($this->db->affected_rows() > 0);
    }

     function get_detail_payroll_history($cond){
        $this->db->select('hr_detail_payroll_history.*,hr_salary_component.id as component_id,hr_salary_component.name as component_name');
        $this->db->from('hr_detail_payroll_history')
        ->join('hr_salary_component', 'hr_salary_component.id = hr_detail_payroll_history.component_id');
        $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    function get_detail_payroll($id){ 
        $this->db->select('users.nip,users.username,users.name,users.id as user_id,hr_jobs.*,hjh.*,');
        $this->db->from('hr_payroll_history hjh')
            ->join('users', 'hjh.user_id = users.id')
            ->join('hr_jobs', 'hjh.jobs_id = hr_jobs.id'); 
       $where = array("hjh.id"=>$id);
        $this->db->where($where);
        $this->db->limit(1);

        $query = $this->db->get();
        $data  = $query->row();

        return $data;
    }

    function get_detail_payroll_history_byuser($id){ 
        $this->db->select('users.nip,sum((hdh.`value` * hsc.is_enhancer)) as total_take_home_pay');
        $this->db->from('users')
            ->join('hr_jobs_history hjh', 'hjh.employee_id = users.id')
            ->join('hr_jobs', 'hjh.jobs_id = hr_jobs.id')
            ->join('hr_payroll_history hph', 'hph.jobs_id = hr_jobs.id')
            ->join('hr_detail_payroll_history hdh', 'hdh.payroll_history_id = hph.id')
            ->join('hr_salary_component hsc', 'hdh.component_id = hsc.id'); 
        $where_date = "NOW() BETWEEN hjh.start_date AND hjh.end_date and users.id = ".$id;
        $this->db->where($where_date); 
        $this->db->limit(1); 
        $query = $this->db->get();
        $data  = $query->row();
       
        return $data;
    }

    function delete_detail_payroll($cond){
         $this->db->where($cond);
        $this->db->delete('hr_detail_payroll_history');
        // $sql = $this->db->last_query();
        // echo $sql;
        return  true;
    }

    function delete_payroll($cond){
         $this->db->where($cond);
        $this->db->delete('hr_payroll_history');
        // $sql = $this->db->last_query();
        // echo $sql;
        return true;
    }

    public function get_office_hours_dropdown()
    {
        $this->db->select('*');
        $this->db->from('hr_office_hours'); 
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = 'Pilih Template Jam Kerja';
        foreach ($data as $row) {
            $results[$row->id] = str_replace("'","&#39;",$row->name);
        }

        return $results;
    }

    function get_standard_schedules_byuser($id){


        $this->db->select('*');
        $this->db->from('hr_schedules')
            ->join('hr_schedule_detail', 'hr_schedule_detail.schedule_id = hr_schedules.id',"LEFT");
        $where = array(
                "user_id"=>$id,
                "is_special_schedule"=>0
        );
        $this->db->where($where);
        $this->db->limit(1);

        $query = $this->db->get();
        $data  = $query->row();

        return $data;
    }

    function get_schedules_where($params=array()){
        return $this->db->query("
          SELECT hs.*,u.name
          from users u
          inner join (
            select hr_schedules.*,hr_schedule_detail.start_time,hr_schedule_detail.end_time
            from hr_schedules
            inner join hr_schedule_detail on hr_schedules.id=hr_schedule_detail.schedule_id
            where current_date()>=hr_schedules.start_date and (hr_schedules.enum_repeat=1 or (current_date()<=hr_schedules.end_date))
            order by hr_schedules.id desc
          ) hs on u.id=hs.user_id
          where u.id='".$params['user_id']."' and hs.start_date<='".$params['date']."'
          group by u.id
          order by u.name asc
        ")->result();
        // $this->db->select('*');
        // $this->db->from('hr_schedules')
        // ->join('hr_schedule_detail', 'hr_schedule_detail.schedule_id = hr_schedules.id',"LEFT");
         
        // $this->db->where($cond);
        // $this->db->group_by("hr_schedules.id");
        // $this->db->order_by("hr_schedules.id","desc");
        // $this->db->limit(1);

        // $query = $this->db->get();
        // $data  = $query->result();

        // return $data;
    }

    function get_taken_total_holidays_byuser($cond){
         $this->db->select('sum(days) as day_total');
        $this->db->from('hr_holidays');
        $this->db->where($cond);
        $this->db->where("enum_holiday_status", 1);
        $this->db->limit(1);

        $query = $this->db->get();
        $data  = $query->row();

        return $data;
    }

    public function get_user_dropdown($cond = array())
    {

        $this->db->select('*');
        $this->db->from('users');
        $this->db->order_by('name', 'ASC');
        
        if($cond){
            $this->db->where($cond);
        }
        $this->db->where('name !=', $this->config->config['sync_user_username']);
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = 'Pilih Karyawan';
        foreach ($data as $row) {
            $results[$row->id] = str_replace("'","&#39;",$row->name);
        }

        return $results;
    }
    function get_holidays_by_id($cond){
        $this->db->select('*');
        $this->db->from('hr_holidays');
           $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->row();
          return $data;
    }
    function update_holidays($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('hr_holidays', $data);
        // $sql = $this->db->last_query();
        // return $sql;
        return ($this->db->affected_rows() > 0);
    } 
    public function get_enum_attendance_dropdown()
    {
        $this->db->select('*');
        $this->db->from('hr_enum_status_attendance');
          $this->db->where('id !=', "2");
          //yang bukan terlambat
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = 'Pilih Status';
        foreach ($data as $row) {
            $results[$row->id] = str_replace("'","&#39;",$row->name);
        }

        return $results;
    }
    public function get_enum_attendance()
    {
        $this->db->select('*');
        $this->db->from('hr_enum_status_attendance');
       
        $query = $this->db->get();
        $data  = $query->result();
 
        return $data;
    }
   public function get_enum_attendance_byid($id){
        $this->db->select('*');
        $this->db->from('hr_enum_status_attendance');
           $this->db->where("id",$id);
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    public function get_attendance_statistic_byuser($user_id,$enum_status_attendance){
        $query = "
					select m.name,count(hra.id) as total_days
					from hr_enum_months m
					left join (
						select * from hr_attendances group by user_id,created_at,enum_status_attendance
					) hra on MONTH(hra.created_at)=m.id and hra.user_id='".$user_id."' and hra.enum_status_attendance='".$enum_status_attendance."'
					group by m.id
					order by m.id asc
        " ;
       return $this->db->query($query)->result();
    }

    public function get_attendance_statistic($enum_status_attendance){
        $query = "select b.name,(select count(user_id) 
                                    from hr_attendances 
                                    where enum_status_attendance = ".$enum_status_attendance."
                                    and MONTH(created_at) = b.id 
                                    ) as total_days from hr_attendances a
            right   join
            hr_enum_months b
            on(MONTH(a.created_at)= b.id &&  (year(a.created_at) = year(curdate())) ) 
            group by MONTH(a.created_at) ,b.id  
            order by b.id  ASC
        " ;
       return $this->db->query($query)->result();
    }
    function get_taken_total_reimburse_byuser($cond){
         $this->db->select('sum(total) as total');
        $this->db->from('hr_reimburse');
        $this->db->where($cond); 
        $this->db->limit(1);

        $query = $this->db->get();
        $data  = $query->row();

        return $data;
    }

    function get_enum_repayment_method($id){
        $this->db->select('*');
        $this->db->from('hr_enum_repayment_method');
           $this->db->where("id",$id);
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    function get_attendance_today_by_user($id){
      $this->db->select('*');
      $this->db->from('hr_attendances');
      $this->db->where("user_id",$id);
      $this->db->where("created_at = curdate()");
      $query = $this->db->get();
      $data  = $query->row();
      return $data;
    }
     function del_attendance_today($id){
      
        $this->db->where("user_id", $id);
        $this->db->where("created_at = curdate()");
        $this->db->delete('hr_attendances');
      
         return ($this->db->affected_rows() >= 0);
    }

    function get_jobs_payroll_history($period,$status,$jobs){
        $this->db->select(' users.name,users.id,store.store_name, hr_jobs.jobs_name,
                            hph.period, sum(hdp.value * hsc.is_enhancer) as payroll_total');
        $this->db->from('hr_jobs_history hjh') 
        ->join('users', 'users.id = hjh.employee_id')
        ->join('hr_jobs', 'hr_jobs.id = hjh.jobs_id')
        ->join('store', 'users.store_id = store.id')
        ->join('hr_payroll_history hph', 'hjh.id = hph.job_history_id')
        ->join('hr_detail_payroll_history hdp', 'hph.id = hdp.payroll_history_id') 
        ->join('hr_salary_component hsc', 'hsc.id = hdp.component_id') ;  
        $this->db->where("hph.period",$period);
        $this->db->where("(hjh.e_affair_id IN (".$status.") or hjh.jobs_id IN (".$jobs."))");
         $this->db->group_by('users.id ');
        $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }
    function get_print_payroll_history($params=array()){
        $this->db->select('hjh.employee_id,users.name,hr_jobs.jobs_name,hsc.name as component_name,hsc.is_enhancer, hdp.value,hsc.key,hsc.formula_default,hee.background_color,hee.name as employee_affair_name');
        $this->db->from('users') 
        ->join('(
          select * from hr_jobs_history group by employee_id
        )as hjh','hjh.employee_id=users.id','left')
				->join('hr_enum_employee_affair hee','hjh.e_affair_id=hee.id')
        ->join('hr_jobs', 'hr_jobs.id = hjh.jobs_id')
        ->join('store', 'users.store_id = store.id')
        ->join('hr_payroll_history hph', 'hjh.id = hph.job_history_id')
        ->join('hr_detail_payroll_history hdp', 'hph.id = hdp.payroll_history_id','left') 
        ->join('hr_salary_component hsc', 'hsc.id = hdp.component_id','left');  
        $this->db->where("hph.period",$params['periode']);
        $this->db->where(($params['status']!="" ? "(hjh.e_affair_id IN (".$params['status'].")" : "(hjh.e_affair_id!=''")." and ".($params['jobs']!="" ? "hjh.jobs_id IN (".$params['jobs']."))" : "hjh.jobs_id!='')"));
        if(isset($params['id']) && $params['id']!=""){
          $this->db->where("hph.id",$params['id']);
        }
        $this->db->order_by("hsc.is_static,hsc.id","ASC");
        $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }


     function get_user_job($status,$jobs){
        $this->db->select('users.active, users.name,users.id,store.store_name, 
                        hr_jobs.jobs_name,hjh.id as job_history_id,hjh.*,hee.background_color,hee.name as employee_affair_name');
        $this->db->from('hr_jobs_history hjh') 
				->join('hr_enum_employee_affair hee','hjh.e_affair_id=hee.id')
        ->join('users', 'users.id = hjh.employee_id')
        ->join('hr_jobs', 'hr_jobs.id = hjh.jobs_id')
        ->join('store', 'users.store_id = store.id');  
        $this->db->where("users.active ",1);
        $this->db->where(($status!="" ? "(hjh.e_affair_id IN (".$status.")" : "(hjh.e_affair_id!=''")." and ".($jobs!="" ? "hjh.jobs_id IN (".$jobs."))" : "hjh.jobs_id!='')"));
        $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }

    function get_all_jobs($cond = false){
        $this->db->select('*');
        $this->db->from('hr_jobs'); 
        if($cond) $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    function get_all_employee_affair($cond = false){
        $this->db->select('*');
        $this->db->from('hr_enum_employee_affair'); 
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    }

    function get_all_template_appraisals($cond = false){
          $this->db->select('*');
        $this->db->from('hr_appraisal_template'); 
         if($cond) $this->db->where($cond);
        $query = $this->db->get();
        $data  = $query->result();
          return $data;
    } 

    public function get_template_appraisal_dropdown()
    {
        $this->db->select('*');
        $this->db->from('hr_appraisal_template');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results    = array();
        $results[0] = "Pilih Template Appraisal";
        foreach ($data as $store) {
            $results[$store->id] = $store->name;
        }

        return $results;
    }

    function get_appraisal_process_byid($process_id){
        $this->db->select('users.name, ap.*');
        $this->db->from('hr_appraisal_process ap') 
        ->join('users', 'users.id = ap.user_id') ;
        $this->db->where_in("ap.id ",$process_id); 
        $query = $this->db->get();
        $data  = $query->row();
        return $data;
    }

     function get_appraisal_percentage_process_byid($process_id){
       $query = "   
                select sum(c.value) grade, 
                        sum(hadc.point) as max
                from 
                hr_appraisal_process a 
                join hr_appraisal_process_detail b on(a.id  =b.appraisal_process_id)
                join hr_appraisal_process_detail_category c on(b.id = c.appraisal_process_detail_id)
                join hr_appraisal_detail_category hadc ON (hadc.id = c.detail_category_id)
                where a.id = $process_id";
        return $this->db->query($query)->row();
    }

    function get_grade_appraisal($category_id){
        $query = "   
              select * from hr_appraisal_detail_category a join hr_appraisal_process_detail_category b
            on(a.id = b.detail_category_id) where a.appraisal_category_id = $category_id";
        return $this->db->query($query)->result();
    }

    function get_audit_process_byid($process_id){
        $this->db->select('store.store_name, ap.*');
        $this->db->from('hr_audit_process ap') 
        ->join('store', 'store.id = ap.store_id') ;
        $this->db->where_in("ap.id ",$process_id); 
        $query = $this->db->get();
        $data  = $query->row();
        return $data;
    }

     function get_audit_percentage_process_byid($process_id){
        $query = "   
                select sum(c.value) grade, 
                        (select sum(point) from hr_audit_template_detail_category where category_id = b.category_id )
                        as max
                from 
                hr_audit_process a 
                join hr_audit_process_detail b on(a.id  =b.audit_process_id)
                join hr_audit_process_detail_category c on(b.id = c.audit_process_detail_id)

                where a.id = $process_id";
        return $this->db->query($query)->row();
    }

    function get_grade_audit($category_id){
        $query = "   
              select * from hr_audit_template_detail_category a join hr_audit_process_detail_category b
            on(a.id = b.detail_category_id) where a.audit_category_id = $category_id";
        return $this->db->query($query)->result();
    }

    function get_report_attendance($cond){
        $this->db->select('u.id as userid, s.store_name as sname,u.name as name,
                                    date(ha.created_at) as curdate,
                                    hsd.start_time,
                                    hsd.end_time,
                                    ha.checkin_time,
                                    ha.checkout_time,
                                    FLOOR(sum(TIME_TO_SEC(IFNULL(ha.checkout_time,hsd.end_time)) - TIME_TO_SEC(ha.checkin_time))/3600) spent_hour, 
                                    FLOOR(sum(TIME_TO_SEC(IFNULL(ha.checkin_time,0)) - TIME_TO_SEC(hsd.start_time))/3600) late_total,
                                    FLOOR(sum(TIME_TO_SEC(IFNULL(ha.checkout_time,ha.over_checkout_time)) - TIME_TO_SEC(hsd.end_time))/3600) overtime_total
                                ',false)
        ->from('users u') 
        ->join('store s', 's.id = u.store_id')
        ->join('hr_schedules hs', 'hs.user_id = u.id', 'left')
        ->join('hr_schedule_detail hsd', 'hsd.schedule_id = hs.id', 'left')
        ->join('hr_attendances ha', 'ha.user_id = u.id')
        ->join('hr_enum_status_attendance hesa', 'hesa.id=ha.enum_status_attendance', 'left')
        ->where($cond) 
		->group_by("u.id,date(ha.created_at)")
		->order_by("date(ha.created_at),u.name","asc");
        $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }
		
    function get_report_attendance_detail($cond){
        $this->db->select('u.name,ha.created_at,hsd.start_time,hsd.end_time,ha.checkin_time,ha.checkout_time,hesa.name as status,ha.note')
        ->from('users u')
        ->join('store s', 'u.store_id = s.id')
        ->join('hr_schedules hs', 'hs.user_id = u.id', 'left')
        ->join('hr_schedule_detail hsd', 'hsd.schedule_id = hs.id', 'left')
        ->join('hr_attendances ha', 'ha.user_id = u.id')
        ->join('hr_enum_status_attendance hesa', 'hesa.id=ha.enum_status_attendance', 'left')
        ->where($cond) 
        ->group_by("u.id,date(ha.created_at)")
        ->order_by("date(ha.created_at),u.name","asc");
         $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }
    function get_report_payroll($cond){
       $this->db->select("u.`name` as pname,
    hph.id,
    hj.jobs_name,
    SUBSTR(
        hph.period,
        4,4) AS years,
    SUBSTR(
            hph.period,
            1,2) AS months,
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
            ->join('users u ', 'hph.user_id = u.id')
            ->where($cond);
         $query = $this->db->get();
        $data  = $query->result();
        return $data;
    }

    function get_report_appraisal($cond){
        $this->db->select('hap.id as appraisal_process_id,
                                users.name,hap.created_at, hat.name as template_name,hap.description,
                                hap.period,sum(hc.value) as total_nilai,

                                sum((select point from hr_appraisal_detail_category 
                                where id = hc.detail_category_id)) as max_nilai

                                ')
            ->from('hr_appraisal_process hap') 
            ->join('hr_appraisal_process_detail hd','hap.id = hd.appraisal_process_id')
            ->join('hr_appraisal_process_detail_category hc','hd.id = hc.appraisal_process_detail_id') 
            ->join('users','hap.user_id = users.id')
            ->join('hr_appraisal_template hat','hap.template_id = hat.id')
            ->where($cond)
            ->group_by("users.id") ;
             $query = $this->db->get();
        $data  = $query->result();
        return $data;
        
    }
    function get_payroll_static_data($params=array())
    {
      $insentive=$this->db->query("
        select 
          hrjc.value
        from hr_jobs_components hrjc
        inner join hr_salary_component hrsc on hrjc.component_id=hrsc.id
        where hrsc.key='insentif' and hrjc.job_id=(
          select jobs_id from
          hr_jobs_history where employee_id='".$params['user_id']."' and current_date()>=start_date and IF(end_date is null,1=1,current_date()<=end_date) 
          order by id desc limit 0,1
        )
      ")->row();
			$present=$this->db->query("
			select sum(a.total) as total from(
        select 
          count(*)  as total
        from hr_attendances ha
        where ha.user_id='".$params['user_id']."' and ha.created_at>='".$params['start_date']."' and ha.created_at<='".$params['end_date']."'
        and ha.enum_status_attendance in (1,7)
				group by ha.created_at 
				) a 
      ")->row();
			$present_sunday=$this->db->query("
        select 
          count(*) as total
        from hr_attendances ha
        where ha.user_id='".$params['user_id']."' and ha.created_at>='".$params['start_date']."' and ha.created_at<='".$params['end_date']."'
        and ha.enum_status_attendance in (1) and dayname(ha.created_at)='Sunday'
				group by ha.created_at
      ")->row();
      $late_1=$this->db->query("
        SELECT
          count(*) as total
        FROM (users)
        inner JOIN store ON users.store_id = store.id
        inner JOIN hr_schedules hs ON hs.user_id = users.id
        AND(
          IF(hs.enum_repeat = 1,'".$params['start_date']."' >= hs.start_date,'".$params['start_date']."' >= hs.start_date AND '".$params['end_date']."' <= hs.end_date)
        )
        inner JOIN hr_schedule_detail hsd ON hsd.schedule_id = hs.id
        inner JOIN hr_attendances ha ON ha.user_id = users.id
        where users.id='".$params['user_id']."' AND ha.created_at >= '".$params['start_date']."' AND ha.created_at <= '".$params['end_date']."'
        and ha.checkin_time>DATE_ADD(hsd.start_time,INTERVAL (select value from hr_setting where name='max_late') minute)
        and ha.checkin_time<=DATE_ADD(hsd.start_time,INTERVAL (select value from hr_setting where name='max_late')+10 minute)
				group by ha.created_at
      ")->row();
      $late_2=$this->db->query("
        SELECT
          count(*) as total
        FROM (users)
        inner JOIN store ON users.store_id = store.id
        inner JOIN hr_schedules hs ON hs.user_id = users.id
        AND(
          IF(hs.enum_repeat = 1,'".$params['start_date']."' >= hs.start_date,'".$params['start_date']."' >= hs.start_date AND '".$params['end_date']."' <= hs.end_date)
        )
        inner JOIN hr_schedule_detail hsd ON hsd.schedule_id = hs.id
        inner JOIN hr_attendances ha ON ha.user_id = users.id
        where users.id='".$params['user_id']."' AND ha.created_at >= '".$params['start_date']."' AND ha.created_at <= '".$params['end_date']."'
        and ha.checkin_time>DATE_ADD(hsd.start_time,INTERVAL (select value from hr_setting where name='max_late')+10 minute)
				group by ha.created_at
      ")->row();
      $permission_go_home=$this->db->query("
        select 
          count(*) as total
        from hr_attendances ha
        where ha.user_id='".$params['user_id']."' and ha.created_at>='".$params['start_date']."' and ha.created_at<='".$params['end_date']."'
        and ha.enum_status_attendance=7
				group by ha.created_at
      ")->row();
      $permission_alpha=$this->db->query("
        select 
          count(*) as total
        from hr_attendances ha
        where ha.user_id='".$params['user_id']."' and ha.created_at>='".$params['start_date']."' and ha.created_at<='".$params['end_date']."'
        and ha.enum_status_attendance=3
				group by ha.created_at
      ")->row();
      $diff=date_diff(new DateTime($params['end_date']),new DateTime($params['start_date']));
      $attendance=$this->db->query("
	    select sum(a.total) as total from (
        select 
          IF(count(id)>0,1,0) as total
        from hr_attendances ha
        where ha.enum_status_attendance>0 and ha.user_id='".$params['user_id']."' and ha.created_at>='".$params['start_date']."' and ha.created_at<='".$params['end_date']."'
        
        
		group by ha.created_at
		) a
      ")->row();
	  //and DATE_FORMAT(ha.created_at,'%a') not in (select day from hr_employee_holidays where user_id='".$params['user_id']."')
	  //and (select count(*) from hr_holidays where user_id='".$params['user_id']."' and ha.created_at>=start_date and ha.created_at<=end_date)=0
      $start=$params['start_date'];
      $employee_holidays=$this->db->query("select * from hr_employee_holidays where user_id='".$params['user_id']."'")->result();
      $employee_off_works=$this->db->query("select * from hr_holidays where user_id='".$params['user_id']."'")->result();
      $days=array();
      foreach($employee_holidays as $e){
        array_push($days,$e->day);
      }
      $holidays=0;
      $off_works=0;
      while(strtotime($start)<=strtotime($params['end_date'])){
        if(in_array(date("D",strtotime($start)),$days)){
          $holidays++;
        }
        $check=false;
        foreach($employee_off_works as $e){
          if(strtotime($start)>=strtotime($e->start_date) and strtotime($start)<=strtotime($e->end_date))$check=true;
        }
        if($check==true){
          $off_works++;
        }
        $start=date("Y-m-d",strtotime($start." +1 day"));
      }
	  $result=array(
        "insentive"=>(sizeof($insentive)>0 ? $insentive->value : 0),
        "present"=>(empty($present) ? 0 : $present->total),
        "present_sunday"=>(empty($present_sunday) ? 0 : $present_sunday->total),
        "late_1"=>(empty($late_1) ? 0 : $late_1->total),
        "late_2"=>(empty($late_2) ? 0 : $late_2->total),
        "permission_go_home"=>(empty($permission_go_home) ? 0 : $permission_go_home->total),
        "permission_alpha"=>(empty($permission_alpha) ? 0 : $permission_alpha->total),
        "alpha"=>($diff->days+1)-(empty($attendance) ? 0 : $attendance->total),
      );
      return $result;
    }
    function generate_resign_number()
    {
      $check=$this->db->query("
        select*from hr_resign where resign_number like '%".date("m/Y")."'
        order by resign_number DESC limit 0,1
      ")->row();
      if(sizeof($check)==0){
        $number="SK/HRD/001"."-".date("m")."/".date("Y");
      }else{
        $number="SK/HRD/";
        $temp=str_replace("SK/HRD/","",$check->resign_number);
        $temp=str_replace("-".date("m")."/".date("Y"),"",$temp);
        $counter=(int)$temp+1;
        $number.=substr("000",0,3-strlen($counter)).$counter;
        $number.="-".date("m")."/".date("Y");
      }
      return $number;
    }
    function get_data_resign($user_id=""){
      return $this->db->query("
        select u.name,u.gender,hrj.jobs_name,hrr.resign_number,hrr.date,
        (
          select start_date from
          hr_jobs_history where employee_id='".$user_id."' and current_date()>=start_date
          order by id asc limit 0,1
        ) as start
        from users u
        inner join hr_jobs_history hrjh on hrjh.jobs_id=(
          select jobs_id from
          hr_jobs_history where employee_id='".$user_id."' and current_date()>=start_date 
          order by id desc limit 0,1
        )
        inner join hr_jobs hrj on hrjh.jobs_id=hrj.id
        inner join hr_resign hrr on u.id=hrr.user_id
        where u.id='".$user_id."'
      ")->row();
    }

    function get_detail_employee_office_hour_1($params=array())
    {
      return $this->db->query("SELECT
    hs.user_id,
    u.name,
    hsd.start_time,
    hsd.end_time,
    hs.start_date,
    hs.end_date
FROM
    hr_schedules hs
INNER JOIN hr_schedule_detail hsd ON hsd.schedule_id = hs.id
INNER JOIN hr_office_hours hoh ON hsd.office_hour_id = hoh.id
INNER JOIN users u ON hs.user_id = u.id
WHERE
hoh.id = '".$params['office_hour_id']."'
GROUP BY
    hs.id,
    hs.user_id
ORDER BY
    hs.start_date ASC
    ")->result();
    }
    function get_detail_employee_office_hour($params=array())
    {
      return $this->db->query("
        select hs.user_id,u.name,hsd.start_time,hsd.end_time,hs.start_date,hs.end_date,
        (
          select hj.jobs_name
          from hr_jobs_history hjh
          inner join hr_jobs hj on hjh.jobs_id=hj.id
          where hjh.employee_id=u.id
          order by hjh.start_date desc
                    limit 0,1
        ) as jobs_name
        from hr_schedules hs
        inner join hr_schedule_detail hsd on hsd.schedule_id=hs.id
        inner join hr_office_hours hoh on hsd.office_hour_id=hoh.id
        inner join users u on hs.user_id=u.id
        where 
                date(hs.start_date)=(
                    select b.start_date
                    from hr_schedule_detail a
                    inner join hr_schedules b on a.schedule_id=b.id
                    where office_hour_id='".$params['office_hour_id']."'
                    order by b.end_date DESC
                    limit 0,1
                ) and 
                date(hs.end_date)=(
                    select b.end_date
                    from hr_schedule_detail a
                    inner join hr_schedules b on a.schedule_id=b.id
                    where office_hour_id='".$params['office_hour_id']."'
                    order by b.end_date DESC
                    limit 0,1
                )
                and hoh.id='".$params['office_hour_id']."'
                and hs.start_date = (
                    select b.start_date
                    from hr_schedule_detail a
                    inner join hr_schedules b on a.schedule_id=b.id
                    where b.user_id=u.id
                    order by b.end_date DESC
                    limit 0,1
                )
        group by hs.id,hs.user_id
                order by hs.start_date asc
      ")->result();
    }
    function get_employee_holidays($params=array())
    {
      $this->db->select("u.id,u.name")
      ->from("hr_employee_holidays heh")
      ->join("users u","heh.user_id=u.id")
      ->where("heh.day",$params['day']);
      return $this->db->get()->result();
    }
    function get_office_hour_active($params=array())
    {
      $parameter="";
      if($params['office_hour_id']!=""){
        $parameter.=" hs.office_hour_id='".$params['office_hour_id']."'";
      }
      if(isset($params['user_id']) && $params['user_id']!=""){
        if($parameter!="")$parameter.=" and ";
        $parameter.=" u.id='".$params['user_id']."'";
      }
      $date=date("Y-m-d");
      if(isset($params['date']) && $params['date']!=""){
        $date=$params['date'];
      }
      if($parameter!="")$parameter=" where ".$parameter;
      return $this->db->query("
        SELECT hs.*,u.name
        from users u
        inner join (
          select hr_schedules.*,hr_schedule_detail.office_hour_id,hr_schedule_detail.start_time,hr_schedule_detail.end_time
          from hr_schedules
          inner join hr_schedule_detail on hr_schedules.id=hr_schedule_detail.schedule_id
          inner join hr_office_hours on hr_schedule_detail.office_hour_id=hr_office_hours.id
          where '".$date."'>=hr_schedules.start_date and '".$date."'<=hr_schedules.end_date
          order by hr_schedules.id desc
        ) hs on u.id=hs.user_id
      ".$parameter."
        group by u.id
        order by u.name asc
      ")->result();
    }
    function get_user_loans($params=array()){
      return $this->db->query("
        select l.*,sum(r.repayment_total) as total_payment
        from hr_loan l
        left join hr_repayments r on l.id=r.loan_id
        where l.payment_option!=3 and l.user_id='".$params['user_id']."'
        group by l.id
      ")->result();
    }
    function get_payroll_histories($user_id=""){
      return $this->db->query("
        SELECT e.name,a.id,c.store_name,d.jobs_name,a.period,
        sum(IF(hsc.`key` NOT IN('late_1','late_2','permission_go_home','permission_alpha','alpha'),b.VALUE*hsc.is_enhancer,0))-
        IF(sum(IF(hsc.`key` IN('late_1','late_2','permission_go_home','permission_alpha','alpha'),b.VALUE*hsc.is_enhancer *-1,0))>(
            SELECT hr_detail_payroll_history.`value` FROM hr_detail_payroll_history
            INNER JOIN hr_salary_component ON hr_salary_component.id = hr_detail_payroll_history.component_id
            WHERE payroll_history_id = a.id AND `key` = 'insentif'
          ),
          (
            SELECT hr_detail_payroll_history.`value` FROM hr_detail_payroll_history
            INNER JOIN hr_salary_component ON hr_salary_component.id = hr_detail_payroll_history.component_id
            WHERE payroll_history_id = a.id AND `key` = 'insentif'
          ),
          sum(IF(hsc.`key` IN('late_1','late_2','permission_go_home','permission_alpha','alpha'),b.VALUE* hsc.is_enhancer *- 1,0))
        )AS payroll_total,
        (SELECT sum(total) FROM hr_reimburse WHERE user_id = e.id )AS total_reimburse,
        (SELECT sum(loan_total) FROM hr_loan WHERE user_id = e.id)AS total_loan
        FROM (`hr_payroll_history` a)
        JOIN `hr_detail_payroll_history` b ON `a`.`id` = `b`.`payroll_history_id`
        JOIN `hr_salary_component` hsc ON `hsc`.`id` = `b`.`component_id`
        JOIN `hr_jobs` d ON `d`.`id` = `a`.`jobs_id`
        JOIN `store` c ON `c`.`id` = `d`.`store_id`
        JOIN `users` e ON `a`.`user_id` = `e`.`id`
        WHERE a.user_id='".$user_id."'
        GROUP BY `a`.`user_id`
        ORDER BY e.`name` DESC
      ")->result();
    }
    function get_jobs_histories($user_id=""){
      return $this->db->query("
        SELECT
          `a`.`id` AS id,
          `b`.`name` AS status_name,
          `a`.`start_date`,
          `a`.`end_date`,
          `d`.`jobs_name`,
          `c`.`store_name`,
          `reimburse`,
          `vacation`
        FROM
          (`hr_jobs_history` a)
        JOIN `hr_jobs` d ON `d`.`id` = `a`.`jobs_id`
        JOIN `users` ON `users`.`id` = `a`.`employee_id`
        JOIN `hr_enum_employee_affair` b ON `a`.`e_affair_id` = `b`.`id`
        JOIN `store` c ON `c`.`id` = `a`.`store_id`
        WHERE `users`.`id` = '".$user_id."'
        ORDER BY `status_name` ASC
      ")->result();
    }
	//DAFTAR APAKAH USER SUDAH MEMPUNYAI JADWAL 
	function checking_schedule($params=array()){
		return $this->db->query("
			select u.name,hrs.*
			from hr_schedules hrs
			inner join users u on hrs.user_id=u.id
			inner join hr_schedule_detail hrsd on hrs.id=hrsd.schedule_id
			where hrs.user_id='".$params['user_id']."' and 
			IF(hrs.enum_repeat=1,
				hrs.start_date<='".$params['start_date']."'
			,
				(
					(hrs.start_date<='".$params['start_date']."' and '".$params['start_date']."'<=hrs.end_date)
					or 
					(hrs.start_date<='".$params['end_date']."' and '".$params['end_date']."'<=hrs.end_date)
				)
			)
		")->result();
	}
    //AMBIL DATA JAM KERJA ROLLING
    function get_office_hour_target($params=array())
    {
        return $this->db->query("
            select b.*
            from hr_office_hour_rolling a
            inner join hr_office_hours b on a.office_hour_target_id=b.id
            where a.office_hour_id='".$params['office_hour_id']."'
        ")->result();
    }

    function get_schedule_start_date($user_id)
    {
        return $this->db->query("
            select *
            from hr_schedules a
            where a.user_id='".$user_id."'
        ")->result();
    }
    function get_schedule_user_start_date($user_id,$start_date)
    {
        return $this->db->query("
            1select *
            from hr_schedules a
            where a.user_id='".$user_id."' and a.start_date='".$start_date."'
        ")->result();
    }

	//function for get attend, sick, leave, permission
	function get_attend($start_date, $end_date, $user_id, $type)
	{
        //get all attend day
        if ($type == 1) {
            $query = "select count(IF(enum_status_attendance = 1,1,NULL )) as attend from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at)  and user_id ='".$user_id."'";
        } elseif ($type == 2) { //get all sick day
            $query = "select count(IF(enum_status_attendance = 4,1,NULL )) as attend from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at)  and user_id ='".$user_id."'";
        } elseif ($type == 3) { //get all leave day
            $query = "select count(IF(enum_status_attendance = 6,1,NULL )) as attend from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at)  and user_id ='".$user_id."'";
        } elseif ($type == 4) { //get all permission day
            $query = "select count(IF(enum_status_attendance = 3,1,NULL )) as attend from hr_attendances where date(created_at) >= ".$start_date." and ".$end_date." <=  date(created_at)  and user_id ='".$user_id."'";
        }
        $result = $this->db->query($query)->row();
        foreach ($result as $value) {
            return $value;
        }
		return false;
	}
    function get_user_shift($user_id){
        return $this->db->query("
            SELECT
                hs.id,
                hs.user_id,
                hs.start_date,
                hs.end_date,
                hs.enum_repeat,
                hsd.schedule_id,
                hsd.start_time,
                hsd.end_time,
                hsd.office_hour_id
            FROM
                (`users` u)
            JOIN `hr_schedules` hs ON `hs`.`user_id` = `u`.`id`
            JOIN `hr_schedule_detail` hsd ON `hsd`.`schedule_id` = `hs`.`id`
            JOIN `hr_office_hours` ho ON `ho`.`id` = `hsd`.`office_hour_id`
            WHERE
                `u`.`id` ='".$user_id."'
        ")->result();
    }
}