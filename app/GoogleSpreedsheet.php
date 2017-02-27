<?php

namespace App;

use App\Subject;
use App\Test;
use App\TestConfig;

use Log;
use Google_Client;
use Google_Service_Sheets;

use Config;

use Exception;

/**
* Manage GoogleSpreedSheet connexion
*
* //// WARNING \\\\ TODO in PHP.ini :
* https://laracasts.com/discuss/channels/general-discussion/curl-error-60-ssl-certificate-problem-unable-to-get-local-issuer-certificate/replies/37017
*
*
*/
class GoogleSpreedsheet
{

    private $test_config;
    private $google_client;
    private $spreed_sheet_values;


    /**
    *   @param $test : Test model
    *   Raise Exception in case of bad configuration
    */
    function __construct(Test $test)
    {
        try
        {
            $this->test_config = $test->testConfig;

            $this->getGoogleClient();
            $this->refreshSpreadSheetValues();
        }
        catch(\Google_Service_Exception $e)
        {
            throw new Exception("Mauvaise configuration, impossible de récuérer les évaluations Google");
        }
        catch(\ErrorException $e)
        {
            throw new Exception("Configuration des résultats Google manquante pour ce test, impossible de récuérer les évaluations");
        }
       
    }

    /**
    *   Build the Google client and return it
    *
    *   Project infos:
    *   https://console.developers.google.com/apis/credentials?project=ergocrowd-152712
    *   projet id =  ergocrowd-152712
    *   ID client = 782695111633-gvednr88pmcgib5u7dr1k27hl61ekafc.apps.googleusercontent.com
    *   Code secret = KcteSUoHxq3wkMoILD_cGweo
    *   API Key = AIzaSyDfUMgMckgi6GxSul5Igno1ZNKHxX_aj4Q
    *
    *   May raise \Google_Service_Exception if bad configuration. handle always like this:
    *   try
    *   {
    *       ...
    *   }
    *   catch(\Google_Service_Exception $e)
    *   {
    *       ...
    *   }
    *
    */
    private function getGoogleClient()
    {
        $app_name = Config::get('googleapi.app_name');
        $dev_key = Config::get('googleapi.dev_key');

        $this->google_client = new Google_Client();
        $this->google_client->setApplicationName($app_name);

        //$this->google_client->setClientId('782695111633-gvednr88pmcgib5u7dr1k27hl61ekafc.apps.googleusercontent.com');
        //$this->google_client->setClientSecret('KcteSUoHxq3wkMoILD_cGweo');

        $this->google_client->setDeveloperKey($dev_key);

        return $this->google_client;
    }

    /**
    *   Return full content of SpreadSheet in array
    *   First Row is header
    *   SRC:
    *   http://stackoverflow.com/questions/14780804/how-do-i-read-a-google-drive-spreadsheet-in-php
    *   https://github.com/google/google-api-php-client
    *   http://stackoverflow.com/questions/31652889/how-to-add-google-api-php-client-library-in-laravel-4-2
    *
    *   May raise \Google_Service_Exception if bad configuration. handle always like this:
    *   try
    *   {
    *       ...
    *   }
    *   catch(\Google_Service_Exception $e)
    *   {
    *       ...
    *   }
    *
    */
    public function refreshSpreadSheetValues()
    {
        $spreadsheetId = $this->getSpreadSheetIdWithUrl($this->test_config->google_results_url);
        
        $service = new Google_Service_Sheets($this->google_client);
        
        $values = $service->spreadsheets_values->get($spreadsheetId, $this->test_config->sheet);
        
        $this->spreed_sheet_values = $values->getValues();
        
        return $this->spreed_sheet_values;
    }


    /**
    *   Get a subject evaluation
    */
    public function getSubjectEvaluation(Subject $subject)
    {
        $name_column_id = $this->convertColumnToNumber($this->test_config->name_column);
        $evaluation_column_id = $this->convertColumnToNumber($this->test_config->evaluation_column);
        
        $subject_row = $this->getSubjectRow(
                            $this->spreed_sheet_values, 
                            $subject->name, 
                            $name_column_id);

        Log::info($subject_row);

        if($subject_row === FALSE)
        {
            return FALSE;
        }
        else
        {
            try
            {
                return $subject_row[$evaluation_column_id];
            }
            catch(Exception $e)
            {
                return FALSE;
            }
            
        }
    }


    /*
    *   Get The spread sheet id from URL
    *   From: https://docs.google.com/spreadsheets/d/1_eseAcMUTj-pQn2edE71zEW9dzbo0Bd9--l1MUXbGFw/edit#gid=1785092430
    *   Return: 1_eseAcMUTj-pQn2edE71zEW9dzbo0Bd9--l1MUXbGFw
    */
    private function getSpreadSheetIdWithUrl($url)
    {
        $cleaned_url = str_replace("https://docs.google.com/spreadsheets/d/", "", $url);
        $url_parts = explode("/", $cleaned_url);

        return  $url_parts[0];
    }


    /**
    *   Find a row of a subject with $subject_name in $sheet_datas and return it
    *   $column_name_id must be a number !
    *   Return FALSE if not found
    */
    private function getSubjectRow($sheet_datas, $subject_name, $column_name_id)
    {
        $subject_name = strtolower($subject_name);
        $subject_names = explode(" ", $subject_name);

        foreach($sheet_datas as $row)
        {
            $row_subject_name = strtolower($row[$column_name_id]);
            $row_subject_names = explode(" ", $row_subject_name);

            $compared_array = array_diff($subject_names, $row_subject_names);
            if(count($compared_array) == 0)
            {
                return $row;
            }
        }

        return FALSE;
    }


    /**
    *   Convert column Letter to number 
    *   A => 0
    *   B => 1 ...
    *   AA => 26
    *   AB => 27
    */
    private function convertColumnToNumber($column)
    {   
        $column = strtolower($column);
        $alphabet_column = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

        $column_number = array_search($column, $alphabet_column);
        if($column_number === FALSE)
        {
            $column_number = 0;
            $letters = array_reverse(str_split($column));
            for($i=0; $i<count($letters); $i++)
            {
                $column_number += array_search($letters[$i], $alphabet_column) * pow(26, $i);
            }
        }

        return $column_number;
    }
}
