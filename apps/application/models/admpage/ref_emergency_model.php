<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Ref Emergency Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Ref Emergency model
 ***********************************/
class Ref_emergency_model extends CI_Model
{
    /**
     * count total emergency
     * @param mixed $s_search1
     * @return int count total rows
     */
    function TotalRefEmergency($s_search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->like("name", $s_search1);
        }

        $query = $this->db->get('emergency_list');
        echo $this->db->_error_message();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Emergency
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllRefEmergency($s_search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != null) {
            $this->db->like("name", $s_search1);
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('emergency_list');
        return $query;
    }

    /**
     * get Emergency by id
     * @param type $Id
     * @return object $query
     */
    function GetRefEmergencyById($Id)
    {
        $this->db->where('id_emergency', $Id);
        $this->db->limit(1);
        $query = $this->db->get('emergency_list');
        return $query;
    }

    /**
     * change Emergency publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishRefEmergency($Id)
    {
        $this->db->where('id_emergency', $Id);
        $query = $this->db->get('emergency_list');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_emergency', $row['id_emergency']);
            $this->db->update('emergency_list', array('name' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Emergency
     * @param string $name
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsRefEmergency($name, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_emergency != ', $Id);
        }
        $this->db->where('name', $name);
        $query = $this->db->get('emergency_list');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert Emergency
     * @param array $data
     * @return last id inserted
     */
    function InsertRefEmergency($data)
    {
        $this->db->insert('emergency_list', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update Emergency
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateRefEmergency($data, $Id)
    {
        $this->db->where('id_emergency', $Id);
        $this->db->update('emergency_list', $data);
    }

    /**
     * delete Emergency by id
     * @param int $Id
     */
    function DeleteRefEmergency($Id)
    {
        $this->db->where_in('id_emergency', $Id);
        $this->db->delete('emergency_list');
    }
}