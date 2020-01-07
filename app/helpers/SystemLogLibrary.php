<?php
class SystemLogLibrary
{
	
	public static function createAdminLog($data)
	{
		$logs = new AdminLogs();
		return $logs->createData($data);
	}

	public static function serializeData($data)
	{
		return serialize($data);
	}
}
?>