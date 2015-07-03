<?php
$type_err = __('type_err', true, false);
if (empty($_GET['location_id']))
{
	echo $type_err[0];
	exit;
}
if (empty($tpl['wt_arr']))
{
	echo $type_err[1];
	exit;
}
if (!isset($tpl['wt_arr']['end_ts']))
{
	echo $type_err[2];
	exit;
}
if ($tpl['wt_arr']['end_ts'] < time())
{
	echo $type_err[3];
	exit;
}

$index = $_GET['index'];
$STORAGE = &$_SESSION[$controller->defaultStore];

switch ($_GET['type'])
{
	case 'pickup':
		$h_key = 'p_hour';
		$m_key = 'p_minute';
		$m_class = 'fdPickupMinutes';
		break;
	case 'delivery':
		$h_key = 'd_hour';
		$m_key = 'd_minute';
		$m_class = 'fdDeliveryMinutes';
		break;
}

$hf = isset($STORAGE) && isset($STORAGE[$h_key]) ? $STORAGE[$h_key] : NULL;
$mf = isset($STORAGE) && isset($STORAGE[$m_key]) ? $STORAGE[$m_key] : null;

$start = (int) $tpl['wt_arr']['start_hour'];
$end = (int) $tpl['wt_arr']['end_hour'];
$hSkip = $mSkip = array();
if (strtotime($tpl['date']) == strtotime(date("Y-m-d")))
{
	list($hour, $minute) = explode("-", date("G-i"));
	foreach (range(0, 23) as $i)
	{
		if ($i < $hour)
		{
			$hSkip[] = $i;
		}
	}
	$minute = (int) $minute;
	for ($i = 0; $i < 60; $i += 5)
	{
		if ($i <= $minute)
		{
			$mSkip[] = $i;
		}
	}
	if (count($mSkip) === 12 && count($hSkip) > 0)
	{
		$el = $hSkip[count($hSkip)-1] + 1;
		if ($el <= 23)
		{
			$hSkip[] = $el;
			$mSkip = array();
		}
	}
}

$pjTimeHour = pjTime::factory();
$pjTimeMin = pjTime::factory();

$pjTimeHour
		->attr('name', $h_key)
		->attr('id', $h_key.'_'.$index)
		->attr('class', 'fdSelect fdFloatLeft fdW70 fdMr5')
		->prop('start', $start)
		->prop('end', $end)
		->prop('skip', $hSkip)
		->prop('selected', $hf);
$pjTimeMin
	->attr('name', $m_key)
	->attr('id', $m_key.'_'.$index)
	->attr('class', 'fdSelect fdW70 fdFloatLeft')
	->prop('step', 5)
	->prop('skip', $mSkip)
	->prop('selected', $mf);
	
echo $pjTimeHour->hour();
echo $pjTimeMin->minute();
?>