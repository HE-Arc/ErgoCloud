<?php
namespace App\Http\Helpers;
use App\Subject;
use Illuminate\Support\Collection;

/**
 * Calibration tools
 *
 */
class CalibrationHelper { 

    public static function sortCalibrations($subjects)
    {  
        $totalCalibs = 0;
        $counter = 0;
        $maxCalibrations = 0;
        $perfectCalibs = new Collection();
        $goodCalibs = new Collection();
        $moderateCalibs = new Collection();
        $poorCalibs = new Collection();
        $disablePerfect = false;
        $disableGood = false;
        $disableModerate = false;
        $disablePoor = false;
        foreach ($subjects as $subject)
        { 
            $lastCalib = $subject->calibrations->last();            
            $totalCalibs += $subject->calibrations->count();
            if($lastCalib != null){
                if(($lastCalib['accuracy'])> 0 && ($lastCalib['accuracy'])<= 0.5)
                {
                    $perfectCalibs->push($lastCalib);        
                    $counter++; 
                    if($subject->eliminate == 1)
                    {
                        $disablePerfect=true;
                    }
                }
                else if(($lastCalib['accuracy'])> 0.5 && ($lastCalib['accuracy'])<= 0.7)
                {
                    $goodCalibs->push($lastCalib) ;                   
                    $counter++;                    
                    if($subject->eliminate == 1)
                    {
                        $disableGood=true;
                    }
                }          
                else if(($lastCalib['accuracy'])> 0.7 && ($lastCalib['accuracy'])<= 1.0)
                {
                    $moderateCalibs->push($lastCalib) ;
                    $counter++;
                    if($subject->eliminate == 1)
                    {
                        $disableModerate=true;
                    }
                }
                else if((($lastCalib['accuracy'])> 1.0 && ($lastCalib['accuracy'])<= 1.5) || ($lastCalib['accuracy'])==0)
                {
                    $poorCalibs->push($lastCalib) ;
                    $counter++;
                    if($subject->eliminate == 1)
                    {
                        $disablePoor=true;
                    }
                }   
            }              
        }
        if($counter != 0)
            $response = array( "perfectCalibs" => $perfectCalibs, 
                               "perfectPr" =>($perfectCalibs->count()/$counter)*100,
                               "perfectDisable" => $disablePerfect,
                               "goodCalibs" => $goodCalibs,
                               "goodPr" => ($goodCalibs->count()/$counter)*100,
                               "goodDisable" => $disableGood,
                               "moderateCalibs" => $moderateCalibs,
                               "moderatePr" => ($moderateCalibs->count()/$counter)*100,
                               "moderateDisable" => $disableModerate,
                               "poorCalibs" => $poorCalibs,
                               "poorPr" => ($poorCalibs->count()/$counter)*100,
                               "poorDisable" =>$disablePoor,
                               "counter" => $counter,
                               "totalC" => $totalCalibs
                             );
        else 
            $response = null;
        return $response;
    }

} 