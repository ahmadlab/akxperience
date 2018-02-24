<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Vendor Model Class
 * @Author : Seto Kuslaksono
 * @Email  : seto@jayadata.co.id
 * @Type   : Model
 * @Desc   : Province model
 ***********************************/
class Ref_province_model extends CI_Model
{

    public $table = 'ref_province';

    /**
     * To Insert/add vendor
     * @param mixed $data
     * @return int
     */
    public function add($data)
    {
        $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * To edit/update vendor
     * @param array $data
     * @param int $id
     */
    public function update($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
    }

    /**
     * To delete vendor
     * @param int $id
     */
    public function delete($id)
    {
        $this->db->where_in('id', $id);
        $this->db->delete($this->table);
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $query = $this->db->get($this->table);
        return $query;
    }

    public function getAll()
    {
        $this->db->order_by('name', 'asc');
        $query = $this->db->get($this->table);
        return $query;
    }

    public function getTotal($search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->like('name', $search1);
        }

        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    function isExist($field, $val, $var)
    {
        if ($var) {
            $this->db->where_not_in('id', array($var));
        }
        $chk = $this->db->where("$field = '$val'")->get($this->table)->num_rows();
        if ($chk > 0) {
            echo last_query();
            return false;
        }
        return true;
    }
}