<?php

namespace CoreBundle\Service;

class FxStringsTools {

    public static function quickSlugify($stringToSlugify) {

        // Remove accents chars
        $unwanted_chars_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

        $stringToSlugifyWithoutAccents = strtr( $stringToSlugify, $unwanted_chars_array );

        // Remove special chars.
        $stringToSlugifyWithoutSpecialChars = preg_replace('#[^\w]#','-',$stringToSlugifyWithoutAccents);

        // Remove multiple hyphens
        $stringToSlugifyWithoutSpecialChars = preg_replace('#-{2,}#','-',$stringToSlugifyWithoutSpecialChars);

        // Trim beginning and ending hyphens
        $stringToSlugifyWithoutSpecialChars = trim($stringToSlugifyWithoutSpecialChars,'-');

        return strtolower($stringToSlugifyWithoutSpecialChars);
    }
}