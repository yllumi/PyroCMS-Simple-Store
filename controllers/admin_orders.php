<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Products Module
 *
 * @author 	Toni Haryanto
 * @website	http://toniharyanto.com
 * @package 	PyroCMS
 * @subpackage 	Orders Module
 */
class Admin_orders extends Admin_Controller
{
	protected $section = 'orders';

	public function __construct()
	{
		parent::__construct();

		$this->load->model('orders_m');
		$this->load->library('form_validation');
		$this->lang->load('general');
		$this->lang->load('orders');

		$this->item_validation_rules = array(
			array(
				'field' => 'name',
				'label' => lang('categories:name'),
				'rules' => 'trim|max_length[100]|required'
			),
			array(
				'field' => 'type',
				'label' => lang('categories:type'),
				'rules' => 'trim|max_length[100]|required'
			)
		);

		$this->template
			->append_js('module::jquery.min.js')
			->append_js('module::jquery.ui.js')
			->append_js('module::jquery.cookie.js')
			->append_js('module::admin.js')
			->append_css('module::admin.css');
	}

	public function index()
	{
		$items = $this->orders_m->get_all();

		$this->template
			->title($this->module_details['name'])
			->set('items', $items)
			->build('admin/orders/items');
	}

	public function view($id = 0)
	{
		$id = $this->uri->segment(5);
		$id or redirect('admin/'.$this->module);
		
		$order = $this->orders_m->get($id);
		$products = $this->orders_m->get_order_products($id);
		$status = array();
		foreach($this->orders_m->get_order_status() as $value){
			$status[$value->id] = $value->status;
		};


		$this->template
			->title($this->module_details['name'], lang('orders:view'))
			->set('order', $order)
			->set('products', $products)
			->set('status', $status)
			->build('admin/orders/detail');
	}
	
	public function delete($id = 0)
	{
		if (isset($_POST['btnAction']) AND is_array($_POST['action_to']))
		{
			$this->custom_fields_m->delete_many($this->input->post('action_to'));
		}
		elseif (is_numeric($id))
		{
			$this->custom_fields_m->delete($id);
		}
		redirect('admin/'.$this->module.'/fields');
	}
	
	public function order()
	{
		if($this->input->is_ajax_request()) {
			$this->custom_fields_m->order($this->input->post('order'));
		}
	}
}
