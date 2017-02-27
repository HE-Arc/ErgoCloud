<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
 * Home route (liste of all the tests available)
 */
Route::group(['middleware' => 'web'], function () { 

    // Upload DB
    Route::get('/import', ['as'=>'import', 'uses'=>'ImportDBController@importDB']);
    Route::post('/upload', 'ImportDBController@uploadDB');
    Route::get('/test/{test_id}/import/images', ['as'=>'import_images', 'uses'=>'ImportDBController@importImages']);
    Route::post('/test/{test_id}/upload/images', 'ImportDBController@uploadImages');
    Route::get('/test/import/missing_images', ['as'=>'import_missing_images', 'uses'=>'ImportDBController@importMissingImages']);
    Route::post('/test/upload/missing_images', 'ImportDBController@uploadMissingImages');

    // Statitics
    Route::get('/test/{test_id}/trial/{trial_name}/statistics',['as'=>'statistics_trial', 'uses'=>'TrialController@statistics']);
    Route::get('/test/{test_id}/statistics',['as'=>'statistics_test', 'uses'=>'TestController@statistics']);
   
    //Heatmap
    Route::get('/test/{test_id}/trial/{trial_name}/heatmap',['as'=>'heatmap_trial', 'uses'=>'TrialController@heatmap']);

    //Scanpath
    Route::get('/test/{test_id}/trial/{trial_name}/scanpath',['as'=>'scanpath_trial', 'uses'=>'TrialController@scanpath']);


    // TestConfig
    Route::group(['prefix' => 'test/{test_id}'], function () {
        Route::resource('testconfig', 'TestConfigController',
                        ['only' => ['create', 'store', 'edit', 'update']]);
    });


    // Home routes
    Route::get('/',['as'=>'home', 'uses'=>'TestController@index']);
    Route::get('/help',['as'=>'help', 'uses'=>'TestController@help']);
    Route::get('/{board_id}',['as'=>'test', 'uses'=>'TestController@subjects_trails'] );
    Route::get('test/{test_id}/urlpath',['as'=>'urlpath', 'uses'=>'TestController@url_path'] );
    Route::get('/test/{test_id}', ['as'=>'show_test', 'uses'=>'TestController@show']);

    // Calibrations
    Route::get('/test/{test_id}/calibration', ['as'=>'calibration', 'uses'=>'CalibrationController@index']);
    Route::post('/calibration/state', ['as'=>'state', 'uses'=>'CalibrationController@state']);

    //Filters
    Route::get('/filter/pathURL', ['as' =>'path', 'uses'=>'FilterController@redirect']);
    Route::get('test/{test_id}/filter', ['as'=>'filter', 'uses'=>'FilterController@index']);
    Route::post('/filter/subject', ['as'=>'subject', 'uses'=>'FilterController@getSubjectDetails']);
    Route::post('/filter/test', ['as'=>'test', 'uses'=>'FilterController@testFilters']);
    Route::post('/filter/trials', ['as' => 'trials', 'uses'=> 'FilterController@getTrials']);
    Route::post('filter/aois', ['as' =>'aois', 'uses'=>  'FilterController@getAois']);
    Route::post('filter/{test_id}/subjects', ['as' =>'subjects', 'uses'=>'FilterController@getSubjectsAccordingToFilters']);
});






