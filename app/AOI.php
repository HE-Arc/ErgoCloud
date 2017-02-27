<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * Description of AOI - Area of interest *
 * @author claudiag.gheorghe
 */
class AOI  extends Model{
       
    /**
     * The table associated with the model.     
     * @var string
     */
    protected $table = 'aois';
    public $timestamps = false;
    protected $fillable = array ('trial_id', 'name', 'type', 'no_points', 'points', 'aoi_group_id');
    
    
    /**
     * Get the trial on which the Area is drawn 
     * @return Trial
     */
    public function trial()
    {
        return $this->belongsTo('App\Trial');
    }
    
    /**
     * Get the aoi group
     */
    public function aoi_group()
    {
        return $this->hasOne('App\AOI_group');
    }
    
    /**
     * Get fixations on AOI
     * @return array
     */
    public function fixations()
    {
        return $this->belongsToMany('App\Fixation', 'fixation_aoi', 'aoi_id', 'fixation_id');
    }
    
    public function mouseDowns()
    {
        return $this->belongsToMany('App\TrialEvent', 'mousedown_aoi', 'aoi_id', 'mousedown_id');
    }
     
    public function getAOIPoints()
    {
        $aoiPoints =[];
        $points = explode(" ", $this->points);
        $patternX = '/\(.*/';
        $pointsCoord=[];
        foreach ($points as $point)
        {            
            if(!empty($point))
            {                 
                preg_match($patternX, $point, $matches);
                $matches = str_replace("(", "", $matches);
                $matches = str_replace(")", "", $matches);
                $temp= implode("", $matches);
                $temp = explode(";", $temp);
                array_push($pointsCoord, $temp[0]) ;
                array_push($pointsCoord, $temp[1]) ;              
            }
            $aoiPoints[$this->id] = $pointsCoord;
        }
        return $aoiPoints;
    }    
    
    
    public function getFirstFixation()
    {
        var_dump($this->trial->fixation);
    }
}
