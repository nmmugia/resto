<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:03 AM
 */
class MY_Model extends CI_Model
{

    /**
     * Optional Message returned to user
     *
     * @var string
     */
    public $msg;

    /**
     * Optional  - TRUE if everything goes fine
     *
     * @var boolean
     */
    public $ok;


    /**
     * data from user's session
     *
     * @var object
     */
    public $user;


    /**
     * Wrapper to __construct for when loading
     * class is a superclass to a regular controller,
     * i.e. - extends Base not extends Controller.
     *
     * @return void
     */

    /**
     * The class constructer
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generic function to get data
     *
     * @param   string
     *
     * @return  object
     */
    public function get($table = '', $limit = 0)
    {
        if ($limit > 0) {
            $this->db->limit($limit);
        }

        return $this->db->get($table);
    }

    /**
     * @param string $table
     * @param string $column
     * @param bool   $distinct
     *
     * @return mixed
     */
    public function get_column($table = '', $column = 'id', $distinct = FALSE)
    {
        if ($distinct === TRUE) {
            $this->db->distinct();
        }
        $this->db->select($column);

        return $this->db->get($table);
    }

    /**
     * Get paged data
     *
     * @access public
     *
     * @param   string
     * @param   int
     * @param   int
     * @param   boolean /array
     *
     * @return  integer/boolean
     */
    public function paged($table = '', $limit = 0, $offset = 0, $where = FALSE, $order_by = FALSE)
    {
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($where) {
            $this->db->where($where);
        }

        if ($order_by !== FALSE) {
            $this->db->order_by($order_by);
        }

        return $this->get($table);
    }

    /**
     * Get paged data with join
     *
     * @access public
     *
     * @param   string
     * @param   string
     * @param   string
     * @param   int
     * @param   int
     * @param   boolean /array
     *
     * @return  integer/boolean
     */
    public function paged_join($table = '', $second_table = '', $fields = '*', $join = '', $limit = 0, $offset = 0, $where = FALSE, $order_by = FALSE)
    {
        $this->db->select($fields);
        $this->db->from($table);

        if ($second_table && $join) {
            $this->db->join($second_table, $join);
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($where) {
            $this->db->where($where);
        }

        if ($order_by !== FALSE) {
            $this->db->order_by($order_by);
        }

        return $this->get();
    }

    /**
     * get one row by id
     *
     * @param   string
     * @param   int
     * @param   bool
     *
     * @return  object
     */
    public function get_one($table = '', $id = 0, $result_array = FALSE)
    {
        $this->db->where(array('id' => $id))->limit(1);

        return $result_array ? $this->get($table)->row_array() : $this->get($table)->row();
    }


    /**
     * get one row by any field
     *
     * @param   string
     * @param   int
     * @param   string
     *
     * @return  object
     */
    public function get_by($table = '', $value = 0, $field = 'id')
    {
        $this->db->where($field, $value)->limit(1);

        return $this->db->get($table)->row();
    }

    /**
     * A generic function to delete data from a selected table
     * You just need to provide the table name and the item id
     *
     * @access  public
     *
     * @param   string
     * @param   integer
     * @param   string
     *
     * @return  boolean
     */
    public function delete($table = '', $value = 0, $field = 'id')
    {
        $this->db->limit(1);

        return (boolean)$this->db->delete($table, array($field => $value));
    }

    public function delete_by_where($table = '', $where)
    {
        $this->db->limit(1);

        return (boolean)$this->db->delete($table, $where);
    }

    public function delete_by_limit($table = '', $where, $limit = 1)
    {
        if ($limit > 0) {
            $this->db->limit($limit);
        }

        return (boolean)$this->db->delete($table, $where);
    }


    /**
     * Save data on db
     * the 1st parameter is the table name
     * the 2nd is an array with the data that will be saved
     * the 3dt is optional. If you give an id the function will update the current id.
     * Without id a new entry will add into db
     *
     * @access public
     *
     * @param   string - table name
     * @param   array - columns w/ data to be added
     * @param   integer - pass ID to insert data
     *
     * @return  integer/boolean
     */
    public function save($table = '', $columns = array(), $id = 0, $where = false)
    {
        if ($where !== false) {
            $this->db->where($where);
        }

        if ($id > 0) {
            $this->db->where('id', $id);
            $result = $this->db->update($table, $columns);
        }
        else {
            $result = $this->db->insert($table, $columns);
        }

        if ($result) {
            return ((int)$id == 0) ? $this->db->insert_id() : $id;
        }
        else {
            return FALSE;
        }
    }

       /**
     * @param string $table
     * @param array $columns
     * @param bool $id
     * @param string $field
     * @return bool
     */
    public function save_by($table = '', $columns = array(), $id = FALSE, $field = 'id')
    {
        if ($id)
        {
            $this->db->where($field, $id);
            $result = $this->db->update($table, $columns);
        }
        else
        {
            $result = $this->db->insert($table, $columns);
        }

        if ($result)
        {
            return ($id === FALSE) ? $this->db->insert_id() : $id;

        }
        else
        {
            return FALSE;
        }
    }
    

    /**
     * @param string $table
     * @param array  $columns
     * @param bool   $where
     *
     * @return bool
     */
    public function update_where($table = '', $columns = array(), $where = false)
    {
        if ($where !== false) {
            $this->db->where($where);
        }

        $result = $this->db->update($table, $columns);

        return (bool)$this->db->affected_rows();
    }


    /**
     * returns num rows
     *
     * @access public
     *
     * @param   string
     * @param   boolean /array
     *
     * @return  integer
     */
    public function total_rows($table = '', $where = FALSE)
    {
        if ($table == '') {
            return 0;
        }

        if ($where) {
            $this->db->where($where);
        }

        return $this->db->count_all_results($table);
    }


    /* lista c/ id e nome*/
    public function drop_down($obj, $val = 'name', $key = 'id', $empty = '', $ignore = FALSE)
    {
        $return = array();

        if ($empty != '') {
            $return['empty'] = $empty;
        }

        if (isset($obj->row_data) && $obj->row_data == NULL) {
            return $return;
        }

        foreach ($obj as $res) {
            if ($ignore && $res->{$key} == $ignore) {

            }
            else {
                $return[$res->{$key}] = $res->{$val};
            }
        }

        return $return;

    }

    /**
     * increment any field w/ +1
     *
     * @access public
     *
     * @param   string
     * @param   int
     * @param   string
     *
     * @return  boolean
     */
    public function update_clicks($table = '', $id = 0, $column = 'views')
    {
        $this->db->set("{$column}", "{$column}+1", FALSE)->where('id', $id)->limit(1)->update($table);

        return (bool)$this->db->affected_rows();
    }


    /**
     * @param string $table
     * @param        $slug
     * @param bool   $id
     *
     * @return bool
     */
    public function check_slug($table = '', $slug, $id = false)
    {
        if ($id) {
            $this->db->where('id !=', $id);
        }
        $this->db->where('slug', $slug);

        return (bool)$this->db->count_all_results($table);
    }

    /**
     * @param string $table
     * @param        $slug
     * @param bool   $id
     * @param bool   $count
     *
     * @return string
     */
    public function validate_slug($table = '', $slug, $id = false, $count = false)
    {
        if ($this->check_slug($table, $slug . $count, $id)) {
            if (! $count) {
                $count = 1;
            }
            else {
                $count++;
            }

            return $this->validate_slug($table, $slug, $id, $count);
        }
        else {
            return $slug . $count;
        }
    }


    /**
     * @param string $table
     * @param string $column
     *
     * @return mixed
     */
    function get_max_order_table($table = '', $column = 'sequence')
    {
        $this->db->select_max($column);
        $query = $this->db->get($table);

        return $query->row();
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return mixed
     */
    public function get_year($table = '', $column = '')
    {
        $this->db->select('YEAR(' . $column . ') as my_year');

        return $this->db->get($table);
    }

    function get_random_unique_id($table = '', $column = '')
    {
        $query = $this->db->query('SELECT FLOOR(1 + RAND() * 2147483646) AS my_id FROM `' . $table . '` WHERE "' . $column . '" NOT IN (SELECT ' . $column . ' FROM `' . $table . '` WHERE ' . $column . ' IS NOT NULL) LIMIT 1');

        if ($query->num_rows() > 0) {
            return $query->row()->my_id;
        }
        else {
            /*$pattern = "123456789";
            $ID      = $pattern{rand(0, 6)};
            for ($i = 1; $i < 7; $i++) {
                $ID .= $pattern{rand(0, 6)};
            }

            return $ID;*/
            $query = $this->db->query('SELECT FLOOR(1 + RAND() * 2147483646) AS my_id');

            return $query->row()->my_id;
        }

    }

    public function delete_diffrence($table, $column, $filter_set = array())
    {
        if (empty($table) || empty($column)) return false;
        $query = $this->db->query("DELETE FROM `" . $table . "` WHERE $column NOT IN ('" . implode("','", $filter_set) . "')");

        return true;
    }

    public function delete_all($table = '', $value = 0)
    {

        return (boolean)$this->db->delete($table, array('id !=' => $value));
    }

     public function get_all_where($table = '', $where = '', $limit=FALSE, $order = null)
    {
        if($where!=''){
          $this->db->where($where);
        }
        if ($limit > 0)
        {
            $this->db->limit($limit);
        }
        
        if($order != null){
            $this->db->order_by($order[0], $order[1]);
        }
        return $this->db->get($table)->result();
    }


}