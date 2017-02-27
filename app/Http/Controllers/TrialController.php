<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Test;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Subject;
use App\Http\Helpers\StatisticsSubjectHelper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Collection;

use Log;
class TrialController extends Controller
{

    /**
    *   Render Statistics view
    *   GET /statistics/{test_id}/trial/{trial_name}
    */
    public function statistics($test_id, $trial_name, Request $request)
    {
        // Inputs
        //$showEliminated = $request->input('eliminated');
        $test = Test::with(['trials', 'subjects'])->find($test_id);


        Log::Info(' $trial_name = '.$trial_name);

        foreach($test->trials as $t){
            Log::Info($t);
        }
        
        // Get subjects
        $trial = $test->trials()
                ->where('name', '=', $trial_name)
                ->firstOrFail();

        $all_subjects = $trial->subjects()
                ->where('test_id', '=', $test_id)
                ->get()
                ->unique();

        $filtered_subjects = $request->session()->get('subjectsFiltered');


        $all_subjects_count_test = $test->subjectsTroughTrials()
                ->count();
                
        $all_subjects_count_trial = count($all_subjects);
       
        
        // hide eliminated

        $subjects = $trial->subjects()
            ->where('test_id', '=', $test_id)
            ->where('eliminate', '!=', true)
            ->get()
            ->unique();

        $final_subject = $subjects->intersect($filtered_subjects);
        $subjects = $final_subject;

        $subjects_count = count($subjects);
        
      
        return view('trial.statistics')
            ->with('test', $test)
            ->with('trial', $trial)
            ->with('generation_counts', StatisticsSubjectHelper::generation_counts($subjects, $subjects_count))
            ->with('sex_counts', StatisticsSubjectHelper::sexes_count($subjects))
            ->with('glasses_counts', StatisticsSubjectHelper::glasses_count($subjects))
            ->with('langage_counts', StatisticsSubjectHelper::langages_count($subjects))
            ->with('handedness_counts', StatisticsSubjectHelper::handednesses_count($subjects))
            ->with('type_counts', StatisticsSubjectHelper::types_count($subjects))

            ->with('light_counts', StatisticsSubjectHelper::lights_count($all_subjects))
            ->with('eliminate_counts', StatisticsSubjectHelper::eliminates_count($all_subjects))
            ->with('ambiance_counts', StatisticsSubjectHelper::ambiances_count($all_subjects))

            ->with('subjects_count', $subjects_count)
            ->with('all_subjects_count_test', $all_subjects_count_test)
            ->with('all_subjects_count_trial', $all_subjects_count_trial)


            ->with('page_status', 2)
            ->with('breadcrumps', array(
                    'liste' => URL::route('home'), 
                    $test->name => URL::route('show_test', array('test_id' => $test->id)), 
                    'filtre' => URL::route('filter', array('id' => $test->id)),
                    'visualisation' => URL::route('urlpath', array('id' => $test->id)),
                    'statistiques' => '#'
            ));
    }




    /**
     * Get filtered fixations points json string
     */
    private function getPointsJson($trial, Request $request){
        $filtredSubjects = $request->session()->get('subjectsFiltered');
        
        if(empty($filtredSubjects))
        {
            $filtredSubjects=array();
        }
        elseif(!is_array($filtredSubjects))
        {
            $filtredSubjects=$filtredSubjects->toArray();
        }

        $filtredSubjectsIds = array_map(function ($subject){return $subject['id'];}, $filtredSubjects);


        $filteredFixations = $trial->fixations->filter(
            function ($fix) use ($filtredSubjectsIds){
                return in_array($fix->trial_sequence->subject->id, $filtredSubjectsIds);
            }
        );

        $points = $filteredFixations->map(
            function ($fix){
                return array(
                    'x' => floatval($fix->posx),
                    'y' => floatval($fix->posy),
                    'l' => floatval($fix->length),
                    'subject' => $fix->trial_sequence->subject->name
                );
            }
        );

        return json_encode(array('screenshoot' => $trial->getImagePath(), 'points' => $points));
    }

    /**
    *   Render Heatmap view
    *   GET /heatmap/{test_id}/trial/{trial_name}
    */
    public function heatmap($test_id, $trial_name, Request $request)
    {
        $test = Test::with(['trials', 'subjects'])->find($test_id);
        
        $trial = $test->trials()
                ->where('name', '=', $trial_name)
                ->firstOrFail();

       $json = $this->getPointsJson($trial, $request);

        return view('trial.heatmap')
            ->with('test', $test)
            ->with('trial', $trial)
            ->with('json', $json)

            ->with('page_status', 2)
            ->with('breadcrumps', array(
                    'liste' => URL::route('home'), 
                    $test->name => URL::route('show_test', array('test_id' => $test->id)), 
                    'filtre' => URL::route('filter', array('id' => $test->id)),
                    'visualisation' => URL::route('urlpath', array('id' => $test->id)),
                    'heatmap' => '#'
            ));
    }

    /**
    *   Render Scanpath view
    *   GET /scanpath/{test_id}/trial/{trial_name}
    */
    public function scanpath($test_id, $trial_name, Request $request)
    {
        $test = Test::with(['trials', 'subjects'])->find($test_id);

        $trial = $test->trials()
                ->where('name', '=', $trial_name)
                ->firstOrFail();

        $json = $this->getPointsJson($trial, $request);
       
        return view('trial.scanpath')
            ->with('test', $test)
            ->with('trial', $trial)
            ->with('json', $json)

            ->with('page_status', 2)
            ->with('breadcrumps', array(
                    'liste' => URL::route('home'), 
                    $test->name => URL::route('show_test', array('test_id' => $test->id)), 
                    'filtre' => URL::route('filter', array('id' => $test->id)),
                    'visualisation' => URL::route('urlpath', array('id' => $test->id)),
                    'scanpath' => '#'
            ));
    }

    
}
