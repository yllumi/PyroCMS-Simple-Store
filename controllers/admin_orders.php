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

	public function create()
	{
		$this->form_validation->set_rules($this->item_validation_rules);

		if($this->form_validation->run())
		{
			if($this->custom_fields_m->create($this->input->post()))
			{
				$this->session->set_flashdata('success', lang('general:success'));
				redirect('admin/'.$this->module.'/fields');
			}
			else
			{
				$this->session->set_flashdata('error', lang('general:error'));
				redirect('admin/'.$this->module.'/fields/create');
			}
		}
		
		foreach ($this->item_validation_rules AS $rule)
		{
			$fields->{$rule['field']} = $this->input->post($rule['field']);
		}

		$this->template
			->title($this->module_details['name'], lang('fields:create'))
			->append_metadata( $this->load->view('fragments/wysiwyg', $this->data, TRUE) )
			->set('fields', $fields)
			->build('admin/fields/form');
	}
	
	public function edit($id = 0)
	{
		$id = $this->uri->segment(5);
		$id or redirect('admin/'.$this->module);
		
		$fields = $this->custom_fields_m->get($id);

		$this->form_validation->set_rules($this->item_validation_rules);

		if($this->form_validation->run())
		{
			unset($_POST['btnAction']);
			
			if($this->custom_fields_m->update($id, $this->input->post()))
			{
				$this->session->set_flashdata('success', lang('general:success'));
				redirect('admin/'.$this->module.'/fields');
			}
			else
			{
				$this->session->set_flashdata('error', lang('general:error'));
				redirect('admin/'.$this->module.'/fields/create');
			}
		}
		
		$this->template
			->title($this->module_details['name'], lang('fields:edit'))
			->append_metadata( $this->load->view('fragments/wysiwyg', $this->data, TRUE) )
			->set('fields', $fields)
			->build('admin/fields/form');
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
