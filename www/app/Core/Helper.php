<?php

declare (strict_types = 1);

namespace Songfolio\Core;

class Helper
{
    public static $googleFonts;

    public static function isCmsInstalled(){
        if(file_exists('app/config/.installed')){
            return true;
        }

        return false;
    }

    /**
     * @param $targetDirProp
     * @param $name
     * @return string
     */
    public static function uploadImage(string $targetDirProp, string $name)
    {
        $targetDir = $targetDirProp;
        $fileName = basename($_FILES[$name]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
        if ($fileType !== '') {
            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($_FILES[$name]["tmp_name"], $targetFilePath)) {
                    $statusMsg = 'OK';
                } else {
                    $statusMsg = "Désolé, une erreur se produit.";
                }
            } else {
                $statusMsg = 'Format n\'est pas bon.';
            }

            return $statusMsg === 'OK' ? $targetFilePath : $statusMsg;
        } else {
            return null;
        }
    }

    /**
     * Return label function
     *
     * @param string $value
     * @return string
     */
    public static function getLabelFromMapping(string $value): string
    {
        $conf = yaml_parse_file(__DIR__ . '/../config/back.global.yml');
        return $conf['mapping_header_name'][$value] ?? $conf['mapping_header_name']['admin'];
    }

 

    /**
     * @param string $classname
     * @return string
     */
    public static function getCalledClass(string $classname): string
    {
        return substr(strrchr($classname, "\\"), 1);
    }

    /**
     * @param string $date
     * @return string
     */
    public static function getFormatedDate($date): string
    {
        if ($date !== null)
            return date("d/m/Y", strtotime($date));
        return '';
    }

    public static function getFormatedDateWithTime($date): string
    {
        if ($date !== null) {
            $dt = date("d/m/Y  H:i", strtotime($date));
            return substr_replace($dt, ' à ', strrpos($dt, " "), 0);;
        }

        return '';
    }

    /**
     * @param $arr
     * @param $compare
     * @param $return
     * @return bool|string
     */
    public static function searchInArray($arr, $compare, $return)
    {
        foreach ($arr as $item) {
            if ($item['id'] === $compare) {
                return $item[$return];
                break;
            }
        }
        return false;
    }

    public static function getImageName(string $str): string
    {
        return substr(strrchr($str, "/"), 1);
    }

    public static function getTimeAgo($date)
    {
        $time = strtotime($date);
        $time_difference = time() - $time;

        if ($time_difference < 1) {
            return 'il y a moins d\'une seconde';
        }
        $condition = array(
            12 * 30 * 24 * 60 * 60 =>  'année',
            30 * 24 * 60 * 60       =>  'mois',
            24 * 60 * 60            =>  'jour',
            60 * 60                 =>  'heur',
            60                      =>  'minute',
            1                       =>  'seconde'
        );

        foreach ($condition as $secs => $str) {
            $d = $time_difference / $secs;

            if ($d >= 1) {
                $t = round($d);
                return 'il y a  ' . $t . ' ' . $str . ($t > 1 ? 's' : '');
            }
        }
        return null;
    }

    public static function getNameAfterConfig(string $key):string
    {
        if(strpos($key, '_')){
            return substr($key, 0, strpos($key, "_"))."s";
        }
        return $key;
    }

    public static function getGoogleFonts(){

        if(!self::$googleFonts){
            $api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCqeJdCjZTdGqQxWX51_xoPcJ_TP1tnDfk&fields=items/family,items/files';
            $api_result = Helper::curl_request($api_url);
            foreach(json_decode($api_result, true)['items'] as $font){
                if(isset($font['files']['regular'])){
                    $return[] = $font['family'];
                }
            }
            self::$googleFonts = $return;
        }
        return self::$googleFonts;
    }

    public static function getGoogleFontsCss($font){
        $api_url = 'https://fonts.googleapis.com/css?family='.urlencode($font);
        $api_result = Helper::curl_request($api_url);
        return $api_result;
    }


    public static function curl_request($url){
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($handle);
        if(curl_errno($handle))
        {
            echo 'Curl error: ' . curl_error($handle);
        }
        curl_close($handle);

        return $result;
    }
}
