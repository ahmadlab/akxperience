<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Ref Car Color Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Ref Car Color
 ***********************************/
class Ref_car_color extends CI_Model
{
    /**
     * count total cars Ref Car model
     * @param mixed $s_series search by car series
     * @param mixed $s_model search by car model
     * @return int count total rows
     */
    function TotalCarsColor($s_color = null)
    {
        $this->db->select('count(*) as total');
        if ($s_color != null) {
            $this->db->where("LCASE(color) LIKE '%" . utf8_strtolower($s_color) . "%'");
        }


        $query = $this->db->get('ref_color');
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
    function GetAllCarsColor($s_color = 0, $limit = 0, $per_pg = 0)
    {
        if ($s_color != null) {
            $this->db->where("LCASE(color) LIKE '%" . utf8_strtolower($s_color) . "%'");
        }

        $this->db->limit($per_pg, $limit);

        $query = $this->db->get('ref_color');
        return $query;
    }

    /**
     * get car by car id
     * @param type $Id
     * @return object $query
     */
    function GetCarsColorById($Id)
    {
        $this->db->where('id_ref_color', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('ref_color');
        return $query;
    }

    /**
     * change cars publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishColor($Id)
    {
        $this->db->where('id_ref_color', $Id);
        // $this->db->where('is_delete',0);
        $query = $this->db->get('ref_color');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_ref_color', $row['id_ref_color']);
            $this->db->update('ref_color', array('ref_publish' => $val));

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
    function CheckExistsCarsColor($cc, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_ref_color != ', $Id);
        }
        $this->db->where('color', $cc);
        $query = $this->db->get('ref_color');
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
    function InsertCarsColor($data)
    {
        $this->db->insert('ref_color', $data);
        $id = $this->db->insert_id();
        return $id;
    }


    /**
     * update Car Model
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarsColor($data, $Id)
    {
        $this->db->where('id_ref_color', $Id);
        $this->db->update('ref_color', $data);
    }


    /**
     * delete Model by id car
     * @param Model $Id
     */
    function DeleteCarsColor($Id)
    {

        $this->db->where_in('id_ref_color', $Id);
        $this->db->delete('ref_color');
    }


}