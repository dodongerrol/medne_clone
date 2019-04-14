<?php

class BulkTransaction extends Eloquent 
{

		protected $table = 'bulk_transaction';
    protected $guarded = ['bulk_transaction_id'];

    public function createBulkTransaction($data)
    {
    	return BulkTransaction::create($data);
    }

    public function getBulkTransactionByDate($clinic_id, $start, $end)
    {
    	$start = date('Y-m-d', strtotime($start));
    	$end = date('Y-m-d', strtotime($end));
    	$result = DB::table('bulk_transaction')
    				->join('user', 'user.UserID', '=', 'bulk_transaction.user_id')
    				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'bulk_transaction.procedure_id')
    				->where('bulk_transaction.clinic_id', $clinic_id)
    				->where('bulk_transaction.book_date', '>=', $start)
    				->where('bulk_transaction.book_date', '<=', $end)
    				->select('user.Name as Name', 'clinic_procedure.Name as clinic_procedure_name', 'bulk_transaction.amount as procedure_cost', 'bulk_transaction.updated_at as updated_at', 'bulk_transaction.created_at as Created_on', 'bulk_transaction.book_date as BookDate')
    				->groupBy('bulk_transaction.bulk_transaction_id')
            ->orderBy('bulk_transaction.book_date', 'asc')
    				->get();

    	$new_data = [];
			foreach ($result as $key => $value) {
				$temp = array(
					'Name'									=> $value->Name,
					'BookDate'							=> strtotime($value->BookDate),
					'clinic_procedure_name' => $value->clinic_procedure_name,
					'current_wallet_amount'	=> 0,
					'doctor_name'						=> null,
					'medi_percent'					=> 0,
					'credit_cost'						=> 0,
					'clinic_discount'				=> "0%",
					'paid_medi'							=> 0,
					'updated_at'						=> $value->updated_at,
					'revenue'								=> null,
					'Created_on'						=> strtotime($value->Created_on),
					'procedure_cost'				=> $value->procedure_cost
				);
				array_push($new_data, $temp);
			}
			return $new_data;
    }
}
