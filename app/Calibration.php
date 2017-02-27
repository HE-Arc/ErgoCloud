<?php namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of Calibration*
 * This class gets the calibrations table from dashboard db
 * @author claudiag.gheorghe
 */

class Calibration extends Model
{
    protected $table ='calibrations';
    public $timestamps = false;
    protected $fillable = array('accuracy', 'accuracy_left', 'accuracy_right');
    
    /**
     * Get the subject for a calibration 
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }
    
    
    
    
}
