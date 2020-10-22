<?php

class CustomerAdminRole extends Eloquent 
{

    protected $table = 'customer_admin_roles';
    protected $guarded = ['id'];

    public function getAdminRoles($id)
    {
        return CustomerAdminRole::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertAdminRoles($data)
    {
        return CustomerAdminRole::create($data);
    }

    public function updateAdminRoles($id, $data)
    {
        return CustomerAdminRole::where('id', $id)->update($data);
    }   
}
