<?php
use Illuminate\Support\Facades\Input;
class NetworkPatnerController extends \BaseController {

	public function createLocalNetwork( )
	{
		$input = Input::all();
		$network = new LocalNetwork();
		return $network->createLocalNetwork($input);
	}

	public function createLocalNetworkPartners( )
	{
		$input = Input::all();
		$partners = new LocalNetworkPartners();

		return $partners->createLocalNetworkPartners($input);
	}

	public function getLocalNetworkList( )
	{
		return DB::table('local_network')->get();
	}

	public function getLocalNetworkPartnerList($id)
	{
		return DB::table('local_network_partners')->where('local_network_id', $id)->get();
	}
}
