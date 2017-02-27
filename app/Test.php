<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
* The class gets the test(=a user test) table from the dashboard db     
*/
class Test extends Model
{
    /**
     * The table associated with the model.     
     * @var string
     */
    protected $table = 'tests';
    public $timestamps = false;
    protected $fillable = array('name', 'width_screen', 'height_screen', 'instruction');
    /**
     * Get the subjects for the test     
     */
    public function subjects()
    {
        return $this->hasMany('App\Subject');
    }


    /**
    *   for debug purpose
    */
    public function subjectsTroughTrials(){
        $id = $this->id;
        return Subject::whereHas('trials', function($query) use ($id){
             $query->where('trials.test_id', '=', $id);
        });
    }

     /**
     * Get the trials for the test     
     */
    public function trials()
    {
        return $this->hasMany('App\Trial');
    }
    
    public function trialSequences()
    {
        return $this->hasManyThrough('App\TrialSequence', 'App\Subject');
    }
    
    public function testConfig()
    {
        return $this->hasOne('App\TestConfig', 'test_id', 'id');
    }
    
    
}
