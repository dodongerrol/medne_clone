<?php

class UserPermissionsHelper
{
    public static function getUserPemissions($id, $user_type)
    {
        if($user_type == "member_admin") {
            $adminRole = DB::table('customer_admin_roles')->where('member_id', $id)->select('id')->first();
        } else {
            $adminRole = DB::table('customer_admin_roles')->where('hr_id', $id)->select('id')->first();
        }

        if(!$adminRole) {
            return false;
        }

        return DB::table('employee_and_dependent_permissions')
                        ->where('customer_admin_role_id', $adminRole->id)
                        ->select('customer_admin_role_id', 'view_employee_dependent', 'edit_employee_dependent', 'enroll_terminate_employee', 'approve_reject_edit_non_panel_claims', 'create_remove_edit_admin_unlink_account', 'manage_billing_and_payments','add_location_departments')
                        ->first();
    }

    public static function getLocationAndDepartments($id, $user_type)
    {
        if($user_type == "member_admin") {
            $adminRole = DB::table('customer_admin_roles')->where('member_id', $id)->select('id')->first();
        } else {
            $adminRole = DB::table('customer_admin_roles')->where('hr_id', $id)->select('id')->first();
        }
        // get location permission
        $locationPermission = DB::table('location_admin_permission')->where('customer_admin_role_id', $id)->select('id')->first();
        $departmentPermission = DB::table('department_admin_permission')->where('customer_admin_role_id', $id)->select('id')->first();

        $permissions_applied = ['All Employees & Dependents'];

        if($locationPermission) {
            $permissions_applied[] = 'Locations';
        }

        if($departmentPermission) {
            $permissions_applied[] = 'Department';
        }

        return $permissions_applied;
    }
}