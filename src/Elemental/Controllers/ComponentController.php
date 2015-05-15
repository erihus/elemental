<?php namespace Elemental\Controllers;

use \ReflectionClass;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ComponentController extends RootController {

	protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

	
	/**
	 * Display the specified resource.
	 *
	 * @param  string  $prototype
	 * @param  string  $type
	 * @return Response
	 */
	public function show($prototype, $type)
	{
		try {
			$componentArray = [];
	        $componentClassName = "App\\Elemental\\Components\\".ucfirst($prototype)."s\\".ucfirst($type)."Component";
	        $component = new $componentClassName;
	        $reflector = new ReflectionClass($componentClassName);
	        $properties = $reflector->getProperties();

	        foreach($properties as $prop) {
	            $propName = $prop->name;
	            if($propName !== 'rules') {
	                $componentArray[$propName] = $component->$propName;
	            } else {
	                $componentArray['rules'] = [];
	                if(is_array($component->rules)) {
	                    foreach($component->rules as $fieldName => $ruleList) {
	                        $rules = explode('|', $ruleList);
	                        $componentArray['rules'][$fieldName] = $rules;
	                    }
	                }
	            }
	           
	        }
	        return response()->json($componentArray, 200);
	    } catch (Exception $e) {
	     	return response()->json(['error'=> 'Component Not Found.'], 500);
	    }
	}

}
