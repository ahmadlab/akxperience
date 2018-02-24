<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Transaction Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Transaction model
 ***********************************/
class Transaction_model extends CI_Model
{
    /**
     * count total Accessories Transaction
     * @param mixed $s_search1
     * @return int count total rows
     */
    function TotalTransAccessories($s_search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->where_in("id_user", array($s_search1));
        }

        $query = $this->db->get('car_accessories_transaction');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Spare Part Transaction
     * @param mixed $s_search1
     * @return int count total rows
     */
    function TotalTransSparePart($s_search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->where_in("id_user", array($s_search1));
        }

        $query = $this->db->get('car_spare_parts_transaction');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Accessories Transaction
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTransAccessories($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where_in("id_user", array($search1));
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_accessories_transaction');
        return $query;
    }

    /**
     * retrieve all Spare Part Transaction
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTransSparePart($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where_in("id_user", array($search1));
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_spare_parts_transaction');
        return $query;
    }

    /**
     * get Accessories Transaction by id
     * @param type $Id
     * @return object $query
     */
    function GetTransAccessoriesById($Id)
    {
        $this->db->where('id_car_accessories_transaction', $Id);
        $this->db->limit(1);
        $query = $this->db->get('car_accessories_transaction');
        return $query;
    }

    /**
     * get Spare Part Transaction by id
     * @param type $Id
     * @return object $query
     */
    function GetTransSparePartById($Id)
    {
        $this->db->where('id_car_spare_part_transaction', $Id);
        $this->db->limit(1);
        $query = $this->db->get('car_spare_parts_transaction');
        return $query;
    }

    /**
     * Insert Accessories
     * @param array $data
     * @return last id inserted
     */
    function InsertTransAccessories($data)
    {
        $this->db->insert('car_accessories_transaction', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Insert Spare Part
     * @param array $data
     * @return last id inserted
     */
    function InsertTransSparePart($data)
    {
        $this->db->insert('car_spare_parts_transaction', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update Accessories
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateTransAccessories($data, $Id)
    {
        $this->db->where('id_car_accessories_transaction', $Id);
        $this->db->update('car_accessories_transaction', $data);
    }

    /**
     * Update Spare Part
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateTransSparePart($data, $Id)
    {
        $this->db->where('id_car_spare_part_transaction', $Id);
        $this->db->update('car_spare_parts_transaction', $data);
    }


}