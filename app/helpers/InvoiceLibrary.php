<?php
class InvoiceLibrary 
{
	public static function getInvoiceNuber($table, $invoice_type)
	{
		$year = date('y');
		$count = DB::table($table)->count();
		$invoice_number = $year.$invoice_type.str_pad($count + 1, 6, "0", STR_PAD_LEFT);
		return $invoice_number;
	}	
}
?>