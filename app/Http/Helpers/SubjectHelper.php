<?php 
namespace App\Http\Helpers;

use DB;


/**
 * Description of SubjectHelper = helper methods to filter the subjects
 *
 * @author claudiag.gheorghe
 */
class SubjectHelper {      
    
    
    /**
     * This method  select the subjects according to filters
     * @param type $subjects = array of subjects
     * @param type $subjectSex = male/female
     * @param type $subjectAge = all/18-30/31-40
     * @param type $subjectGlasses = with/without
     * @param type $subjectLanguage = array (all/French/English/German/Italian)
     * @return type
     */
    public static function filterSubjects($subjects, $subjectSex = null, $subjectAge = null, $subjectGlasses = null, $subjectLanguage =null )
    {
        $query = DB::table('subjects')->whereIn('id', array_values($subjects));         
        switch ($subjectSex) {
            case "male":
                $query->where('sex', 'like', 'homme' );
                break;
            case "female":
                $query->where('sex', 'like', 'femme' );
                break;                
        }
        
        switch($subjectAge){
            case "30":
                $query->whereBetween ('age', [18, 30]);
                break;
            case "40":
                $query->whereBetween ('age', [31, 40]);
                $break;
        }
        
        switch($subjectGlasses){
            case "with":
                $query->where('glasses', 'like', 'oui' );
                break;
            case "without":
                $query->where('glasses', 'like', 'no' );
                break;
        }
        if($subjectLanguage[0] != 'all')
        {
            $query->whereIn('language', $subjectLanguage);             
        } 
        
        $subjectsFiltered = $query->get();        
        return $subjectsFiltered;
    }
    
   
    
    /**
     * This method check if the total time spend by the subject to do the test is in the given interval
     * @param type $subject = the subjet to test
     * @param type $minValue 
     * @param type $maxValue
     * @return boolean
     */
    public static function isTestDurationBetween($subject, $minValue, $maxValue)
    {
        if( $minValue <= $subject->getTestDuration() && $subject->getTestDuration() <=$maxValue)
            return true;
        else 
            return false;
    }
    /**
     * This method return un array with the time spend by each subject to do the test
     * @param type $subjects
     * @return array of durations for the given $subjects
     */    
    public static function getTestDurationsArrayFor($subjects)
    {
        $duration = [];
        foreach ($subjects as $subject)
        {
            $duration[$subject->id] = $subject->getTestDuration();                              
        }
        return $duration;
    }
    
     /**
     * This method check if nombre of visited trials by the subject is in the given interval
     * @param type $subject = the subjet to test
     * @param type $minValue 
     * @param type $maxValue
     * @return boolean
     */
    public static function isNoOfVisitedTrialsBetween($subject, $minValue, $maxValue)
    {
        if( $minValue <= $subject->getNumberOfVisitedTrials() && $subject->getNumberOfVisitedTrials() <=$maxValue)
            return true;
        else 
            return false;
    }
    
    /**
     * This method return un array with the number of visited pages by each subject during the test
     * @param type $subjects
     * @return array with the total number of visited pages for the given subjects
     */
    public static function getNumberOfVisitedTrialsFor($subjects)
    {
        $visitedTrialsParSubject =[];
        foreach ($subjects as $subject)
        {
            $visitedTrialsParSubject[$subject->id] = $subject->getNumberOfVisitedTrials() ;            
        }
        return $visitedTrialsParSubject;
    }
    
    /**
     * This method return un array with total test time / the number of visited pages by each subject during the test
     * @param type $subjects
     * @return array 
     */
    public static function getTotalTimeDividedByVisitedPage($subjects)
    {
        $averageParSubject=[]; 
        $durations = self::getTestDurationsArrayFor($subjects);
        $visitedTrials = self::getNumberOfVisitedTrialsFor($subjects);
        foreach ($subjects as $subject)
        {
            $averageParSubject[$subject->id] = $durations[$subject->id] / (!empty($visitedTrials) ? $visitedTrials[$subject->id] : 1);
        }
        return $averageParSubject;        
    }
    
    /**
     * This method return un array with all the trials (pages) for a given array of subjects
     * @param type $subjects
     * @return trials array 
     */
    public static function getTrialsFor($subjects)
    {
        $allTrials = [];       
        foreach ($subjects as $subject)
        {   //$allTrials[$subject->id] = $subject->trials->toArray();                  
            foreach ($subject->trials as $trial)
            {
                $trial = $trial->toArray();                
                array_push($allTrials, $trial);   
            }     
        }        
        return $allTrials;
    }  
    
    /**
     * Get the test sequences (=the order in which the test was register) for a given array of subjects
     * @param type $subjects
     * @return sequences array
     */
    public static function getSequencesFor($subjects)
    {
        $allSequences = [];
        foreach ($subjects as $subject)
        {
            foreach($subject->trial_sequences as $sequence)
            {
                array_push($allSequences, $sequence);
            }
        }
        return $allSequences;           
    }
    
    /**
     * Get the subjects who abandoned the test (pressed F2)
     * @param type $subjects
     * @return type
     */    
    public static function getSubjectsAbandons($subjects)
    {
        $subjectsIds=[];
        foreach ($subjects as $subject)
        {
            if($subject->hasAbandonedTest())
                $subjectsIds[] = $subject->id;
        }
        return $subjectsIds;
    }
    
    public static function getFixationsFor($subjects)
    {
        $allFixations = [];
        foreach ($subjects as $subject)
        {
            $allFixations[$subject->id] = $subject->fixations;
        }
        return $allFixations;
    }
    
    public static function getFixations($subjectsFixations, $aoisIds)
    {
        $fixationList=[];
        foreach ($subjectsFixations as $subjectId =>$fixations)
        {
            $fixationParSubject = [];
            foreach ($fixations as $key =>$fixation)
            {                
                if($fixation->aois->count())
                    $fixationParSubject[] = $fixation;           
            }
            $fixationList[$subjectId] = $fixationParSubject;
        }    
        return $fixationList;        
    }
    
    
    public static function getEventsFor($subjects)
    {
        $allEvents=[];
        foreach ($subjects as $subject)
        {
             $allEvents[$subject->id] = $subject->trial_events;
        }
        return $allEvents;
    }
    
    public static function getEvents($subjectsEvents, $aoisIds)
    {
        $eventsList = [];
        foreach ($subjectsEvents as $subjectId =>$events)
        {
            $eventsParSubject = [];
            foreach ($events as $key =>$event)
            {
                if($event->aois->count())
                   $eventsParSubject[] = $event;         
            }
            $eventsList[$subjectId] = $eventsParSubject;
        } 
               
        return $eventsList;
    }
    
}
