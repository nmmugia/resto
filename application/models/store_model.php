<?php

class Store_model  extends MY_Model{

	private $_table = 'store';

	function __construct(){
        parent::__construct();
    }

    
	public function get_store($store_id)
    {
		$this->db->select('*')
		->from('store')
        ->where('id',$store_id);
        return $this->db->get()->result();
	}	
	public function get_all_store()
    {
		$this->db->select('*')
		->from('store');
        return $this->db->get()->result();
	}	
	public function get_all_account()
    {
		$this->db->select('*')
		->from('account');
        return $this->db->get()->result();
	}	

	public function get_outlet($outlet_id)
    {
		$this->db->select('*')
		->from('outlet')
        ->where('id',$outlet_id);
        return $this->db->get()->result();
	}
	
	/**
	 * Get outlets with spesific condition
	 * @param  mixed $cond Condition for query in a form of array where each key is the field's name and the value is the condition
	 * @return mixed       Array of outlets which satisfy the condition
	 */
	public function get_outlets($cond)
	{
		$this->db->select('*')
		->from('outlet')
		->where($cond);
		return $this->db->get()->result();
	}

	public function get_outlets_from_warehouse($cond)
	{
		$this->db->select('*')
		->from('outlet')
		->where($cond)
		->where('is_warehouse',1);
		return $this->db->get()->result();
	}

	public function get_all_outlet()
    {
		$this->db->select('*,outlet.id as outlet_id')
		->from('outlet')->join('store','outlet.store_id = store.id');
        return $this->db->get()->result();
	}
	public function get_store_outlet_by_menu($menu_id)
	{
		$outlet_id = $this->db->select('category.outlet_id')->from('menu')
					->join('category','menu.category_id = category.id')->where('menu.id',$menu_id)->get()->row();
		if($outlet_id){
			$outlet_id = $outlet_id->outlet_id;
			$this->db->select('*')
			->from('outlet')->join('store','outlet.store_id = store.id')
			->where('outlet.id',$outlet_id);
			return $this->db->get()->row();
		}
		
        return false;
		
	}
	public function get_floor_by_store($store_id)
    {
		$this->db->select('*')
		->from('floor')
        ->where('store_id',$store_id)
        ->where('is_active', 1);
        return $this->db->get()->result();
	}
	
	public function get_table_all($store_id)
    {
		$this->db->select('table.*, table.id as table_id')
			->from('table')
			->join('floor','table.floor_id = floor.id')
			->join('store','floor.store_id = store.id')
			->where('store.id',$store_id)
			->where('table.is_active',1);
		
        return $this->db->get()->result();
	}
	
	public function get_table_by_floor($store_id,$floor_id)
    {
		$this->db->select('table.*, table.id as table_id, floor.floor_name, enum_table_status.*')
			->from('table')
			->join('floor','table.floor_id = floor.id')
			->join('store','floor.store_id = store.id')
			->join('enum_table_status','table.table_status = enum_table_status.id')
			->where('store.id',$store_id)
			->where('floor.id',$floor_id)
			->where('table.is_active', 1)
			->where('floor.is_active', 1)
			->order_by('length(table_name), table_name',false);
		
        return $this->db->get()->result();
	}
	
	public function get_order_by_table($table_id)
    {
      $this->db->select('order.id as order_id,order.reservation_id')
			->from('order')
      ->join("reservation","order.reservation_id=reservation.id","left")
			->where('order.table_id',$table_id)
			->where('order.order_status',0)
			->where('date(order.start_order) <=',"'".date("Y-m-d")."'",false)
      ->where('IF(IFNULL(order.reservation_id,0)!=0,IF(reservation.status_posting=1,true,false),true)')
			->order_by('order.id', 'asc');
      return $this->db->get()->row();
	}
	
	public function update_status_table($table_id,$data)
    {		
		$this->db->where('id', $table_id)
		->update('table', $data); 
				
		return ($this->db->affected_rows() > 0);
	}

	function get_store_dropdown(){
		$data = $this->get_all_store();
    
		$results    = array();
    	$results[0]="Pilih Resto";
		foreach ($data as $row) {
			$results[$row->id] = $row->store_name;
		}

		return $results;

	}

	function get_store_dropdown1($store){
		$data = $this->db->select("*")
        ->where('id', $store)
        ->order_by('store_name', 'ASC')
        ->get('store')->result();
    
		$results    = array();
    	$results[0]="Pilih Resto";
		foreach ($data as $row) {
			$results[$row->id] = $row->store_name;
		}

		return $results;

	}

	function get_store_dropdown2($store){
		$data = $this->db->select("*")
        ->where_not_in('id', $store)
        ->order_by('store_name', 'ASC')
        ->get('store')->result();
    
		$results    = array();
    	$results[0]="Pilih Resto";
		foreach ($data as $row) {
			$results[$row->id] = $row->store_name;
		}

		return $results;

	}

	function get_account_dropdown(){
		$data = $this->get_all_account();
    
		$results    = array();
    	$results[0]="Pilih Akun";
		foreach ($data as $row) {
			$results[$row->id] = $row->name;
		}

		return $results;

	}

	function get_country_dropdown(){
		$data = $this->get('country')->result();

		$results    = array();
		$results[0] = "- Pilih Negara -";
		foreach ($data as $row) {
			$results[$row->id] = $row->name;
		}

		return $results;

	}

	public function get_city_dropdown($id = 0)
    {
        $this->db->select('city.id, city.name, city.province_id');
        $this->db->from('city')
        ->join('province p', 'p.id = city.province_id');

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();

        $results                         = array();
        $results['0" class="chain_cl_0'] = "- Pilih Kota -";
        foreach ($data as $row) {
            if ($id != 0 && $id == $row->user_id) {
                $results[$row->id . '" class="select_me chain_cl_' . $row->province_id] = $row->name;
            }
            else {
                $results[$row->id . '" class="chain_cl_' . $row->province_id] = $row->name;
            }
        }

        return $results;
    }

    public function get_province_dropdown($id = 0)
    {
        $this->db->select('province.id, province.name, province.country_id');
        $this->db->from('province')
        ->join('country c', 'c.id = province.country_id');

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        $data  = $query->result();
        
        $results                         = array();
        $results['0" class="chain_cl_0'] = "- Pilih Provinsi -";
        foreach ($data as $row) {
            if ($id != 0 && $id == $row->user_id) {
                $results[$row->id . '" class="select_me chain_cl_' . $row->country_id] = $row->name;
            }
            else {
                $results[$row->id . '" class="chain_cl_' . $row->country_id] = $row->name;
            }
        }

        return $results;
    }
	public function get_outlet_by_store_id($store_id){
		$this->db->select('id,outlet_name')
		->from('outlet')
        ->where('store_id',$store_id);
        $data =  $this->db->get()->result();
	 
		foreach ($data as $row) {
			$results[$row->id] = $row->outlet_name;
		}

		return $results;

	}
	
	function get_table_dropdown(){
		$this->db->select('*')
			->from('table')
			->where('is_active', 1)
			->order_by('table_name * 1', 'asc');
        $data =  $this->db->get()->result();

		$results    = array();
		foreach ($data as $row) {
			$results[$row->id] = $row->table_name;
		}

		return $results;

	}

	function get_general_setting($name=FALSE){


		$qry = $this->db->select("
			id, default,
			value, name");
		$qry->from('master_general_setting');

		if($name){
			$qry->where('name', $name);
			return $qry->get()->row();
		}else{
			return $qry->get()->result();

		}
		

	}
	public function get_outlet_not_id($store_id,$outlet_id){
        $results                         = array();
		$this->db->select('id,outlet_name')
		->from('outlet')
        ->where('store_id',$store_id)
        ->where_not_in('id',$outlet_id);
         $data =  $this->db->get()->result();
		foreach ($data as $row) {
			$results[$row->id] = $row->outlet_name;
		}
		return $results;

	}
	public function get_dropdown_outlet_by_outlet_id($outlet_id){
		$this->db->select('id,outlet_name')
		->from('outlet')
        ->where('id',$outlet_id);
          $query = $this->db->get();
        $data  = $query->result();
		foreach ($data as $row) {
			$results[$row->id] = $row->outlet_name;
		}

		return $results;

	}
	public function get_store_by_outlet_id($outlet_id){
		$this->db->select('s.id,s.store_name')
		->from('store s')
		->join('outlet o','o.store_id = s.id')
        ->where('o.id',$outlet_id);
         
        $query = $this->db->get();
        $data  = $query->result();
		
		foreach ($data as $row) {
			$results[$row->id] = $row->store_name;
		}

		return $results;

	}

	function get_user_open_close($pin, $access= FALSE){
		$this->load->library('encrypt');
		$get_users = $this->db->get('users')->result();
		$identity = array();
		foreach ($get_users as $key) {
	  		$identity[$key->pin] = $this->encrypt->decode($key->pin);
		}
		$get_pin = array_search($pin, $identity);
		if ($get_pin != "") {
			$this->db->select('u.*')
					->from('users u')
					->join('users_groups g','g.user_id = u.id ')
			        ->where('u.pin',$get_pin);
			$result = $this->db->get()->row();

			if($result){
				return $result;
			}
		}

		return false;

	}

	
	function get_open_close($date = FALSE, $id= FALSE){
		if(!$date){
			$date = date('Y-m-d');
		}

		$qry = $this->db->select('o.*,
			(select name from users
				where id = o.open_by) as open_by,

		(select name from users
				where id = o.close_by) as close_by

		')
		->from('open_close_cashier o')
		->order_by('id', 'desc');

		if($id){
			$this->db->where('id', $id);
		}
		$result = $qry->get()->row();

		if($result){
			// if(strtotime($result->close_at)){
			// 	$data = array(
			// 		'status' =>2
			// 		);
			// 	$this->save('open_close_cashier', $data);				
			// }
			return $result;
		}else{
			$data = array(
				'status' =>2
				);
			$this->save('open_close_cashier', $data);
			return $this->get_open_close();
		}

	}


	function get_open_close_today($date = FALSE, $id= FALSE){
		if(!$date){
			$date = date('Y-m-d');
		}

		$qry = $this->db->select('o.*')
		->from('open_close_cashier o')
		->order_by('id', 'desc')
		->where("date_format(open_at,'%Y-%m-%d')",$date); 
		$result = $qry->get()->row(); 

		return $result;
	}





	function get_user_access($pin, $access= FALSE){

        $this->load->library('encrypt');
		
		$data = $this->db->select('*')
		->from('users u')
		->join('users_groups ug', 'u.id = ug.user_id');

		if($access =='void'){
      $this->db->join("feature_access fa","fa.groups_id=ug.group_id")
      ->join("feature f","f.id=fa.feature_id")
      ->where("f.key","void")->group_by("u.id");
			// $this->db->where('ug.group_id', 1)
			// ->or_where('ug.group_id', 2)
			// ->or_where('ug.group_id', 3)
			// ->or_where('ug.group_id', 5);

		}

		$data = $this->db->get()->result();
		
		foreach ($data as $key => $row) {
			if($this->encrypt->decode($row->pin) == $pin){
			  	return $row;
			}
		}

		return false;


	}

	/**
	 * Get stock request last sync
	 * @param  integer 	$store_id Store id
	 * @return datetime           Last sync
	 */
	public function get_stock_last_sync($store_id=0)
	{
		$this->db->select('stock_request_last_sync')
		->from('store')
		->where('id', $store_id);
		$result = array_pop($this->db->get()->result());

		return $result->stock_request_last_sync;
	}

	public function update($data, $cond)
	{
		return $this->update_where($this->_table, $data, $cond);
	}

	public function get_all_order_company()
    {
		$this->db->select('*')
		->from('order_company')->where('order_status', 0);
        return $this->db->get()->result();
	}	
	function get_order_company_dropdown(){
		$data = $this->get_all_order_company();

		$results    = array();
		foreach ($data as $row) {
			$results[$row->id] = $row->company_name." - ".$row->order_id;
		}

		return $results;

	}

	public function get_all_employee()
    {
		$this->db->select('*')
		->from('users')->join('users_groups','users.id= users_groups.user_id')
    ->join('feature_access','feature_access.groups_id= users_groups.group_id')
    ->join('feature','feature_access.feature_id= feature.id')
    

		->where_not_in('feature.key',"admincms")->group_by("users.id");
        return $this->db->get()->result();
	}	

	function get_employee_dropdown(){
		$data = $this->get_all_employee();

		$results    = array();
		foreach ($data as $row) {
			$results[$row->id] = $row->name;
		}

		return $results;
	}

	public function get_all_employee_member(){
		$member_category = $this->get_general_setting('member_karyawan_kategori_id');

		$this->db->select('*')
		->from('member')
		->where('member_category_id',$member_category->value);
        return $this->db->get()->result();
	}

	function get_member_employee_dropdown(){
		$data = $this->get_all_employee_member();

		$results    = array();
		foreach ($data as $row) {
			$results[$row->id] = $row->name;
		}

		return $results;
	}

	public function get_all_non_employee_member(){
		$member_category = $this->get_general_setting('member_karyawan_kategori_id');

		$this->db->select('*')
		->from('member')
		->where('member_category_id !=',$member_category->value)
		->order_by("name ASC");
        return $this->db->get()->result();
	}

	 
	public function get_all_bank(){
		$store_id = $this->get_general_setting('store_id');

		$this->db->select('*')
		->from('bank_account')
		->where('store_id',$store_id->value);
        return $this->db->get()->result();
	}
 

	public function get_compliment_data(){
		$store_id = $this->get_general_setting('store_id');

		$data = $this->db->query('
			SELECT
				user_id,
				`name`
			FROM
				(`compliment`)
			JOIN `users` ON `compliment`.`user_id` = `users`.`id`
			WHERE
				compliment.is_available_all_store = 1
			UNION
				SELECT
					user_id,
					`name`
				FROM
					(`compliment`)
				JOIN `users` ON `compliment`.`user_id` = `users`.`id`
				JOIN compliment_store ON compliment.id = compliment_store.compliment_id
				WHERE
					compliment_store.store_id = '.$store_id->value.'
		')->result();

		return $data;
	}
  public function get_table_by_floor_id($store_id,$floor_id)
  {
    return $this->db->query("
      select t.*,t.id as table_id,f.floor_name,ets.*,
        IFNULL((
          select `table`.id from `table` inner join table_merge on `table`.id=table_merge.parent_id where table_merge.table_id=t.id limit 0,1
        ),0) as parent_id,
        IFNULL((
          select `table`.table_name from `table` inner join table_merge on `table`.id=table_merge.parent_id where table_merge.table_id=t.id
        ),'') as parent_name,
        IFNULL((
          select count(table_merge.id) from table_merge inner join `table` on table_merge.table_id=`table`.id  where table_merge.parent_id=t.id
        ),0) as is_parent,
        IFNULL(a.order_id,0) as order_id,IFNULL(a.status_unavailable,0) as status_unavailable
      from `table` t
      inner join floor f on t.floor_id=f.id
      inner join store s on f.store_id=s.id
      inner join enum_table_status ets on t.table_status=ets.id
      left join (
        select `order`.table_id,`order`.id as order_id,
        (
          select count(IFNULL(order_menu.id,0)) from order_menu where order_menu.order_id=`order`.id and order_menu.cooking_status=6
        )as status_unavailable
        from `order`
        where order_status=0 and date(start_order)<=current_date()
        order by `order`.id desc
      ) a on a.table_id=t.id
      where s.id='".$store_id."' and f.id='".$floor_id."'
      group by t.id
      order by t.table_name*1 asc
    ")->result();
	}
  public function get_all_bank_dropdown(){
		$store_id = $this->get_general_setting('store_id');
		$this->db->select('*')->from('bank_account')->where('store_id',$store_id->value);
    $data = $this->db->get()->result();
    $results= array();
    // $results['0" class="chain_cl_0'] = "";
    foreach ($data as $row) {
      $results[$row->id . '" data-account-id="'.$row->account_id.'" class="chain_cl_' . $row->id] = $row->bank_name;
    }
    return $results;
	}
  public function get_bank_account_card_dropdown()
  {
    $this->db->select('bac.id,bac.card_type_id,bac.bank_account_id,c.card_name');
    $this->db->from('bank_account_card bac')
    ->join('enum_card_type c', 'c.id = bac.card_type_id','left');
    $data = $this->db->get()->result();
    $results= array();
    $results['0" class="chain_cl_0'] = "Tanpa Jenis Kartu";
    foreach ($data as $row) {
      $results['0" class="chain_cl_' . $row->bank_account_id] = "Tanpa Jenis Kartu";
      $results[$row->card_type_id . '" class="chain_cl_' . $row->bank_account_id] = $row->card_name;
    }
    return $results;
  }
	public function get_checker_users()
	{
		return $this->db->query("
			select u.* from users u
			inner join users_groups ug on u.id=ug.user_id
			inner join feature_access fa on ug.group_id=fa.groups_id
			inner join feature f on fa.feature_id=f.id
			where f.key='checker' and u.active=1
			group by u.id
		")->result();
	}
	public function get_checker_groups()
	{
		return $this->db->query("
			select GROUP_CONCAT(u.name) as checker_users,IF(checker_number=1,'A',IF(checker_number=2,'B','C')) as checker_number_name
			from checker_group cg
			inner join users u on cg.user_id=u.id
			group by cg.checker_number
		")->result();
	}
}