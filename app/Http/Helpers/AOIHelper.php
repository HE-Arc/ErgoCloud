<?php
namespace App\Http\Helpers;
use Log;
use App\AOI;
use App\TrialSequence;
use App\Fixation;
/**
 * Description of AOIHelper = help methods for aois 
 *
 * @author claudiag.gheorghe
 */
class AOIHelper {  
  
    /**
     * Get an array of TFF ( time until first fixation in aoi) for a given list of aois 
     * @param a list of aois $aoisList
     * @return array with aoiId => time until first fixation (column start_time in table fixation)
     */    
    
    public static function getTFFForAoisList($fixationList)
    {
        $tffList = [];
        foreach ($fixationList as $subjectId => $fixations)
        { 
            if(!empty($fixations))
            {
                $fixationForSubj = [];
                foreach ($fixations as $fixation)
                {
                    $fixationForSubj[] = $fixation['start_time'];                       
                }
                $tffList[$subjectId] = min($fixationForSubj); 
            } 
        }
        return $tffList;
    }    
      
    /**
     * Get an array of TFC ( time until first click in aoi) for a given list of aois 
     * @param a list of aois $aoisList
     * @return array with aoiId => time until first click (column time in table trial_events)
     */    
    public static function getTFCFor($eventsList)
    {
        $tcfList = [];
        foreach ($eventsList as $subjectId => $events)
        {
            if(!empty($events))
            {
                $mouseDown =[];
                foreach ($events as $event)
                {
                    $mouseDown[] = $event['time'];
                }
                $tcfList [$subjectId]= min($mouseDown);
            }
        }
        return $tcfList;
    }
    
    
    public static function getIdList($aoisList)
    {
        $ids =[];
        foreach ($aoisList as $key => $aoi)
        {
            $ids[]=$aoi['id'];
        }
        return $ids;
    }
    
    /**
     * Get un array with the total time of fixations for a list of aois 
     * @param type $aoisList
     * @return array aoiId => total fixation time on aoi
     */
    public static function getTotalTimeFixationForAoisList($aoisList)	
    {	
        $totalTime = [];
        foreach ($aoisList as $aoi)	
        {	
            $time = 0;	
            foreach ($aoi->fixations()->orderBy('start_time')->get() as $fixation)	
            {	
                $time += $fixation->length;	
            } 
            $totalTime[$aoi->id] = $time;	
        }	
        return $totalTime;	
    }
    
    public static function getTotalTimeFixationFor($subjectsFixations)
    {
        $totalTime = [];
        foreach ($subjectsFixations as $subjectId => $fixations)
        { 
            if(!empty($fixations))
            {
                $time = 0;
                foreach ($fixations as $fixation)
                {
                    $time += $fixation->length;                       
                }
                $totalTime[$subjectId] = $time; 
            } 
        }
        return $totalTime;
    }   
    
    /**
     * Count the nombre of clicks for a given aoi list
     * @param type $aoisList
     * @return array aoiId => no of clicks on aoi ( only for the Mouse Down events)
     */
    public static function getMouseDownCountFor($subjectsEvents)
    {
        $countMouseDown = [];
        foreach ($subjectsEvents as $subjectId => $events)
        {
            $mouseDown =0;
            if(!empty($events))
            {
                $mouseDown = count($events);
            }
            $countMouseDown [$subjectId]= $mouseDown;
        }
        return $countMouseDown;
    }
    
    /**
     * Get un array with the difference between TFF and TFC for a given aoi list
     * @param $tffs => time first fixieS
     * @param $tcfs => time first clickS
     * @return array aoiId => TFF - TFC( only for the aois on which we have both a fixation and a click)
     */
    public static function TFF_TCFForAoisList($tffs, $tcfs)
    {
        $dif = [];
        foreach ($tffs as $key => $tff_value)
        {
            if(array_key_exists ($key, $tcfs))
            {
                $dif[$key] = $tcfs[$key] - $tff_value;
            }
        }
        return $dif;
    }


    /**
    *   TODO: FIND WHY RESULT IS DIFFERENT WITH getRelativeTimeforAoiList
    *   param : $subjectsFiltered = array with wanted subjects
    *   param : $aoiIds list of id of AOIs wanted
    *   return : array with key => subjectId, value => compared time on page with AOIs
    */
    public static function getRelativeTimeForAoiList2($subjectsFiltered, $aoiIds)
    {
        $fixationParpage = array();

        foreach ($subjectsFiltered as $subject)
        {
            Log::info("---------------------------------------");
            //Log::info($subject);

            
            $fixations_subject_on_aois = $subject->fixations()
                                    ->whereHas('aois', function($q) use ($aoiIds){
                                        $q->whereIn('id', $aoiIds);
                                    })
                                    ->get();


            Log::info($fixations_subject_on_aois);

            // get trial of subject where has a AOI in list

            $trial_sequence_subject = TrialSequence::whereHas('trial', function($q) use ($aoiIds){
                                            $q->whereHas('aois', function($q2) use ($aoiIds){
                                                $q2->whereIn('id', $aoiIds);
                                            });
                                        })
                                        ->where('subject_id', '=', $subject->id)
                                        ->get();
           /**$trial_sequence_subject = AOI::whereIn('id', $aoiIds)->get()->trial->trial_sequences
                                                            ->where('subject_id', '=', $subject->id)
                                                            ->get();**/

            /**$trial_sequence_subject = $subject->trial_sequences()
                                    ->whereHas('trial', function($q) use ($aoiIds){
                                        $q->whereHas('aois', function($q2) use ($aoiIds){
                                            $q2->whereIn('id', $aoiIds);
                                        });
                                    })
                                    ->get();**/

            Log::info($trial_sequence_subject);

            if(count($fixations_subject_on_aois)>0)
            {
                $duration_page = 0;
                $duration_aoi = 0;
                foreach($fixations_subject_on_aois as $fixation)
                {
                    $duration_aoi += $fixation->length;
                }
                
                foreach($trial_sequence_subject as $trial_sequence)
                {
                    $duration_page += $trial_sequence->duration;
                }

                if($duration_page > 0)
                {
                    $time_relative = ($duration_aoi/$duration_page);
                    $fixationParpage[$subject->id] = $time_relative;
                }

                Log::info('duration_page : '.$duration_page);
                Log::info('duration_aoi : '.$duration_aoi);
            }

           
        }

        return $fixationParpage;
    }
    
    public static function getRelativeTimeforAoiList($aoisList, $subjectsFiltered, $trialsList)
    {        
        $relativeDurations = [];

        $trialsIds = [];
        foreach ($trialsList as $trial)
        {
            $trialsIds[]= $trial["id"];
        }        
        sort($trialsIds);
        
        $durations = TrialHelper::getArrayOfDurationFor(SubjectHelper::getSequencesFor($subjectsFiltered), $trialsIds);
        $sumDuration = [];
        foreach ($durations as $key=>$trialDuration)
        {
            $sumDuration[$key] = array_sum($trialDuration);
        }            
        $fixationDuration = self::getTotalTimeFixationForAoisList($aoisList);
        foreach ($aoisList as $aoi)
        {
            if(array_key_exists($aoi->trial_id, $sumDuration))
            {
                $relativeDurations[$aoi->id] = $fixationDuration[$aoi->id]/$sumDuration[$aoi->trial_id];
            }
        }        
        return $relativeDurations;
    }

            
            
}
