<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Inventory Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Inventory model
 ***********************************/
class Inventory_model extends CI_Model
{
    /**
     * count total Spare Part
     * @param mixed $s_sparepart_type search by spare part type
     * @param mixed $s_sparepart search by spare part
     * @return int count total rows
     */
    function TotalSparePart($s_sparepart_type = null, $s_sparepart = null)
    {
        $this->db->select('count(*) as total');
        if ($s_sparepart_type != null) {
            $this->db->where_in("id_ref_spare_part", array($s_sparepart_type));
        }
        if ($s_sparepart != null) {
            $this->db->where("LCASE(spare_part) LIKE '%" . utf8_strtolower($s_sparepart) . "%'");
        }

        $query = $this->db->get('car_spare_parts');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Accessories
     * @param mixed $search1
     * @param mixed $search2
     * @return int count total rows
     */
    function TotalAccessories($search1 = null, $search2 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("LCASE(id_ref_accessories) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(accessories) LIKE '%" . utf8_strtolower($search2) . "%'");
        }

        $query = $this->db->get('car_accessories');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Spare Part
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllSparePart($search1 = null, $search2 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where_in("id_ref_spare_part", array($search1));
        }
        if ($search2 != null) {
            $this->db->where("LCASE(spare_part) LIKE '%" . utf8_strtolower($search2) . "%'");
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_spare_parts');
        return $query;
    }

    /**
     * retrieve all Accessories
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllAccessories($search1 = null, $search2 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("LCASE(id_ref_accessories) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(accessories) LIKE '%" . utf8_strtolower($search2) . "%'");
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_accessories');
        return $query;
    }

    /**
     * get spare part by spare part id
     * @param type $Id
     * @return object $query
     */
    function GetSparePartById($Id)
    {
        $this->db->where('id_car_spare_part', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('car_spare_parts');
        return $query;
    }

    /**
     * get Accessories by Accessories id
     * @param type $Id
     * @return object $query
     */
    function GetAccessoriesById($Id)
    {
        $this->db->where('id_car_accessories', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('car_accessories');
        return $query;
    }

    /**
     * change cars Spare Part publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishSparePart($Id)
    {
        $this->db->where('id_car_spare_part', $Id);
        $query = $this->db->get('car_spare_parts');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_car_spare_part', $row['id_car_spare_part']);
            $this->db->update('car_spare_parts', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * change cars Accessories publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishAccessories($Id)
    {
        $this->db->where('id_car_accessories', $Id);
        $query = $this->db->get('car_accessories');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_car_accessories', $row['id_car_accessories']);
            $this->db->update('car_accessories', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }


    /**
     * check existing Spare Part
     * @param string $sparepart
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsSparePart($sparepart, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_car_spare_part != ', $Id);
        }
        $this->db->where('spare_part', $sparepart);
        $query = $this->db->get('car_spare_parts');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Accessories
     * @param string $name
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsAccessories($name, $type, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_car_accessories != ', $Id);
        }
        $where = array('accessories' => $name, 'id_type' => $type);
        // $this->db->where('accessories',$name);
        $this->db->where($where);
        $query = $this->db->get('car_accessories');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert Spare Part
     * @param array $data
     * @return last id inserted
     */
    function InsertSparePart($data)
    {
        $this->db->insert('car_spare_parts', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Insert Accessories
     * @param array $data
     * @return last id inserted
     */
    function InsertAccessories($data)
    {
        $this->db->insert('car_accessories', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update Spare Part
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateSparePart($data, $Id)
    {
        $this->db->where('id_car_spare_part', $Id);
        $this->db->update('car_spare_parts', $data);
    }

    /**
     * Update Accessories
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateAccessories($data, $Id)
    {
        $this->db->where('id_car_accessories', $Id);
        $this->db->update('car_accessories', $data);
    }


    /**
     * delete car spare part by id car spare part
     * @param series $Id
     */
    function DeleteCarSparePart($Id)
    {
        foreach ($Id as $ida) {
            $this->DeleteSparePartThumbByID($ida);
        };
        $this->db->where_in('id_car_spare_part', $Id);
        $this->db->delete('car_spare_parts');
    }

    /**
     * delete car Accessories by id car Accessories
     * @param series $Id
     */
    function DeleteCarAccessories($Id)
    {
        foreach ($Id as $ida) {
            $this->DeleteAccessoriesThumbByID($ida);
        };
        $this->db->where_in('id_car_accessories', $Id);
        $this->db->delete('car_accessories');
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteSparePartThumbByID($Id)
    {
        $data = $this->db->get_where('car_spare_parts', array('id_car_spare_part' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/sparepart/' . $data['thumb'];
            if (file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id_car_spare_part', $Id)->update('car_spare_parts', array('thumb' => ''));
        }
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteAccessoriesThumbByID($Id)
    {
        $data = $this->db->get_where('car_accessories', array('id_car_accessories' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/accessories/' . $data['thumb'];
            if (file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id_car_accessories', $Id)->update('car_accessories', array('thumb' => ''));
        }
    }


}