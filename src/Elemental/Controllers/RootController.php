<?php namespace Elemental\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class RootController extends BaseController {

    use DispatchesCommands, ValidatesRequests;

}
