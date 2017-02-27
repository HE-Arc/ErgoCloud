<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of TrialSequence
 * this table has the sequence of an recorded test (the path of each visited page) 
 *
 * @author claudiag.gheorghe
 */
class TrialSequence extends Model {
    
    /**
     * The table associated with the model.     
     * @var string
     */
    protected $table = 'trial_sequences';
    public $timestamps = false;
    protected $fillable = array('subject_id', 'sequence', 'trial_id', 'start_time', 'duration');
    
    /**
     * Get the subject
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }
        
    /**
    * Get trial
    */
    public function trial()
    {
        return $this->belongsTo('App\Trial');
    }
    
    /**
    * Get events on the sequence
    */
    public function trial_events()
    {
        return $this->hasMany('App\TrialEvent');
    }
    
    public function fixations ()
    {
        return $this->hasMany('App\Fixation');
    }
    
    
}
