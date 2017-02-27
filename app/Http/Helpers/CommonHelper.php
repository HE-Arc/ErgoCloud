<?php
namespace App\Http\Helpers;
/**
 * Description of CommonHelper = general methods  *  
 *
 * @author claudiag.gheorghe
 */
class CommonHelper {
    /**
     * This method search un element into a multiple array  
     * @param type $elem
     * @param type $array
     * @param type $field
     * @return boolean
     */
    public static function in_multiarray($elem, $array, $field)
    {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while($bottom <= $top)
        {
            if($array[$bottom][$field] == $elem)
                return true;
            else 
                if(is_array($array[$bottom][$field]))
                    if(in_multiarray($elem, ($array[$bottom][$field])))
                        return true;

            $bottom++;
        }        
        return false;
    }
}
