<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Acc_price_model extends CI_Model
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
        $this->db->insert_batch('acc_price', $data);
    }

    public function delete($id)
    {
        $this->db->where('acc_id', $id);
        $this->db->delete('acc_price');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}