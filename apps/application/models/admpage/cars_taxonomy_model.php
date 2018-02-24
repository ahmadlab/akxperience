<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Taxonomy Cars Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Taxonomy Cars model
 ***********************************/
class Cars_taxonomy_model extends CI_Model
{
    /**
     * count total Taxonomy Cars
     * @return int count total rows
     */
    function TotalTestDrive()
    {
        $this->db->select('count(*) as total');

        $query = $this->db->get('car_test_drive');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Taxonomy Cars
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTestDrive($limit = 0, $per_pg = 0)
    {
        $this->db->limit($per_pg, $limit);
        $this->db->select('a.*,a.id_car_test_drive as id,b.msc_code,b.ref_publish,b.brands,b.types,b.series,b.model,b.transmisi,b.car_cc')
            ->from('car_test_drive a')
            ->join('view_cars b', 'a.id_car = b.id', 'inner')
            ->where('b.ref_publish', 1);
        $query = $this->db->get();
        // echo $this->db->_error_message();
        return $query;
    }

    /**
     * get Car Taxonomy Cars by id
     * @param type $Id
     * @return object $query
     */
    function GetTestDriveById($Id)
    {
        $this->db->where('id_car_test_drive', $Id);
        $this->db->limit(1);
        $query = $this->db->get('car_test_drive');
        return $query;
    }

    /**
     * check existing Car name
     * @param string $carname
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarName($carname, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_car_test_drive != ', $Id);
        }
        $this->db->where('id_car', $carname);
        $query = $this->db->get('car_test_drive');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * insert Car Taxonomy
     * @param array $data
     * @return int $id last inserted
     */
    function InsertTestDrive($data)
    {
        $this->db->insert('car_test_drive', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * update Car Taxonomy
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateTestDrive($data, $Id)
    {
        $this->db->where('id_car_test_drive', $Id);
        $this->db->update('car_test_drive', $data);
    }

    /**
     * delete Car Taxonomy by id
     * @param $Id
     * @param $taxonomy
     */
    function DeleteTestDrive($Id)
    {
        $this->db->where_in('id_car_test_drive', $Id);
        // $this->db->where('taxonomy',$taxonomy);
        $this->db->delete('car_test_drive');
    }

}