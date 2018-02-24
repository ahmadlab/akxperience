<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Reserve Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Reserve model
 ***********************************/
class Service_model extends CI_Model
{
    /**
     * count total Service
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @return int count total rows
     */
    function TotalService($s_search1 = null, $s_search2 = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->where("id_service_type", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->like("service", $s_search2);
        }

        $query = $this->db->get('car_services');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Service
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllService($s_search1 = null, $s_search2 = null, $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != null) {
            $this->db->where("id_service_type", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->like("service", $s_search2);
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_services');
        return $query;
    }

    /**
     * get Service by id
     * @param type $Id
     * @return object $query
     */
    function GetServiceById($Id)
    {
        $this->db->where('id_service', $Id);
        $this->db->limit(1);
        $query = $this->db->get('car_services');
        return $query;
    }

    /**
     * change Service publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishService($Id)
    {
        $this->db->where('id_service', $Id);
        $query = $this->db->get('car_services');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_service', $row['id_service']);
            $this->db->update('car_services', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Service
     * @param string $name
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsRefService($name, $categ, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_service != ', $Id);
        }
        $this->db->where(array('service' => $name, 'id_service_type' => $categ));
        $query = $this->db->get('car_services');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert Service
     * @param array $data
     * @return last id inserted
     */
    function InsertService($data)
    {
        $this->db->insert('car_services', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update Service
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateService($data, $Id)
    {
        $this->db->where('id_service', $Id);
        $this->db->update('car_services', $data);
    }

    /**
     * delete Service by id
     * @param int $Id
     */
    function DeleteService($Id)
    {
        $this->db->where_in('id_service', $Id);
        $this->db->delete('car_services');
    }
}