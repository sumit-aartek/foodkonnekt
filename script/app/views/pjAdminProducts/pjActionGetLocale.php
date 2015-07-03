<?php
$category = '<select name="category_id[]" id="category_id" multiple="multiple" size="5" class="pj-form-field required w300">';
foreach ($tpl['category_arr'] as $v)
{
	$category .= sprintf('<option value="%u">%s</option>', $v['id'], stripslashes($v['name']));
}
$category .= '</select>';

$extra = '<select name="extra_id[]" id="extra_id" multiple="multiple" size="5" class="pj-form-field w300">';
foreach ($tpl['extra_arr'] as $v)
{
	$extra .= sprintf('<option value="%u">%s</option>', $v['id'], stripslashes($v['name']));
}
$extra .= '</select>';

pjAppController::jsonResponse(compact('category', 'extra'));
?>