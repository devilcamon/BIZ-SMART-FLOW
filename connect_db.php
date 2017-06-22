<?php
/*
 * Class Connect Database
 * Created by:  Tawatchai Anuchat
 * Date:  05/05/2017
 * Version:  1.1
 */


Class db
{
	protected static $_host, $_user, $_password, $_autoIncrement = "Y";
	protected static $_systemConnect, $_systemQuery, $_systemRecordCount, $_systemResult;
	public static $_dbType = "MYSQL", $_dbName ,$_langDate;

	/*
	 * ตั้งค่าการเชื่อมต่อฐานข้อมูล
	 * @host		IP หรือชื่อเครื่องฐานข้อมูล
	 * @user		username ที่ใช้เข้าฐานข้อมูล
	 * @password	password ที่ใช้เข้าฐานข้อมูล
	 * @dbName		ชื่อฐานข้อมูล
	 * @dbType		ประเภทฐานข้อมูล (MYSQL, MSSQL, ORACLE)
	 */

	public static function setupDatabase()
	{
		self::connectServer();
	}

	/*
	 * Connect Database
	 */
	protected static function connectServer()
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				self::$_systemConnect = mssql_connect(self::$_host, self::$_user, self::$_password);
				self::chooseDBName();
				break;
			case 'MYSQL':
				self::$_systemConnect = mysqli_connect(self::$_host, self::$_user, self::$_password, self::$_dbName);
				self::query('SET NAMES \'utf8\'');

				if(mysqli_connect_errno())
				{
					echo "<strong>ไม่สามารถเชื่อมต่อฐานข้อมูลได้: </strong>".mysqli_connect_error();
					exit;
				}
				break;
			case 'ORACLE':
				$db1 = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".self::$_host.")(PORT = 1521)))(CONNECT_DATA = (SERVICE_NAME=orcl)))";
				self::$_systemConnect = oci_connect(self::$_user, self::$_password, $db1,"UTF8");
				if(!self::$_systemConnect)
				{
					$e = oci_error();
					trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}

				break;
		}

		return self::$_systemConnect;
	}

	/*
	 * เลือกฐานข้อมูลที่เชื่อมต่อ
	 */
	protected static function chooseDBName()
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				mssql_select_db(self::$_dbName);
				break;
			case 'MYSQL':
				mysqli_select_db(self::$_systemConnect, self::$_dbName);
				break;
			case 'ORACLE':
				break;
		}
	}

	/*
	 * Query ข้อมูลโดยรับคำสั่ง SQL เข้ามา
	 */
	public static function query($sql)
	{
		global $show_query;

		if($show_query == "Y")
		{
			echo date("H:i:s")."<br>";
			echo $sql."<hr>";
		}

		$error = "N";
		$error_txt = "";
		switch(self::$_dbType)
		{
			case 'MSSQL':
				self::$_systemQuery = mssql_query($sql);
				break;
			case 'MYSQL':
				self::$_systemQuery = mysqli_query(self::$_systemConnect, $sql);
				if(!self::$_systemQuery)
				{
					$error = "Y";
					$error_txt = mysqli_error(self::$_systemConnect);
					echo "<strong>Error Description: </strong>".$error_txt;
				}
				break;
			case 'ORACLE':
				$obj = oci_parse(self::$_systemConnect, $sql);
				oci_execute($obj) or die($sql);;
				self::$_systemQuery = $obj;
				if(!self::$_systemQuery)
				{
					$error = "Y";
					$error_txt = OCIError();
					echo "<strong>Error Description: </strong>".$error_txt;
				}
				break;
		}

		if($error == "Y")
		{
			self::write_log_error($sql, $error_txt);
		}

		return self::$_systemQuery;
	}

	/*
	 * Query ข้อมูลโดยรับคำสั่ง SQL เข้ามาพร้อมจำกัดการแสดงจำนวนแถวข้อมูล
	 * @sql		statement
	 * @offset	เริ่มต้นจาก
	 * @limit	จำนวนที่ต้องการแสดง
	 */
	public static function query_limit($sql, $offset, $limit)
	{
		global $show_query;

		if($show_query == "Y")
		{
			echo date("H:i:s")."<br>";
			echo $sql."<hr>";
		}

		$error = "N";
		$error_txt = "";
		switch(self::$_dbType)
		{
			case 'MSSQL':

				break;
			case 'MYSQL':
				$sql_limit = " limit ".$offset.", ".$limit;

				self::$_systemQuery = mysqli_query(self::$_systemConnect, $sql.$sql_limit);
				if(!self::$_systemQuery)
				{
					$error = "Y";
					$error_txt = mysqli_error(self::$_systemConnect);
					echo "<strong>Error Description: </strong>".$error_txt;
				}
				break;
			case 'ORACLE':
				$STOP =  $offset + $limit;
				if($offset != -1)
				{
					$offset = $offset+1;
				}
				$sql_limit = 'select * from ( select a.*, rownum rnum from ( '.$sql.' ) a ) where rnum between '.$offset.' and '.$STOP.' ';


				$obj = oci_parse(self::$_systemConnect, $sql_limit);
				oci_execute($obj);
				self::$_systemQuery = $obj;
				if(!self::$_systemQuery)
				{
					$error = "Y";
					$error_txt = OCIError();
					echo "<strong>Error Description: </strong>".$error_txt;
				}
				break;
		}

		if($error == "Y")
		{
			self::write_log_error($sql, $error_txt);
		}

		return self::$_systemQuery;
	}

	/*
	 * Fetch Array
	 */
	public static function fetch_array($query)
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				$resultType = "MSSQL_BOTH";
				self::$_systemResult = mssql_fetch_array($query);
				break;
			case 'MYSQL':
				self::$_systemResult = mysqli_fetch_array($query);
				break;
			case 'ORACLE':
				self::$_systemResult = oci_fetch_array($query);
				break;
		}

		return self::$_systemResult;
	}

	/*
	 * Num Rows
	 */
	public static function num_rows($query)
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				self::$_systemRecordCount = mssql_num_rows($query);
				break;
			case 'MYSQL':
				self::$_systemRecordCount = mysqli_num_rows($query);
				break;
			case 'ORACLE':
				self::$_systemRecordCount = oci_num_rows($query);
				break;
		}

		return self::$_systemRecordCount;
	}

	/*
	 * Insert ข้อมูล
	 * @tbName		ชื่อตารางที่จะ Insert
	 * @data		ข้อมูลที่จะ Insert เป็น Array โดย Key คือชื่อ Field, Value คือ ข้อมูลที่จะเพิ่ม
	 * @pk			PK ของตารางที่ต้องการ select max
	 * @outID		ต้องการเลข PK ล่าสุดที่เพิ่ม  ถ้าต้องการใส่ Y
	 */
	public static function db_insert($tbName, $data, $pkSelectMax = "", $outID = "")
	{
		$fieldArray = array();
		$valueArray = array();

		if(self::$_autoIncrement == "N")
		{
			$get_last_id = self::get_max($tbName, $pkSelectMax);
			$last_id = $get_last_id + 1;
			$data[$pkSelectMax] = $last_id;
		}

		foreach($data as $_key => $_val)
		{
			array_push($fieldArray, $_key);
			array_push($valueArray, "'".$_val."'");
		}

		$setSQL = "insert into ".$tbName." (".implode(', ', $fieldArray).") values (".implode(', ', $valueArray).")";
		self::query($setSQL);

		if($outID != "")
		{
			switch(self::$_dbType)
			{
				case 'MSSQL':
					$query = self::query("select @@identity");
					$rs = self::fetch_array($query);

					$last_id = $rs['computed'];
					break;
				case 'MYSQL':
					$last_id = mysqli_insert_id(self::$_systemConnect);
					break;
				case 'ORACLE':
					$last_id = self::get_max($tbName, $outID);
					break;
			}
		}

		if(self::$_autoIncrement == "N" || $outID != "")
		{
			return $last_id;
		}
		else
		{
			return null;
		}
	}

	/*
	 * Update ข้อมูล
	 * @tbName		ชื่อตารางที่จะ Update
	 * @data		ข้อมูลที่จะ Update เป็น Array โดย Key คือชื่อ Field, Value คือ ข้อมูลที่จะเพิ่ม
	 * @cond		เงื่อนไข เป็น Array โดย Key คือชื่อ Field ที่จะ Where, Value คือ ข้อมูลที่จะ Where
	 */
	public static function db_update($tbName, $data, $cond)
	{
		if(count($data)>0){
		$updateData = self::setArray2String($data);
		$condition = self::setArray2String($cond, " and ");

		$setSQL = "update ".$tbName." set ".$updateData." where 1=1 and ".$condition;
		self::query($setSQL);
		}
	}

	/*
	 * Show Field ในตาราง
	 * @tables		ชื่อตารางที่ต้องการ Show Fields
	 */
	public static function show_field($tables)
	{
		$arr_data = array();
		if(strtoupper(self::$_dbType) == 'MYSQL')
		{
			$tables = strtolower($tables);
			$q_auto = self::query("SHOW FIELDS FROM ".$tables."");
			while($r_auto = self::fetch_array($q_auto))
			{
				array_push($arr_data, $r_auto['Field']);
			}
		}
		elseif(strtoupper(self::$_dbType) == 'ORACLE')
		{

			$tables = strtoupper($tables);
			$q_auto = self::query("SELECT column_name FROM all_tab_cols WHERE table_name = '".$tables."' AND OWNER = '".strtoupper(self::$_dbName)."'  ORDER BY SEGMENT_COLUMN_ID");
			while($r_auto = self::fetch_array($q_auto))
			{
				array_push($arr_data, $r_auto['COLUMN_NAME']);
			}
		}

		return $arr_data;
	}

	/*
	 * Delete ข้อมูล
	 * @tbName		ชื่อตารางที่จะ Delete
	 * @cond		เงื่อนไข เป็น Array โดย Key คือชื่อ Field ที่จะ Where, Value คือ ข้อมูลที่จะ Where
	 */
	public static function db_delete($tbName, $cond)
	{
		$condition = self::setArray2String($cond, " and ");

		$setSQL = "delete from ".$tbName." where 1=1 and ".$condition;
		self::query($setSQL);
	}

	/*
	 * Query + Fetch ข้อมูล
	 * @return	ส่งค่ากลับเป็น Array 2 มิติ
	 */
	public static function store_select($sql)
	{
		$data_stored = array();

		switch(self::$_dbType)
		{
			case 'MSSQL':
				break;
			case 'MYSQL':
				$result = self::query($sql);

				while($record = mysqli_fetch_assoc($result))
				{
					$data_stored[] = $record;
				}
				break;
			case 'ORACLE':
				break;
		}

		return $data_stored;
	}

	/*
	 * หาค่ามากสุด
	 * @table		ชื่อตารางที่ต้องการหา
	 * @fieldGetMax	ชื่อฟิลที่ต้องการหา
	 * @cond		เงื่อนไข เป็น Array โดย Key คือชื่อ Field ที่จะ Where, Value คือ ข้อมูลที่จะ Where
	 */
	public static function get_max($table, $fieldGetMax, $cond = array())
	{
		if(count($cond) > 0)
		{
			$condition = self::setArray2String($cond, " and ");
			$where = " where ".$condition;
		}
		else
		{
			$where = "";
		}
		
		$sql = "select max(".$fieldGetMax.") as mx from ".$table.$where;
		$res = self::query($sql);
		$rec = self::fetch_array($res);
		
		return $rec['MX'] > 0 ? $rec['MX'] : '0';
	}


	//query เพื่อหา field จากตาราง
	public static function query_field($sql)
	{
		$res = self::query($sql);
		$ncols = oci_num_fields($res);
		$arr_field = array();
		for ($i = 1; $i <= $ncols; $i++) {
			$arr_field[] = oci_field_name($res, $i);
		}
		
		return $arr_field;
	}
	
	/*
	 * เก็บ SQL Error
	 * @sql			คำสั่งที่ error
	 * @errorTxt	รายละเอียดที่ error
	 */
	protected static function write_log_error($sql, $errorTxt = "")
	{
		if($errorTxt != "")
		{
			$errorTxt = " (".$errorTxt.")";
		}

		$file_name = date('Ymd').".txt";
		$content = date('H:i:s')."[".$_SESSION['WF_USER_ID']."][".$_SESSION['WF_USER_NAME']."][".$_SERVER['REQUEST_URI']."] : ".$sql.$errorTxt."\n";
		$handle = fopen('../logs_error/'.$file_name, 'a');

		fwrite($handle, $content);
		fclose($handle);
	}

	/*
	 * ปิดการเชื่อมต่อฐานข้อมูล
	 */
	public static function db_close()
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				MSSQL_CLOSE(self::$_systemConnect);
				break;
			case 'MYSQL':
				mysqli_close(self::$_systemConnect);
				break;
			case 'ORACLE':
				oci_close(self::$_systemConnect);
				break;
		}
	}

	private static function setArray2String($dataArray, $operator = ", ")
	{
		$data = "";

		foreach($dataArray as $_key => $_val)
		{
			$data[] = $_key." = '".$_val."'";
		}

		return implode($operator, $data);
	}

	/*
	 * ตั้งค่าพาธฐานข้อมูล
	 */
	public static function setHost($txt)
	{
		self::$_host = $txt;
	}

	/*
	 * ตั้งค่า username ฐานข้อมูล
	 */
	public static function setUser($txt)
	{
		self::$_user = $txt;
	}

	/*
	 * ตั้งค่า password ฐานข้อมูล
	 */
	public static function setPassword($txt)
	{
		self::$_password = $txt;
	}

	/*
	 * ตั้งค่าชื่อฐานข้อมูล
	 */
	public static function setDBName($txt)
	{
		self::$_dbName = $txt;
	}

	/*
	 * ตั้งค่าประเภทฐานข้อมูล
	 */
	public static function setDBType($txt)
	{
		self::$_dbType = strtoupper($txt);
	}
	
	/*
	 * ตั้งค่า Auto Increment
	 */
	public static function setAutoIncrement($txt)
	{
		self::$_autoIncrement = strtoupper($txt);
	}
	
	/*
	 * ตั้งค่ารูปแบบเวลา ใน db
	 */
	public static function setLangDate($txt)
	{
		self::$_langDate = strtoupper($txt);
	}
}

## Main Variable Array

$arr_operator = array(
	1 => 'เท่ากับ (=)',
	2 => 'มีบางคำ (Like)',
	3 => 'ขึ้นต้นด้วย (-%)',
	4 => 'ลงท้ายด้วย (%-)',
	5 => 'มากกว่า (>)',
	6 => 'มากกว่าเท่ากับ (>=)',
	7 => 'น้อยกว่า (<)',
	8 => 'น้อยกว่าท่ากับ (<=)',
	9 => 'ไม่เท่ากับ (!=)',
);

$arr_wf_detail_type = array(
	'P' => 'กระบวนงาน',
	'S' => 'เริ่มกระบวนงาน',
	'E' => 'จบกระบวนงาน',
	'T' => 'โยนค่าไปกระบวนการอื่น',
	'M' => 'โยนค่าไป Master'
);

$arr_data_type = array(
	'varchar2' => 'varchar',
	'number' => 'int',
	'date' => 'date',
	'text' => 'text'
);

$arr_textbox_format = array(
	'' => 'ไม่มีรูปแบบ',
	'E' => 'อีเมล์',
	'C' => 'เลขที่บัตรประชาชน',
	'P' => 'รหัสผ่าน',
	'N' => 'ตัวเลข (จำนวนเต็ม)',
	'N1' => 'ตัวเลข (ทศนิยม 1 ตำแหน่ง)',
	'N2' => 'ตัวเลข (ทศนิยม 2 ตำแหน่ง)',
	'N3' => 'ตัวเลข (ทศนิยม 3 ตำแหน่ง)',
	'TU' => 'ตัวอักษรใหญ่ทั้งหมด (ABC)',
	'TL' => 'ตัวอักษรเล็กทั้งหมด (abc)',
	'TC' => 'ขึ้นต้นตัวอักษรใหญ่ (Abc)'
	
);

$arr_system_data = array(
	'S_U' => 'ผู้ใช้งานระบบ',
	'S_P' => 'ตำแหน่ง',
	'S_D' => 'หน่วยงาน'
);

## Main Function

/*
 * Create Table
 */
function create_table_wf($table_name)
{
	switch(db::$_dbType)
	{
		case 'MSSQL':
			$sql_create = "CREATE TABLE [dbo].[".$table_name."](
					[WFR_ID] [int] IDENTITY(1,1) NOT NULL,
					[WFR_TIMESTAMP]	[date],
					[WF_DET_STEP]	[int],
					[WF_DET_NEXT]	[int]
					[WFR_UID]	[int]
				)";
			break;
		case 'MYSQL':
			$sql_create = "CREATE TABLE ".$table_name."(
					WFR_ID int(11) NOT NULL AUTO_INCREMENT,
					WFR_TIMESTAMP date DEFAULT NULL, 
					WF_DET_STEP	int(11),
					WF_DET_NEXT	int(11),
					WFR_UID	int(11),
					PRIMARY KEY (WFR_ID) 
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			break;
		case 'ORACLE':
			$sql_create = "CREATE TABLE ".$table_name."
					( WFR_ID NUMBER(20) NOT NULL,
					  WFR_TIMESTAMP DATE,
					  WF_DET_STEP	NUMBER(20),
					  WF_DET_NEXT	NUMBER(20),
					  WFR_UID	NUMBER(20),
					  CONSTRAINT ".$table_name."_pk PRIMARY KEY (WFR_ID)
					)";
			break;
	}

	if($sql_create != "")
	{
		db::query($sql_create);
	}
}

/*
 * Create Table
 */
function create_table($table_name, $field_name, $field_type, $field_length)
{
	switch(db::$_dbType)
	{
		case 'MSSQL':
			$sql_create = "CREATE TABLE [dbo].[".$table_name."](
					[".$field_name."] [".$field_type."] ".$field_length." NOT NULL
				)";
			break;
		case 'MYSQL':
			$sql_create = "CREATE TABLE ".$table_name."(
					".$field_name." ".$field_type."(".$field_length.") NOT NULL AUTO_INCREMENT,
					PRIMARY KEY (".$field_name.") 
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			break;
		case 'ORACLE':
			$sql_create = "CREATE TABLE ".$table_name."
					( ".$field_name." ".$field_type."(".$field_length.") NOT NULL,
					  CONSTRAINT ".$table_name."_pk PRIMARY KEY (".$field_name.")
					)";
			break;
	}

	if($sql_create != "")
	{
		db::query($sql_create);
	}
}

/*
 * Alter Add Field
 */
function add_field($table_name, $field_name, $data_type, $length, $comment = "")
{
	if($length != "")
	{
		$type = $data_type."(".$length.")";
	}
	else
	{
		$type = $data_type;
	}

	$alter = "ALTER TABLE ".$table_name." ADD ".strtoupper($field_name)." ".strtoupper($type)." ";
	db::query($alter);

	if($comment != "")
	{
		if(db::$_dbType == "ORACLE")
		{
			$comment_sql = "COMMENT ON COLUMN ".$table_name.".".$field_name." IS '".$comment."' ";
		}
		elseif(db::$_dbType == "MYSQL")
		{

		}
		elseif(db::$_dbType == "MSSQL")
		{

		}

		db::query($comment_sql);
	}
}


/*
 * Rename Field
 */
function rename_field($table,$table_name_new,$table_name_old)
{
	if(db::$_dbType == "ORACLE")
	{
		$rename = "ALTER TABLE ".$table." RENAME COLUMN ".$table_name_old." TO ".$table_name_new;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$rename = "";
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$rename = "";
	}

	db::query($rename);
}




/*
 * Modify Field
 */
function modify_field($table_name, $field_name, $field_type,$field_length)
{
	if($field_length != ''){
		$length = "(".$field_length.")";
			
	}else{
		$length = "";
	}
	
	
	if(db::$_dbType == "ORACLE")
	{
		$modify = "ALTER TABLE ".$table_name." MODIFY ".$field_name." ".$field_type.$length;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		//$modify = "ALTER TABLE ".$table_name." CHANGE ".$field_name_old." ".$field_name." ";
	}
	elseif(db::$_dbType == "MSSQL")
	{
		//$modify = "EXEC sp_rename '".$table_name.".".$field_name_old."', '".$field_name."', 'COLUMN'";
	}
	
	db::query($modify);
}


/*
 * Drop Field
 */
function Drop_field($table_name, $field_name)
{
	
	if(db::$_dbType == "ORACLE")
	{
		$drop_field = "ALTER TABLE ".$table_name." DROP COLUMN ".$field_name;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		//$drop_field = "";
	}
	elseif(db::$_dbType == "MSSQL")
	{
		//$drop_field = "";
	}
	
	db::query($drop_field);
}


/*
 * Rename Table
 */
function rename_table($table_name_old, $table_name_new)
{
	if(db::$_dbType == "ORACLE")
	{
		$rename_table = "ALTER TABLE ".$table_name_old." RENAME TO ".$table_name_new;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$rename_table = "";
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$rename_table = "";
	}

	db::query($rename_table);
}



/*
 * Drop Table
 */
function drop_table($table_name)
{
	db::query("DROP TABLE ".db::$_dbName.".".trim(strtoupper($table_name)));
}

function get_month($type)
{
	if($type == "S")
	{
		$month = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
	}
	elseif($type == "F")
	{
		$month = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
	}
	elseif($type == "E")
	{
		$month = array("", "JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"); 
	}

	return $month;
}

function print_pre($text)
{
	echo "<pre>";
	print_r($text);
	echo "</pre>";
}

function conText($text, $format = "")
{
	$outText = stripslashes(htmlspecialchars(trim($text), ENT_QUOTES));

	if($format == "number")
	{
		$outText = str_replace(',', '', $outText);
	}
	elseif($format == "date")
	{
		$outText = date2db($outText);
	}

	return $outText;
}
// เช็ค type กับ length
function checkFieldDB($table, $field)
{
	$wf_field = array();
	switch(db::$_dbType)
	{
		case 'MSSQL':
			break;
		case 'MYSQL':
			break;
		case 'ORACLE':
			$sql_check = db::query("select DATA_TYPE,DATA_LENGTH from user_tab_cols where column_name = '".strtoupper($field)."' and table_name = '".$table."'");
			$F = db::fetch_array($sql_check);
			$type = $F["DATA_TYPE"];
			$len = $F["DATA_LENGTH"];
			break;
	}
	$wf_field["len"] = $len;
	if($type == "NUMBER"){
		$wf_field["type"] = "N";
	}else{
		$wf_field["type"] = "T";
	}
	return $wf_field;
}

/*
 * แปลงวันที่จากเว็บเข้าแต่ละรูปแบบฐานข้อมูล
 * Input = 20/05/2560
 * Output = 2017-05-20 (Mysql)
 */
function date2db($value='')
{
	$new_date = "";
	switch(db::$_dbType)
	{
		case 'MSSQL':
			break;
		case 'MYSQL':
			if($value != "")
			{
				$old_date = explode("/", $value);
				$new_date = ($old_date[2] - 543)."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}
			break;
		case 'ORACLE':
		if($value != "")
			{
			if(db::$_langDate == "EN")
			{
				$mont_th_short = get_month('E');
				$sp_date = explode("/", $value);
				// หา เป็นเดือนที่มี 5 char หรือ 4   ถ้า 5  จะเว้นช่องว่างแค่ 1  ช่อง
				$new_date = $sp_date[0] . "-" . $mont_th_short[($sp_date[1] * 1)] . "-" . ($sp_date[2] - 2543);
			}
			else
			{
				$mont_th_short = get_month('S');
				$sp_date = explode("/", $value);
				// หา เป็นเดือนที่มี 5 char หรือ 4   ถ้า 5  จะเว้นช่องว่างแค่ 1  ช่อง
				if(strlen($mont_th_short[($sp_date[1] * 1)]) == 5)
				{
					$space = " ";
				}
				else
				{
					$space = "  ";
				}
				$new_date = $sp_date[0] . " " . $mont_th_short[($sp_date[1] * 1)] . $space . ($sp_date[2] - 543);
			}
			}
			else
			{
				$new_date = "";
			}
			break;
	}

	return $new_date;
}

/*
 * แปลงวันที่จากฐานข้อมูล ไปเข้า Date Picker
 * Output = 20/05/2560
 */
function db2date($value)
{
	if($value == "" || $value == "0000-00-00")
	{
		$new_date = "";
	}
	else
	{
		switch(db::$_dbType)
		{
			case 'MSSQL':
				break;
			case 'MYSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".($old_date[0] + 543);

				break;
			case 'ORACLE':
				if(db::$_langDate == "EN")
				{
					$mont_th_short = get_month('E'); 
					$d = explode("-", $value);
					$new_date = $d[0]."/".sprintf("%02d", array_search($d[1], $mont_th_short))."/".($d[2] + 2543);
				}
				else
				{
					$mont_th_short = get_month('S');
					$date1 = str_replace("  ", " ", $value);
					$d = explode(" ", $date1);
					$new_date = $d[0]."/".sprintf("%02d", array_search($d[1], $mont_th_short))."/".($d[2] + 543);
				}
				
				break;
		}
	}

	return $new_date;
}

/*
 * แปลงวันที่จากฐานข้อมูลไปแสดงผล
 * Output = 20 พ.ค. 2560
 */
function db2date_show($value)
{
	if($value == "" || $value == "0000-00-00")
	{
		$new_date = "";
	}
	else
	{
		$mont_th_short = get_month('S');

		switch(db::$_dbType)
		{
			case 'MSSQL':
				break;
			case 'MYSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]." ";
				$new_date .= $mont_th_short[number_format($old_date[1])]." ";
				$new_date .= ($old_date[0] + 543);

				break;
			case 'ORACLE':
				$year = (substr($value, -4) + 543);

				$new_date = substr($value, 0, -5)."".$year;
				break;
		}
	}

	return $new_date;
}
function redirect($url, $text = false)
{
	if($text != "")
	{
		$alert = 'alert("'.$text.'");';
	}
	else
	{
		$alert = "";
	}
	echo '<script>';
	echo $alert;
	echo 'window.location.href="'.$url.'"';
	echo '</script>';
}

/*
 * หาข้อมูลจากตารางกลับมาเป็นข้อความ
 * @table_name		ชื่อตารางที่ต้องการหา
 * @field_id		ฟิลที่ต้องการหา
 * @field_name		ฟิลที่ต้องการแสดงผล
 * @field_value		ค่าที่เอาไปเป็นเงื่อนไข
 * @where			เงื่อนไขเพิ่มเติม
 */
function get_data($table_name, $field_id, $field_name, $field_value, $where = "")
{
	$sql = db::query("select ".$field_name." from ".$table_name." where ".$field_id." = '".$field_value."' ".$where);
	$rec = db::fetch_array($sql);

	return $rec[$field_name];
}

/*
 * Select ข้อมูลมาเป็น Array
 * @table_name		ชื่อตารางที่ต้องการหา
 * @field_id		ฟิลที่ต้องการหา
 * @field_name		ฟิลที่ต้องการแสดงผล
 * @where			เงื่อนไขเพิ่มเติม
 */
function build_data($table_name, $field_id, $field_name, $where = "")
{
	$data = array();
	if($where != "")
	{
		$where = " where ".$where;
	}
	$sql = db::query("select ".$field_id.", ".$field_name." from ".$table_name." ".$where." order by ".$field_id." asc");
	while($rec = db::fetch_array($sql))
	{
		$data[$rec[$field_id]] = $rec[$field_name];
	}

	return $data;
}

/*
 * Select Count
 * @table_name		ชื่อตารางที่ต้องการหา
 * @field_name		ฟิลที่ต้องการหา
 * @where			เงื่อนไขเพิ่มเติม
 */
function count_data($table_name, $field_name = "*", $where = "")
{
	$data = array();
	if($where != "")
	{
		$where = " where ".$where;
	}
	$sql = db::query("select count(".$field_name.") as total from ".$table_name." ".$where." ");
	$rec = db::fetch_array($sql);

	return $rec['TOTAL'];
}

/*
 * สร้าง Drop down list
 * @name			ชื่อและ ID
 * @data			ข้อมูล เป็น array
 * @selected		ข้อมูลที่ต้องการเลือกเป็น Default
 * @extra			Attribute อื่นๆ
 */
function form_dropdown($name, $data = array(), $selected = "", $extra = "")
{
	$html = '<select name="'.$name.'" id="'.$name.'" class="select2 form-control" '.$extra.'>'.PHP_EOL;
	$html .= '<option value=""></option>'.PHP_EOL;
	foreach($data as $_key => $_val)
	{
		$select_data = $_key == $selected ? 'selected' : '';
		$html .= '<option value="'.$_key.'" '.$select_data.'>'.$_val.'</option>'.PHP_EOL;

	}
	$html .= '</select>';

	echo $html;
}
/*
 * สร้าง Drop down list
 * @name			ชื่อและ ID
 * @value			ค่าที่แสดง
 * @extra			Attribute อื่นๆ
 */
function form_itext($name, $value = "",$class="",$extra = "")
{
	$html = '<input name="'.$name.'" id="'.$name.'" class="form-control'.$class.'" value="'.$value.'" '.$extra.'>';
	return $html;
}
function form_iarea($name, $value = "",$class="",$extra = "")
{
	$html = '<textarea name="'.$name.'" id="'.$name.'" class="form-control'.$class.'" '.$extra.'>'.$value.'</textarea>';
	return $html;
}
function form_idate($name, $value = "",$class="",$extra = "")
{
	$html = '<input name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$extra.'  class="form-control datepicker'.$class.'" placeholder="วว/ดด/ปปปป"><span class="input-group-addon bg-primary"><span class="icofont icofont-ui-calendar"></span></span>';
	return $html;
}
function form_iradio_old($name,$data = array(),$value = "",$class="",$extra = "")
{
	$html = '<div class="form-radio">';
	foreach($data as $_key => $_val)
	{
		$check_data = $_key == $value ? 'checked' : '';
		$html .= '<div class="radio'.$class.'"><label><input type="radio" name="'.$name.'" id="'.$name.'" value="'.$_key.'" '.$check_data.' '.$extra.'><i class="helper"></i> '.$_val.'</label></div>';

	}
	$html .= '</div>';
	return $html;
}
function form_iradio($name,$data = array(),$value = "",$class="",$extra = "")
{
	$html = '<div class="form-radio">';
	foreach($data as $_key => $_val)
	{
		$check_data = $_key == $value ? 'checked' : '';
		$html .= '<div class="radio'.$class.'"><label><input type="radio" name="'.$name.'" id="'.$name.'" value="'.$_key.'" '.$check_data.' '.$extra.'><i class="helper"></i> '.$_val.'</label></div>';
	}
	$html .= '</div>';
	return $html;
}
function form_icheck($num,$chk_id,$chk_name=array(),$chk_label=array(),$chk_value=array(),$chk_checked=array(),$chk_opt=array(),$class="",$extra = "")
{
	$html = '';
	for($i=0;$i<$num;$i++)
	{
		$html .='<div class="checkbox-color checkbox-primary'.$class.'"><input name="'.$chk_name[$i].'" id="'.$chk_name[$i].'" chk-id="'.$chk_id.'" chk-value="'.$chk_value[$i].'" type="checkbox" '.$chk_checked[$i].' value="'.$chk_value[$i].'" '.$extra.'><label for="'.$chk_name[$i].'">'.$chk_label[$i].'</label><input type="hidden" name="'.$chk_name[$i].'_TYPE" id="'.$chk_name[$i].'_TYPE" value="'.$chk_opt[$i].'"></div>';

	}
	if($num > 0){
	$html .='<input type="hidden" name="'.$chk_id.'_COUNT" id="'.$chk_id.'_COUNT" value="'.$num.'">';
	}
	return $html;
}
function form_ifile($name, $value = array(),$title="",$mult="",$extra = "")
{
	if($title == ""){ $title = "เลือกไฟล์"; }
	$html = '<div class="md-group-add-on" '.$extra.'><span class="md-add-on-file"><button class="btn btn-primary waves-effect waves-light"><i class="zmdi zmdi-attachment-alt"></i> '.$title.'</button></span><div class="md-input-file"><input type="file" name="'.$name.'" class=""  multiple  /><input type="text" class="md-form-control md-form-file"><label class="md-label-file"></label></div></div>';
	return $html;
}
function form_iselect($name,$data = array(),$value = "",$class="",$extra = "")
{
	$html = '<select name="'.$name.'" id="'.$name.'" class="form-control'.$class.'" '.$extra.'>';
	foreach($data as $_key => $_val)
	{
		$check_data = $_key == $value ? 'selected' : '';
		$html .= '<option value="'.$_key.'" '.$check_data.'>'.$_val.'</option>';

	}
	$html .= '</select>';
	return $html;
}
