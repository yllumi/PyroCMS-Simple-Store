<?php
    $curr = $this->settings->currency;
    $decimal = $this->settings->decimal_separator;
    $thousand = $this->settings->thousand_separator;
?>
<section class="title">
	<h4><?php echo lang('orders:order_detail'); ?></h4>
</section>

<section class="item">
    <table>
        <thead>
            <tr class="title"><th width="150px"><?php echo lang('orders:order_code'); ?></th>
                <th width="150px"><?php echo lang('orders:ordered_on'); ?></th>
                <th><?php echo lang('orders:status'); ?></th>
                <th></th></tr>
        </thead>
        <tbody>
            <tr><td><?php echo $order->order_code; ?></td>
                <td><?php echo $order->ordered_on; ?></td>
                <td><?php 
                    echo form_open('products/orders/change_status/'.$order->id);
                    echo form_dropdown('status', $status, $order->status);
                    echo form_submit('update_status', lang('orders:update_status'));
                    echo form_close(); ?></td>
                <td class="actions">
                    <?php echo anchor('admin/products/orders/delete/'.$order->id, lang('general:delete'), 'class="confirm red btn"'); ?></td>
            </tr>
        </tbody>
    </table>
    <br />
    <table>
        <thead>
            <tr><th class="title"><em><?php echo lang('orders:customer_detail'); ?></em></th></tr>
        </thead>
        <tbody>
            <tr><td><?php echo $order->address; ?>
                <?php echo $order->city; ?>
                <?php echo $order->postalcode; ?></td></tr>
            <tr><td><?php echo $order->firstname.' '.$order->lastname; ?> |
                <?php echo $order->email; ?> |
                <?php echo $order->phone; ?></td></tr>
        </tbody>
    </table>
    <br />
    <table>
        <thead>
            <tr><th colspan="5" class="title"><em><?php echo lang('orders:order_items'); ?></em></th></tr>
            <tr><th><?php echo lang('orders:product_name'); ?></th>
                <th><?php echo lang('orders:description'); ?></th>
                <th><?php echo lang('orders:price'); ?></th>
                <th><?php echo lang('orders:qty'); ?></th>
                <th><?php echo lang('orders:total'); ?></th></tr>
        </thead>
        <tbody>
            <?php foreach ($products as $value) : ?>
            <tr><td><?php echo $value->name; ?></td>
                <td><?php echo $value->description; ?></td>
                <td><?php echo $curr.' '.number_format($value->price, 2, $decimal, $thousand); ?></td>
                <td><?php echo $value->quantity; ?></td>
                <td><?php echo $curr.' '.number_format($value->total, 2, $decimal, $thousand); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr><td colspan="4" style="text-align:right"><strong><?php echo lang('orders:total'); ?></strong></td>
                <td><strong><?php echo $curr.' '.number_format($order->total, 2, $decimal, $thousand); ?></strong></td>
            </tr>
        </tbody>
    </table>
</section>

<style>
th.title, tr.title {
    background: #eee;
}
</style>