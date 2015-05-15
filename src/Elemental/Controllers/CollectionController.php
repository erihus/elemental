<?php namespace Elemental\Controllers;

use \ReflectionClass;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Collection;


class CollectionController extends RootController {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('auth', ['except' => ['show']]);
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
            if(!Collection::create($input, true)) {
              return response()->json(['ok'=>0, 'errors'=>Collection::errors()], 500);      
            }
        } catch(Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>Collection::errors()], 500);  
        }
        
        try {
            if(Collection::attach($input['slug'], $parent)) {
              $collection = Collection::read($input['slug']);
              return response()->json(['ok' => 1, $collection], 200);
            } else {
              Collection::delete($input['slug']);
              return response()->json(['ok'=>0, 'errors'=>Collection::errors()], 500);  
            }
        } catch (Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>Collection::errors()], 500);  
        }

    }

    public function linkList() 
    {
      $pages = Collection::readAll('page');
      $pageList = [];
      foreach($pages as $page) {
        $data = ["name" => $page['nickname'], "url" => $page['slug']];
        array_push($pageList, $data); 
      }

      return response()->json($pageList);

    }


    public function show($lookupType = null, $lookup = null, $showChildren = null) {
      if($lookupType == 'type') {
          $response = $this->_showByType($lookup, $showChildren);
      } elseif($lookupType == 'slug') {
          $response = $this->_showBySlug($lookup, $showChildren);
      } else {
        $response = Collection::readAll();
      }

      return response()->json($response);
    }


    public function edit($slug) {
      return response()->json(Collection::read($slug));
    }


    public function update($slug) {
       $updated = $this->request->all();

       die(print_r($updated));

       if(isset($updated['attributes'])) {
            return $this->_updateAttributes($slug, $updated['attributes']);
        } elseif (isset($updated['order'])) {
            return $this->_updateOrder($slug, $updated['type'], $updated['id'], $updated['order']);
        } elseif (isset($updated['status'])) {
            return $this->_updateMetadata($slug, $updated);
        }
    }


     public function destroy($slug) {
        try {
           if(Collection::delete($slug)){
               return response()->json(['ok' => 1], 200); 
           } else {
                return response()->json(['ok'=>0, 'errors'=>Collection::errors()], 500);
           }

        } catch (Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>Collection::errors()], 500);  
        }
    }


    private function _showByType($type, $children = null)
    {
        $type = studly_case($type);
        $collection = Collection::readAll($type);

        if(!is_null($children)) {
          $response = $collection[0]['children'];
        } else {
          $response = $collection;
        }
          
        return $response;
    }


    private function _showBySlug($slug, $children = null)
    {
        $collection = Collection::query(['slug' => $slug], false);

        if(!is_null($children)) {
          $response = $collection[0]['children'];
        } else {
          $response = $collection;
        }
          
        return $response;
    }


    private function _updateAttributes($slug, $attributes) {
       $attrUpdates = [];

       foreach($attributes as $attribute) {
          $attrUpdates[$attribute['key']] = (isset($attribute['value'])) ? $attribute['value'] : null;
       }

        if(!Collection::update($slug, $attrUpdates)) {
          $errors = Collection::errors();
          return response()->json(['ok'=>0, 'errors'=>$errors], 500);  
        } else { 
          return response()->json(['ok' => 1], 200);
        }
    }

    private function _updateOrder($slug, $type, $id, $order) {
       if(!Collection::updateOrder($slug, $type, $id, $order)) {
            $errors = Collection::errors();
            return response()->json(['ok' => 0,'errors'=>$errors], 500);  
        } else { 
            return response()->json(['ok' => 1]);
        }
    }

    private function _updateMetadata($slug, $updates) {
      if(!Collection::updateMeta($slug, $updates)){
        $errors = Collection::errors();
        return response()->json(['ok'=>0, 'errors'=>$errors], 500);  
      } else {
        return response()->json(['ok' => 1], 200);
      }
    }


}