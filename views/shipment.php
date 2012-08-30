<h3><?php echo lang('products:shipment_info'); ?></h3>

<?php echo form_open('products/checkout/shipment'); ?>
<div id="shipping_address">

    <div>Silakan isi form berikut sebagai data pemesan. 
        Pastikan data yang Anda masukkan benar dan lengkap agar tidak terjadi kendala saat pengiriman barang.</div>
    <br />

    <div class="form_wrap">
        <label for="">First Name<b class="r"> *</b></label>
        <?php echo form_error('firstname', '<i class="r">', '</i><br />'); ?>
        <input type="text" name="firstname" value="<?php echo set_value('firstname'); ?>">
        <label for="">Last Name</label>
        <?php echo form_error('lastname', '<i class="r">', '</i><br />'); ?>
        <input type="text" name="lastname" value="<?php echo set_value('lastname'); ?>">
    </div>

    <div class="form_wrap">
        <label for="">Email<b class="r"> *</b></label>
        <?php echo form_error('email', '<i class="r">', '</i><br />'); ?>
        <input type="text" name="email" value="<?php echo set_value('email'); ?>">
        <label for="">Phone<b class="r"> *</b></label>
        <?php echo form_error('phone', '<i class="r">', '</i><br />'); ?>
        <input type="text" name="phone" value="<?php echo set_value('phone'); ?>">
    </div>

    <div class="form_wrap">
        <label for="">Address<b class="r"> *</b></label>
        <?php echo form_error('address', '<i class="r">', '</i><br />'); ?>
        <textarea name="address"><?php echo set_value('address'); ?></textarea>
    </div>

    <div class="form_wrap">
        <label>City<b class="r"> *</b></label>
        <?php echo form_error('city', '<i class="r">', '</i><br />'); ?>
        <input type="text" name="city" value="<?php echo set_value('city'); ?>">
        <label>Zip / Post Code</label>
        <?php echo form_error('postalcode', '<i class="r">', '</i><br />'); ?>
        <input type="text" name="postalcode" value="<?php echo set_value('postalcode'); ?>" maxlength="5">
        <br /><br />
        <input type="submit" value="<?php echo lang('products:payment'); ?>" class="button right">
    </div>

</div>
<?php echo form_close(); ?>


<table cellpadding="0" cellspacing="0" style="width:100%" class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo lang('products:description'); ?></th>
            <th>QTY</th>
            <th style="text-align:right"><?php echo lang('products:price'); ?></th>
            <th style="text-align:right"><?php echo lang('products:subtotal'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->cart->contents() as $items): ?>

            <tr>
                <td><?php echo $items['name']; ?></td>
                <td><?php echo $items['qty']; ?></td>
                <td style="text-align:right"><?php echo $this->settings->currency . ' ' . number_format($items['price'], $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
                <td style="text-align:right"><?php echo $this->settings->currency . ' ' . number_format($items['subtotal'], $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <td colspan="3" style="text-align:right"><strong>Total</strong></td>
            <td style="text-align:right"><strong><?php echo $this->settings->currency . ' ' . number_format($this->cart->total(), $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></strong></td>
        </tr>
    </tbody>
</table>

<style>
    .form_wrap {
        display: inline-block;
        border: 0px solid #111;
        min-width: 300px;
        vertical-align: top;
        margin-left: 20px;
    }
    .r {
        color: red;
        font-size: 12px;
    }
</style>