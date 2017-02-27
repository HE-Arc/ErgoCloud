<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class TestConfig extends Model
{
    protected $connection = 'mysql';
    protected $fillable = array('google_results_url', 'name_column', 'evaluation_column', 'sheet');

    public static function validate($input)
    {
        $rules = array(
            'google_results_url' => 'Required|regex:/^https:\/\/docs.google.com\/spreadsheets\/d\/.*$/',
            'name_column' => 'Required|Min:1|Max:3|Alpha',
            'evaluation_column' => 'Max:3|Alpha'
        );

        return Validator::make($input, $rules);
    }

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id');
    }
}
