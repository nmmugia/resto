<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Member_model extends MY_Model
{
	public function get_detail_member($id)
	{
		$qry = $this->db->select('m.*, member_category.name as member_category, member_category.discount, 
			city.name as city, province.name as province, country.name as country, store.store_name ')
		->from('member m ')
		->join('member_category', 'member_category.id = m.member_category_id')
		->join('city', 'city.id = m.city_id')
		->join('province', 'province.id = m.province_id')
		->join('country', 'country.id = m.country_id')
		->join('store', 'store.id = m.join_store_id')

		->where('m.id', $id);

		return $qry->get()->row();
	}
}	