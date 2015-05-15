<?php namespace Elemental\Controllers;

use \ReflectionClass;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Element;
use Collection;

class ElementController extends RootController {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('auth', ['except' => 'show']);
    }



    // public function show($type)
    // {
    //     $type = studly_case($type);
    //     return response()->json(Element::readAll($type));
    // }


    public function show($lookupType = null, $lookup = null) {
      if($lookupType == 'type') {
          $response = $this->_showByType($lookup);
      } elseif($lookupType == 'slug') {
          $response = $this->_showBySlug($lookup);
      } else {
        $response = Element::readAll();
      }

      return response()->json($response);
    }



    
    public function update($slug) {
       $updated = $this->request->all();
       if(isset($updated['attributes'])) {
            return $this->_updateAttributes($slug, $updated['attributes']);
        } elseif (isset($update['order'])) {
            return $this->_updateOrder($slug, $order);
        } elseif (isset($updated['status'])) {
            return $this->_updateMetadata($slug, $updated);
        }
      
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $parent = $this->request->input('parent');
        $input = $this->request->except(['except' => 'parent']);

        try {
            if(!Element::create($input, true)) {
              return response()->json(['ok'=>0, 'errors'=>Element::errors()], 500);  
            }
        } catch(Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>Element::errors()], 500);  
        }
        
        try {
          if(Element::attach($input['slug'], $parent)) {
              $element = Element::read($input['slug']);
              return response()->json(['ok' => 1, $element], 200);
          } else {
            return response()->json(['ok'=>0, 'errors'=>Element::errors()], 500);  
          }
        } catch (Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>Element::errors()], 500);  
        }

    }


    public function destroy($childSlug, $parentSlug = null) {
        try {
           if(Element::delete($childSlug, $parentSlug)){
               return response()->json(['ok' => 1], 200); 
           } else {
                return response()->json(['ok'=>0, 'errors'=>Element::errors()], 500);
           }

        } catch (Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>Element::errors()], 500);  
        }
    }


    private function _showByType($type)
    {
        $type = studly_case($type);
        $element = Element::readAll($type);
        return $element;
    }


    private function _showBySlug($slug)
    {
        $element = Element::query(['slug' => $slug], false);          
        return $element;
    }


    private function _updateAttributes($slug, $attributes) {
       $attrUpdates = [];

        foreach($attributes as $attribute) {
          $attrUpdates[$attribute['key']] = (isset($attribute['value'])) ? $attribute['value'] : null;
        }

        if(!Element::update($slug, $attrUpdates)) {
          $errors = Element::errors();
          return response()->json(['ok'=>0, 'errors'=>$errors], 500);  
        } else { 
          return response()->json(['ok' => 1], 200);
        }
    }

    private function _updateOrder($slug, $order) {
       if(!Element::updateOrder($slug, $order)) {
            $errors = Element::errors();
            return response()->json(['ok'=>0, 'errors'=>$errors], 500);    
        } else { 
            return response()->json(['ok' => 1], 200);
        }
    }

     private function _updateMetadata($slug, $updates) {
      if(!Element::updateMeta($slug, $updates)){
        $errors = Collection::errors();
        return response()->json(['ok'=>0, 'errors'=>$errors], 500);  
      } else {
        return response()->json(['ok' => 1], 200);
      }
    }
}