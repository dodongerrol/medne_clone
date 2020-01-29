<?php

class AdminLogs extends Eloquent 
{

	protected $table = 'admin_logs';
  protected $guarded = ['admin_log_id'];

  public function createData($data)
  {
  	return AdminLogs::create($data);
  }
}
