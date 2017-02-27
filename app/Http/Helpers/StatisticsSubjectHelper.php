<?php
namespace App\Http\Helpers;
use App\Subject;

/**
 * Description of StatisticsSubjectHelper = Statistics tools
 *
 * @author steve.visinand
 */
class StatisticsSubjectHelper { 


    public static function types_count($subjects)
    {
        return StatisticsSubjectHelper::generic_counts($subjects, 'type');
    }

    public static function lights_count($subjects)
    {
        return StatisticsSubjectHelper::generic_counts($subjects, 'light');
    }

    public static function eliminates_count($subjects)
    {
        $aliases = array(
            '0' => 'non',
            '1' => 'oui'
        );
        return StatisticsSubjectHelper::generic_counts($subjects, 'eliminate', $aliases);
    }

    public static function ambiances_count($subjects)
    {
        return StatisticsSubjectHelper::generic_counts($subjects, 'ambiance');
    }

    public static function handednesses_count($subjects)
    {
        return StatisticsSubjectHelper::generic_counts($subjects, 'handedness');
    }

    public static function sexes_count($subjects)
    {
        return StatisticsSubjectHelper::generic_counts($subjects, 'sex');
    }

    public static function glasses_count($subjects)
    {
        $aliases = array(
            'yes' => 'oui',
            'no' => 'non'
        );
        return StatisticsSubjectHelper::generic_counts($subjects, 'glasses', $aliases);
    }

    public static function langages_count($subjects)
    {
        return StatisticsSubjectHelper::generic_counts($subjects, 'language');
    }

    /**
    *   Build an array with: key => value
    *       key => all differents row values
    *       value => count of each row values
    */
    public static function generic_counts($subjects, $row, $aliases_ask=NULL){
        
        $aliases = array(
            '' => 'non renseigné'
        );
        
        if($aliases_ask != NULL){
            $aliases = array_merge($aliases_ask, $aliases);
        }

        $datas = array();
        foreach ($subjects as $subject){
            $k = strtolower($subject->$row);

            if($aliases != NULL && array_key_exists($k, $aliases)){
                $k = $aliases[$k];
            }

            if (array_key_exists($k, $datas))
            {
                $datas[$k] += 1;
            }
            else
            {
                $datas[$k] = 1;
            }
        }
        return $datas;
    }


    /**
    *   Generate array with generation statistics
    *   $subjects is an array with subjects
    */
    public static function generation_counts($subjects, $all_count)
    {
        // Build id array
        $ids = array();
        foreach($subjects as $subject){
            array_push($ids, $subject['id']);
        }

        // Filter generations
        $generation_x_count = Subject::whereIn('id', $ids)
            ->where('age', '>=', '40')
            ->where('age', '<=', '50')
            ->count();

        $generation_y_count = Subject::whereIn('id', $ids)
            ->where('age', '>=', '26')
            ->where('age', '<=', '39')
            ->count();

        $generation_z_count = Subject::whereIn('id', $ids)
            ->where('age', '>=', '14')
            ->where('age', '<=', '25')
            ->count();

        $other_count = $all_count - ($generation_x_count + $generation_y_count + $generation_z_count);

        return array(
            'Génération x (40-50)' => $generation_x_count,
            'Génération y (26-39)' => $generation_y_count,
            'Génération z (14-25)' => $generation_z_count,
            'Autre' => $other_count
        );
    }
} 