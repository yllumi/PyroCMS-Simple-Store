<?php if (count($cart) > 0) { ?>
    <h3>Keranjang Belanja</h3>
    <p>
        <?php echo $this->cart->total_items(); ?> item, 
        <?php echo count($cart); ?> jenis<br />
        <strong>total :</strong> <?php echo $this->settings->currency . ' ' . number_format($this->cart->total(), $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?>
    </p>
    <p>
        <a href="<?php echo site_url('products/checkout'); ?>" class="button"><?php echo $options['checkout_caption']; ?></a>
    </p>
    <br />
<?php } else { ?>
    <small style="display:none;">there is no item in the cart.</small>
<?php } ?>

