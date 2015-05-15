<?php namespace Elemental\Controllers;

use Collection;
use Blade;

class AdminController extends RootController {


	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		Blade::setContentTags('<%', '%>');        // for variables and all things Blade
    	Blade::setEscapedContentTags('<%%', '%%>');   // for escaped data

		return view('elemental::dashboard');
	}

}
