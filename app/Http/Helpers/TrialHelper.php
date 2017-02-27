<?php
namespace App\Http\Helpers;
use App\Trial;
use App\TrialSequence;
use App\TrialEvent;


/**
 * Description of TrialHelper = helper methods to filter the trials *
 * @author claudiag.gheorghe
 */
class TrialHelper {
    
    /**
     * This method return un array with the durations (the time each subject passed on the page)
     * @param Trials[] $trials = list of trials ids
     * @param $sequecesFilterdParSubject = list of sequences filtred by selected user
     * @return array of duration par trial
     */
    public static function getArrayOfDurationFor($sequecesFilteredParSubject, $trialsIds)
    { 
        $durations = []; 
        $sequences = self::getArraySequencesFor($sequecesFilteredParSubject, $trialsIds);         
        foreach ($sequences as $key => $seqs)
        {                       
            $durationParTrial = [];
            foreach ($seqs as $seq)
            {                
                array_push($durationParTrial, $seq['duration']);
            }
            $durations[$key] = $durationParTrial;            
        }
        return $durations;  
    }    
    
    /**
     * This method return un array with the number of clicks (NOC)
     * @param Trials[] $trials
     * @param $sequecesFilterdParSubject = list of sequences filtred by selected user
     * @return array of number of click
     */
    public static function getArrayOfNOClicksFor($sequecesFilterdParSubject, $trialsIds)
    {
        foreach (self::getEventsFor($sequecesFilterdParSubject, $trialsIds) as $key =>$events)
        {
            $mouseEvents =[]; 
            foreach($events as $event)
            {
                if($event['type']== 'Mouse' )
                {      
                    array_push($mouseEvents, $event);
                }
            } 
            $clicks[$key] = (count($mouseEvents)); 
            
        }
        return $clicks;
    }
    
    
    /**
     * This method return un array with the number of scroll (NOS)
     * @param Trials[] $trials
     * @param $sequecesFilterdParSubject = list of sequences filtred by selected user
     * @return array of number of scroll
     */
    public static function getArrayOfNOScrollFor($sequecesFilterdParSubject, $trialsIds)
    {
        foreach (self::getEventsFor($sequecesFilterdParSubject, $trialsIds) as $key =>$events)
        {
            $scrollEvents =[]; 
            foreach($events as $event)
            {
                if($event['type']== 'Scroll' )
                {      
                    array_push($scrollEvents, $event);
                }
            } 
            $scrolls[$key] = (count($scrollEvents));             
        }        
        return $scrolls;       
    }    
    
    
    /**
     * This method return the events occured during a test for selected subjects and trials
     * @param TrialSequence $sequecesFilterdParSubject = list of sequences filtred by selected user
     * @param Trial $trialsIds
     * @return array
     */
    public static function getEventsFor($sequecesFilterdParSubject, $trialsIds)
    {
        $events = [];
        foreach (self::getArraySequencesFor($sequecesFilterdParSubject, $trialsIds) as $key => $seqs)
        { 
            $eventsParTrial =[];
            foreach ($seqs as $seq)
            { 
                foreach (TrialEvent::where('trial_sequence_id', $seq['id'])->get() as $event)
                {
                    array_push($eventsParTrial, $event->toArray());
                }                
            } 
            $events[$key] = $eventsParTrial;
        }        
        return $events;
    }
    
    /**
     * This method return the registred sequence occured during a test for selected subjects and trials
     * @param TrialSequence $sequecesFilterdParSubject = list of sequences filtred by selected user     
     * @param Trial $trialsIds
     * @return array
     */
    public static function getArraySequencesFor($sequecesFilterdParSubject, $trialsIds)
    {
        $filterdSequences = [];       
        foreach ($trialsIds as $trial)
        {
            $sequenceParTrial = [];
            foreach ($sequecesFilterdParSubject as $sequence)
            {
               if($sequence->trial_id == $trial){
                   array_push($sequenceParTrial, $sequence->toArray());
               }
            }
            $filterdSequences[$trial] = $sequenceParTrial;
        }
        return $filterdSequences;         
    }
    
    /**
     * Get Aois for the trials liste
     * @param Trial $trials 
     * @return liste with all the aois
     */
    public static function getAOISFor($trials)
    {
        $allAois = [];
        $trials = Trial::hydrate($trials);
        foreach ($trials as $trial){
            $aois = $trial->aois;
            foreach ($aois as $aoi)
            {               
                $allAois[] = $aoi;
            }           
        }
        return $allAois;        
    }
    
    public static function getFixationsFor($trials)
    {
        
        
    }
   
}
