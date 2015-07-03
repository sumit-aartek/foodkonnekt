<?php
if(!empty($tpl['category_arr']))
{
	$_yesno = __('_yesno', true, false);
	$i = 1;
	foreach($tpl['category_arr'] as $k => $v)
	{
		?>
		<tr id="category_row_<?php echo $v['id'];?>" data-id="id_<?php echo $v['id'];?>" class="pj-table-row<?php echo $i % 2 == 0 ? ' pj-table-row-even' : ' pj-table-row-odd';?>">
			<td style="width: 20px;"><input type="checkbox" name="record[]" value="<?php echo $v['id'];?>" class="pj-table-select-row"></td>
			<td style="width: 552px;"><?php echo pjSanitize::clean($v['name']);?></td>
			<td style="width: 70px;"><?php echo $v['is_open'] == 1 ? $_yesno['T'] : $_yesno['F'];?></td>
			<td style="width: 100px;">
				<a href="index.php?controller=pjAdminCategories&amp;action=pjActionUpdate&amp;id=<?php echo $v['id'];?>" class="pj-table-icon-edit"></a>
				<a href="index.php?controller=pjAdminCategories&amp;action=pjActionDeleteCategory&amp;id=<?php echo $v['id'];?>" rev="<?php echo $v['id'];?>" class="pj-table-icon-delete fd-delete-category"></a>
				<a href="javascript:void(0)" class="pj-table-icon-move"></a>
			</td>
		</tr>
		<?php
		$i++;
	}
}else{
	?>
	<tr>
		<td colspan="3"><?php __('gridEmptyResult', false, true); ?></td>
	</tr>
	<?php
}
?>