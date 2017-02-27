<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\GoogleResultsHelper;

/**
* The class gets the subjects table from the dashboart db     
*/
class Subject extends Model
{
    /**
     * The table associated with the model.     
     * @var string
     */
    protected $table = 'subjects';
    public $timestamps = false;
    protected $fillable = array('test_id', 'name', 'age', 'sex', 'category', 'language', 'type', 'glasses', 'ambiance', 'light', 'comments', 'eliminate');
    

    /**
    *   Get the google evaluation
    *   @param $test : Test model
    *   @return String with result or FALSE if not found
    *   May raise \Google_Service_Exception if bad configuration. handle always like this:
    *   
    *   try
    *   {
    *       ...
    *   }
    *   catch(\Google_Service_Exception $e)
    *   {
    *       ...
    *   }
    *
    */
    public function getGoogleEvaluation($test)
    {
          return GoogleResultsHelper::getSubjectEvaluation(
                $this->name, 
                $test->testConfig->name_column, 
                $test->testConfig->evaluation_column,
                $test->testConfig->sheet, 
                $test->testConfig->google_results_url);
    }


    /**
     * Get the parent test for a subject     
     */
    public function test()
    {
        return $this->belongsTo('App\Test');
    }
    
    /**
     * Get the sequences for the subject     
     */
    public function trial_sequences()
    {
        return $this->hasMany('App\TrialSequence');
    }
    
    /**
     * Get the trails for the subject     
     */
    public function trials()
    {
        return $this->belongsToMany('App\Trial', 'trial_sequences',
                        'subject_id', 'trial_id');
    }  
    
    /**
     * Get the calibrations for the subject     
     */
    public function calibrations()
    {
        return $this->hasMany('App\Calibration');
    } 
    
    /**
     * Get the fixations registerd during th test for the user
     */
    public function fixations()
    {
        return $this->hasManyThrough('App\Fixation', 'App\TrialSequence');
                
    }
    
    /**
     * Get the events registerd during th test for the user
     */
    public function trial_events()
    {
        return $this->hasManyThrough('App\TrialEvent', 'App\TrialSequence');
    }
    
    /**
     * This method get the total time spend by the subject to do the test
     * @return $duration = the sum of trial_sequence[duration] for the subject (miliseconds)
     */
    public function getTestDuration()
    {
        $sequences = $this->trial_sequences;        
        $duration = 0;
        foreach ($sequences as $sequence)
        {            
            $duration += $sequence->duration;
        }       
        return $duration;         
    }
    
    /**
     * The method return the total number of visited trials(pages) for the user
     * If one page is visited multiple times, it's counted only one time
     * @return number of visited trials
     */
    public function getNumberOfVisitedTrials()
    {
        $trialIds=[];
        foreach ($this->trial_sequences as $sequence)
        {   
            if(!in_array($sequence->trial_id, $trialIds))
            {
                $trialIds[]= $sequence->trial_id;
            }  
        }
        return count($trialIds);
    }
    
    /**
     * The method return the total number of visited trials(pages) for the user
     * If one page si visited multiple times only, it is couted each time
     * @return number of visited trials
     */
    public function getNumberOfVisitedTrials_NoneExcluded()
    {
        $trialIds = [];
        foreach ($this->trial_sequences as $sequence)
        {   
            $trialIds[]= $sequence->trial_id; 
        }
        return count($trialIds);
    }
    
    /**
     * Check if the user pressed F2 for quiting the test
     * @return boolean
     */
    public function hasAbandonedTest()
    {
        foreach ($this->trial_events() as $event)
        {
            if($event->type == "Key" && $event->param =="Key: F2")
                return true;
        }
        return false;
        
    }
    
    /**
     * Return the url path for the user ( all the url ao visited pages)
     * @return array trial_event object(id, trial_sequence_id, time, type, task, param)
     */    
    public function urlPath()
    {
        $urlPath = [];        
        foreach ($this->trial_events as $event)
        {
            if($event->type == "Response" && $event->task == "SlideChange")
            {
                $urlPath = $event;
            }
        }
        return $urlPath;        
    }
    
    /**
     * This method regroup informations from 3 tables : trial event, trial  sequence and trial
     * @return array [event_id] keys:
     * -> sequence_id = the id of the recorded sequence 
     *  -> event_time = when the click event occured
     *  -> sequence_start_time = start_time of the recorded sequence
     *  -> sequence_duration = duration of the recorded sequence
     *  -> trial_name  = page name
     *  -> trial_category = page category
     *  -> trial_image = page image name
     */
    public function urlPathDetailed()
    {
        $trialPath = []; 
        foreach ($this->trial_events as $event)
        {            
            if($event->type == "Response" && $event->task == "SlideChange")
            {  
                $trialDetails = [];                
                $seq = TrialSequence::find($event->trial_sequence_id);      
                $trialDetails['sequence_id'] = $event->trial_sequence_id;
                $trialDetails['event_time'] = $event->time;
                $trialDetails['event_param'] = $event->param;
                $trialDetails['sequence_start_time'] = $seq->start_time;
                $trialDetails['sequence_duration'] = $seq->duration;
                $trialDetails['trial_name'] = $seq->trial->name;

                $trialDetails['trial_category'] = $seq->trial->category;
                $trialDetails ['trial_image'] = $seq->trial->image;               
                $trialPath[$event->id] = $trialDetails;
            }              
        }
        return $trialPath;        
    }



    public function orderedUrlPath()
    {
        $trialPath = []; 
        foreach ($this->trial_events as $event)
        {            
            if($event->type == "Response" && $event->task == "SlideChange")
            {  
                $trialDetails = [];                
                $seq = TrialSequence::find($event->trial_sequence_id);      
                $trialDetails['trial_name'] = $seq->trial->name;
                $trialDetails['trial_id'] = $seq->trial->id;
                $trialPath[] = $trialDetails;
            }              
        }
        return $trialPath;        
    }
}
