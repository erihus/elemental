<?php


Route::group(['prefix' => 'api'], function() {

    //Collection Endpoints
    Route::get('page-list', 'Elemental\Controllers\CollectionController@linkList');    
    Route::resource('collection', 'Elemental\Controllers\CollectionController', ['except' => ['index', 'create']]);
    Route::get('collection/{lookupType?}/{lookup?}/{children?}', ['uses' => 'Elemental\Controllers\CollectionController@show']);
    
    //Element Endpoints
    Route::delete('element/{childSlug}/{parentSlug}', ['as' => 'elements.destroy', 'uses' => 'Elemental\Controllers\ElementController@destroy']);
    Route::resource('element', 'Elemental\Controllers\ElementController', ['except' => ['index', 'create']]);
    Route::get('element/{lookupType?}/{lookup?}', ['uses' => 'Elemental\Controllers\ElementController@show']);


    //Component Endpoints
    Route::get('component/{prototype}/{type}', ['as' => 'component.show', 'uses' => 'Elemental\Controllers\ComponentController@show']);
    Route::resource('component', 'Elemental\Controllers\ComponentController', ['only' => ['show']]);

    //User Endpoints
    Route::resource('user', 'Elemental\Controllers\UserController', ['except' => ['create', ]]);

    //File Endpoints
    Route::get('file/upload', 'Elemental\Controllers\FileController@chunkTest');
    Route::post('file/upload', 'Elemental\Controllers\FileController@upload');
    Route::post('file/redactor-upload', 'Elemental\Controllers\FileController@redactorUpload');
    Route::get('file/list', 'Elemental\Controllers\FileController@listFiles');

});


Route::get('cms', 'Elemental\Controllers\AdminController@index');