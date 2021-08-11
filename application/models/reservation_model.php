<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Reservation_model extends MY_Model
{
	

	public function get_table($table_id, $status=FALSE){
		$qry = $this->db->select('r.id, r.*')
		->from('reservation r')
		->where('table_id', $table_id);

		if($status){
			$this->db->where('status', $status);
			$this->db->where('book_date >=', date('Y-m-d'));
		}
    $this->db->order_by("book_date","asc");
		return $qry->get()->row();
	}
  public function update_expire_table()
  {
    $this->db->query("
      update `table` set table_status=1 where id in (select table_id from reservation where status=1 and date(book_date)<date(now())) or (table_status=6 and id not in (select table_id from  reservation where date(book_date)=date(now())))
    ");
    // $this->db->query("
      // update `reservation` set status=3 where date(book_date)<date(now()) and status=1
    // ");
  }

	public function get_all_table(){
		$this->db->select('DISTINCT(table.id) as table_id, table.table_name, reservation.*')
			->from('table')
			->join('reservation', 'reservation.table_id = table.id','left')
			->order_by('table_name', 'asc');
        $data =  $this->db->get()->result();
		return $data;
	}

  public function get_by_id($id){
		return $this->db->select('r.*,t.table_name,ers.value as reservation_status')
			->from('reservation r')
      ->join('enum_reservation_status ers', 'ers.id = r.status')
			->join('table t', 'r.table_id = t.id','left')
      ->where("r.id",$id)
      ->get()->row();
	}

	public function get_reservation($cond){
		$this->db->select('*')->from('reservation')
			 
			->where($cond);
        $data =  $this->db->get()->result();
		return $data;
	}
}