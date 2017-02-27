<?php
 
namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of TrialEvent
 *
 * @author claudiag.gheorghe
 */

class TrialEvent extends Model
{
    /**
     * The table associated with the model.     
     * @var string
     */
    protected $table = 'trial_events';
    public $timestamps = false;
    protected $fillable = array('trial_sequence_id', 'time', 'type', 'task', 'param');
    
    public function trialSequence()
    {
        return $this->belongsTo('App\TrialSequence');
    }
    
    public function aois()
    {
        return $this->belongsToMany('App\AOI', 'mousedown_aoi', 'mousedown_id', 'aoi_id');
    }
    
    
}
