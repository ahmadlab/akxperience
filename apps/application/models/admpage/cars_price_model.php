<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Car Price Model Class
 * @Author : Seto
 * @Email  : seto@jayadata.co.id
 * @Type   : Model
 * @Desc   : Car Price model
 ***********************************/
class Cars_price_model extends CI_Model
{

    public function populateBranch()
    {
        $branch = $this->db->select('id_ref_location, location')
            ->from('jdi_ref_location')
            ->where('company_code', 'AK')->get()->result_array();

        return $branch;
    }

    /**
     * Insert price to DB
     * @param $data
     */
    public function add($data, $id = 0)
    {
        $this->delete($id);
        $this->db->insert_batch('car_price', $data);
    }

    public function delete($id)
    {
        $this->db->where('car_id', $id);
        $this->db->delete('car_price');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}