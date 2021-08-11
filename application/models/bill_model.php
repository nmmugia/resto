<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

class Bill_Model extends MY_Model{

    /**
     * Table's name to be used throughout the model
     * @var string
     */
    private $_table = 'bill';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new data
     * @param   mix     $data    Detail transfer request to be added
     * @return  boolean          Status whether success of failed
     */
    public function add($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) return $this->save($this->_table, $data);
        else return $this->db->insert_batch($this->_table, $data);
    }

    public function get_all($cond)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->where($cond);
        return $this->db->get()->result();
    }

    public function get_all_join_order_user($cond)
    {
        $this->db->select('bill.*, time(bill.start_order) as time_order, store.store_name')
        ->from($this->_table)
        ->join('store','store.id = bill.store_id')
        //->join('order','bill.order_id = order.id')
        //->join('users','order.created_by = users.id')
        ->where($cond);
        return $this->db->get()->result();
    }

    public function get_sales_of_sales($params=array())
    {
        return $this->db->query("
            SELECT bill.id, bill.receipt_number, bill.payment_date, bill.total_price, bill.total_cogs, SUM(bill_menu.quantity) as qty
            FROM bill
            JOIN bill_menu ON bill.id = bill_menu.bill_id
            WHERE bill.is_refund = 0 and date(bill.payment_date)>='".$params['start_date']."' and date(bill.payment_date)<='".$params['end_date']."' 
            GROUP BY bill_menu.bill_id
        ")->result();
    }

    public function get_journal_data($id) {
        $this->db->select('a.id,bill.receipt_number, b.code, b.name,
                            a.created_at,
                            debit, credit');
        $this->db->from('account_data a');
        $this->db->join('account b', 'a.account_id = b.id');
        $this->db->join('bill', 'a.foreign_id = bill.id');
        $this->db->where(array('a.foreign_id' => $id));
        $this->db->order_by('debit','desc');

        return $this->db->get()->result();
    }

    public function get_journal_data_sum($id) {
        $this->db->select('a.foreign_id,
                            sum(debit) as debit, sum(credit) as credit');
        $this->db->from('account_data a');
        $this->db->join('account b', 'a.account_id = b.id');
        $this->db->join('bill', 'a.foreign_id = bill.id');
        $this->db->where(array('a.foreign_id' => $id));
        $this->db->order_by('debit','desc');
        $this->db->group_by('a.foreign_id');

        return $this->db->get()->row();
    }

    /**
     * Update table
     * @param  mixed  $data Data to be set
     * @param  mixed  $cond Condition of searching
     * @return boolean
     */
    public function update($data=array(), $cond=array())
    {
        return $this->update_where($this->_table, $data, $cond);
    }

    public function get_mealtime($store_id){
        $this->db->select('setting_mealtime.* , value')
        ->from('setting_mealtime')
        ->join('enum_mealtime','setting_mealtime.mealtime_enum = enum_mealtime.id')
        ->where('store_id',$store_id)
        ->order_by('mealtime_enum', 'asc');

        return $this->db->get()->result();
    }

    public function get_mealtime_enum(){
        $this->db->select('*')
        ->from('enum_mealtime');

        return $this->db->get()->result();
    }

    public function get_revenue_by_outlet_mealtime($where_clause, $total){
        $this->db->select('bill_menu.id, bill_menu.store_id, time(bill_menu.created_at) as jam, 
            sum(bill_menu.price * quantity) as total_revenue,  category.outlet_id, outlet.outlet_name,
            sum(bill_menu.price * quantity)/' . $total .' as percentage')
        ->from('bill_menu')
        ->join('bill_payment',"bill_payment.bill_id = bill_menu.bill_id")
        ->join('menu', 'bill_menu.menu_id = menu.id')
        ->join('category' , 'menu.category_id = category.id')
        ->join('outlet' , 'category.outlet_id = outlet.id')
        ->where($where_clause) 
        ->where("bill_payment.payment_option !=","5") 
        ->group_by('category.outlet_id');

        return $this->db->get()->row();
    }

    public function get_customer_count_by_outlet_mealtime($where_clause, $total){
        $this->db->select('sum(customer_count) as total_customer, 
            sum(customer_count)/' . $total .' as percentage')
        ->from('bill')
        ->where($where_clause);

        return $this->db->get()->row();
    }

    public function get_outlet_by_store($store_id){
        $this->db->select('*')
        ->from('outlet')
        ->where('store_id',$store_id);

        return $this->db->get()->result();
    }

    public function get_mealtime_by_store($store_id){
        $this->db->select('*')
        ->from('setting_mealtime')
        ->where('store_id',$store_id);

        return $this->db->get()->result();
    }

    public function get_sum_revenue_by_store_date($store_id,$date){
        $this->db->select('sum(quantity * price) as total')
        ->from('bill_menu')
         ->join('bill_payment','bill_menu.bill_id = bill_payment.bill_id')
        ->where('bill_menu.store_id',$store_id)
        ->where('date(bill_menu.created_at)',$date)
        ->where('bill_payment.payment_option !=',"5")  ;

        return $this->db->get()->row();
    }

    public function get_sum_revenue_by_store_month($store_id,$month){
        $this->db->select('sum(quantity * price) as total')
        ->from('bill_menu')
          ->join('bill_payment','bill_menu.bill_id = bill_payment.bill_id')
        ->where('bill_menu.store_id',$store_id)
        ->where('month(bill_menu.created_at)',$month)
         ->where('bill_payment.payment_option !=',"5");

        return $this->db->get()->row();
    }

    public function get_sum_customer_by_store_date($store_id,$time_filter){
        $this->db->select('sum(bill.customer_count) as total')
        ->from('bill')
         ->join('bill_payment','bill.id = bill_payment.bill_id')
        ->where('bill.store_id',$store_id)
          ->where('bill_payment.payment_option !=',"5")
        ->where($time_filter);

        return $this->db->get()->row();
    }

    public function get_sum_discount_by_store($store_id,$where_clause){
        $this->db->select('sum(bill_information.amount) as total')
        ->from('bill_information')
        ->join('bill_payment','bill_information.bill_id = bill_payment.bill_id')
        ->where('bill_information.store_id',$store_id)
        ->where($where_clause)
        ->where('bill_information.type',2)
        ->where('bill_payment.payment_option !=',"5");

        return $this->db->get()->row();
    }

    public function get_sum_service_by_store($store_id,$where_clause){
        $this->db->select('sum(amount) as total')
        ->from('bill_information')
        ->where('store_id',$store_id)
        ->where('type',1)
        ->where($where_clause)
        ->like('lower(info)','service','both');

        return $this->db->get()->row();
    }

    public function get_sum_pembulatan_by_store($store_id,$where_clause){
        $this->db->select('sum(amount) as total')
        ->from('bill_information')
        ->where('store_id',$store_id)
        ->where('type',1)
        ->where($where_clause)
        ->like('lower(info)','pembulatan','both');

        return $this->db->get()->row();
    }

    public function get_sum_tax_by_store($store_id,$where_clause){
        $this->db->select('sum(amount) as total')
        ->from('bill_information')
        ->where('store_id',$store_id)
        ->where('type',1)
        ->where($where_clause)
        ->not_like('lower(info)','pembulatan','both')
        ->not_like('lower(info)','service','both');

        return $this->db->get()->row();
    }

    public function get_payment_by_store($store_id,$condition,$condition_date){
        $this->db->select('sum(amount) as total')
        ->from('bill_payment')
        ->where('store_id',$store_id)
        ->where($condition_date)
        ->where($condition);

        return $this->db->get()->row();
    }

    public function get_bill_menu_for_refund($bill_id)
    {
        $this->db->select('id, bill_id, menu_id, menu_name, SUM(quantity) AS quantity, price', false)
        ->from('bill_menu')
        ->where('bill_id', $bill_id)
        ->group_by('menu_id');

        return $this->db->get()->result();
    }
}