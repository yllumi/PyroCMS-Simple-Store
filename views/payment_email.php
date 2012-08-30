<h3><?php echo lang('products:invoice'); ?></h3>

<div>Terima kasih telah berbelanja online di <?php echo $this->settings->site_name; ?>.</div><br />
<div>Silakan ikuti dengan seksama langkah-langkah di bawah ini untuk teknis pembayaran.</div>
<div>Simpan invoice ini sebagai bukti pemesanan atau Anda dapat mengecek salinan invoice di inbox atau spam di dalam email <strong><?php echo $customer['email']; ?></strong>. 
    Setelah Anda beralih ke halaman lain, semua sessi pada halaman ini akan terhapus.</div><br />

<div>Produk yang telah Anda pesan akan dikirim kepada:<br />
    <blockquote>
        <strong>Nama : </strong><?php echo $customer['firstname'] . ' ' . $customer['lastname']; ?><br />
        <strong>Alamat : </strong><?php echo $customer['address'] . ' ' . $customer['city'] . ' ' . $customer['postalcode']; ?>
    </blockquote>
    dengan rincian produk sebagai berikut:</div><br />

<table cellpadding="0" cellspacing="0" style="width:90%">
    <thead>
        <tr>
            <th><?php echo lang('products:description'); ?></th>
            <th>QTY</th>
            <th style="text-align:right"><?php echo lang('products:price'); ?></th>
            <th style="text-align:right"><?php echo lang('products:subtotal'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order_items as $items): ?>

            <tr>
                <td><?php echo $items['name']; ?></td>
                <td><?php echo $items['qty']; ?></td>
                <td style="text-align:right"><?php echo $this->settings->currency . ' ' . number_format($items['price'], $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
                <td style="text-align:right"><?php echo $this->settings->currency . ' ' . number_format($items['subtotal'], $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <td colspan="3" style="text-align:right"><strong>Total</strong></td>
            <td style="text-align:right"><strong><?php echo $this->settings->currency . ' ' . number_format($total, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></strong></td>
        </tr>
    </tbody>
</table>

<div>Untuk selanjutnya silakan Anda melakukan pembayaran sebesar 
    <strong><?php echo $this->settings->currency . ' ' . number_format($total, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></strong> 
    ke nomor rekening berikut:<br />
    <blockquote>
        <strong>Bank: </strong><?php echo $this->settings->bank_transfer; ?><br />
        <strong>No. Rekening: </strong><?php echo $this->settings->rekening; ?><br />
        <strong>a.n. </strong><?php echo $this->settings->rekening_owner; ?>
    </blockquote>
    Setelah Anda mengirimkan pembayaran, segera lakukan konfirmasi via SMS ke nomor <strong><?php echo $this->settings->shop_phone; ?></strong> dengan isi nama dan kode order, yakni sebagai berikut:<br />
    <blockquote style="background: lightsteelblue">
        <strong><?php echo $customer['firstname'] . ' ' . $customer['lastname'] .' '. $ordercode; ?></strong>
    </blockquote>
    Kami akan mengirimkan produk pesanan Anda ke alamat yang tertera di atas segera setelah kami menerima pembayaran dari Anda.<br />
</div><br />
<div style="margin-bottom: 80px;">Terima kasih.</div>