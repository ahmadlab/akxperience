<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Twitter Model Class
 * @Author : Seto Kuslaksono
 * @Email  : seto@jayadata.co.id
 * @Type   : Model
 * @Desc   : Twitter model
 ***********************************/
class Twitter_model extends CI_Model
{
    /**
     * To Insert/add twitter
     * @param mixed $data
     * @return int
     */
    public function add($data)
    {
        $this->db->insert('ref_twitter', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * To edit/update twitter
     * @param array $data
     * @param int $id
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('ref_twitter', $data);
    }

    /**
     * To delete twitter
     * @param int $id
     */
    public function delete($id)
    {
        $this->db->where_in('id', $id);
        $this->db->delete('ref_twitter');
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('ref_twitter');
        return $query;
    }

    public function getAll($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->like('name', $search1);
        }

        $this->db->order_by('sort', 'asc');
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('ref_twitter');
        return $query;
    }

    public function showAll()
    {
        $this->db->order_by('sort', 'asc');
        $query = $this->db->get('ref_twitter');
        return $query;
    }

    public function getTotal($search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->like('name', $search1);
        }

        $query = $this->db->get('ref_twitter');
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
    public function GetRefSortMax()
    {
        $this->db->select('max(sort) as sort');
        $query = $this->db->get('ref_twitter');
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
        $query = $this->db->get('ref_twitter');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['sort'];
        } else {
            return '0';
        }
    }

    function isExist($field, $val, $var)
    {
        if ($var) {
            $this->db->where_not_in('id', array($var));
        }
        $chk = $this->db->where($field, $val)->get('ref_twitter')->num_rows();
        if ($chk > 0) {
            echo last_query();
            return false;
        }
        return true;
    }

    /**
     * change twitter sort
     * @param type $id_page
     * @param type $urut
     * @param type $direction
     */
    function ChangeSort($id, $sort, $direction)
    {
        $this->db->where('id', $id);
        $this->db->where('sort', $sort);
        $this->db->order_by('sort', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('ref_twitter');

        if ($query->num_rows() > 0) {

            if ($direction == "down") {
                $this->db->where('sort > ', $sort);
                $this->db->order_by('sort', 'asc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_twitter');

                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->update($row_1['id'], array('sort' => $sort));
                    $this->update($id, array('sort' => $row_1['sort']));
                }
            } elseif ($direction == "up") {
                $this->db->where('sort < ', $sort);
                $this->db->order_by('sort', 'desc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_twitter');

                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->update($row_1['id'], array('sort' => $sort));
                    $this->update($id, array('sort' => $row_1['sort']));
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
        $query = $this->db->get('ref_twitter');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['is_published'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id', $row['id']);
            $this->db->update('ref_twitter', array('is_published' => $val));

            if ($val == 1) {
                return 'Published';
            } else {
                return 'Not Published';
            }
        }
    }
}