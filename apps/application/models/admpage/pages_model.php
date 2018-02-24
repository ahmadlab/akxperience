<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Pages_model
 * @Author : Latada
 * @Email    : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : pages model
 *************************************/
class Pages_model extends CI_Model
{

    /**
     * Constructor
     * @desc to load extends
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * function getTotalPages.
     * @desc this function is same as main
     * @param string $search1 search menu title (optional)
     * @param string $search3 search publish reference (optional)
     * @param string $search3 search parent pages (optional)
     * @param string $search4 search site pages (optional)
     * @return string total rows
     */
    function getTotalPages($search1 = null, $search2 = null, $search3 = null, $search4 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("LCASE(menu_title) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where('static_pages.id_ref_publish', $search2);
        }
        if ($search3 != null) {
            $this->db->where('static_pages.id_parent_pages', $search3);
        }
        if ($search4 != null) {
            $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
            $this->db->where('pages_sites.id_site', $search4);
        }
        $this->db->where('static_pages.is_delete', 0);
        $query = $this->db->get('static_pages');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * function GetAllPages.
     * @desc this function is same as main
     * @param string $search1 search menu title (optional)
     * @param string $search3 search publish reference (optional)
     * @param string $search3 search parent pages (optional)
     * @param string $search4 search site pages (optional)
     */
    function GetAllPages($search1 = null, $search2 = null, $search3 = null, $search4 = null, $limit = 0, $perpage = 0)
    {
        if ($search1 != null) {
            $this->db->where("LCASE(menu_title) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where('static_pages.id_ref_publish', $search2);
        }
        if ($search3 != null) {
            $this->db->where('static_pages.id_parent_pages', $search3);
        }
        if ($search4 != null) {
            $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
            $this->db->where('pages_sites.id_site', $search4);
        }

        if ($perpage > 0) {
            $this->db->limit($perpage, $limit);
        }
        $this->db->where('static_pages.is_delete', 0);
        $this->db->order_by('static_pages.urut', 'asc');
        $query = $this->db->get('static_pages');
        return $query;
    }

    /**
     * get site id of pages
     * @param type $id_page
     * @return type id site
     */
    function getPagesIDSites($id_page)
    {
        $id_site = 0;
        $this->db->where('pages_sites.id_static_pages', $id_page);
        $this->db->order_by('pages_sites.id_site', 'asc');
        $this->db->join('sites', 'sites.id_site=pages_sites.id_site', 'left');
        $query = $this->db->get('pages_sites');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $id_site = $row->id_site;
        }
        return $id_site;
    }

    /**
     * get site name of pages
     * @param type $id_page
     * @return type list of site name
     */
    function getPagesSites($id_page)
    {
        $return = '--';
        $this->db->where('pages_sites.id_static_pages', $id_page);
        $this->db->order_by('pages_sites.id_site', 'asc');
        $this->db->join('sites', 'sites.id_site=pages_sites.id_site', 'left');
        $query = $this->db->get('pages_sites');
        $a = 0;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $site_name[$a] = $row['site_name'];
                $a++;
            }
            $return = implode(', ', $site_name);
        }
        return $return;
    }

    /**
     * get parent by id parent
     * @param type $id_parent
     * @return type string $query
     */
    function GetParentById($id_parent)
    {
        //$this->db->order_by('id_parents_menu_admin', 'asc');
        $this->db->order_by('id_static_pages', 'asc');
        $this->db->limit(1);
        $this->db->where('id_static_pages', $id_parent);
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');
        return $query;
    }

    /**
     * get parent title by id parent
     * @param type $id_parent
     * @return type string parent title
     */
    function GetParentTitleById($id_parent)
    {
        if ($id_parent == 0) {
            return 'ROOT';
        } else {
            $this->db->limit(1);
            $this->db->where('id_static_pages', $id_parent);
            $this->db->where('is_delete', 0);
            $query = $this->db->get('static_pages');
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                return $row['menu_title'];
            } else {
                return '--';
            }
        }
    }

    /**
     * get all parent
     * @param type $id_parent
     * @return type string $query
     */
    function getAllParent($id_parent = 0)
    {
        $this->db->where('id_parent_pages', $id_parent);
        $this->db->where('is_delete', 0);
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('static_pages');
        return $query;
    }

    /**
     * get max urut
     * @param type $id_parent
     * @return type string $row['urut'] maximum urut value
     */
    function GetRefUrutMax($id_parent = null)
    {
        $this->db->select('max(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parent_pages', $id_parent);
        }
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');
        $row = $query->row_array();

        return $row['urut'];
    }

    /**
     * get minimum urut value
     * @param type $id_parent
     * @return type type string $row['urut'] minimum urut value
     */
    function GetRefUrutMin($id_parent = null)
    {
        $this->db->select('min(urut) as urut');
        if ($id_parent) {
            $this->db->where('id_parent_pages', $id_parent);
        }
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');
        $row = $query->row_array();

        return $row['urut'];
    }

    /**
     * get pages by id page
     * @param type $Id
     * @return type string $query
     */
    function GetPagesByPageID($Id = 0)
    {
        $this->db->where('id_static_pages', $Id);
        $this->db->limit(1);
        $this->db->order_by('id_static_pages', 'desc');
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');

        return $query;
    }

    /**
     * get page content
     * @param type $keyword_file
     * @return type string $query
     */
    function GetPageContent($keyword_file)
    {
        $this->db->where('menu_path', $keyword_file);
        $this->db->where('file_control', $keyword_file);
        $this->db->where('is_page', 2);
        $this->db->limit(1);
        $this->db->order_by('id_static_pages', 'desc');
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');

        return $query;
    }

    /**
     * update pages by id
     * @param type $Id
     * @param type $data
     */
    function UpdatePage($Id, $data)
    {
        $this->db->where('id_static_pages', $Id);
        $this->db->update('static_pages', $data);
    }

    /**
     * insert pages
     * @param type $data
     * @return type integer $id_page last inserted id
     */
    function InsertPage($data)
    {
        $this->db->insert('static_pages', $data);
        $id_page = $this->db->insert_id();
        return $id_page;
    }

    /**
     * delete page by id (set status to deleted, but not delete the database just incase)
     * @param type $Id
     */
    function DeletePage($Id)
    {
        $query = $this->GetPagesByPageID($Id);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $data = array('is_delete' => 1);
            $this->db->where('id_static_pages', $Id);
            $this->db->update('static_pages', $data);
        }
    }

    /**
     * check existing page path file
     * @param type $path
     * @param type $Id
     * @return type boolean (true or false)
     */
    function CheckExistsPath($path, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_static_pages !=', $Id);
        }
        $this->db->where('menu_path', $path);
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * delete picture page by id page
     * @param type $id_pages
     * @param type $type
     */
    function DeletePictureByID($id_pages, $type)
    {
        $data = array();
        $this->db->where('id_static_pages', $id_pages);
        $query = $this->db->get('static_pages');

        $path = './uploads/static/';

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($row['picture_' . $type] != '' && file_exists($path . $row['picture_' . $type])) {
                    unlink($path . $row['picture_' . $type]);
                }

                $data = array('picture_' . $type => '');

                $this->db->where('id_static_pages', $id_pages);
                $this->db->update('static_pages', $data);
            }
        }
    }

    /**
     * delete file page by id page
     * @param type $id_pages
     */
    function DeleteFileByID($id_pages)
    {
        $this->db->where('id_static_pages', $id_pages);
        $data = array();
        $query = $this->db->get('static_pages');

        $path = './uploads/static/';

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($row['file_content'] != '' && file_exists($path . $row['file_content'])) {
                    unlink($path . $row['file_content']);
                }

                $data = array('file_content' => '');

                $this->db->where('id_static_pages', $id_pages);
                $this->db->update('static_pages', $data);
            }
        }
    }

    /**
     * get all installed module
     * @return string $query
     */
    function getInstalledModuleList()
    {
        $this->db->where('is_installed', 1);
        $this->db->order_by('module_title', 'asc');
        $query = $this->db->get('modules');
        return $query;
    }

    /**
     * get age gallery
     * @param type $Id
     * @return type string $query
     */
    function GetGallery($Id = null)
    {
        if ($Id != null) {
            $this->db->where('id_static_pages', $Id);
        }

        $this->db->order_by('id_static_pages', 'desc');
        $query = $this->db->get('pages_gallery');

        return $query;
    }

    /**
     * get all widget
     * @return type string $query
     */
    function getAllWidget()
    {
        $this->db->where('is_delete', 0);
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('widget');

        return $query->result_array();
    }

    /**
     * get all widget by id page and position
     * @param type $id_pages
     * @param type $position
     * @return type string $query
     */
    function GetWidgetByPageId($id_pages, $position = null)
    {
        $this->load->database();
        if ($position != null) {
            $this->db->where('widget.position', $position);
        }
        $this->db->where('pages_widget.id_static_pages', $id_pages);
        $this->db->order_by('widget.urut', 'asc');
        $this->db->join('widget', 'widget.id_widget=pages_widget.id_widget', 'left');
        $query = $this->db->get('pages_widget');
        return $query;
    }

    /**
     * get widget in right position by id page
     * @param type $id_pages
     * @return type string $query
     */
    function GetWidgetRightByPageId($id_pages)
    {
        $this->load->database();

        $this->db->where('pages_widget.id_static_pages', $id_pages);
        $this->db->where('widget.is_delete', 0);
        $this->db->where('widget.position', 'Right');
        $this->db->order_by('widget.urut', 'asc');
        $this->db->join('widget', 'widget.id_widget=pages_widget.id_widget', 'left');
        $query = $this->db->get('pages_widget');
        return $query;
    }

    /**
     * widget connection to pages
     * @param type $id_pages
     * @param type $id_widget
     * @return type integer $id_connect connector id
     */
    function WidgetConnection($id_pages, $id_widget)
    {
        $this->db->where('id_static_pages', $id_pages);
        $this->db->where('id_widget', $id_widget);
        $query = $this->db->get('pages_widget');
        $id_connect = '';
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $id_connect = $row['id_widget'];
        }
        return $id_connect;
    }

    /**
     * delete widget connection pages by id page
     * @param type $id_pages
     */
    function DeleteWidgetPages($id_pages)
    {
        $this->db->where('id_static_pages', $id_pages);
        $query = $this->db->get('pages_widget');

        if ($query->num_rows() > 0) {
            $this->db->where('id_static_pages', $id_pages);
            $this->db->delete('pages_widget');
        }
    }

    /**
     * insert widget connection to pages
     * @param type $id_pages
     * @param type $id_widget
     */
    function InsertWidgetPages($id_pages, $id_widget)
    {
        $data = array();
        $data = array('id_static_pages' => $id_pages, 'id_widget' => $id_widget);
        $this->db->insert('pages_widget', $data);
    }

    /**
     * get all sites
     * @return type string $query
     */
    function getAllSites()
    {
        // $this->db->order_by('id_site', 'asc');
        // $query = $this->db->get('sites');

        // return $query->result_array();
    }

    /**
     * get id site from connection pages
     * @param type $id_pages
     * @param type $id_site
     * @return type integer $id_connect
     */
    function SitesConnection($id_pages, $id_site)
    {
        $this->db->where('id_static_pages', $id_pages);
        $this->db->where('id_site', $id_site);
        $this->db->order_by('id_site', 'asc');
        $query = $this->db->get('pages_sites');
        $id_connect = '';
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $id_connect = $row['id_site'];
        }
        return $id_connect;
    }

    /**
     * insert site connection to pages
     * @param type $id_pages
     * @param type $id_widget
     */
    function InsertSitePages($id_pages, $id_site)
    {
        $data = array();
        $data = array('id_static_pages' => $id_pages, 'id_site' => $id_site);
        $this->db->insert('pages_sites', $data);
    }

    /**
     * delete widget connection pages by id page
     * @param type $id_pages
     */
    function DeleteSitePages($id_pages)
    {
        $this->db->where('id_static_pages', $id_pages);
        $query = $this->db->get('pages_sites');

        if ($query->num_rows() > 0) {
            $this->db->where('id_static_pages', $id_pages);
            $this->db->delete('pages_sites');
        }
    }

    /**
     * change pages sort
     * @param type $id_page
     * @param type $parent_id
     * @param type $urut
     * @param type $direction
     */
    function ChangeSort($id_page, $parent_id, $urut, $direction)
    {
        $this->db->where('id_static_pages', $id_page);
        $this->db->where('id_parent_pages', $parent_id);
        $this->db->where('urut', $urut);
        $this->db->where('is_delete', 0);
        $this->db->order_by('urut', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('static_pages');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($direction == "down") {
                $this->db->where('urut > ', $urut);
                $this->db->order_by('urut', 'asc');
                $this->db->limit(1);
                $query_1 = $this->db->get('static_pages');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdatePage($row_1['id_static_pages'], array('urut' => $urut));
                    $this->UpdatePage($id_page, array('urut' => $row_1['urut']));
                }
            } elseif ($direction == "up") {
                $this->db->where('urut < ', $urut);
                $this->db->order_by('urut', 'desc');
                $this->db->limit(1);
                $query_1 = $this->db->get('static_pages');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdatePage($row_1['id_static_pages'], array('urut' => $urut));
                    $this->UpdatePage($id_page, array('urut' => $row_1['urut']));
                }
            }
        }
    }

    /**
     * change page publish status
     * @param type $Id
     * @return type string status
     */
    function ChangePublish($Id)
    {
        $this->db->where('id_static_pages', $Id);
        $this->db->where('is_delete', 0);
        $query = $this->db->get('static_pages');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['id_ref_publish'] == 1) {
                $val = 2;
            } else {
                $val = 1;
            }

            $this->db->where('id_static_pages', $row['id_static_pages']);
            $this->db->update('static_pages', array('id_ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }


    /**
     * get meta tags by path file for use in front end
     * @param type $path
     * @return type array $meta_tags
     */
    function getMetaTags($path)
    {
        $this->db->limit(1);
        $this->db->where("(LCASE(menu_path) = '" . $path . "' OR LCASE(module) = '" . $path . "')");
        $this->db->where('id_ref_publish', 1);
        $this->db->where('is_delete', 0);
        $this->db->order_by('urut', 'asc');
        $query = $this->db->get('static_pages');
        $meta_tags = array();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $meta_key = $row['meta_keyword'];
            $meta_desc = strip_tags($row['meta_desc']);
            $meta_img = $row['picture_content'];
            if ($meta_img == '') {
                $meta_img = $row['picture_header'];
            }
            if ($meta_key == '') {
                $meta_key = $row['title'];
            }
            if ($meta_desc == '') {
                $meta_desc = strip_tags($row['content']);
            }
            if ($meta_img == '') {
                $meta_img = base_url('images/logo.png');
            } else {
                $meta_img = base_url('uploads/pages/' . $meta_img);
            }

            $meta_tags = array(
                'keywords' => $meta_key . ' - ' . get_setting('app_title'),
                'desc' => get_setting('web_description') . ' - ' . $meta_desc,
                'image' => $meta_img,
            );
        } else {
            $meta_tags = array(
                'keywords' => get_setting('app_title'),
                'desc' => get_setting('web_description'),
                'image' => base_url('images/logo.png'),
            );
        }
        return $meta_tags;
    }

    /**
     * get metatag module
     * @param type $module
     * @param type $path
     * @return array meta tags
     */
    function getModuleMetaTags($module, $path)
    {
        $this->db->limit(1);
        $this->db->where('LCASE(menu_path)', $path);
        $this->db->where('id_ref_publish', 1);
        $this->db->where('is_delete', 0);
        $this->db->order_by('urut', 'asc');

        if ($module == 'news') {
            $db = 'news';
        } elseif ($module == 'gallery') {
            $db = 'galleries';
        } else {
            $db = $module;
        }
        $query = $this->db->get($db);
        $meta_tags = array();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $meta_desc = (!empty($row['content'])) ? strip_tags($row['content']) : $row['title'];
            if ($module == 'gallery') {
                $meta_img = $row['picture_header'];
            } else {
                $meta_img = $row['picture_content'];
            }
            if ($meta_img == '') {
                $meta_img = $row['picture_thumbnail'];
            }

            if ($meta_img == '') {
                $meta_img = base_url('images/logo.png');
            } else {
                $meta_img = base_url('uploads/' . $module . '/' . $meta_img);
            }

            $meta_tags = array(
                'keywords' => $row['title'] . ' - ' . get_setting('app_title'),
                'desc' => get_setting('web_description') . ' - ' . $meta_desc,
                'image' => $meta_img,
            );
        } else {
            $this->getMetaTags($path);
        }
        return $meta_tags;
    }

    /**
     * get id site by site url
     * @param type $path
     * @return int $return id site
     */
    function getIDSiteByPath($path)
    {
        $this->db->where('site_url', $path);
        $this->db->order_by('id_site', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $return = $row['id_site'];
        } else {
            $return = 0;
        }
        return $return;
    }

    /**
     * get site url path by id site
     * @param type $id_site
     * @return type string site url path
     */
    function getSitePathByID($id_site)
    {
        $this->db->select('site_url');
        $this->db->where('id_site', $id_site);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['site_url'];
        } else {
            return '';
        }
    }

    /**
     * get site name by id site
     * @param type $id_site
     * @return string site name
     */
    function getSiteNameByIdSite($id_site)
    {
        $this->db->select('site_name');
        $this->db->where('id_site', $id_site);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['site_name'];
        } else {
            return '';
        }
    }

    /**
     * get page by keyword path file
     * @param type $id_site
     * @param type $path
     * @return type string $query
     */
    function GetPageByPath($id_site = 1, $path = '')
    {
        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.menu_path', $path);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->limit(1);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');

        return $query;
    }


    function GetPageByPathModule($id_site = 1, $path = '')
    {
        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.module', $path);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->limit(1);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');

        return $query;
    }

    /**
     * get parent navigation
     * @param type $keyword
     * @return int $id_page
     */
    function GetParentNavigation($keyword = '', $module = 'path', $id_site = 1)
    {
        if ($module == 'module') {
            $query = $this->GetPageByPathModule($id_site, $keyword);
        } else {
            $query = $this->GetPageByPath($id_site, $keyword);
        }
        $id_page = 0;
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['id_parent_pages'] == 0) {
                $id_page = $row['id_static_pages'];
            } else {
                $this->db->where('pages_sites.id_site', $id_site);
                $this->db->where('static_pages.id_static_pages', $row['id_parent_pages']);
                $this->db->where('static_pages.is_delete', 0);
                $this->db->where('static_pages.id_ref_publish', 1);
                $this->db->limit(1);
                $this->db->order_by('static_pages.urut', 'asc');
                $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
                $query2 = $this->db->get('static_pages');

                if ($query2->num_rows() > 0) {
                    $row2 = $query2->row_array();
                    if ($row2['page_type'] == 2) {
                        $id_page = $this->GetParentNavigation($row2['module'], 'module', $id_site);
                    } else {
                        $id_page = $this->GetParentNavigation($row2['menu_path'], 'path', $id_site);
                    }
                } else {
                    $id_page = 0;
                }
            }
        } else {
            $id_page = 0;
        }
        return $id_page;
    }

    /**
     * get navigation menu
     * @param int $id_site
     * @param int $id_parent
     * @return string $query
     */
    function getMenuNavigation($id_site = 1, $id_parent = 0)
    {
        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_parent_pages', $id_parent);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->where('static_pages.is_header', 1);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');
        return $query;
    }


    function getFooterNavigation($id_site = 1, $id_parent = 0)
    {
        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_parent_pages', $id_parent);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->where('static_pages.is_footer', 1);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');
        return $query;
    }

    function getSubMenu($id_site = 1, $id_parent = 0, $is_active = 0)
    {
        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_static_pages', $id_parent);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->where('static_pages.is_header', 1);
        $this->db->limit(1);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $query = $this->db->get('static_pages');

        $print = '';

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['id_parent_pages'] == 0) {
                $this->db->where('pages_sites.id_site', $id_site);
                $this->db->where('static_pages.is_delete', 0);
                $this->db->where('static_pages.id_parent_pages', $row['id_static_pages']);
                $this->db->where('static_pages.id_ref_publish', 1);
                $this->db->where('static_pages.is_header', 1);
                $this->db->order_by('static_pages.urut', 'asc');
                $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
                $query2 = $this->db->get('static_pages');

                if ($query2->num_rows() > 0) {
                    foreach ($query2->result_array() as $row2) {
                        $sub_url = ($row2['page_type'] == 2) ? $row2['module'] : 'main/pages/' . $row2['menu_path'];
                        $sub_title = $row2['menu_title'];
                        $active = ($is_active == $row2['id_static_pages']) ? 'class="active"' : '';

                        $print .= '<a href="' . site_url($sub_url) . '" ' . $active . '>' . $sub_title . '</a>';
                    }
                }
                return $print;
            } else {
                $this->getSubMenu($id_site, $row['id_parent_pages']);
            }
        }
    }

    /**
     * get site logo
     * @param type $id_site
     * @return string site logo file
     */
    function getSiteLogo($id_site)
    {
        $this->db->where('id_site', $id_site);
        $this->db->where('id_ref_publish', 2);
        $this->db->where('is_delete', 0);
        $this->db->limit(1);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['site_logo'];
        } else {
            return '';
        }
    }


    /**
     * get page by keyword path file
     * @param type $id_site
     * @param type $path
     * @return type string $query
     */
    function get_page_by_path($id_site = 1, $path = '')
    {
        $this->db->where('pages_sites.id_site', $id_site);
        $this->db->where('static_pages.menu_path', $path);
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->limit(1);
        $this->db->order_by('static_pages.urut', 'asc');
        $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
        $this->db->join('sites', 'sites.id_site=pages_sites.id_site');
        $query = $this->db->get('static_pages');

        return $query;
    }


    /**
     * get the child of page based on the id page
     * @param int $id_page id of page
     * @param int $id_site site refered to
     * return string $query
     */
    function get_child_page_by_id_page($id_page, $id_site = 1)
    {
        if ($id_site) {
            $this->db->join('pages_sites', 'pages_sites.id_static_pages=static_pages.id_static_pages', 'left');
            $this->db->where('pages_sites.id_site', $id_site);
        }
        $this->db->where('static_pages.is_delete', 0);
        $this->db->where('static_pages.id_ref_publish', 1);
        $this->db->where('static_pages.id_parent_pages', $id_page);
        $this->db->order_by('static_pages.urut', 'asc');

        $query = $this->db->get('static_pages');
        return $query;
    }

}

/* End of file pages_model.php */
/* Location: ./application/model/pages_model.php */

