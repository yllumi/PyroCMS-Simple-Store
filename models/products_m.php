<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Specials Module
 *
 * @author 		Patrick Kivits - Woodits Webbureau
 * @website		http://woodits.nl
 * @package 	PyroCMS
 * @subpackage 	Specials Module
 */
class Products_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'simpleshop_products';
    }

    public function get_all() {
        $this->db
                ->select('p.*, c.name as category_name, i.`id` as img_id, i.`product_id`, i.`filename`, i.`thumbnailname`, i.`uploadedname`, i.`isdefault` ')
                ->from('simpleshop_products as p')
                ->join('simpleshop_categories as c', 'c.id = p.category', 'left')
                ->join('(select * from '.$this->db->dbprefix("simpleshop_images").' where isdefault = 1) as i', 'i.product_id = p.id', 'left')
                ->order_by('category_name', 'asc')
                ->order_by('name', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_simple($id) {
        $this->db
                ->select('p.*, c.name as category_name')
                ->from('simpleshop_products as p')
                ->join('simpleshop_categories as c', 'c.id = p.category', 'left')
                ->where('p.id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    public function get($id) {
        $this->db
                ->select('p.*, c.name as category_name, i.`id` as img_id, i.`product_id`, i.`filename`, i.`thumbnailname`, i.`uploadedname`, i.`isdefault` ')
                ->from('simpleshop_products as p')
                ->join('simpleshop_categories as c', 'c.id = p.category', 'left')
                ->join('(select * from '.$this->db->dbprefix("simpleshop_images").' where isdefault = 1) as i', 'i.product_id = p.id', 'left')
                ->where('p.id', $id);

        $query = $this->db->get();
        $product = $query->row();

        $this->db
                ->from('simpleshop_products_x_fields as x')
                ->join('simpleshop_fields as f', 'f.id = x.field', 'left')
                ->where('x.product', $product->id);

        $query = $this->db->get();
        $fields = $query->result();

        foreach ($fields as $field) {
            // Put the custom fields in there own array
            $custom_field[$field->field] = $field->value;

            // And also in the main array for easy refference
            if (!isset($product->{$field->slug})) {
                // Only if the array not already contains the key
                $product->{$field->slug} = $field->value;
            }
        }
        if (isset($custom_field)) {
            $product->custom_fields = $custom_field;
        }

        return $product;
    }

    public function get_by_slug($slug) {
        $this->db
                ->select('p.*, c.name as category_name')
                ->from('simpleshop_products as p')
                ->join('simpleshop_categories as c', 'c.id = p.category', 'left')
                ->where('p.slug', $slug);

        $query = $this->db->get();
        $product = $query->row();

        $this->db
                ->from('simpleshop_products_x_fields as x')
                ->join('simpleshop_fields as f', 'f.id = x.field', 'left')
                ->where('x.product', $product->id);

        $query = $this->db->get();
        $fields = $query->result();

        foreach ($fields as $field) {
            // Put the custom fields in there own array
            $custom_field[$field->field] = $field->value;

            // And also in the main array for easy refference
            if (!isset($product->{$field->slug})) {
                // Only if the array not already contains the key
                $product->{$field->slug} = $field->value;
            }
        }
        if (isset($custom_field)) {
            $product->custom_fields = $custom_field;
        }

        $product->images = $this->get_images($slug);

        return $product;
    }

    public function get_for_cart($slug) {
        $this->db->from('simpleshop_products')->where('slug', $slug);

        $query = $this->db->get();
        $product = $query->row_array();

        $this->db->from('simpleshop_products_x_fields as x')
                ->join('simpleshop_fields as f', 'f.id = x.field', 'left')
                ->where('x.product', $product['id']);

        $query = $this->db->get();
        $fields = $query->result_array();

        foreach ($fields as $field) {
            // Put the custom fields in there own array
            $custom_field[$field['slug']] = $field['value'];

            // And also in the main array for easy refference
            if (!isset($product[$field['slug']])) {
                // Only if the array not already contains the key
                $product[$field['slug']] = $field['value'];
            }
        }
        if (isset($custom_field)) {
            $product['custom_fields'] = $custom_field;
        }

        return $product;
    }

    public function get_by_category($category) {
        $this->db
                ->select('p.*, c.name as category_name')
                ->from('simpleshop_products as p')
                ->join('simpleshop_categories as c', 'c.id = p.category', 'left')
                ->where('p.category', $category)
                ->order_by('category_name', 'asc')
                ->order_by('name', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    //create a new item
    public function create($input) {
        $to_insert = array(
            'name' => $input['name'],
            'slug' => ($input['slug']) ? $input['slug'] : $this->_check_slug($input['name']),
            'category' => $input['category'],
            'price' => ($input['price'])?$input['price']:0,
            'description' => $input['description']
        );

        $result = $this->db->insert($this->_table, $to_insert);

		if($result){
			$result = $this->db->insert_id();
			if (isset($input['custom_field'])) {

				foreach ($input['custom_field'] as $field => $value) {
					$to_insert = array(
						'product' => $result,
						'field' => $field,
						'value' => $value
					);

					$this->db->insert('simpleshop_products_x_fields', $to_insert);
				}
			}
		}
        return $result;
    }

    //make sure the slug is valid
    public function _check_slug($slug) {
        $slug = strtolower($slug);
        $slug = preg_replace('/\s+/', '-', url_title($slug, 'dash', TRUE));

        return $slug;
    }

    //create a new item
    public function update($id, $input) {
        $to_insert = array(
            'name' => $input['name'],
            'slug' => ($input['slug']) ? $input['slug'] : $this->_check_slug($input['name']),
            'category' => $input['category'],
            'price' => $input['price'],
            'description' => $input['description']
        );

        $this->db->where('id', $id);
        $this->db->update($this->_table, $to_insert);

        if (isset($input['custom_field'])) {

            foreach ($input['custom_field'] as $field => $value) {
                $to_insert = array(
                    'product' => $id,
                    'field' => $field,
                    'value' => $value
                );

                $this->db->where('product', $id);
                $this->db->insert('simpleshop_products_x_fields', $to_insert);
            }
        }
        return true;
    }

    public function get_categories() {
        return $this->db->get('simpleshop_categories')->result();
    }

    public function get_fields() {
        $this->db->from('simpleshop_fields')
                ->order_by('order', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_images($id = '') {
        $this->load->library('files/files');
        $this->load->model('images_m');

        if(empty($id)){
            return false;
        }
        if(!is_numeric($id)){
            $row = $this->images_m->get_file_folder_by_slug($id);
            $id = $row->id;
        }

        $files = Files::folder_contents($id);
        return $files['data']['file'];
    }

}
