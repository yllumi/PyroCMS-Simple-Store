<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Products Module
 *
 * @author      Toni Haryanto
 * @website	http://toniharyanto.com
 * @package 	PyroCMS
 * @subpackage 	Checkout Module
 */
class Checkout extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('cart');

        $this->load->model('orders_m');
        $this->load->model('custom_fields_m');
        $this->load->library('form_validation');
        $this->lang->load('general');
        $this->lang->load('products');

        $this->customer_info_rules = array(
            array(
                'field' => 'firstname',
                'label' => lang('products:firstname'),
                'rules' => 'trim|max_length[32]|alpha|required'
            ),
            array(
                'field' => 'lastname',
                'label' => lang('products:lastname'),
                'rules' => 'trim|max_length[32]|alpha'
            ),
            array(
                'field' => 'address',
                'label' => lang('products:address'),
                'rules' => 'trim|max_length[200]|required'
            ),
            array(
                'field' => 'email',
                'label' => lang('products:email'),
                'rules' => 'trim|valid_email|required'
            ),
            array(
                'field' => 'phone',
                'label' => lang('products:phone'),
                'rules' => 'trim|numeric|required'
            ),
            array(
                'field' => 'city',
                'label' => lang('products:city'),
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'postalcode',
                'label' => lang('products:postalcode'),
                'rules' => 'trim|numeric|exact_length[5]'
            )
        );
    }

    public function index() {
        $this->template->build('checkout');
    }

    public function add_to_cart($slug = null) {
        if (!$slug)
            redirect('products');

        $data['items'] = $this->orders_m->get_for_cart($slug);
        $data['items']['qty'] = (intval($this->input->post('qty'))) ? trim($this->input->post('qty')) : $data['items']['min_buy'];

        $cart = array(
            'id' => $data['items']['id'],
            'qty' => $data['items']['qty'],
            'price' => $data['items']['price'],
            'name' => $data['items']['name'],
            'options' => ($data['items']['custom_fields'])?$data['items']['custom_fields']:null
        );

        $this->cart->insert($cart);

        redirect('products/checkout');
    }

    public function update_cart() {
        $this->cart->update($this->input->post());
        redirect(getenv('HTTP_REFERER'));
    }

    public function empty_cart() {
        $this->cart->destroy();
        redirect(getenv('HTTP_REFERER'));
    }

    public function shipment() {
        $this->form_validation->set_rules($this->customer_info_rules);

        if ($this->form_validation->run()) {
            if ($id = $this->orders_m->save_customer_info($this->input->post())) {
                $customer = $this->input->post();
                $customer['customer_id'] = $id;
                $this->session->set_userdata('customer', $customer);

                // save order
                $ordercode = $this->orders_m->save_order($this->cart->total(), $this->cart->contents(), $this->session->userdata('customer'));
                $this->session->set_userdata('ordercode', $ordercode);

                // go to payment information page
                redirect('products/checkout/payment');
            } else {
                $this->template->build('shipment');
            }
        }

        $this->template->build('shipment');
    }

    public function payment() {
        // if cart is empty
        if (!$this->cart->contents()) {
            redirect('/');
        }

        $this->load->library('email');
        // set some data for sending email
        $data['customer'] = $this->session->userdata('customer');
        $data['subject']    = $this->settings->site_name . ' - Order Invoice';
        $data['slug']       = 'order_invoice';
        $data['to']         = $data['customer']['email'];
        $data['from']       = $this->settings->server_email;
        $data['name']       = $this->settings->site_name;
        $data['reply-to']   = $this->settings->contact_email;
        // Add in some extra details
        $data['order_items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        $data['ordercode'] = $this->session->userdata('ordercode');       
        // send the email using the template event found in system/cms/templates/
        Events::trigger('email', $data, 'array');

        // then send email notification to admin
        $data2['subject']    = $this->settings->site_name . ' - Order Notification';
        $data2['slug']       = 'Order Notification';
        $data2['to']         = $this->settings->contact_email;
        $data2['from']       = $this->settings->server_email;
        $data2['name']       = $data['customer']['firstname'].' di '.$this->settings->site_name;
        $data2['reply-to']   = $this->settings->server_email;
        $data2['total']      = $this->cart->total();
        Events::trigger('email', $data2, 'array');
        
        $this->cart->destroy();
        $this->session->unset_userdata('customer');
        $this->session->unset_userdata('ordercode');

        $this->template
                ->title('Order Product', 'Invoice')
                ->build('payment', $data);
    }
}
