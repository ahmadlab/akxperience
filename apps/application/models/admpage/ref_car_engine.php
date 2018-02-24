<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Ref Car Engine Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Engine
 * @Desc    : Ref Car Engine
 ***********************************/
class Ref_car_engine extends CI_Model
{
    /**
     * count total cars Ref Car model
     * @param mixed $s_engine search by car engine
     * @return int count total rows
     */
    function TotalCarsEngine($s_engine = null)
    {
        $this->db->select('count(*) as total');
        if ($s_engine != null) {
            $this->db->where("LCASE(engine) LIKE '%" . utf8_strtolower($s_engine) . "%'");
        }


        $query = $this->db->get('car_engines');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all cars
     * @param type $s_engine
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCarsEngine($s_engine = 0, $limit = 0, $per_pg = 0)
    {
        if ($s_engine != null) {
            $this->db->where("LCASE(engine) LIKE '%" . utf8_strtolower($s_engine) . "%'");
        }

        $this->db->limit($per_pg, $limit);

        $query = $this->db->get('car_engines');
        return $query;
    }

    /**
     * get car by car id
     * @param type $Id
     * @return object $query
     */
    function GetCarsEngineById($Id)
    {
        $this->db->where('id_car_engine', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('car_engines');
        return $query;
    }

    /**
     * change cars publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishEngine($Id)
    {
        $this->db->where('id_car_engine', $Id);
        // $this->db->where('is_delete',0);
        $query = $this->db->get('car_engines');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_car_engine', $row['id_car_engine']);
            $this->db->update('car_engines', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Engine name
     * @param string $model
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarsEngine($engine, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_car_engine != ', $Id);
        }
        $this->db->where('engine', $engine);
        $query = $this->db->get('car_engines');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * insert Car Engine
     * @param array $data
     * @return int $id last inserted
     */
    function InsertCarsEngine($data)
    {
        $this->db->insert('car_engines', $data);
        $id = $this->db->insert_id();
        return $id;
    }


    /**
     * update Car Engine
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarsEngine($data, $Id)
    {
        $this->db->where('id_car_engine', $Id);
        $this->db->update('car_engines', $data);
    }


    /**
     * delete Engine by id car
     * @param Engine $Id
     */
    function DeleteCarsEngine($Id)
    {

        $this->db->where_in('id_car_engine', $Id);
        $this->db->delete('car_engines');
    }


}