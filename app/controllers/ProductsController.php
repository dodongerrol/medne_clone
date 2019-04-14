<?php

class ProductsController extends \BaseController {

	public function getProducts( )
	{
		$products = new Products();
		return $products->getProducts();
	}
}
