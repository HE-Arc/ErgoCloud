<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Test;
use App\Subject;
use App\AOI;
use App\Fixation;
use App\Trial;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Helpers\SubjectHelper;
use App\Http\Helpers\TrialHelper;
use App\Http\Helpers\AOIHelper;
use Session;
use Illuminate\Support\Facades\URL;

use Log;


/**
 * Description of FilterController *
 * @author claudiag.gheorghe
 */
class FilterController extends Controller 
{
    public function index(Request $request, $id)
    {
        $test = Test::find($id);
        $subjects = $test->subjects()->where('eliminate', 0)->get();    
        $trials = SubjectHelper::getTrialsFor($subjects);


        $request->session()->put('subjectsFiltered', $subjects); //reset old filters


        return view('test.filter') 
                 ->with('subjects', $subjects)
                 ->with('id', $id)
                 ->with('trials', $test->trials)
                 ->with('test', $test)
                 ->with('page_status', 1)
                 ->with('breadcrumps', array(
                    'liste' => URL::route('home'), 
                    $test->name => URL::route('show_test', array('test_id' => $test->id)), 
                    'filtre' => URL::route('filter', array('id' => $test->id))
                 ));
        
    }
    
    public function getSubjectDetails(Request $request)
    {        
        if($request->ajax())
        {             
            $requestData = $request->all();
            $subject = Subject::find($requestData['subjectId'])->toArray(); 
            return response()->json(['success'=>"success", 
                                     'sex'=>$subject[0]['sex'],
                                     'age'=>$subject[0]['age'],
                                     'job'=>$subject[0]['comments'],
                                     'glasses'=>$subject[0]['glasses'],
                                     'language'=>$subject[0]['language']]);
            
        }        
    }
    
    
    
    public function testFilters(Request $request)
    {        
        if($request->ajax())
        {
            $filters = $request->all();
            if(!empty($filters['subjects']))
            {
                $subjectsFiltered = SubjectHelper::filterSubjects($filters['subjects'], $filters['sex'], $filters['age'], $filters['glasses'], $filters['language']);  
                $subjectsFiltered = Subject::hydrate($subjectsFiltered);
                $request->session()->put('subjects', $subjectsFiltered);
                
                $durations = SubjectHelper::getTestDurationsArrayFor($subjectsFiltered);              
                $visitedTrials = SubjectHelper::getNumberOfVisitedTrialsFor($subjectsFiltered); 
                $urlPath =[];
                $averageParSubject=[];             
                foreach ($subjectsFiltered as $subject)
                {
                    $averageParSubject[$subject->id] = $durations[$subject->id] / (!empty($visitedTrials) ? $visitedTrials[$subject->id] : 1);
                    $urlPath[$subject->id] = $subject->urlPathDetailed();
                    $test_id = $subject->test_id;
                }
                $abandons = SubjectHelper::getSubjectsAbandons($subjectsFiltered);   
                
            }   
            return response()->json(['success'=>"true",                                      
                                     'duration'=>isset($durations) ? $durations : 0, 
                                     'max_duration'=>!empty($durations) ? max($durations)/1000 : 0,
                                     'min_duration'=>!empty($durations) ? min($durations)/1000 : 0,
                                     'average_duration'=> !empty($durations) ? ceil(array_sum($durations)/( count($durations)))/1000 : 1,
                                     'visited'=>isset($visitedTrials) ? $visitedTrials : 0,
                                     'min_visited'=>!empty($visitedTrials) ? min($visitedTrials) :0,
                                     'max_visited'=>!empty($visitedTrials) ? max($visitedTrials): 0,
                                     'average_visited'=> !empty($visitedTrials) ? round((array_sum($visitedTrials))/ count($visitedTrials)) : 1 , 
                                     'avr_perPage'=>isset($averageParSubject) ? $averageParSubject : 0,
                                     'min_avrPerPage' => !empty($averageParSubject) ? round(min($averageParSubject)/1000, 3) :0,
                                     'max_avrPerPage'=>!empty($averageParSubject) ? round(max($averageParSubject)/1000, 3): 0,
                                     'average_perPage'=> !empty($averageParSubject) ?round((array_sum($averageParSubject) / count($averageParSubject) )/1000, 3): 0,
                                     'abandons' => !empty($abandons) ? count($abandons) : 0, 
                                    
                ]);
            
        }
    }  
    
    
    public function getTrials(Request $request)
    {   
        if($request->ajax())
        {
            $filters = $request->all();
            $changeTrials = false;
            if(!empty($filters['subjects']))
            {
                $subjectsFiltered = Subject::hydrate(SubjectHelper::filterSubjects($filters['subjects'], $filters['sex'], $filters['age'], $filters['glasses'], $filters['language'])); 
                $request->session()->put('subjects', $subjectsFiltered);
                $trials = SubjectHelper::getTrialsFor($subjectsFiltered);
                $oneTimeTrialList = [];           
                foreach($trials as $trial)
                {
                    if(!in_array($trial['id'], $oneTimeTrialList))
                    {
                        $oneTimeTrialList[$trial['id']]=$trial['name'];                  
                    }
                }
                $changeTrials = true;
                $trialsIds = array_keys($oneTimeTrialList);
                sort($trialsIds);
                $durations = TrialHelper::getArrayOfDurationFor(SubjectHelper::getSequencesFor($subjectsFiltered), $trialsIds );
                $sumDuration = [];
                foreach ($durations as $key=>$trialDuration)
                {
                    $sumDuration[$key] = array_sum($trialDuration);
                }            
//                $min = min($sumDuration);
//                $max = max($sumDuration);
                $min= min( array_map("min", $durations) );
                $max= max( array_map("max", $durations) );                
                $clicks = TrialHelper::getArrayOfNOClicksFor(SubjectHelper::getSequencesFor($subjectsFiltered), $trialsIds);
                $scrolls = TrialHelper::getArrayOfNOScrollFor(SubjectHelper::getSequencesFor($subjectsFiltered), $trialsIds);
            } 
            elseif(!empty($filters['trials']))
            {
                $trialsIds = $filters['trials'];
                $oneTimeTrialList = [];
                foreach ($trialsIds as $key => $value)
                {
                    $trial = Trial::find($value);
                    $oneTimeTrialList[$value] = $trial['name'];
                } 
                $changeTrials = false;
                sort($trialsIds);
                $durations = TrialHelper::getArrayOfDurationFor(SubjectHelper::getSequencesFor($request->session()->get('subjects')), $trialsIds );
                $sumDuration = [];
                foreach ($durations as $key=>$trialDuration)
                {
                    $sumDuration[$key] = array_sum($trialDuration);
                }            
//                $min = min($sumDuration);
//                $max = max($sumDuration);     
                $min= min( array_map("min", $durations) );
                $max= max( array_map("max", $durations) );       
                $clicks = TrialHelper::getArrayOfNOClicksFor(SubjectHelper::getSequencesFor($request->session()->get('subjects')), $trialsIds);
                $scrolls = TrialHelper::getArrayOfNOScrollFor(SubjectHelper::getSequencesFor($request->session()->get('subjects')), $trialsIds);

            }
            return response()->json(['success'=> "success", 
                            'change_trials' => $changeTrials,
                            'trials' => isset($oneTimeTrialList) ? $oneTimeTrialList : null,    
                            'all' => $trialsIds,
                            'min' => isset($min) ? $min/1000 : 0,
                            'max' => isset($max) ? $max/1000 : 0,
                            'average_duration'=>!empty($sumDuration) ? ceil((array_sum($sumDuration)/count($sumDuration))/1000) : 1,                           
                            'min_c' => isset($clicks) ? min($clicks): 0,
                            'max_c' => isset($clicks) ? max($clicks): 0,
                            'average_clicks'=>!empty($clicks) ? ceil((array_sum($clicks))/(( count($clicks)))) : 0,                            
                            'min_s' => isset($scrolls) ? min($scrolls) : 0,
                            'max_s' => isset($scrolls) ? max($scrolls) : 0,
                            'average_scrolls'=> !empty($scrolls) ? ceil((array_sum($scrolls)/ count($scrolls) )) : 1,                            
                            'duration' => $durations,
                            'clicks' => $clicks,
                            
            ]);
        }
        
    }
    
    public function getAois(Request $request)
    {
        if($request->ajax())
        {
            $filters = $request->all();                     
            $subjectsFiltered = Subject::hydrate(SubjectHelper::filterSubjects($filters['subjects'], $filters['sex'], $filters['age'], $filters['glasses'], $filters['language'])); 
            $request->session()->put('subjects', $subjectsFiltered);
            $trials = SubjectHelper::getTrialsFor($subjectsFiltered);
            $oneTimeTrialList = [];           
            foreach($trials as $trial)
            {
                if(!\App\Http\Helpers\CommonHelper::in_multiarray($trial['id'], $oneTimeTrialList, 'id'))
                {
                    $oneTimeTrialList[]=$trial;                  
                }
            }        
            $aois = TrialHelper::getAOISFor($oneTimeTrialList);
            $aoiIds = AOIHelper::getIdList($aois);
            $subjectsFixations = SubjectHelper::getFixations(SubjectHelper::getFixationsFor($subjectsFiltered), $aoiIds);    
            $tff = AOIHelper::getTFFForAoisList($subjectsFixations);
            
            $subjectsEvents = SubjectHelper::getEvents(SubjectHelper::getEventsFor($subjectsFiltered), $aoiIds);            
            $tfc = AOIHelper::getTFCFor($subjectsEvents);
            
            $diff_f_c = AOIHelper::TFF_TCFForAoisList($tff, $tfc);
            $time_fixations =  AOIHelper::getTotalTimeFixationFor($subjectsFixations);
            $no_clicks = AOIHelper::getMouseDownCountFor($subjectsEvents);
            
            //$fixationParpage = AOIHelper::getRelativeTimeforAoiList($aois, $subjectsFiltered, $oneTimeTrialList);
            $fixationParpage = AOIHelper::getRelativeTimeForAoiList2($subjectsFiltered, $aoiIds);

            return response()->json(['success'=> "success",
                            'aois' => $aois,
                            'min_tff' => !empty($tff) ? min($tff)/1000 :0,
                            'max_tff' => !empty($tff)? max($tff)/1000 :0,
                            'avg_tff' => ceil((array_sum($tff))/((!empty($tff) ? count($tff) : 1)))/1000, 
                            'min_tfc' => !empty($tfc)? min($tfc)/1000 : 0,
                            'max_tfc' => !empty($tfc) ? max($tfc)/1000 : 0,
                            'avg_tfc' => ceil((array_sum($tfc))/((!empty($tfc) ? count($tfc) : 1)))/1000, 
                            'min_diff' => (!empty($diff_f_c)) ? min($diff_f_c)/1000 : 0,
                            'max_diff' => (!empty($diff_f_c)) ? max($diff_f_c)/1000 : 0,
                            'avg_diff' => ceil((array_sum($diff_f_c))/((!empty($diff_f_c) ? count($diff_f_c) : 1)))/1000, 
                            'min_time' => !empty($time_fixations) ? min($time_fixations)/1000 : 0,
                            'max_time' => !empty($time_fixations) ? max($time_fixations)/1000 : 0, 
                            'avg_time' => ceil((array_sum($time_fixations))/((!empty($time_fixations) ? count($time_fixations) : 1)))/1000,
                            'min_clicks' => (!empty($no_clicks)) ? min($no_clicks): 0,
                            'max_clicks' => (!empty($no_clicks)) ? max($no_clicks): 0,  
                            'avg_clicks' => ceil((array_sum($no_clicks))/((!empty($no_clicks) ? count($no_clicks) : 1))),
                            'min_relative' => !empty($fixationParpage)? round(min($fixationParpage)*100, 3) : 0,
                            'max_relative' => !empty($fixationParpage) ? round(max($fixationParpage)*100, 3) : 0,
                            'avg_relative' => (array_sum($fixationParpage) /(!empty($fixationParpage) ? count($fixationParpage) : 1))*100,
               ]);
        }
    }
    
    public function getSubjectsAccordingToFilters($test_id, Request $request)
    {
        $test = Test::find($test_id);

        if($request->ajax())
        {
            //$request->session()->forget('subjectsFiltered');
            $filters = $request->all();
            if(!empty($filters['subjectsF']['subjects']))
            {
                $subjectsFiltered = Subject::hydrate(SubjectHelper::filterSubjects($filters['subjectsF']['subjects'], $filters['subjectsF']['sex'], $filters['subjectsF']['age'], $filters['subjectsF']['glasses'], $filters['subjectsF']['language']));
            }                
            else
            {
                $subjectsFiltered = null; 
            }
              
            $subjectToEliminate = [];
           

            // Test filters
            if(isset($filters['testFilters']))
            {
                if(isset($filters['testFilters']['duration']))
                {
                    $durations = SubjectHelper::getTestDurationsArrayFor($subjectsFiltered);
                    foreach ($durations as $key => $value)
                    {
                        if(!SubjectHelper::isTestDurationBetween(Subject::find($key), $filters['testFilters']['duration']['min']*1000, $filters['testFilters']['duration']['max']*1000))
                        {
                            $subjectToEliminate[] = $key;
                        }
                    }                    
                }
                if(isset($filters['testFilters']['visited']))
                {
                    $visitedTrials = SubjectHelper::getNumberOfVisitedTrialsFor($subjectsFiltered);
                    foreach ($visitedTrials as $key=>$value)
                    {
                        if(!SubjectHelper::isNoOfVisitedTrialsBetween(Subject::find($key), $filters['testFilters']['visited']['min'], $filters['testFilters']['visited']['max']))
                        {
                            if(!in_array($key, $subjectToEliminate))
                                $subjectToEliminate[] = $key;
                        }                        
                    }                    
                } 
                if(isset($filters['testFilters']['perpage']))
                {
                    $timeParPage = SubjectHelper::getTotalTimeDividedByVisitedPage($subjectsFiltered);
                    foreach ($timeParPage as $key => $value)
                    { 
                        if(!($filters['testFilters']['perpage']['min']*1000 <= $value && $value <= $filters['testFilters']['perpage']['max']*1000))
                        {
                            if(!in_array($key, $subjectToEliminate))
                                $subjectToEliminate[] = $key;
                        }
                    }           
                }
                if(isset($filters['testFilters']['abandon']))
                {
                    $abandons = SubjectHelper::getSubjectsAbandons($subjectsFiltered);
                    foreach ($abandons as $key => $value)
                    {
                        if(!in_array($value, $subjectToEliminate))
                                $subjectToEliminate[] = $key;
                    }                    
                }
                if(isset($filters['testFilters']['paths']))
                {
                    // We need to find every subject that do not (at least) one of these paths (trial sequences)
                    $subjects = $test->subjects;

                    $pathsWanted = $filters['testFilters']['paths'];
                    array_shift($pathsWanted); //Work around
                    //Log::info($pathsWanted);

                    foreach($subjects as $subject)
                    {
                        if(!$this->did_subject_do_one_path($subject, $pathsWanted)){
                            $subjectToEliminate[] = $subject->id;
                        }
                    }      
                }         
            }



            // Trial filters
            if(isset($filters['trialFilters']))
            {
                $trialsIds = $filters['trialFilters']['trials'];
                if (isset($trialsIds) && is_array($trialsIds))
                {
                    sort($trialsIds);
                }

                //Mode params
                if(isset($filters['trialFilters']['trial_novisited']))
                {   
                    // all passed trial must be not visited !
                    if($filters['trialFilters']['trial_novisited'] == "true")
                    {
                        //$trials = Trial::with('subjects')->get()->find($ids);
                        $trials_hidden = Trial::whereIn('id', $trialsIds)->with('subjects')->get();
                        
                        foreach ($trials_hidden as $trial)
                        {   
                            foreach($trial->subjects as $subject)
                            {
                                if(!in_array($subject->id, $subjectToEliminate))
                                {
                                    $subjectToEliminate[] = $subject->id;
                                }
                            }
                        }
                    }
                }

                    
                if(isset($filters['trialFilters']['trialduration'])) // Trial time
                {                   
                    $subjectsSequences = SubjectHelper::getSequencesFor($request->session()->get('subjects'));
                    $durationsTrials = TrialHelper::getArrayOfDurationFor($subjectsSequences, $trialsIds );                    
                    foreach ($durationsTrials as $key => $trialDuration)
                    {
                        foreach ($trialDuration as $time)
                        {
                            if(
                                !($filters['trialFilters']['trialduration']['min']*1000 <= $time && 
                                $time <= $filters['trialFilters']['trialduration']['max']*1000))
                            {                  
                                $trialsToEliminate[$key] = $time;
                            }
                        }                       
                    } 
                    foreach ($trialsToEliminate as $key => $time)
                    {
                        foreach ($subjectsSequences as $seq)
                        {
                            if($seq['trial_id'] == $key && $seq['duration'] == $time && !(in_array($seq['subject_id'], $subjectToEliminate)) ) 
                            {
                                $subjectToEliminate[] = $seq['subject_id'];
                            }                   
                        }
                    }         
                }                
                if(isset($filters['trialFilters']['noc']))
                {
                    $subjectsSequences = SubjectHelper::getSequencesFor($request->session()->get('subjects')); 
                    $clicks = TrialHelper::getArrayOfNOClicksFor($subjectsSequences, $trialsIds);                    
                    foreach ($clicks as $key => $noClick)
                    {
                        if(!($filters['trialFilters']['noc']['min'] <= $noClick && $noClick<= $filters['trialFilters']['noc']['max']))
                        {
                            $trialsToElim[] = $key;
                        }
                    }                   
                    foreach ($trialsToElim as $key =>$trial_id)
                    {
                        foreach ($subjectsSequences as $seq)
                        {
                            if($seq['trial_id']== $trial_id  && !(in_array($seq['subject_id'], $subjectToEliminate)))
                                $subjectToEliminate[] = $seq['subject_id'];
                        }
                    }                    
                } 
                if(isset($filters['trialFilters']['nos']))
                {
                    $subjectsSequences = SubjectHelper::getSequencesFor($request->session()->get('subjects')); 
                    $scrolls = TrialHelper::getArrayOfNOScrollFor(SubjectHelper::getSequencesFor($request->session()->get('subjects')), $trialsIds);
                    foreach ($scrolls as $key=>$scroll)
                    {
                        if(!($filters['trialFilters']['nos']['min'] <= $scroll && $scroll<= $filters['trialFilters']['nos']['max']))
                        {
                            $trialsToElim[] = $key;
                        }                        
                    }
                    foreach ($trialsToElim as $key =>$trial_id)
                    {
                        foreach ($subjectsSequences as $seq)
                        {
                            if($seq['trial_id']== $trial_id && !(in_array($seq['subject_id'], $subjectToEliminate)))
                                $subjectToEliminate[] = $seq['subject_id'];
                        }
                    }                    
                }      
            }


            // AOI Filters
            if(isset($filters['aoiFilters']))
            {
                $aois = [];
                if(isset($filters['aoiFilters']['aois']))
                {
                    $aois = $filters['aoiFilters']['aois'];
                }

                //Mode params
                if(isset($filters['aoiFilters']['aoi_novisited']))
                {   
                    // all passed aois must be not visited !
                    if($filters['aoiFilters']['aoi_novisited'] == "true")
                    {
                        //get all subject that they visit the aoi
                        $subjects_aois = Subject::whereHas('fixations', function($q) use ($aois){
                                            $q->whereHas('aois', function($q2) use ($aois){
                                                $q2->whereIn('id', $aois);
                                            });
                                        })->get();

                        foreach($subjects_aois as $subject)
                        {
                            if(!in_array($subject->id, $subjectToEliminate))
                            {
                                $subjectToEliminate[] = $subject->id;
                            }
                        }
                    }
                }


                if(isset($filters['aoiFilters']['tff']))
                {
                    $subjectsFixations = SubjectHelper::getFixations(SubjectHelper::getFixationsFor($subjectsFiltered), $aois);               
                    $tffs = AOIHelper::getTFFForAoisList($subjectsFixations);
                    foreach($tffs as $subjectId => $tff)
                    {
                        if(!($filters['aoiFilters']['tff']['min']*1000 <= $tff && $tff <= $filters['aoiFilters']['tff']['max']*1000))
                        {    
                            if(!in_array($subjectId, $subjectToEliminate))
                                $subjectToEliminate[] = $subjectId;
                        }
                    }                   
                }
                if(isset($filters['aoiFilters']['tfc']))
                {
                    $subjectsEvents = SubjectHelper::getEvents(SubjectHelper::getEventsFor($subjectsFiltered), $aois);            
                    $tfcs = AOIHelper::getTFCFor($subjectsEvents);
                    foreach($tfcs as $subjectId => $tfc)
                    {
                        if(!($filters['aoiFilters']['tfc']['min']*1000 <= $tfc && $tfc <= $filters['aoiFilters']['tfc']['max']*1000))
                        {    
                            if(!in_array($subjectId, $subjectToEliminate))
                                $subjectToEliminate[] = $subjectId;
                        }                        
                    }                    
                } 
                if(isset($filters['aoiFilters']['tff-tfc']))
                {
                    $subjectsFixations = SubjectHelper::getFixations(SubjectHelper::getFixationsFor($subjectsFiltered), $aois);               
                    $tffs = AOIHelper::getTFFForAoisList($subjectsFixations);
                    $subjectsEvents = SubjectHelper::getEvents(SubjectHelper::getEventsFor($subjectsFiltered), $aois);            
                    $tfcs = AOIHelper::getTFCFor($subjectsEvents);
                    $diff_f_c = AOIHelper::TFF_TCFForAoisList($tffs,$tfcs );
                    foreach($diff_f_c as $subjectId => $dif)
                    {
                        if(!($filters['aoiFilters']['tff-tfc']['min']*1000 <= $dif && $dif <= $filters['aoiFilters']['tff-tfc']['max']*1000))
                        {    
                            if(!in_array($subjectId, $subjectToEliminate))
                                $subjectToEliminate[] = $subjectId;
                        }                        
                    }         
                }
                if(isset($filters['aoiFilters']['time_f']))
                {
                    $subjectsFixations = SubjectHelper::getFixations(SubjectHelper::getFixationsFor($subjectsFiltered), $aois);  
                    $time_fixations =  AOIHelper::getTotalTimeFixationFor($subjectsFixations);
                    foreach($time_fixations as $subjectId => $time)
                    {
                        if(!($filters['aoiFilters']['time_f']['min']*1000 <= $time && $time <= $filters['aoiFilters']['time_f']['max']*1000))
                        {    
                            if(!in_array($subjectId, $subjectToEliminate))
                                $subjectToEliminate[] = $subjectId;
                        }                        
                    }               
                }
                if(isset($filters['aoiFilters']['time_relative']))
                {
                    //TODO !
                    foreach ($subjectsFiltered as $subject)
                    {
                        $aoiOK = false;

                        //getTotalTimeFixationFor maybe util

                        $fixations_subject_on_aois = $subject->fixations()
                                                ->whereHas('aois', function($q) use ($filters){
                                                    $q->whereIn('id', $filters['aoiFilters']['aois']);
                                                })
                                                ->get();

                        // get trial of subject where has a AOI in list
                        $trial_sequence_subject = $subject->trial_sequences()
                                                ->whereHas('trial', function($q) use ($test, $filters){
                                                    $q->whereHas('aois', function($q2) use ($filters){
                                                        $q2->whereIn('id', $filters['aoiFilters']['aois']);
                                                    });
                                                })
                                                ->get();
                        
                        
                        Log::info("fixations_subject_on_aois".$fixations_subject_on_aois);
                        
                        Log::info("trial_sequence_subject".$trial_sequence_subject);                        

                        if(count($fixations_subject_on_aois)>0){

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

                            Log::info("duration_aoi: ".$duration_aoi);
                            Log::info("duration_page: ".$duration_page);

                            if($duration_page > 0)
                            {
                                $time_relative = ($duration_aoi/$duration_page) * 100;

                                Log::info("time_relative: ".$time_relative);

                                if($time_relative <= $filters['aoiFilters']['time_relative']['max'] && $time_relative >= $filters['aoiFilters']['time_relative']['min'])
                                {
                                    Log::info("OK!");
                                    $aoiOK = true;
                                }
                            }
                        }
                        
                        // Eliminate subject ?
                        if(!$aoiOK)
                        {
                            Log::info("KO");
                            if(!in_array($subject->id, $subjectToEliminate))
                            {
                                Log::info("Eliminate");
                                $subjectToEliminate[] = $subject->id;
                            }
                        }



                    }
    


                }
                if(isset($filters['aoiFilters']['aoi_noc']))
                {
                    // Check all wanted subjects
                    foreach ($subjectsFiltered as $subject) {
                        
                        // Find how many click he did on wanted AOIS
                        $nb_click = $subject
                                        ->trial_events()
                                        ->where('type', '=', 'Mouse')
                                        ->whereHas('aois', function($q3) use ($filters){
                                            $q3->whereIn('id', $filters['aoiFilters']['aois']);
                                        })->count();
                        
                        // Filter
                        if(!($nb_click >= $filters['aoiFilters']['aoi_noc']['min'] && $nb_click <= $filters['aoiFilters']['aoi_noc']['max']))
                        {
                            if(!in_array($subject->id, $subjectToEliminate))
                            {
                                $subjectToEliminate[] = $subject->id;
                            }
                        }
                    }
                }
            }            


            // Apply results of AOI, Test and Trial filters to Test filters
            $finalMergedSubjects = array();

            foreach ($subjectsFiltered as $key => $subject)
            {
                if(!in_array($subject->id, $subjectToEliminate))
                {
                    array_push($finalMergedSubjects, $subject);
                    //unset($subjectsFiltered[$key]);
                }

            }

            $request->session()->put('subjectsFiltered', $finalMergedSubjects);

            //
            // Warn: subjectToEliminate not all eliminated subjects. TODO ?
            // Use result_subjects instead, $subjectToEliminate seems to be a tool (?)
            //

            return response()->json([
                    'success'=> "success",
                    'result_subjects' => $finalMergedSubjects,              
                   ]);           
        }

        
    }


    /**
    *   Verify that a subject do or not at least one path in $pathWanted
    *   return boolean
    */
    public function did_subject_do_one_path($subject, $pathsWanted)
    {
        $subjectPath = $subject->orderedUrlPath(); //Get path of the subject
        
        //foreach($pathsSubjects as $subjectPath) //One user do only one test !?!
        //{

        foreach($pathsWanted as $wantedPath)
        {
            $size = count($subjectPath);

            if(is_array($wantedPath))
            {
                if(count($wantedPath) == $size || $this->contain_more($wantedPath))
                { 
                    $syncronizeCounters = True;

                    

                    $j=0;
                    for($i = 0; $i < $size; $i++)
                    {
                        $trialSubjectId = $subjectPath[$i]['trial_id'];
                        $trialWantedName = $wantedPath[$j]['page_name'];
                        $trialWantedId = $wantedPath[$j]['id'];
                        $trialSubject = Trial::find($trialSubjectId);
                        Log::info($trialSubjectId . '=>' . $trialSubject->name);
                        Log::info('Wanted =>' . $trialWantedName);

                        $syncronizeCountersThisTurn = False;

                        //Log::info("Actual page (j=".$j."): ".$wantedPath[$j]['id']." - ".$wantedPath[$j]['page_name']);

                        if($trialWantedId == "more") // "more" replace N trials
                        { 
                            $syncronizeCounters = False;

                            if($j == count($wantedPath)-1) // if end with more slide that's OK
                            {
                                return True; // OK he do one path
                            }
                            else
                            {
                                Log::info("Checking... more....");
                                $next = $wantedPath[$j+1]['page_name'];
                                Log::info("wanted next : ".$next);
                                Log::info("we have now : ".$trialSubject);
  
                                if($next == $trialSubject->name)
                                {
                                    Log::info("NEXT == trialSubjectId");
                                    $syncronizeCounters = True;
                                    $syncronizeCountersThisTurn = True;
                                    $trialWantedId = $next;
                                    $trialWantedName = $trialSubject->name; //temporary to pass next test
                                }
                            }
                        }

                        if($syncronizeCounters)
                        { 
                            $j++;
                        }
                        if($syncronizeCountersThisTurn)
                        {
                            $j++;
                        }



                        if(($syncronizeCounters) && $trialSubject->name != $trialWantedName && $trialWantedId != "one")
                        {

                            Log::info("early breacking");
                            break; //this path isn't good, see next now
                        }
                        elseif($i == $size-1 && !($wantedPath[0]['id'] == "more" && $j==0)) // if first is more we are blocked in
                        {
                            Log::info($syncronizeCounters);
                            Log::info($trialSubject->name);
                            Log::info($trialWantedName);
                            Log::info($trialWantedId);
                            
                            Log::info("PAth OK");
                            Log::info($wantedPath);
                            Log::info($subjectPath);
                            return true; // OK he do one path
                        }
                    }
                }
            }
        }
        //}

        return false;
        
    }

    /**
    * Does the array contain key "more" ?
    */
    public function contain_more($wantedPath)
    {
        foreach($wantedPath as $page)
        {
            if($page['id'] == 'more'){
                return true;
            }
        }
        return false;
    }

    
    public function redirect()
    {
        return view('test.urlpath');
    }
    
}
