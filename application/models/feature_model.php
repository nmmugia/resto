<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @Author: Fitria Kartika
 * @Date:   2015-10-13 18:32:08
 * @Last Modified by:   Fitria Kartika
 * @Last Modified time: 2015-10-22 17:14:35
 */
class Feature_model extends MY_Model
{

	/**
     * get non selected feature access 
     * @param  int  $group_id 
     * @param  boolean $selected 
     *
     * @return array
     *
     * @author fkartika
     */
    function get_non_selected_access($group_id, $selected = FALSE){
        if(!$selected){
            $selected = $this->get_selected_access($group_id);
        }
        $selected_temp =array(0);
        foreach ($selected as $key => $row) {
            $selected_temp[] = $row->feature_id;
        }
        $qry = $this->db->select(' a.id, b.feature_id, b.id as feature_access_id, a.name');
        $qry->from('feature a');
        $qry->join('feature_access b', 'b.feature_id = a.id' ,'left');
        $qry->where_not_in('a.id', $selected_temp);
        $qry->group_by('a.id');
        return $qry->get()->result();
    }

    /**
     * get selected feature access
     * @param  [int] $group_id 
     *
     * @return array
     *
     * @author fkartika
     */
    function get_selected_access($group_id){

        $qry = $this->db->select(' a.id, b.feature_id, b.id as feature_access_id, a.name, a.key');
        $qry->from('feature a');
        $qry->join('feature_access b', 'b.feature_id = a.id' );
        $qry->where('b.groups_id', $group_id);

        return $qry->get()->result();
    }
    function get_feature_securities()
    {
      $this->db->select("f.*")
      ->from("feature f")
      ->where_in("key",array("merge_table","change_table","void_order","split_bill","member_bill","pending_bill","compliment_bill","reservation","bon_bill","petty_cash","post_to_ready","delete_order","refund","sales_report"));
      return $this->db->get()->result();
    }
    function get_feature_lock_securities()
    {
      $feature_lists = array("merge_table","change_table","void_order","split_bill","member_bill","pending_bill","compliment_bill","reservation","bon_bill","petty_cash","post_to_ready","delete_order","refund","discount_member");
      if ($this->data['setting']['summary_sales_on_cashier'] == 1) {
        array_push($feature_lists, "sales_report");
      }
      $this->db->select("f.*")
      ->from("feature f")
      ->where_in("key",$feature_lists);
      $results=array();
      foreach($this->db->get()->result() as $d){
        $results[]=$d;
        $d->users_can_confirmation="";
        $temp=array();
        foreach(explode(",",$d->users_unlock) as $u){
          if($u!=""){            
            $user=$this->feature_model->get_one("users",$u,FALSE);
            if(!empty($user)){
              array_push($temp,$user->name);
            }
          }
        }
        
        $d->users_can_confirmation=implode(",",$temp);
      }
      return $results;
      return $this->db->get()->result();
    }

    public function check_user_allowed($userid,$key_access){
      $qry = $this->db->select('count(1) as jum',false);
      $qry->from('users_groups ug');
      $qry->join('feature_access fa', 'fa.groups_id = ug.group_id' );
      $qry->join('feature f', 'f.id = fa.feature_id' );
      $qry->where('ug.user_id', $userid);
      $qry->where('f.key', $key_access);
      $row = $qry->get()->row();
      return isset($row->jum) && !empty($row->jum)?TRUE:FALSE;
    }
}