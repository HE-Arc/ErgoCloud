<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\TestConfig;
use App\Test;

use Input;
use Redirect;

class TestConfigController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($test_id)
    {   
        $test = Test::find($test_id);
        $testConfig = new TestConfig();
        return view('testconfig.create')
                ->with('page_status', 1)
                ->with('testConfig', $testConfig)
                ->with('test', $test);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $test_id)
    {
        $input = array_except(Input::all(), '_method');

        $validation = TestConfig::validate($input);
        if($validation->passes())
        {   
            $testconfig = TestConfig::create($input);

            $test = Test::find($test_id);
            $test->testConfig()->save($testconfig);
            $test->save();

            return Redirect::route('show_test', array('test_id' => $test_id));
        }
        else
        {
            return Redirect::route('test.{test_id}.testconfig.create', 
                                array(
                                    'test_id' => $test_id
                                    )
                            )->withError($validation->errors()->toArray());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($test_id, $id)
    {
        $test = Test::find($test_id);
        $testConfig = TestConfig::find($id);
        return view('testconfig.edit')
                ->with('page_status', 1)
                ->with('testConfig', $testConfig)
                ->with('test', $test);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $test_id, TestConfig $testconfig)
    {
        $input = array_except(Input::all(), '_method');

        $validation = TestConfig::validate($input);
        if($validation->passes())
        {
            $testconfig->update($input);

            return Redirect::route('show_test', array('test_id' => $test_id));
        }
        else
        {
            return Redirect::route('test.{test_id}.testconfig.edit', 
                                array(
                                    'test_id' => $test_id, 
                                    'testconfig' => $testconfig->id
                                    )
                            )->withError($validation->errors()->toArray());
        }
    }
}
