<section class="title">
	<h4><?php echo lang('orders:item_list'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/orders/delete');?>
	
	<?php if (!empty($items)): ?>
	
		<table>
			<thead>
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
					<th><?php echo lang('orders:order_code'); ?></th>
					<th><?php echo lang('orders:customer'); ?></th>
                    <th><?php echo lang('orders:ordered_on'); ?></th>
                    <th><?php echo lang('orders:status'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach( $items as $item ): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $item->id); ?></td>
					<td><?php echo $item->order_code; ?></td>
                    <td><?php echo $item->firstname .' '.$item->lastname; ?></td>
                    <td><?php echo date("d F Y H:i:s", strtotime($item->ordered_on)); ?></td>
                    <td><strong><?php echo $item->status; ?></strong></td>
					<td class="actions">
						<?php echo
						anchor('admin/products/orders/view/'.$item->id, lang('general:view'), 'class="button"'); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<div class="table_action_buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		
	<?php else: ?>
		<div class="no_data"><?php echo lang('orders:no_items'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
</section>
