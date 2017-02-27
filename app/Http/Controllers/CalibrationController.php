<?php namespace App\Http\Controllers;

/**
 * Description of CalibrationController *
 * @author claudiag.gheorghe
 */

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Test;
use App\Subject;
use App\Http\Helpers\CalibrationHelper;

class CalibrationController extends Controller
{
   
    public function index($id)
    {
        $test = Test::find($id);
        $subjects = $test->subjects;
        $sorted = CalibrationHelper::sortCalibrations($subjects);        
        return view('test.calibration')
                ->with('test', $test)
                ->with('totalSubjects', $subjects->count())
                ->with('sortedCalibs', $sorted);                
    }
    
    public function state(Request $request)
    {
        if($request->ajax())
        {
            $requestData = $request->all();
            $subjects = Test::find($requestData['id'])->subjects;           
            $sortedCalibs = CalibrationHelper::sortCalibrations($subjects);  
            
            if($requestData['type'] == 'perfect')
            {
                foreach ($sortedCalibs["perfectCalibs"] as $calib)
                {
                    $currentSubject = Subject::find($calib->subject_id);
                    if($requestData['state'] == 'Eliminate')
                    {
                       $currentSubject->eliminate = 1;
                    }
                    elseif($requestData['state'] == 'Add')
                    {
                        $currentSubject->eliminate = 0;
                    }
                    $currentSubject->save();                    
                }
            }
            
            if($requestData['type'] == 'good')
            {          
                foreach ($sortedCalibs["goodCalibs"] as $calib)
                {                     
                    $currentSubject = Subject::find($calib->subject_id);                   
                    if($requestData['state'] == 'Eliminate')
                    {
                       $currentSubject->eliminate = 1;                       
                    }
                    elseif($requestData['state'] == 'Add')
                    {
                        $currentSubject->eliminate = 0;                       
                    }              
                    $currentSubject->save();                    
                }                          
            }             
            if($requestData['type'] == 'moderate')
            {
                foreach ($sortedCalibs["moderateCalibs"] as $calib)
                {
                    $currentSubject = Subject::find($calib->subject_id);
                    if($requestData['state'] == 'Eliminate')
                    {
                       $currentSubject->eliminate = 1;
                    }
                    elseif($requestData['state'] == 'Add')
                    {
                        $currentSubject->eliminate = 0;
                    }            
                    $currentSubject->save();
                }           
            }
            if($requestData['type'] == 'poor')
            {
                foreach ($sortedCalibs["poorCalibs"] as $calib)
                {
                    $currentSubject = Subject::find($calib->subject_id);
                    if($requestData['state'] == 'Eliminate')
                    {
                       $currentSubject->eliminate = 1;
                    }
                    elseif($requestData['state'] == 'Add')
                    {
                        $currentSubject->eliminate = 0;
                    }            
                    $currentSubject->save();
                }           
            }            
            return response()->json(['success'=>"success"]);             
        }
    }

}
