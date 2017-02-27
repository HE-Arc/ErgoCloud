<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * Description of Fixation *
 * @author claudiag.gheorghe
 */
class Fixation extends Model {
    
    protected $table = 'fixations';
    public $timestamps = false;
    protected $fillable = array ('trial_sequence_id', 'trial_id', 'count_in_trial', 'start_time', 'length', 'posx', 'posy');
    
    /**
     * Get the trial    
     */
    public function trial()
    {
        return $this->belongsTo('App\Trial');
    }    
    
    /**
     * Get the trial sequence   
     */
    public function trial_sequence()
    {
        return $this->belongsTo('App\TrialSequence');
    }
    
    
    /**
     * Get aois   
     */
    public function aois()
    {
        return $this->belongsToMany('App\AOI', 'fixation_aoi', 'fixation_id', 'aoi_id');
    }
    
}
