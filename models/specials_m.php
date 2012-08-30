<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Products Module
 *
 * @author 		Patrick Kivits - Woodits Webbureau
 * @website		http://woodits.nl
 * @package 	PyroCMS
 * @subpackage 	Specials Module
 */
class Specials_m extends MY_Model {

	public function __construct()
	{		
		parent::__construct();
		$this->_table = 'simpleshop_specials';
	}
	
	public function get_current()
	{
		$this->db->from($this->_table)
			->where('start <=', date('Y-m-d'))
			->where('end >=', date('Y-m-d'));
			
		$query = $this->db->get();
			
		return $query->result();

	}
	
	//create a new item
	public function create($input)
	{
		$to_insert = array(
			'name' => $input['name'],
			'start' => date("Y-m-d", strtotime($input['start'])),
			'end' => date("Y-m-d", strtotime($input['end'])),
			'slug' => $this->_check_slug($input['name']),
			'description' => $input['description']
		);
		
		if($this->db->insert($this->_table, $to_insert)) {
			$this->assign_special_products($this->db->insert_id());
		}

		return true;
	}
	
	//create a new item
	public function update($id, $input)
	{
		$to_insert = array(
			'name' => $input['name'],
			'start' => date("Y-m-d", strtotime($input['start'])),
			'end' => date("Y-m-d", strtotime($input['end'])),
			'slug' => $this->_check_slug($input['name']),
			'description' => $input['description']
		);
		
		$this->db
			->where('id', $id)
			->update($this->_table, $to_insert);
			
		return true;
	}
	
	private function assign_special_products($id)
	{
		$this->db
			->where('special', 0)
			->update('simpleshop_specials_x_products', array('special' => $id));
	}

	//make sure the slug is valid
	public function _check_slug($slug)
	{
		$slug = strtolower($slug);
		$slug = preg_replace('/\s+/', '-', url_title($slug, 'dash', TRUE));

		return $slug;
	}
	
	public function get_all()
	{
		$this->db
			->from($this->_table)	
			->order_by('start', 'desc')
			->order_by('end', 'desc');
		
		$query = $this->db->get();
		return $query->result();
	}
	
	public function get_products()
	{
		return $this->db->get('simpleshop_products')->result();
	}	
	
	public function add_product($input)
	{
		$to_insert = array(
			'special' => $input['special'],
			'product' => $input['product'],
			'old_price' => str_replace(',', '.', str_replace('.', '', $input['old_price'])),
			'new_price' => str_replace(',', '.', str_replace('.', '', $input['new_price']))
		);
		
		$this->db->insert('simpleshop_specials_x_products', $to_insert);
	}
	
	public function edit_product($input)
	{
		$to_insert = array(
			'old_price' => str_replace(',', '.', str_replace('.', '', $input['old_price'])),
			'new_price' => str_replace(',', '.', str_replace('.', '', $input['new_price']))
		);
		
		$this->db
			->where('id', $input['id'])
			->update('simpleshop_specials_x_products', $to_insert);
			
		return true;
	}
	
	public function delete_product($input)
	{
		$this->db
			->where('id', $input['id'])
			->delete('simpleshop_specials_x_products');
			
		return true;
	}
	
	public function get_special_products($special = 0)
	{
		$this->db
			->select('s.id, s.old_price, s.new_price, p.name, c.name as category_name, p.id as product_id')
			->from('simpleshop_specials_x_products as s')
			->where('s.special', $special)
			->join('simpleshop_products as p', 'p.id = s.product')
			->join('simpleshop_categories as c', 'c.id = p.category', 'left')
			->order_by('category', 'asc')
			->order_by('name', 'asc');
			
		$query = $this->db->get();
		return $query->result();
	}
	
	public function get_special_product($id = 0)
	{
		$this->db
			->select('s.id, s.old_price, s.new_price, p.name, c.name as category_name')
			->from('simpleshop_specials_x_products as s')
			->where('s.id', $id)
			->join('simpleshop_products as p', 'p.id = s.product')
			->join('simpleshop_categories as c', 'c.id = p.category', 'left')
			->limit(1);
			
		$query = $this->db->get();
		return $query->result();
	}
	
}
