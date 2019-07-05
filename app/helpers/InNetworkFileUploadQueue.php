<?php
class InNetworkFileUploadQueue
{
	public function fire($job, $data)
	{
		$image = \Cloudinary\Uploader::upload($data['file']);

		$receipt_file = $image['secure_url'];
		$receipt = array(
		    'transaction_id'    => $data['transaction_id'],
		    'user_id'			=> $data['user_id'],
		    'file'      => $receipt_file,
		    'type'     => 'image'
		);

		$trans_docs = new UserImageReceipt( );
		$trans_docs->saveReceipt($receipt);
		$job->delete();
		// unlink($data['file']);
	}
}
?>