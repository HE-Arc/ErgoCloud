<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trial extends Model
{
     /**
     * The table associated with the model.     
     * @var string
     */
    protected $table = 'trials';
    public $timestamps = false;
    protected $fillable = array('test_id', 'name', 'category');
    
    /**
     * Get the parent test for a subject     
     */
    public function test()
    {
        return $this->belongsTo('App\Test');
    }
     /**
     * Get the sequences for the trial     
     */
    public function trial_sequences()
    {
        return $this->hasMany('App\TrialSequence');
    }
     /**
     * Get the trails for the subject     
     */
    public function subjects()
    {
        return $this->belongsToMany('App\Subject', 'trial_sequences',
                        'trial_id', 'subject_id');
    }
    
    /**
     * Get the aois drawn on this trial     
     */
    public function aois()
    {
        return $this->hasMany('App\AOI');
    }
    
    /**
     * Get the fixations on this trial     
     */
    public function fixations()
    {
        return $this->hasMany('App\Fixation');
    }

    /**
    * Return path of the trial image
    */
    public function getImagePath()
    {
        if($this->image != '' || $this->image != NULL)
        {
            return asset('/uploads/images/'.$this->image);
        }
        else
        {
            return '';
        }
    }
}
