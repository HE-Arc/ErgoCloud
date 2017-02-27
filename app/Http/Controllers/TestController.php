<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Test;
use App\TestConfig;
use App\GoogleSpreedsheet;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Subject;
use App\Http\Helpers\StatisticsSubjectHelper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Collection;
use App\Http\Helpers\CalibrationHelper;
use Log;



class TestController extends Controller
{
    protected $test;
    public function __construct(Test $test) {
        $this->test=$test;
    }  

    //return all the tests
    public function index()
    {
        $all_tests = $this->test->all();
        $calibInfo = [];
        
        foreach ($all_tests as $test)
        {
            if(CalibrationHelper::sortCalibrations($test->subjects) != null)
            {
                $calibInfo =[$test->id => CalibrationHelper::sortCalibrations($test->subjects)] ; 
            }
        }
        return view('test.index')
                ->with('tests', $all_tests)
                ->with('calibsInfo', $calibInfo)
                ->with('page_status', 0)
                ->with('breadcrumps', array('liste' => URL::route('home')));
    }

    /**
    *   Show a test details
    */
    public function show(Request $request, $test_id)
    {
        // Calibration
        $test = Test::with(['trials', 'subjects', 'testConfig'])->find($test_id);
        $subjects = $test->subjects;
        $sorted_calibrations = CalibrationHelper::sortCalibrations($subjects);
        

        // Statistics


        $all_subjects = $test->subjects;
        $all_subjects_count = $test->subjects()
                ->count();


        $subjects_count = $test->subjects()
            ->where('eliminate', '!=', true)
            ->count();
        $subjects = $test->subjects()
            ->where('eliminate', '!=', true)
            ->get();


        return view('test.show')
            ->with('test', $test)
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
            ->with('all_subjects_count_test', $all_subjects_count)



            ->with('totalSubjects', $subjects->count())
            ->with('sortedCalibs', $sorted_calibrations)

            ->with('page_status', 1)
            ->with('breadcrumps', array(
                'liste' => URL::route('home'), 
                $test->name => '#'
                ));
    }
    
    public function help()
    {
        return view('help');
    }

    //return the subjects and the trials for a test
    public function subjects_trails($id)
    {
        $test = $this->test->find($id);
        return view('test.overview')
                ->with('subjects', $test->subjects)
                ->with('trials', $test->trials);
    }

    public function url_path(Request $request, $id)
    {
        $test = Test::with(['trials', 'subjects'])->find($id);  


        // URL Path
        $alltrials = new \Illuminate\Database\Eloquent\Collection;
        if($request->session()->has('subjectsFiltered'))
        {
            $subjects = collect($request->session()->get('subjectsFiltered')); //Convert array in session to collection
            foreach($subjects as $subject)
            {
                foreach ($subject->trials as $trial)
                {
                    if(!$alltrials->contains($trial))
                        $alltrials->add($trial);
                }
            }
            $trials = $test->trials;
        }        
        else 
        {
            $subjects =  $test->subjects;
            $trials = $test->trials;
        }      


        // Statistics
        $subjects_count = count($subjects);


        //spreed sheet results
        $google_spreed_sheet = NULL;
        $google_spreed_sheet_errors = NULL;
        try
        {
            $google_spreed_sheet = new GoogleSpreedsheet($test);
        }
        catch(\Exception $e)
        {
            // Advertise user that his configuration is bad
            $google_spreed_sheet_errors = $e->getMessage();
        }
        


        return view('test.urlpath')
            ->with('subjects', $subjects)
            ->with('trials', $trials)
            ->with('test', $test)

            ->with('google_spreed_sheet', $google_spreed_sheet)
            ->with('google_spreed_sheet_errors', $google_spreed_sheet_errors)

            ->with('generation_counts', StatisticsSubjectHelper::generation_counts($subjects, $subjects_count))
            ->with('sex_counts', StatisticsSubjectHelper::sexes_count($subjects))
            ->with('glasses_counts', StatisticsSubjectHelper::glasses_count($subjects))
            ->with('langage_counts', StatisticsSubjectHelper::langages_count($subjects))
            ->with('handedness_counts', StatisticsSubjectHelper::handednesses_count($subjects))
            ->with('type_counts', StatisticsSubjectHelper::types_count($subjects))
            ->with('subjects_count', $subjects_count)

            ->with('page_status', 2)
            ->with('breadcrumps', array(
                    'liste' => URL::route('home'), 
                    $test->name => URL::route('show_test', array('test_id' => $test->id)), 
                    'filtre' => URL::route('filter', array('id' => $test->id)),
                    'visualisation' => URL::route('urlpath', array('id' => $test->id)
            )));
    }

    public function statistics($test_id, Request $request)
    {
        $showEliminated = $request->input('eliminated');
        $test = Test::with(['trials', 'subjects'])->find($test_id);

        $all_subjects = $test->subjects;
        $all_subjects_count = $test->subjects()
                ->count();

        if($showEliminated){
            $subjects_count = $all_subjects_count;
            $subjects = $all_subjects;
        }
        else{
            $subjects_count = $test->subjects()
                ->where('eliminate', '!=', true)
                ->count();
            $subjects = $test->subjects()
                ->where('eliminate', '!=', true)
                ->get();
        }

        return view('test.statistics')
            ->with('test', $test)
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
            ->with('all_subjects_count_test', $all_subjects_count)
            ->with('elminated_showed', $showEliminated)

            ->with('page_status', 2)
            ->with('breadcrumps', array(
                'liste' => URL::route('home'), 
                'statistiques' => '#'
                ));
    }
}
