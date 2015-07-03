
<?php
function file_post_modifier($url, $data)
{
    $opts    = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $data
        )
    );
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}
function addModifier($itemId, $modUrl, $cloModId, $modFilterGroupId, $ACCESS_TOKEN)
{
    $url         = $modUrl . '/line_items/' . $itemId . '/modifications/?access_token=' . $ACCESS_TOKEN;
    $data_string = '{
	  "modifier": {
		"id": "' . $cloModId . '",
		"price": "",
		"name": "",
		"modifierGroup": {
		  "id": "' . $modFilterGroupId . '"
		},
		"alternateName": ""
	  },
	  "amount": "",
	  "id": "' . $cloModId . '",
	  "name": "",
	  "alternateName": ""
	}';
    $response12 = file_post_modifier($url, $data_string);
}
?>