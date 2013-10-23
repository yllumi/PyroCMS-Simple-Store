<h3><?php echo lang('products:your_shopping_cart'); ?></h3>

<?php echo form_open('products/checkout/update_cart'); ?>

<table cellpadding="0" cellspacing="0" style="width:100%" class="table">
    <thead>
        <tr>
            <th>QTY</th>
            <th><?php echo lang('products:description'); ?></th>
            <th style="text-align:right"><?php echo lang('products:price'); ?></th>
            <th style="text-align:right"><?php echo lang('products:subtotal'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>

        <?php foreach ($this->cart->contents() as $items): ?>

            <?php echo form_hidden($i . '[rowid]', $items['rowid']); ?>

            <tr>
                <td><?php echo form_input(array('name' => $i . '[qty]', 'value' => $items['qty'], 'maxlength' => '3')); ?></td>
                <td><?php echo $items['name']; ?></td>
                <td style="text-align:right"><?php echo $this->settings->currency . ' ' . number_format($items['price'], $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
                <td style="text-align:right"><?php echo $this->settings->currency . ' ' . number_format($items['subtotal'], $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
            </tr>

            <?php $i++; ?>

        <?php endforeach; ?>

        <tr>
            <td colspan="2"></td>
            <td style="text-align:right"><strong>Total</strong></td>
            <td style="text-align:right"><strong><?php echo $this->settings->currency . ' ' . number_format($this->cart->total(), $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></strong></td>
        </tr>
        <?php if ($this->cart->total_items() > 0): ?>
            <tr>
                <td colspan="4">
                    <a class="button left" href="<?php echo site_url('products/checkout/empty_cart'); ?>" onclick="return confirm('Anda yakin akan mengosongkan keranjang?')"><?php echo lang('products:empty_cart'); ?></a>
                    <?php echo form_submit('', lang('products:update_cart'), 'class="button left"'); ?>
                </td>
            </tr>
            <tr>
                <td  colspan="4">
                    <a class="button right r" href="<?php echo site_url('products/checkout/shipment'); ?>"><?php echo lang('products:shipment'); ?></a>
                    <a class="button right" href="<?php echo site_url('products'); ?>"><?php echo lang('products:continue_shopping'); ?></a>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php echo form_close(); ?>