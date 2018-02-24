<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Admin_menu Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Adminmenu model
 ***********************************/
class Adminmenu_model extends CI_Model
{

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
    }

    /**
     * get menu admin by path file
     * @param type $file
     * @return type string $query
     */
    function GetMenuAdminByFile($file)
    {
        $this->db->where("file", $file);
        $this->db->limit(1);
        $this->db->order_by('id_menu_admin', 'desc');
        $query = $this->db->get("menu_admin");

        return $query;
    }

    /**
     * get admin menu list option
     * @param int $id_parent
     * @param int $is_superadmin
     * @return string $query
     */
    function getAdminMenuList($id_parent = 0, $is_superadmin = null)
    {
        $this->db->where('id_parents_menu_admin', $id_parent);
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('menu_admin');
        return $query;
    }

    /**
     * get menu admin by group
     * @param type $group
     * @param type $parent
     * @return type
     */
    function GetMenuAdminByGroup($group, $parent = 0)
    {
        $this->db->select('menu_admin.*,auth_pages.id_auth_pages, auth_pages.id_auth_user_group');
        $this->db->where('id_parents_menu_admin', $parent);
        $this->db->where('auth_pages.id_auth_user_group', $group);
        $this->db->order_by('menu_admin.urut', 'asc');
        $this->db->order_by('menu_admin.id_parents_menu_admin', 'asc');
        $this->db->join('auth_pages', 'auth_pages.id_menu_admin = menu_admin.id_menu_admin', 'inner');
        $query = $this->db->get("menu_admin");

        return $query;
    }

    /**
     * check menu admin child
     * @param int $group
     * @param int $parent
     * @return bool true/false
     */
    function CheckMenuChild($group, $parent = 0)
    {
        $this->db->where('id_parents_menu_admin', $parent);
        $this->db->where('auth_pages.id_auth_user_group', $group);
        $this->db->order_by('menu_admin.urut', 'asc');
        $this->db->order_by('menu_admin.id_parents_menu_admin', 'asc');
        $this->db->join('auth_pages', 'auth_pages.id_menu_admin = menu_admin.id_menu_admin', 'left');
        $query = $this->db->get("menu_admin");

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get all front static menu
     * @param int $id_parent
     * @return string $query
     */
    function getAllFrontMenu($id_parent = 0)
    {
        $this->db->where('is_delete', 0);
        $this->db->where('id_parent_pages', $id_parent);
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('static_pages');
        return $query;
    }

    /**
     * get all admin menu
     * @param string $search
     * @param int $is_superadmin
     * @param int $limit
     * @param int $perpage
     * @return string $query
     */
    function getAllAdminMenu($search = '', $is_superadmin = null, $limit = 0, $perpage = 0)
    {
        $this->db->order_by('urut', 'asc');
        if ($search != '') {
            $this->db->where("LCASE(menu) LIKE '%" . utf8_strtolower($search) . "%'");
        }
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $this->db->limit($perpage, $limit);
        $query = $this->db->get('menu_admin');
        return $query;
    }

    /**
     * check menu is super admin
     * @param int $id
     * @return bool
     */
    function check_is_superadmin($id, $is_super_admin)
    {
        $this->db->where('id_menu_admin', $id);
        $this->db->limit(1);
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($is_super_admin == 1) {
                return true;
            } else {
                if ($row['is_superadmin'] == 0 && $is_super_admin == 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * get menu admin title by file path
     * @param type $file
     * @return type string admin menu title
     */
    function getMenuAdminTitle($file)
    {
        $this->db->where("file", $file);
        $this->db->limit(1);
        $this->db->order_by('id_menu_admin', 'desc');
        $query = $this->db->get("menu_admin");

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['menu'];
        } else {
            return '';
        }
    }

    /**
     * get total admin menu
     * @param string $search
     * @param int $is_superadmin
     * @return int $total total record
     */
    function getTotalAdminMenu($search = '', $is_superadmin = null)
    {
        $this->db->select('count(*) as total');
        if ($search != '') {
            $this->db->where("LCASE(menu) LIKE '%" . utf8_strtolower($search) . "%'");
        }
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * get parent name by id parent
     * @param type $id_parent
     * @return type string parent name
     */
    function getParentNameById($id_parent)
    {
        $this->db->order_by('urut', 'asc');
        $this->db->limit(1);
        $this->db->where('id_menu_admin', $id_parent);
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['menu'];
        } else {
            return 'ROOT';
        }
    }

    /**
     * get reference urut for sort
     * @param type $id_parent
     * @return type string maximum field urut
     */
    function GetRefUrut($id_parent = null)
    {
        $this->db->select('max(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('menu_admin');
        return $query;
    }

    /**
     * get reference urut for sort
     * @param type $id_parent
     * @return type string maximum field urut
     */
    function GetRefUrutMax($id_parent = null)
    {
        $this->db->select('max(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['urut'];
        } else {
            return '0';
        }
    }

    /**
     * get reference minimum urut for sort
     * @param type $id_parent
     * @return type string minimum field urut
     */
    function GetRefUrutMin($id_parent = null)
    {
        $this->db->select('min(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['urut'];
        } else {
            return '0';
        }
    }

    /**
     * delete admin menu by id admin menu
     * @param type $Id
     */
    function DeleteAdminMenu($Id = 0)
    {
        $this->DeleteAuthMenu($Id);
        if (is_array($Id)) {
            $this->db->where_in('id_menu_admin', $Id);
        } else {
            $this->db->where('id_menu_admin', $Id);
        }
        $this->db->delete('menu_admin');
    }

    /**
     * delete authentication admin menu by admin menu
     * @param type $id_menu_admin
     */
    function DeleteAuthMenu($id_menu_admin)
    {
        if (is_array($id_menu_admin)) {
            $this->db->where_in('id_menu_admin', $id_menu_admin);
        } else {
            $this->db->where('id_menu_admin', $id_menu_admin);
        }
        $this->db->delete('auth_pages');

    }

    /**
     * get admin menu by id
     * @param type $Id
     * @return type string $query
     */
    function GetAdminMenuById($Id)
    {
        $this->db->where('id_menu_admin', $Id);
        $query = $this->db->get('menu_admin');
        return $query;
    }

    /**
     * update admin menu
     * @param type $Id
     * @param type $data
     */
    function UpdateAdminMenu($Id, $data)
    {
        $this->db->where('id_menu_admin', $Id);
        $this->db->update('menu_admin', $data);
    }

    /**
     * insert admin menu
     * @param type $data
     * @return type integer $id_menu_admin last inserted id admin menu
     */
    function InsertAdminMenu($data)
    {
        $this->db->insert('menu_admin', $data);
        $id_menu_admin = $this->db->insert_id();
        return $id_menu_admin;
    }

    /**
     * change admin menu sort
     * @param type $id_page
     * @param type $parent_id
     * @param type $urut
     * @param type $direction
     */
    function ChangeSort($id_page, $parent_id, $urut, $direction)
    {
        $this->db->where('id_menu_admin', $id_page);
        $this->db->where('id_parents_menu_admin', $parent_id);
        $this->db->where('urut', $urut);
        $this->db->order_by('urut', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($direction == "down") {
                $this->db->where('urut > ', $urut);
                $this->db->order_by('urut', 'asc');
                $this->db->limit(1);
                $query_1 = $this->db->get('menu_admin');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateAdminMenu($row_1['id_menu_admin'], array('urut' => $urut));
                    $this->UpdateAdminMenu($id_page, array('urut' => $row_1['urut']));
                }
            } elseif ($direction == "up") {
                $this->db->where('urut < ', $urut);
                $this->db->order_by('urut', 'desc');
                $this->db->limit(1);
                $query_1 = $this->db->get('menu_admin');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateAdminMenu($row_1['id_menu_admin'], array('urut' => $urut));
                    $this->UpdateAdminMenu($id_page, array('urut' => $row_1['urut']));
                }
            }
        }
    }

    /**
     * check admin menu by group id
     * @param type $id_group
     * @param type $id_menu_admin
     * @return type boolean (true or false)
     */
    function CheckAdminMenuByGroupId($id_group, $id_menu_admin)
    {
        $this->db->where('id_auth_user_group', $id_group);
        $this->db->where('id_menu_admin', $id_menu_admin);
        $query = $this->db->get('auth_pages');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get breadcrumbs menu
     * @param int $id_menu_parent
     * @return array menu
     */
    function getBreadcrumbs($id_menu_parent)
    {
        $return = array();
        $this->db->where('id_menu_admin', $id_menu_parent);
        $this->db->limit(1);
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $href = ($row['file'] == '' || $row['file'] == '#') ? '#' : site_url(getAdminFolder() . '/' . $row['file']);
            $return[] = array(
                'text' => $row['menu'],
                'href' => $href,
                'class' => ''
            );
            $menu = $this->getBreadcrumbs($row['id_parents_menu_admin']);
            $return = array_merge($menu, $return);
        }
        return $return;
    }

    /**
     * get id menu by path/file
     * @param string $file
     * @return int
     */
    function getAdminMenuIdByFile($file)
    {
        $this->db->where('file', $file);
        $this->db->where('file !=', '#');
        $this->db->limit(1);
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('menu_admin');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['id_parents_menu_admin'];
        } else {
            return '0';
        }
    }

    /**
     * get disabled menu admin if there's any record set true, visa versa
     * @param int $id_menu
     * @return bool true/false
     */
    function getDisabledMenuChild($id_menu, $disabled = '')
    {
        $return = false;
        if ($disabled) {
            $this->db->where('id_parents_menu_admin', $id_menu);
            $this->db->order_by('urut', 'asc');
            $query = $this->db->get('menu_admin');
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    if ($row['id_menu_admin'] == $disabled) {
                        $return = true;
                        $disabled = $row['id_menu_admin'];
                    }
                    $this->getDisabledMenuChild($row['id_menu_admin'], $disabled);
                }
            }
        }
        return $return;
    }


    /////////////////////////////////////////////////////////////////////////////////////////////

    function Get_Front_Page($id_site, $id_parent, $site_name, $prefix, $child = '')
    {

        $this->db->select('site_url');
        $this->db->where('id_site', $id_site);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $site_url = $row['site_url'];
            if ($id_site == 1) {
                $site_url = 'navaplus';
            }
        } else {
            $site_url = '';
        }

        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_parent_pages', $id_parent);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');
        $print = '';
        $open = '';
        $key = $this->uri->segment(2);
        $asd = $this->uri->segment(3);
        if ($key == 'pages' && $asd != '') {
            $site = $this->uri->segment(4);
            if ($asd == 'edit') {
                $cek_site = $this->Cek_Site_Url_By_Id_Pages($site);
                $cek_menu_path = $this->Cek_Parent_By_Id($site);
                //echo $site_name.'-'.$cek_site.'<br>';
                if ($site_name == $cek_site || $site_name == $cek_menu_path) {
                    $open = 'in';
                } elseif ($site == $id_parent) {
                    $open = 'in';
                }
            }

        } else {
            $site = $this->uri->segment(2);
            $cek_site = $this->Cek_Site_Url_By_Module($site);
            $cek_menu_path = $this->Cek_Parent_By_Id($cek_site['id_static_pages']);
            //echo $site_name.'-'.$cek_site['site_url'].'<br>';
            if ($site_name == $cek_site['site_url'] || $site_name == $cek_menu_path) {
                $open = 'in';
            }
        }

        //echo $site.'-'.$id_parent.'<br>';

        if ($child) {

            if ($this->uri->segment(4) == $id_parent) {
                $class = 'active';
            } else {
                $class = '';
            }

            $print .= '<div id="' . $site_url . '-' . $site_name . '" class="collapse ' . $open . '">';
            $parent_url = site_url($this->folder . '/pages/edit/' . $id_parent);
            $print .= '<a href="' . $parent_url . '"><button type="button" class="sub-sidenav ' . $class . '">' . $prefix . 'DESCRIPTION</button></a>';
        } else {
            $print .= '<div id="' . $site_name . '" class="collapse ' . $open . '">';
        }


        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {

                $title = $row['title'];
                $page_type = $row['page_type'];
                $menu_path = $row['menu_path'];
                $module = $row['module'];

                //echo $site.'-'.$id_parent.'<br>';
                if ($key == 'pages') {

                    if ($site == $row['id_static_pages']) {
                        $class = 'active';
                    } else {
                        $class = '';
                    }

                } else {
                    if ($cek_site['id_static_pages'] == $row['id_static_pages']) {
                        $class = 'active';
                    } else {
                        $class = '';
                    }
                }

                if ($page_type == 1) {
                    $url = site_url($this->folder . '/pages/edit/' . $row['id_static_pages']);
                } else {
                    $url = site_url($this->folder . '/' . $row['module']);
                }

                if ($this->Cek_Menu_Child($row['id_static_pages'])) {
                    $print .= '<button type="button" class="sub-sidenav" data-toggle="collapse" data-target="#' . $site_name . '-' . $menu_path . '">';
                    $print .= $title . '<img src="' . base_url() . 'assets/images/admin/down.png" style="height:20px; float:right;"></button>';

                    $print .= $this->Get_Front_Page($id_site, $row['id_static_pages'], $menu_path, '-- ', 'child');
                } else {
                    $print .= '<a href="' . $url . '"><button type="button" class="sub-sidenav ' . $class . '">' . $prefix . $title . '</button></a>';
                }
            }
        }
        $print .= '</div>';
        return $print;
    }


    function Cek_Site_Url_By_Id_Pages($id_page)
    {
        $sql = "SELECT a.site_url,a.id_site FROM " . $this->db->dbprefix('sites a') . " , " . $this->db->dbprefix('pages_sites b') . " where b.id_static_pages =$id_page and b.id_site=a.id_site";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $detail = $query->row_array();
            $id_site = $detail['id_site'];

            if ($id_site == 1) {
                $site_url = 'navaplus';
            } else {
                $site_url = $detail['site_url'];
            }

            return $site_url;
        }
    }


    function Cek_Parent_By_Id($id)
    {

        $this->db->where('id_static_pages', $id);
        $this->db->where('is_delete', 0);
        $this->db->where('id_ref_publish', 1);
        $this->db->limit(1);
        $query = $this->db->get('static_pages');

        if ($query->num_rows() > 0) {

            $row = $query->row_array();
            if ($row['id_parent_pages'] != 0) {
                $this->db->where('id_static_pages', $row['id_parent_pages']);
                $this->db->where('is_delete', 0);
                $this->db->where('id_ref_publish', 1);
                $this->db->limit(1);
                $query = $this->db->get('static_pages');
                $asd = $query->row_array();
                return $asd['menu_path'];
            }

        }
    }

    function Cek_Site_Name_By_Path($path)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('sites a') . ", " . $this->db->dbprefix('pages_sites b') . ", " . $this->db->dbprefix('static_pages c') . " where c.menu_path='$path' and c.id_static_pages=b.id_static_pages and b.id_site=a.id_site and c.is_delete=0";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $detail = $query->row_array();
            return $detail;
        }
    }

    function Cek_Menu_Child($id_parent)
    {

        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_parent_pages', $id_parent);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');

        if ($query->num_rows() > 0) {
            return true;
        } else {

            return false;
        }
    }

    function Cek_Site_Url_By_Module($module)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('sites a') . ", " . $this->db->dbprefix('pages_sites b') . ", " . $this->db->dbprefix('static_pages c') . " where c.module='$module' and c.id_static_pages=b.id_static_pages and b.id_site=a.id_site and c.is_delete=0";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $detail = $query->row_array();
            $id_site = $detail['id_site'];
            if ($id_site == 1) {
                $detail['site_url'] = 'navaplus';
            } else {

            }
            return $detail;
        }
    }
}

/* End of file adminmenu_model.php */
/* Location: ./application/model/webcontrol/adminmenu_model.php */


