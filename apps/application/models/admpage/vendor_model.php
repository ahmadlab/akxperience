<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Vendor Model Class
 * @Author : Seto Kuslaksono
 * @Email  : seto@jayadata.co.id
 * @Type   : Model
 * @Desc   : Vendor model
 ***********************************/
class Vendor_model extends CI_Model
{
    /**
     * To Insert/add vendor
     * @param mixed $data
     * @return int
     */
    public function add($data)
    {
        $this->db->insert('ref_vendor', $data);
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
        $this->db->update('ref_vendor', $data);
    }

    /**
     * To delete vendor
     * @param int $id
     */
    public function delete($id)
    {
        $this->db->where_in('id', $id);
        $this->db->delete('ref_vendor');
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $query = $this->db->get('ref_vendor');
        return $query;
    }

    public function getAll($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->like('name', $search1);
        }

        $this->db->order_by('urut', 'asc')->limit($per_pg, $limit);
        $query = $this->db->get('ref_vendor');
        return $query;
    }

    public function showAll()
    {
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('ref_vendor');
        return $query;
    }

    public function getTotal($search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->like('name', $search1);
        }

        $query = $this->db->get('ref_vendor');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * get reference urut for sort
     * @param type $id_parent
     * @return type string maximum field urut
     */
    public function GetRefUrutMax($id_parent = null)
    {
        $this->db->select('max(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('ref_vendor');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['urut'];
        } else {
            return '0';
        }
    }

    /**
     * get reference minimum urut for sort
     * @param type $id_parent
     * @return type string minimum field urut
     */
    public function GetRefUrutMin($id_parent = null)
    {
        $this->db->select('min(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('ref_vendor');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['urut'];
        } else {
            return '0';
        }
    }

    function isExist($field, $val, $var)
    {
        if ($var) {
            $this->db->where_not_in('id', array($var));
        }
        $chk = $this->db->where("$field = '$val'")->get('ref_vendor')->num_rows();
        if ($chk > 0) {
            echo last_query();
            return false;
        }
        return true;
    }

    /**
     * change Bank account sort
     * @param type $id_page
     * @param type $urut
     * @param type $direction
     */
    function ChangeSort($id, $urut, $direction)
    {
        $this->db->where('id', $id);
        // $this->db->where('id_parents_menu_admin',$parent_id);
        $this->db->where('urut', $urut);
        $this->db->order_by('urut', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('ref_vendor');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($direction == "down") {
                $this->db->where('urut > ', $urut);
                $this->db->order_by('urut', 'asc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_vendor');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->update(array('urut' => $urut), $row_1['id']);
                    $this->update(array('urut' => $row_1['urut']), $id);
                }
            } elseif ($direction == "up") {
                $this->db->where('urut < ', $urut);
                $this->db->order_by('urut', 'desc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_vendor');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateBankAccount(array('urut' => $urut), $row_1['id']);
                    $this->UpdateBankAccount(array('urut' => $row_1['urut']), $id);
                }
            }
        }
    }

    /**
     * change cars brands publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishBrand($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('ref_vendor');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['is_published'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id', $row['id']);
            $this->db->update('ref_vendor', array('is_published' => $val));

            if ($val == 1) {
                return 'Published';
            } else {
                return 'Not Published';
            }
        }
    }
}