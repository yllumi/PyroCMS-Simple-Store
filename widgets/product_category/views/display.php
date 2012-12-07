<?php if (count($categories) > 0) { ?>
<ul>
<?php foreach($categories as $cat): ?>
	<li><?php echo anchor('products/category/'.$cat->id, $cat->name); ?></li>
<?php endforeach; ?>
</ul>
<br />
<?php } else { ?>
    <small style="display:none;">there is no any category.</small>
<?php } ?>