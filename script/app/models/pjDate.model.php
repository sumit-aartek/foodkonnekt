<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDateModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'dates';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'start_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'end_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'is_dayoff', 'type' => 'enum', 'default' => 'F')
	);
	
	public static function factory($attr=array())
	{
		return new pjDateModel($attr);
	}
	
	public function getWorkingTime($date, $location_id, $type)
	{
		$arr = $this->reset()
			->where('t1.date', $date)
			->where('t1.location_id', $location_id)
			->where('t1.type', $type)
			->limit(1)
			->orderBy("t1.start_time ASC")
			->findAll()
			->getData();
			
		if (count($arr) == 0)
		{
			return false;
		}

		if ($arr[0]['is_dayoff'] == 'T')
		{
			return array();
		}
		
		$wt = array();
		$d = getdate(strtotime($arr[0]['start_time']));
		$wt['start_hour'] = $d['hours'];
		$wt['start_minutes'] = $d['minutes'];
	
		$d = getdate(strtotime($arr[0]['end_time']));
		$wt['end_hour'] = $d['hours'];
		$wt['end_minutes'] = $d['minutes'];
		
		$wt['start_ts'] = strtotime($date . " " . $arr[0]['start_time']);
		$wt['end_ts'] = strtotime($date . " " . $arr[0]['end_time']);
		
		return $wt;
	}
		
	public function getDatesOff($month, $year)
	{
		$numOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));
		$_arr = array();
		foreach (range(1, $numOfDays) as $i)
		{
			$_arr[date("Y-m-d", mktime(0, 0, 0, $month, $i, $year))] = array();
		}
		$arr = $this->reset()
			->where('MONTH(t1.date)', $month)
			->where('YEAR(t1.date)', $year)
			->orderBy("t1.date ASC")
			->findAll()
			->getData();
		foreach ($arr as $v)
		{
			$_arr[$v['date']] = $v;
			$_arr[$v['date']]['start_ts'] = strtotime($v['date'] . " " . $v['start_time']);
			$_arr[$v['date']]['end_ts'] = strtotime($v['date'] . " " . $v['end_time']);
		}
		
		return $_arr;
	}
}
?>