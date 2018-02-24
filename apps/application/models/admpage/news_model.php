<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * News Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : News model
 ***********************************/
class News_model extends CI_Model
{
    /**
     * count total News
     * @param mixed $s_search
     * @return int count total rows
     */
    function TotalNews($s_search = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search != null) {
            $this->db->like("title", $s_search);
        }

        $this->db->where('type', 'news');

        $query = $this->db->get('news');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Brochure
     * @param mixed $s_search
     * @return int count total rows
     */
    function TotalBrochure($s_search = null)
    {
        $this->db->select('count(*) as total');
        if ($s_search != null) {
            $this->db->where("title", $s_search);
        }

        $query = $this->db->get('brochure');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total News
     * @param mixed $words
     * @return int count total rows
     */
    function TotalNewsTemp($s_search = null)
    {
        // $data = $this->crawling($words);
        if ($data) {
            $this->db->select('count(*) as total')
                ->where('words', $words);
            $query = $this->db->get('news_tmp');
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                return $row['total'];
            }
        }
        return 0;
    }

    /**
     * retrieve all News
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllNews($s_search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != null) {
            $this->db->like("title", $s_search1);
        }

        $this->db->where('type', 'news');

        $this->db->order_by('ref_publish desc, is_new desc, create_date desc')->limit($per_pg, $limit);
        $query = $this->db->get('news');
        return $query;
    }

    /**
     * retrieve all News Temp
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllNewsTemp($limit = 0, $per_pg = 200)
    {
        $this->db->order_by('id', 'desc')->limit($per_pg, $limit);
        $query = $this->db->get('news_tmp');
        return $query;
    }

    /**
     * retrieve all Brochure
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllBrochure($s_search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != null) {
            $this->db->where("title", $s_search1);
        }

        $this->db->order_by('create_date', 'desc')->limit($per_pg, $limit);
        $query = $this->db->get('brochure');
        return $query;
    }

    /**
     * get News by id
     * @param type $Id
     * @return object $query
     */
    function GetNewsById($Id)
    {
        $this->db->where('id_news', $Id);
        $this->db->limit(1);
        $query = $this->db->get('news');
        return $query;
    }

    /**
     * get Brochure by id
     * @param type $Id
     * @return object $query
     */
    function GetBrochureById($Id)
    {
        $this->db->where('id_brochure', $Id);
        $this->db->limit(1);
        $query = $this->db->get('brochure');
        return $query;
    }

    /**
     * change News publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishNews($Id)
    {
        $this->db->where('id_news', $Id);
        $query = $this->db->get('news');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_news', $row['id_news']);
            $this->db->update('news', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * change News status to new stuff
     * @param type $Id
     * @return type string current status
     */
    function ChangeStatNews($Id)
    {
        $this->db->where('id_news', $Id);
        $query = $this->db->get('news');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['is_new'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_news', $row['id_news']);
            $this->db->update('news', array('is_new' => $val));

            if ($val == 1) {
                return 'Yes';
            } else {
                return 'No';
            }
        }
    }

    /**
     * change Brochure publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishBrochure($Id)
    {
        $this->db->where('id_brochure', $Id);
        $query = $this->db->get('brochure');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_brochure', $row['id_brochure']);
            $this->db->update('brochure', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing News
     * @param string $news
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsNews($news, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_news != ', $Id);
        }
        $this->db->where('title', $news);
        $query = $this->db->get('news');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Brochure
     * @param string $news
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsBrochure($news, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_brochure != ', $Id);
        }
        $this->db->where('title', $news);
        $query = $this->db->get('brochure');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Insert News
     * @param array $data
     * @return last id inserted
     */
    function InsertNews($data)
    {
        $this->db->insert('news', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Insert Brochure
     * @param array $data
     * @return last id inserted
     */
    function InsertBrochure($data)
    {
        $this->db->insert('brochure', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * Update News
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateNews($data, $Id)
    {
        $this->db->where('id_news', $Id);
        $this->db->update('news', $data);
    }

    /**
     * Update Brochure
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdateBrochure($data, $Id)
    {
        $this->db->where('id_brochure', $Id);
        $this->db->update('brochure', $data);
    }

    /**
     * delete car spare part by id car spare part
     * @param series $Id
     */
    function DeleteNews($Id)
    {
        foreach ($Id as $ida) {
            $this->DeleteNewsThumbByID($ida);
        };
        $this->db->where_in('id_news', $Id);
        $this->db->delete('news');
    }

    /**
     * delete Brochure by id
     * @param $Id
     */
    function DeleteBrochure($Id)
    {
        foreach ($Id as $ida) {
            $this->DeleteBrochureThumbByID($ida);
            $this->DeleteBrochureFileByID($ida);
        };
        $this->db->where_in('id_brochure', $Id);
        $this->db->delete('brochure');
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteNewsThumbByID($Id)
    {
        $data = $this->db->get_where('news', array('id_news' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/news/' . $data['thumb'];
            if ($data['thumb'] != 'news_default.png') {
                if (file_exists($file)) {
                    unlink($file);
                }
                $this->db->where('id_news', $Id)->update('news', array('thumb' => ''));
            }
        }
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteBrochureThumbByID($Id)
    {
        $data = $this->db->get_where('brochure', array('id_brochure' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();

            $file = './uploads/brochure/' . $data['thumb'];
            if ($data['thumb'] != '' && file_exists($file)) {
                unlink($file);
                unlink(substr($file, 0, -4) . '_thumb' . substr($file, -4));
            }
            $this->db->where('id_brochure', $Id)->update('brochure', array('thumb' => ''));
        }
    }

    /**
     * Delete File By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteBrochureFileByID($Id)
    {
        $data = $this->db->get_where('brochure', array('id_brochure' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/brochure/file/' . $data['file'];
            if ($data['file'] != '' && file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id_brochure', $Id)->update('brochure', array('file' => ''));
        }
    }

    /**
     * Move News Temp into News By Id
     * @param int $id news temp
     * @return void
     */
    function MoveIntoNews($Id)
    {
        if (is_array($Id)) {
            $this->db->where_in('id', $Id);
        } else {
            $this->db->where('id', $Id);
        }
        $news = $this->db->get('news_tmp');
        if ($news->num_rows() > 0) {
            $news = $news->result_array();
            foreach ($news as $v) {
                $thumb_url = $v['thumb'];
                $thumb = '';

                if ($v['thumb'] == '') {
                    $thumb = 'news_default.png';
                    $thumb_url = '';
                }

                $buff[] = array(
                    'title' => $v['title'],
                    'content' => $v['content'],
                    'thumb_url' => $thumb_url,
                    'thumb' => $thumb
                );
            }
            $this->db->insert_batch('news', $buff);
            $this->db->where_in('id', $Id)->delete('news_tmp');
        }

    }

    /**
     * crawling some news from external sites
     * @return void
     */
    function crawling()
    {
        $target = array('http://social.ford.com/syndication/?c=&np=&t=1');
        $resources = cURL_multiple_thread($target);
        // $stat		= false;
        foreach ($resources as $k => $v) {
            if ($v != '') {
                $node = parse_url($k);
                // $stat  = true;
                $obj = new SimpleXMLElement($v);
                $n = 0;
                foreach ($obj->channel->item as $buff) {
                    ++$n;
                    $title = (string)$buff->title;
                    $desc = (string)$buff->description;
                    $link = (string)$buff->guid;
                    $pubDate = (string)$buff->pubDate;
                    $raw = (string)$buff->enclosure;
                    $img = '';
                    if (isset($raw['type']) && isset($raw['url'])) {
                        // $img = "<img src='".$raw['url']."' />";
                        $img = (string)$raw['url'];
                    }

                    $buffs[] = array(
                        'title' => $title,
                        'content' => $desc,
                        'publish_date' => $pubDate,
                        'thumb' => $img,
                        'node' => $node['host'],
                        'link' => $link
                    );
                    if ($n == 20) {
                        break;
                    }
                }
                $this->db->insert_batch('news_tmp', $buffs);
            }
        }
        // return $stat;
    }
}