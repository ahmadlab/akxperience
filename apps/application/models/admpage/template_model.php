<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Template Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Template model
 ***********************************/
class Template_model extends CI_Model
{
    /**
     * count total
     * @param mixed $s_search
     * @return int count total rows
     */
    function TotalTemp($s_search = null)
    {
        $this->db->select('count(*) as total');

        if ($s_search != null) {
            $this->db->where("type", $s_search);
        }

        $query = $this->db->get('web_service_template');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTemp($s_search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != null) {
            $this->db->where("type", $s_search1);
        }

        $this->db->order_by('type', 'asc')->limit($per_pg, $limit);
        $query = $this->db->get('web_service_template');
        return $query;
    }

    /**
     * get by id
     * @param type $Id
     * @return object $query
     */
    function GetTempById($Id)
    {
        $this->db->where('id', $Id);
        $this->db->limit(1);
        $query = $this->db->get('web_service_template');
        return $query;
    }

    /**
     * check existing Template
     * @param string $temp
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsTemp($temp, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id != ', $Id);
        }
        $this->db->where('type', $news);
        $query = $this->db->get('web_service_template');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert
     * @param array $data
     * @return last id inserted
     */
    function InsertPush($data)
    {
        $this->db->insert('web_service_template', $data);
        $id = $this->db->insert_id();

        return $id;
    }

    /**
     * Update
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateTemp($data, $Id)
    {
        $this->db->where('id', $Id);
        $this->db->update('web_service_template', $data);
    }

}