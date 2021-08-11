<?php

class User_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function get_online_users_bygroup(){

		$query = $this->db->query("
      SELECT u.id from users u 
      join users_groups g on u.id = g.user_id
      join feature_access fa on g.group_id=fa.groups_id
      join feature f on fa.feature_id=f.id
      where f.key in ('dinein','checkout') and u.is_login = 1
      group by u.id
    ");
		return $query->result();
	}
  public function get_online_by_group($have_access=""){

		return $this->db->query("
      SELECT u.id,u.name
      from users u 
      inner join users_groups g on u.id = g.user_id
      join feature_access fa on g.group_id=fa.groups_id
      join feature f on fa.feature_id=f.id
			where f.key in (".$have_access.") 
      group by u.id
    ")->result();
	}
	public function get_user_dropdown($id = 0, $group_id = FALSE, $store_id = FALSE)
    {
        $this->db->select('*');
        $this->db->from('users')
        ->join('users_groups ug', 'ug.user_id = users.id');

        if($group_id){
        	$this->db->where('group_id', $group_id);
        }

        if($store_id){
            $this->db->where('store_id', $store_id);
        }


        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = "Semua Kasir";
        foreach ($data as $row) {
            if ($id != 0 && $id == $row->user_id) {
                $results[$row->user_id] = $row->name;
            }
            else {
                $results[$row->user_id] = $row->name;
            }
        }

        return $results;
    }
    public function get_online_all_checker(){
      return $this->db->query("
        SELECT u.id 
        from users u 
        join users_groups g on u.id = g.user_id
        join feature_access fa on g.group_id=fa.groups_id
        join feature f on fa.feature_id=f.id
        where f.key = 'checker' and u.is_login = 1
        group by u.id
      ")->result();
    }
    public function get_user_by_schedule(){
      return $this->db->query("
        SELECT
        *
        FROM
        hr_schedules hs
        JOIN users u ON u.id = hs.user_id

        group by hs.user_id
      ")->result();
    }
    public function get_online_all_kitchen($params=array()){
      return $this->db->query("
        SELECT u.id,u.name,u.outlet_id,o.outlet_name
        from users u 
        join users_groups g on u.id = g.user_id
        join feature_access fa on g.group_id=fa.groups_id
        join feature f on fa.feature_id=f.id
				left join outlet o on u.outlet_id=o.id
        where f.key = 'kitchen' and u.is_login = 1 and u.outlet_id='".$params['outlet_id']."'
        group by u.id
      ")->result();
    }

    public function get_dashboard_access() {
      $this->db->select('u.id,
                          u.`name`,
                          g.`name` AS LEVEL,
                          f.`name` AS feature')
               ->from('users u')
               ->join('users_groups ug', 'ug.user_id = u.id')
               ->join('groups g', 'g.id = ug.group_id')
               ->join('feature_access fa', 'fa.groups_id = g.id')
               ->join('feature f', 'f.id = fa.feature_id')
               ->where('f.id', 8)
               ->where('u.store_id', $this->data['setting']['store_id'])
               ->group_by('u.id')
               ->order_by('u.id', 'ASC');
      return $this->db->get()->result();
    }
}
?>