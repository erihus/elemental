<?php


Route::group(['prefix' => 'api'], function() {

    //Collection Endpoints
    Route::get('page-list', 'CollectionController@linkList');
    // Route::get('collections/children/{type}/', ['as' => 'collections.show.children', 'uses' => 'CollectionsController@showChildren']);
    // Route::get('collections/{slug}/{children}/{childType}', ['as' => 'collections.show.associated', 'uses' => 'CollectionsController@showAssoc']);
    // Route::delete('collection/{childSlug}/{parentSlug}', ['as' => 'api.collection.destroy', 'uses' => 'CollectionController@destroy']);
    Route::resource('collection', 'CollectionController', ['except' => ['index', 'create']]);
    Route::get('collection/{lookupType?}/{lookup?}/{children?}', ['uses' => 'CollectionController@show']);
    
    //Element Endpoints
    Route::delete('element/{childSlug}/{parentSlug}', ['as' => 'elements.destroy', 'uses' => 'ElementController@destroy']);
    Route::resource('element', 'ElementController', ['except' => ['index', 'create']]);
    Route::get('element/{lookupType?}/{lookup?}', ['uses' => 'ElementController@show']);


    //Component Endpoints
    Route::get('component/{prototype}/{type}', ['as' => 'component.show', 'uses' => 'ComponentController@show']);
    Route::resource('component', 'ComponentController', ['only' => ['show']]);

    //User Endpoints
    Route::resource('user', 'UserController', ['except' => ['create', ]]);

    //File Endpoints
    Route::get('file/upload', 'FileController@chunkTest');
    Route::post('file/upload', 'FileController@upload');
    Route::post('file/redactor-upload', 'FileController@redactorUpload');
    Route::get('file/list', 'FileController@listFiles');

});


Route::get('cms', 'AdminController@index');