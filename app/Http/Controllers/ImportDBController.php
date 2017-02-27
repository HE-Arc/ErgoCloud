<?php namespace App\Http\Controllers;
/**
 * Import DB
 * @author steve.visinand
 */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Validator;
use Redirect;

use Session;
use File;
use App\Test;
use App\Trial;

use Illuminate\Support\Facades\Log;

class ImportDBController extends Controller
{   
    /**
    *   Show DB upload file view
    */
    public function importDB()
    {
        return view('import')
                ->with('page_status', 0);
    }

    /**
    *   Upload DB file
    */
    public function uploadDB()
    {
        // getting all of the post data
        $file = array('dbfile' => Input::file('dbfile'));
        
        // setting up rules
        $rules = array('dbfile' => 'required');
        
        
        // doing the validation, passing post data, rules and the messages
        $validator = Validator::make($file, $rules);

        if($validator->fails())
        {
            // send back to the page with the input data and errors
            Session::flash('error', 'Veuillez fournir un fichier');
            return Redirect::to('import');
        }
        else
        {
            $extension = Input::file('dbfile')->getClientOriginalExtension(); // getting image extension
            // checking file is valid.
            if(Input::file('dbfile')->isValid() && $extension == "db")
            {
                $destinationPath = 'uploads'; // upload path
                
                $fileName = 'actualDB.'.$extension; // renameing file

                File::cleanDirectory($destinationPath); //clean directory
                File::makeDirectory('uploads/images');

                Input::file('dbfile')->move($destinationPath, $fileName); // uploading file to given path
                

                // Setting session params
                $tests_id = Test::whereHas('trials', function($q){
                                $q->whereNotNull('image');
                            })->lists('id')->toArray();

                session(['tests_id' => $tests_id]);

                if(count($tests_id)>0)
                {
                    // Start images upload processing
                    Session::flash('importsuccess', 'Base de données importée');
                    return Redirect::route('import_images', $tests_id[0]);
                }
                else
                {
                    // sending back with message
                    Session::flash('success', 'Importation réussie');
                    return Redirect::to('/');
                }
            }
            else
            {
                // sending back with error message
                Session::flash('error', 'Fichier non valide, veuillez fournir un fichier de type ".db"');
                return Redirect::to('import');
            }
        }
    }

    /**
    *   Show import image view
    */
    public function importImages(Request $request, $test_id)
    {
        $test = Test::findOrFail($test_id);

        $trialImages = $test->trials()
                            ->whereNotNull('image')
                            ->where('image', '!=', '')
                            ->lists('image')
                            ->toArray();

        $uploadedImages = $this->uploadedImages('uploads/images');

        $neededImages = array_diff($trialImages, $uploadedImages);

        $uploadedImagesTest = array_intersect($trialImages, $uploadedImages);
        
        $tests_id = $request->session()->get('tests_id');
        $remaning_tests = count($tests_id) - 1;
        $all_tests =  Test::all()->count();

        return view('test.import_images')
                ->with('page_status', 0)
                ->with('test', $test)
                ->with('neededImages', $neededImages)
                ->with('uploadedImages', $uploadedImagesTest)
                ->with('remaning_tests_count', $remaning_tests)
                ->with('all_tests_count', $all_tests);
    }

    /**
    *   Show import last image view
    */
    public function importMissingImages(Request $request)
    {
        $neededImages = $this->allMissingImages('uploads/images');
        
        return view('import_missing_images')
                ->with('page_status', 0)
                ->with('neededImages', $neededImages);
    }

    /**
    *   Upload images for a test
    *   Warning: php_fileinfo extension needed (php.ini)
    *   Warning: Set upload_max_filesize & max_file_uploads & post_max_size in php.ini !!
    */
    public function uploadImages(Request $request, $test_id)
    {
        $test = Test::findOrFail($test_id);

        // create necessary directory
        //File::makeDirectory('uploads/'.$test->name);


        if(Input::get('submit', false))
        {
            if (!$request->hasFile('images'))
            {
                return Redirect::back()->withError('Veuillez fourni une image au minimum');
            }

            // upload images
            $error = $this->saveImages($request);

            if(count($error)>0) // some errors
            {
                //File::removeDirectory($destinationPath); //remove directory
                return Redirect::back()->withErrors($v);
            }
        }

        // update session
        $tests_id = $request->session()->get('tests_id');
        $new_tests_id = array();
        foreach($tests_id as $id)
        {
            if($test_id != $id)
            {
                 array_push($new_tests_id, $id);
            }
        }
        session(['tests_id' => $new_tests_id]);

        // redirects
        if(count($new_tests_id)>0)
        {
            // continue images upload processing
            return Redirect::route('import_images', $new_tests_id[0]);
        }
        else
        {   
            if(count($this->allMissingImages('uploads/images')) == 0)
            {
                 // sending back with message
                Session::flash('success', 'Importation terminée');
                return Redirect::to('/');
            }
            else
            {
                return Redirect::route('import_missing_images');
            }
        }
    }

    /**
    *   Return all missing images for all trials of all tests
    */
    public function allMissingImages($path)
    {
        $trialImages = Trial::whereNotNull('image')
                            ->where('image', '!=', '')
                            ->lists('image')
                            ->toArray();
        $uploadedImages = $this->uploadedImages($path);

        $neededImages = array_diff($trialImages, $uploadedImages);

        return $neededImages;
    }

    /**
    *   Upload missing images
    */
    public function uploadMissingImages(Request $request)
    {
        if(Input::get('submit', false))
        {
            if (!$request->hasFile('images'))
            {
                return Redirect::back()->withError('Veuillez fourni une image au minimum');
            }
            
            $error = $this->saveImages($request);
            if(count($error)>0) // some errors
            {
                return Redirect::back()->withErrors($v);
            }
        }

        Session::flash('success', 'Importation terminée');
        return Redirect::to('/');
    }

    /**
    *   Return array of uploaded images
    */
    private function uploadedImages($path)
    {
        $uploadedImagesPathes = File::files($path);
        $uploadedImages = array();
        
        foreach($uploadedImagesPathes as $imagePath){
            $parts = explode('/', $imagePath);
            array_push($uploadedImages, end($parts));
        }

        return $uploadedImages;
    }

    /**
    *   Upload and save images in request
    */
    private function saveImages($request)
    {
        $error = array();

        // upload files
        if ($request->hasFile('images'))
        {
            $files = $request->file('images');
            $rules = [
                'file' => 'required|image' //php_fileinfo extension needed
            ];

            $destinationPath = 'uploads/images';
            //$destinationPath = 'uploads/'.$test->name;
            
            foreach($files as $one)
            {
                $v = Validator::make(['file' => $one], $rules);
                if($v->passes())
                {
                    $filename = $one->getClientOriginalName();
                    $upload_success = $one->move($destinationPath, $filename);
                    
                    if($upload_success)
                    {
                        $done[] = "Envoi réussi: ".$filename;
                        Session::flash('importsuccess', $done);
                    }
                }
                else
                {
                    $filename = $one->getClientOriginalName();
                    $error[] = $filename;
                }
            }
        }

        return $error;
    } 

}