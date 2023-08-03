<?php

class sobad_user extends _class
{
	public static $table = base . 'user';

	protected static $tbl_join = base . 'user-log';

	protected static $tbl_meta = base . 'user-meta';

	protected static $join = "joined.user ";

	protected static $group = " GROUP BY `" . base . "user-meta`.meta_id";

	protected static $list_meta = array();

	public static function set_listmeta()
	{
		$type = parent::$_type;
		$type = strtolower($type);

		switch ($type) {
			case 'internship':
				self::$list_meta = array(
					'_address', '_email', '_university', '_education', '_study_program', '_faculty', '_semester', '_classes', '_sex', '_province', '_city', '_subdistrict', '_postcode', '_nickname', '_entry_date', '_resign_date'
				);
				break;

			default:
				self::$list_meta = array(
					'_address', '_email', '_sex', '_entry_date', '_place_date', '_birth_date', '_resign_date', '_province', '_city', '_subdistrict', '_postcode', '_marital', '_religion', '_nickname', '_resign_status', '_warning'
				);
				break;
		}
	}

	public static function blueprint($type = 'general')
	{
		self::set_listmeta();

		$args = array(
			'type'		=> $type,
			'table'		=> self::$table,
			'detail'	=> array(
				'divisi'	=> array(
					'key'		=> 'ID',
					'table'		=> base . 'module',
					'column'	=> array('meta_value', 'meta_note')
				),
				'work_time'	=> array(
					'key'		=> 'ID',
					'table'		=> base . 'work',
					'column'	=> array('name')
				),
				'picture'	=> array(
					'key'		=> 'ID',
					'table'		=> base . 'post',
					'column'	=> array('notes')
				)
			),
			'joined'	=> array(
				'key'		=> 'user',
				'table'		=> self::$tbl_join
			),
			'meta'		=> array(
				'key'		=> 'meta_id',
				'table'		=> self::$tbl_meta,
			)
		);

		if ($type == 'internship') {
			unset($args['detail']['divisi']);
		}

		if ($type == 'employee') {
			unset($args['joined']);
		}

		return $args;
	}

	public static function check_login($user = '', $pass = '')
	{
		$conn = conn::connect();
		$args = array('`' . base . 'user`.ID', '`' . base . 'user`.name', '`' . base . 'module`.meta_note AS dept', '`' . base . 'module`.meta_value AS jabatan', '`' . base . 'user`.picture');

		$user = $conn->real_escape_string($user);
		$pass = $conn->real_escape_string($pass);

		$inner = "LEFT JOIN `" . base . "module` ON `" . base . "user`.divisi = `" . base . "module`.ID ";
		$where = $inner . "WHERE `" . base . "user`.username='$user' AND `" . base . "user`.password='$pass' AND `" . base . "user`.end_status='0'";

		return parent::_get_data($where, $args);
	}

	public static function get_divisi($id = 0, $args = array(), $limit = '')
	{
		$where = "WHERE (divisi='$id' AND status='0' AND end_status!='7') OR (divisi='$id' AND status!='7' AND end_status='0') $limit";
		return parent::_check_join($where, $args);
	}

	public static function user_sentiment()
	{
		$args = array('ID', 'name', '_sex');
		$where = "WHERE 1=1";
		$data =  parent::_check_join($where, $args);

		$sentiment = array();
		foreach ($data as $key => $val) {
			$sentiment[$key] = array(
				'ID'		=> $val['ID'],
				'name'		=> $val['name'],
				'sex'		=> $val['_sex'] == 'male' ? 0 : 1
			);
		}

		return $sentiment;
	}

	// -----------------------------------------------------------------
	// --- Function User-log -------------------------------------------
	// -----------------------------------------------------------------

	public static function get_maxNIK()
	{
		$args = array('MAX(no_induk) as nik');
		$where = "WHERE divisi != '0' AND status IN ('0','1','2','3','4','5')";

		$data = parent::_get_data($where, $args);
		$check = array_filter($data);
		if (empty($check)) {
			return 0;
		}

		return $data[0]['nik'];
	}

	public static function get_maxNIM($divisi = 1)
	{
		$year = date('Y');
		$args = array('MAX(no_induk) as nim');
		$where = "WHERE divisi = '$divisi' AND status IN ('0','7') AND YEAR(inserted)='$year'";

		$data = parent::_get_data($where, $args);
		$check = array_filter($data);
		if (empty($check)) {
			return 0;
		}

		return $data[0]['nim'];
	}

	public static function not_work($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='0' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_work($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='1' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_home($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='2' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_holiday($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='3' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_permit($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='4' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_outCity($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='5' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_holiwork($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='6' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_tugas($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='7' $limit";
		return parent::_check_join($where, $args);
	}

	public static function go_sick($args = array(), $limit = '')
	{
		$where = "WHERE `" . self::$tbl_join . "`.type='8' $limit";
		return parent::_check_join($where, $args);
	}

	public static function get_absen($args = array(), $date = '', $limit = '')
	{
		$date = empty($date) ? date('Y-m-d') : $date;

		$where = "WHERE `" . self::$tbl_join . "`._inserted='$date' $limit";
		return parent::_check_join($where, $args);
	}

	public static function get_employees($args = array(), $limit = '')
	{
		$whr = "(`" . base . "user`.status!='7' AND `" . base . "user`.end_status='0' OR `" . base . "user`.status='0' AND `" . base . "user`.end_status!='7')";
		$where = "WHERE $whr $limit";
		return parent::_check_join($where, $args, 'employee');
	}

	public static function get_internships($args = array(), $limit = '')
	{
		$whr = "(`" . base . "user`.status='7' AND `" . base . "user`.end_status='0' OR `" . base . "user`.status='0' AND `" . base . "user`.end_status='7')";
		$where = "WHERE $whr $limit";
		return parent::_check_join($where, $args, 'internship');
	}

	public static function get_metas($args = array(), $limit = '')
	{
		self::$table = base . 'user-meta';
		$where = "WHERE 1=1 $limit";
		$data = parent::_check_join($where, $args, 'meta');

		self::$table = base . 'user';
		return $data;
	}

	public static function count_log($id = 0, $limit = '')
	{
		self::$table = base . 'user-log';
		$where = "WHERE user='$id' $limit";

		$count = parent::_get_data($where, array('count(ID) AS cnt'));

		self::$table = base . 'user';
		return $count[0]['cnt'];
	}

	public static function get_logs($args = array(), $limit = '1=1')
	{
		self::$table = base . 'user-log';
		$where = "WHERE $limit";

		$args = parent::_get_data($where, $args);

		self::$table = base . 'user';
		return $args;
	}

	public static function get_late($date = '', $limit = '')
	{
		self::$table = base . 'user-log';

		$work = array();
		$works = sobad_work::get_all(array('ID', 'name', 'days', 'time_in'));
		foreach ($works as $key => $val) {
			$idx = $val['ID'];
			if (!isset($work[$idx])) {
				$work[$idx] = array();
			}

			$work[$idx][$val['days']] = $val['time_in'];
		}

		if (!empty($date)) {
			$date = date($date);
			$date = strtotime($date);
			$year = date('Y', $date);
			$month = date('m', $date);

			$date = "AND YEAR(_inserted)='$year' AND MONTH(_inserted)='$month'";
		}

		$where = "WHERE punish='1' AND type IN (1,2) $date $limit";

		$data = array();
		$logs = parent::_get_data($where, array('ID', 'user', 'shift', 'type', 'time_in', '_inserted'));
		foreach ($logs as $key => $val) {

			self::$table = base . 'user';
			$stsuser = sobad_user::get_id($val['user'], array('status'));
			self::$table = base . 'user-log';

			if ($stsuser[0]['status'] == 0) {
				continue;
			}

			$_date = date($val['_inserted']);
			$_date = strtotime($_date);
			$_date = date('w', $_date);

			$punish = 30;
			$time = $work[$val['shift']][$_date];
			if ($val['time_in'] >= $time) {
				$time = _calc_time($time, '5 minutes');

				if ($val['time_in'] >= $time) {
					$punish = 60;
				}
				$val['punishment'] = $punish;
				$data[] = $val;
			}
		}

		self::$table = base . 'user';

		return $data;
	}
}
