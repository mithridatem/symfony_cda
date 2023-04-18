<?php
namespace App\Service;
class Utils{
    
    /**
     * @param string $value
     * @return null|string
     */
    public static function cleanInputStatic(string $value):?string{
        return htmlspecialchars(strip_tags(trim($value)));
    }

    /**
     * @param string $value
     * @return null|string
     */
    public function cleanInput(string $value):?string{
        return htmlspecialchars(strip_tags(trim($value)));
    }
}

?>