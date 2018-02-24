<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Ref Inventory Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Ref Inventory model
 ***********************************/
class Ref_inventory_model extends CI_Model
{
    /**
     * count total Ref Spare Part
     * @param mixed $s_search
     * @return int count total rows
     */
    function TotalRefSparePart($s_search = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search != null) {
            $this->db->where("ref_spare_part", array($s_search));
        }

        $query = $this->db->get('ref_spare_part');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Ref Accessories
     * @param mixed $s_search
     * @return int count total rows
     */
    function TotalRefAccessories($s_search = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search != null) {
            $this->db->where("name", array($s_search));
        }

        $query = $this->db->get('ref_accessories');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Ref Spare Part
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllRefSparePart($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("ref_spare_part", array($search1));
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('ref_spare_part');
        return $query;
    }

    /**
     * retrieve all Ref Accessories
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllRefAccessories($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("name", array($search1));
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('ref_accessories');
        return $query;
    }

    /**
     * get ref spare part by id
     * @param type $Id
     * @return object $query
     */
    function GetRefSparePartById($Id)
    {
        $this->db->where('id_ref_spare_part', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_spare_part');
        return $query;
    }

    /**
     * get ref Accessories by id
     * @param type $Id
     * @return object $query
     */
    function GetRefAccessoriesById($Id)
    {
        $this->db->where('id', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_accessories');
        return $query;
    }

    /**
     * change ref Spare Part publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishRefSparePart($Id)
    {
        $this->db->where('id_ref_spare_part', $Id);
        $query = $this->db->get('ref_spare_part');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_ref_spare_part', $row['id_ref_spare_part']);
            $this->db->update('ref_spare_part', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * change ref Accessories publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishRefAccessories($Id)
    {
        $this->db->where('id', $Id);
        $query = $this->db->get('ref_accessories');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['is_published'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id', $row['id']);
            $this->db->update('ref_accessories', array('is_published' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Ref Spare Part
     * @param string $name
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsRefSparePart($name, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_ref_spare_part != ', $Id);
        }
        $this->db->where('ref_spare_part', $name);
        $query = $this->db->get('ref_spare_part');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Ref Accessories
     * @param string $name
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsRefAccessories($name, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id != ', $Id);
        }
        $this->db->where('name', $name);
        $query = $this->db->get('ref_accessories');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert Ref Spare Part
     * @param array $data
     * @return last id inserted
     */
    function InsertRefSparePart($data)
    {
        $this->db->insert('ref_spare_part', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Insert Ref Accessories
     * @param array $data
     * @return last id inserted
     */
    function InsertRefAccessories($data)
    {
        $this->db->insert('ref_accessories', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update Ref Spare Part
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateRefSparePart($data, $Id)
    {
        $this->db->where('id_ref_spare_part', $Id);
        $this->db->update('ref_spare_part', $data);
    }

    /**
     * Update Ref Accessories
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateRefAccessories($data, $Id)
    {
        $this->db->where('id', $Id);
        $this->db->update('ref_accessories', $data);
    }

    /**
     * delete ref spare part by id
     * @param int $Id
     */
    function DeleteRefSparePart($Id)
    {
        foreach ($Id as $ida) {
            $this->DeleteRefSparePartThumbByID($ida);
        };
        $this->db->where_in('id_ref_spare_part', $Id);
        $this->db->delete('ref_spare_part');
    }

    /**
     * delete ref Accessories by id
     * @param int $Id
     */
    function DeleteRefAccessories($Id)
    {
        $this->db->where_in('id', $Id);
        $this->db->delete('ref_accessories');
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteRefSparePartThumbByID($Id)
    {
        $data = $this->db->get_where('ref_spare_part', array('id_ref_spare_part' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/ref_sparepart/' . $data['thumb_ref'];
            if (file_exists($file)) {
                @unlink($file);
            }
            $this->db->where('id_ref_spare_part', $Id)->update('ref_spare_part', array('thumb_ref' => ''));
        }
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteRefAccessorieshumbByID($Id)
    {
        $data = $this->db->get_where('ref_accessories', array('id' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/ref_accessories/' . $data['thumb_ref'];
            if (file_exists($file)) {
                @unlink($file);
            }
            $this->db->where('id', $Id)->update('ref_accessories', array('thumb_ref' => ''));
        }
    }

}