<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Ref Location Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Ref Location model
 ***********************************/
class Ref_location_model extends CI_Model
{
    /**
     * count total location
     * @param mixed $s_search1
     * @return int count total rows
     */
    function TotalRefLocation($s_search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->like("location", $s_search1);
        }
        // $this->db->where('company_code','AK');
        $query = $this->db->get('ref_location');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Location
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllRefLocation($s_search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != null) {
            $this->db->like("location", $s_search1);
        }
        // $this->db->where('company_code','AK');
        $this->db->order_by('sort', 'asc');
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('ref_location');
        return $query;
    }

    /**
     * get Location by id
     * @param type $Id
     * @return object $query
     */
    function GetRefLocationById($Id)
    {
        $this->db->where('id_ref_location', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_location');
        return $query;
    }

    /**
     * change Location publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishRefLocation($Id)
    {
        $this->db->where('id_ref_location', $Id);
        $query = $this->db->get('ref_location');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['is_published'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_ref_location', $row['id_ref_location']);
            $this->db->update('ref_location', array('is_published' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Location
     * @param string $name
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsRefLocation($name, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_ref_location != ', $Id);
        }
        $this->db->where('location', $name);
        $query = $this->db->get('ref_location');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert Location
     * @param array $data
     * @return last id inserted
     */
    function InsertRefLocation($data)
    {
        $this->db->insert('ref_location', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update Location
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateRefLocation($id, $data)
    {
        $this->db->where('id_ref_location', $id);
        $this->db->update('ref_location', $data);
    }

    /**
     * delete Service by id
     * @param int $Id
     */
    function DeleteRefLocation($Id)
    {
        $this->db->where_in('id_ref_location', $Id);
        $this->db->delete('ref_location');
    }

    /**
     * get reference urut for sort
     * @param type $id_parent
     * @return type string maximum field urut
     */
    public function GetRefSortMax()
    {
        $this->db->select('max(sort) as sort');
        $query = $this->db->get('ref_location');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['sort'];
        } else {
            return '0';
        }
    }

    /**
     * get reference minimum urut for sort
     * @param type $id_parent
     * @return type string minimum field urut
     */
    public function GetRefSortMin()
    {
        $this->db->select('min(sort) as sort');
        $query = $this->db->get('ref_location');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['sort'];
        } else {
            return '0';
        }
    }

    /**
     * change twitter sort
     * @param type $id_page
     * @param type $urut
     * @param type $direction
     */
    function ChangeSort($id, $sort, $direction)
    {
        $this->db->where('id_ref_location', $id);
        $this->db->where('sort', $sort);
        $this->db->order_by('sort', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('ref_location');

        if ($query->num_rows() > 0) {

            if ($direction == "down") {
                $this->db->where('sort > ', $sort);
                $this->db->order_by('sort', 'asc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_location');

                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateRefLocation($row_1['id_ref_location'], array('sort' => $sort));
                    $this->UpdateRefLocation($id, array('sort' => $row_1['sort']));
                }
            } elseif ($direction == "up") {
                $this->db->where('sort < ', $sort);
                $this->db->order_by('sort', 'desc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_location');

                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateRefLocation($row_1['id_ref_location'], array('sort' => $sort));
                    $this->UpdateRefLocation($id, array('sort' => $row_1['sort']));
                }
            }
        }
    }
}