<?php

class WebHook extends Eloquent 
{

	protected $table = 'stripe_webhook';
  protected $guarded = ['stripe_webhook_id'];

  public function createWebHook($data)
  {
  	return WebHook::create($data);
  }
}
