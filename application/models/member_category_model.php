<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class Member_category_model extends MY_Model
{
	function get_category_dropdown(){
		$data = $this->get('member_category')->result();

		$results    = array();
		$results[0] = "- Pilih Kategori Member -";
		foreach ($data as $row) {
			$results[$row->id] = $row->name;
		}

		return $results;

	}
}