<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package 		PyroCMS
 * @subpackage 		Cart Widget
 * @author		Toni Haryanto
 *
 * Display a shopping cart
 *
 */
class Widget_Cart extends Widgets {

    public $title = array(
        'en' => 'Cart',
        'id' => 'Kerajang'
    );
    public $description = array(
        'en' => 'Display a shopping cart after there is some item added into cart.',
        'id' => 'Menampilkan keranjang belanja.'
    );
    public $author = 'Toni Haryanto';
    public $website = 'http://toniharyanto.com';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'checkout_caption',
            'label' => 'Checkout Caption',
        )
    );

    public function form($options) {
        !empty($options['checkout_caption']) OR $options['checkout_caption'] = "Checkout";

        return array(
            'options' => $options
        );
    }

    public function run($options) {
        $this->load->library('cart');
        return array('cart' => $this->cart->contents());
    }

}
