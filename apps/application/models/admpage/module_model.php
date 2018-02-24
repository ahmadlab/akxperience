<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Module_model Class
 * @Author  : Latada
 * @Email   : mac_ [at] gxrg [dot] org
 * @Type     : Model
 * @Desc     : Module model
 *************************************/
class Module_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /********* using for admin side **********/
    function getTotalModule($search = null)
    {
        $this->db->select('count(*) as total');
        if ($search != null) {
            $this->db->where("LCASE(module_title) LIKE '%" . utf8_strtolower($search) . "%'");
        }
        $query = $this->db->get('modules');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    function GetAllModule($search = null, $limit = 0, $perpage = 0)
    {
        if ($search != null) {
            $this->db->where("LCASE(module_title) LIKE '%" . utf8_strtolower($search) . "%'");
        }
        if ($perpage > 0) {
            $this->db->limit($perpage, $limit);
        }
        $query = $this->db->get('modules');

        return $query;
    }

    function GetModuleByID($Id = 0)
    {
        $this->db->where('id_module', $Id);
        $this->db->limit(1);
        $this->db->order_by('id_module', 'desc');
        $query = $this->db->get('modules');

        return $query;
    }

    function UpdateModule($Id, $data)
    {
        $this->db->where('id_module', $Id);
        $this->db->update('modules', $data);
    }

    function InsertModule($data)
    {
        $this->db->insert('modules', $data);
        $id_module = $this->db->insert_id();
        return $id_module;
    }

    function DeleteModule($Id)
    {
        $query = $this->GetModuleByID($Id);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $this->db->where('id_module', $row['id_module']);
            $this->db->delete('modules');
        }
    }

    function CheckExistsModule($module, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_module !=', $Id);
        }
        $this->db->where('module', $module);
        $this->db->where('module_link', $module);
        $query = $this->db->get('modules');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    function CheckModuleInstall($id)
    {
        $this->db->where('id_module', $id);
        $this->db->limit(1);
        $this->db->order_by('id_module', 'desc');
        $query = $this->db->get('modules');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['is_installed'] == 1) {
                return 'installed';
            } else {
                return 'uninstall';
            }
        } else {
            return false;
        }
    }

    function getModuleClassName($id)
    {
        $this->db->where('id_module', $id);
        $this->db->limit(1);
        $this->db->order_by('id_module', 'desc');
        $query = $this->db->get('modules');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return strtolower($row['module']);
        } else {
            return false;
        }
    }

    function ChangeInstall($Id)
    {
        $this->db->where('id_module', $Id);
        $this->db->where('is_delete', 0);
        $query = $this->db->get('modules');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['id_ref_publish'] == 1) {
                $val = 2;
            } else {
                $val = 1;
            }

            $this->db->where('id_module', $row['id_module']);
            $this->db->update('modules', array('id_ref_publish' => $val));

            if ($val == 2) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }


}

/* End of file module_model.php */
/* Location: ./application/model/webcontrol/module_model.php */

