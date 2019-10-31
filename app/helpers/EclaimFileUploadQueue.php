<?php

class EclaimFileUploadQueue 
{
	
	public function fire($job, $data)
	{
		\Cloudinary::config(array(
			"cloud_name" => "mednefits-com",
			"api_key" => "881921989926795",
			"api_secret" => "zNoFc7EHPMtafUEt0r8gxkv4V5U"
		));
		$image = \Cloudinary\Uploader::upload($data['file']);
		$e_claim_docs = new EclaimDocs( );

		$receipt_file = $image['secure_url'];
		$receipt = array(
		    'e_claim_id'    => $data['e_claim_id'],
		    'doc_file'      => $receipt_file,
		    'file_type'     => 'image'
		);

		$e_claim_docs = new EclaimDocs( );
		$e_claim_docs->createEclaimDocs($receipt);
		$job->delete();
		// sleep(1);
		// return $receipt;
	}
}
?>