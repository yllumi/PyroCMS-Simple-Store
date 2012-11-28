<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Images Module
 *
 * @author 		Eko Muhammad Isa
 * @website		http://www.enotes.web.id
 * @package 	PyroCMS
 * @subpackage 	Images Module
 */
class Images_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'simpleshop_images';

        $this->load->library('files/files');
    }

    public function save_image($prm = array()) {

        $result = $this->db->insert($this->_table, $prm);

        if($result){
			$result = $this->db->insert_id();
		}
        return $result;
    }

    public function set_default_image($id = '') {

		if(empty($id)){
			return false;
		}

        $file = Files::get_file($id);
		
		if($file['status']){
			$this->db->where('slug', $file['data']->folder_slug);
			$this->db->update('simpleshop_products', array('default_image_id'=> $id));

			return $this->db->affected_rows();
		}else{
			return false;
		}
    }

    public function unset_if_default($id = '') {
        if(empty($id)){
            return false;
        }

        $this->db->where('default_image_id', $id);
        $this->db->update('simpleshop_products', array('default_image_id'=> 0));

        return $this->db->affected_rows();

    }

    /**
     * Gets a Files folder object based on the Product/Name slug.
     *
     * @param string $slug The Slug to query
     * @return object or boolean FALSE on failure
     * @access public
     */
    public function get_file_folder_by_slug($slug)
    {

        $result = $this->db->where('slug', $slug)->get('file_folders');

        if( $result->num_rows() )
        {
            return $result->row();
        }
        
        return FALSE;
    }

    /**
     * Creates a new file folder within a given parent ID, Title and Slug.
     *
     * @param integer $parent The parent folder ID
     * @param string $title The title of the new folder
     * @param string $slug The folder slug
     * @return array or boolean
     * @access public
     */
    public function create_file_folder($parent, $title, $slug)
    {

        // Variables
        $return         = array();
        $original_slug  = $slug;
        $original_title = $title;

        // Append title name if required
        while( $this->db->where('slug', $slug)->get('file_folders')->num_rows() )
        {
            $i++;
            $slug  = $original_slug.'-'.$i;
            $title = $original_title.'-'.$i;
        }

        // Build insert data
        $insert = array(
                        'parent_id'        => $parent, 
                        'slug'             => $slug, 
                        'name'             => $title,
                        'location'         => 'local',
                        'remote_container' => '',
                        'date_added'       => now(), 
                        'sort'             => now()
                    );

        // Insert it
        if( $this->db->insert('file_folders', $insert) )
        {

            // Build return data
            $insert['id'] = $this->db->insert_id();

            // Return
            return $insert;
        }

        // Failed
        return FALSE;
    }

}
