<?php

	class StripeHelper {
		public static function config( )
		{	
			if(StringHelper::Deployment()==1){
				$stripe = array(
			  		"secret_key"      => "sk_test_adGsPEwZUaB3oXGQZ63uxHiw",
			  		"publishable_key" => "pk_test_whm2wdt8IMnLKOuuZEtCe8u9"
				);

				// $stripe = array(
			 //  		"secret_key"      => "sk_live_HSGMJbbXVmVuSEgedJFHggmO",
			 //  		"publishable_key" => "pk_live_uJBcEuYmkSwIltZXE7hvTI3A"
				// );

			} else {
				// $stripe = array(
			 //  		"secret_key"      => "sk_test_eI33sAv4EdGoNsyJWSBjflOK",
			 //  		"publishable_key" => "pk_test_8cFCLwxPLlDVeUb6beViD5Na"
				// );

				$stripe = array(
			  		"secret_key"      => "sk_test_adGsPEwZUaB3oXGQZ63uxHiw",
			  		"publishable_key" => "pk_test_whm2wdt8IMnLKOuuZEtCe8u9"
				);
			}
			return $stripe;
		}
	}
?>