<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Ref Car Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Ref Car model
 ***********************************/
class Ref_car_silinder extends CI_Model
{
    /**
     * count total cars Ref Car model
     * @param mixed $s_series search by car series
     * @param mixed $s_model search by car model
     * @return int count total rows
     */
    function TotalCarsCc($s_cc = null)
    {
        $this->db->select('count(*) as total');
        if ($s_cc != null) {
            $this->db->where("LCASE(cc) LIKE '%" . utf8_strtolower($s_cc) . "%'");
        }


        $query = $this->db->get('car_cc');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all cars
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCarsCc($s_cc = 0, $limit = 0, $per_pg = 0)
    {
        if ($s_cc != null) {
            $this->db->where("LCASE(cc) LIKE '%" . utf8_strtolower($s_cc) . "%'");
        }

        $this->db->limit($per_pg, $limit);

        $query = $this->db->get('car_cc');
        return $query;
    }

    /**
     * get car by car id
     * @param type $Id
     * @return object $query
     */
    function GetCarsCcById($Id)
    {
        $this->db->where('id_cc', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('car_cc');
        return $query;
    }

    /**
     * change cars publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishCc($Id)
    {
        $this->db->where('id_cc', $Id);
        // $this->db->where('is_delete',0);
        $query = $this->db->get('car_cc');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_cc', $row['id_cc']);
            $this->db->update('car_cc', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Model name
     * @param string $model
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarsCc($cc, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_cc != ', $Id);
        }
        $this->db->where('cc', $cc);
        $query = $this->db->get('car_cc');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * insert Car Model
     * @param array $data
     * @return int $id last inserted
     */
    function InsertCarsCc($data)
    {
        $this->db->insert('car_cc', $data);
        $id = $this->db->insert_id();
        return $id;
    }


    /**
     * update Car Model
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarsCc($data, $Id)
    {
        $this->db->where('id_cc', $Id);
        $this->db->update('car_cc', $data);
    }


    /**
     * delete Model by id car
     * @param Model $Id
     */
    function DeleteCarsCc($Id)
    {

        $this->db->where_in('id_cc', $Id);
        $this->db->delete('car_cc');
    }


}