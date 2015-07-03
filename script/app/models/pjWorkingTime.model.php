<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWorkingTimeModel extends pjAppModel
{
	protected $primaryKey = 'location_id';
	
	protected $table = 'working_times';
	
	protected $schema = array(
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'p_monday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_monday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_monday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'p_tuesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_tuesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_tuesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'p_wednesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_wednesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_wednesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'p_thursday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_thursday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_thursday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'p_friday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_friday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_friday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'p_saturday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_saturday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_saturday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'p_sunday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_sunday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'p_sunday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_monday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_monday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_monday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_tuesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_tuesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_tuesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_wednesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_wednesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_wednesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_thursday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_thursday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_thursday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_friday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_friday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_friday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_saturday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_saturday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_saturday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'd_sunday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_sunday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'd_sunday_dayoff', 'type' => 'enum', 'default' => 'F')
	);
	
	public static function factory($attr=array())
	{
		return new pjWorkingTimeModel($attr);
	}
	
	public function init($location_id)
	{
		$data = array();
		$data['location_id']      = $location_id;
		$data['p_monday_from']    = '08:00:00';
		$data['p_monday_to']      = '22:00:00';
		$data['p_tuesday_from']   = '08:00:00';
		$data['p_tuesday_to']     = '22:00:00';
		$data['p_wednesday_from'] = '08:00:00';
		$data['p_wednesday_to']   = '22:00:00';
		$data['p_thursday_from']  = '08:00:00';
		$data['p_thursday_to']    = '22:00:00';
		$data['p_friday_from']    = '08:00:00';
		$data['p_friday_to']      = '22:00:00';
		$data['p_saturday_from']  = '08:00:00';
		$data['p_saturday_to']    = '22:00:00';
		$data['p_sunday_from']    = '08:00:00';
		$data['p_sunday_to']      = '22:00:00';
		$data['d_monday_from']    = '08:00:00';
		$data['d_monday_to']      = '22:00:00';
		$data['d_tuesday_from']   = '08:00:00';
		$data['d_tuesday_to']     = '22:00:00';
		$data['d_wednesday_from'] = '08:00:00';
		$data['d_wednesday_to']   = '22:00:00';
		$data['d_thursday_from']  = '08:00:00';
		$data['d_thursday_to']    = '22:00:00';
		$data['d_friday_from']    = '08:00:00';
		$data['d_friday_to']      = '22:00:00';
		$data['d_saturday_from']  = '08:00:00';
		$data['d_saturday_to']    = '22:00:00';
		$data['d_sunday_from']    = '08:00:00';
		$data['d_sunday_to']      = '22:00:00';
		return $this->reset()->setAttributes($data)->insert()->getInsertId();
	}
	
	public function getWorkingTime($id, $type, $date)
	{
		$prefix = ($type == 'pickup') ? 'p_' : 'd_';
		$day = strtolower(date("l", strtotime($date)));
		$arr = $this->reset()->find($id)->getData();

		if (count($arr) == 0)
		{
			return false;
		}
	
		if ($arr[$prefix . $day . '_dayoff'] == 'T')
		{
			return array();
		}
		
		$wt = array();
		foreach ($arr as $k => $v)
		{
			if (strpos($k, $prefix . $day . '_from') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['start_hour'] = $d['hours'];
				$wt['start_minutes'] = $d['minutes'];
				$wt['start_ts'] = strtotime($date . " " . $v);
				continue;
			}
		
			if (strpos($k, $prefix . $day . '_to') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['end_hour'] = $d['hours'];
				$wt['end_minutes'] = $d['minutes'];
				$wt['end_ts'] = strtotime($date . " " . $v);
				continue;
			}
		}
		return $wt;
	}
}
?>