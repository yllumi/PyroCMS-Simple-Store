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
class Orders_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'simpleshop_orders';
    }

    public function get_all() {
        $this->db
                ->select('o.*, c.name as category_name')
                ->from('simpleshop_orders as o')
                ->join('simpleshop_order_items as c', 'c.id = p.category', 'left')
                ->order_by('category_name', 'asc')
                ->order_by('name', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get($id) {
        $this->db
                ->select('p.*, c.name as category_name')
                ->from('simpleshop_products as p')
                ->join('simpleshop_categories as c', 'c.id = p.category', 'left')
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
            'price' => $input['price'],
            'thumbnail' => $input['thumbnail'],
            'image' => $input['image'],
            'description' => $input['description']
        );

        $this->db->insert($this->_table, $to_insert);

        if (isset($input['custom_field'])) {

            $product = $this->db->insert_id();
            foreach ($input['custom_field'] as $field => $value) {
                $to_insert = array(
                    'product' => $product,
                    'field' => $field,
                    'value' => $value
                );

                $this->db->insert('simpleshop_products_x_fields', $to_insert);
            }
        }
        return true;
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
            'thumbnail' => $input['thumbnail'],
            'image' => $input['image'],
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
        $this->db->from('fields')
                ->order_by('order', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    function save_customer_info($data) {
        $this->db->insert('simpleshop_customers', $data);
        return $this->db->insert_id();
    }

    function save_order($total, $orders_data, $customer) {
        $order = array(
            'customer_id' => $customer['customer_id'],
            'ordered_on' => date('Y-m-d H:i:s'),
            'shipped_on' => date('Y-m-d H:i:s'),
            'subtotal' => $total,
            'shipping' => 0,
            'tax' => 0,
            'total' => $total,
            'status' => 1,
            'order_code' => random_string('numeric', 8)
        );
        $this->db->insert('simpleshop_orders', $order);
        $order_id = $this->db->insert_id();
        
        foreach ($orders_data as $order_data) {
            $order_items = array(
                'order_id' => $order_id,
                'product_id' => $order_data['id'],
                'quantity' => $order_data['qty'],
                'price' => $order_data['price'],
                'total' => $order_data['subtotal']
            );
            $this->db->insert('simpleshop_order_items', $order_items);
        };
        return $order['order_code'];
    }

}
