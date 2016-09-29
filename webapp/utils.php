<?php


//////////////////////////////////////////////////////////////////////////////////////////////////
//                   Sardar Vallabhbhai National Institute of Technology.                       //
//                                                                                              //
// Title:            Document Search Engine                                                     //
// File:             utils.php                                                                 //
// Since:            28-Mar-2016 : PM 06:29                                                     //
//                                                                                              //
// Author:           Heet Sheth                                                                 //
// Email:            u13co005@svnit.ac.in                                                       //
//                                                                                              //
/////////////////////////////////////////////////////////////////////////////////////////////////


class Utils {

    public static function getCurrentDate(){
        date_default_timezone_set("Asia/Kolkata");
        return date("Y-m-d H:i:s");
    }

    public  static  function getImageBucketURL(){
        return Utils::getBaseURL().Constants::IMG_BUCKET_BASE;
    }

    public  static  function getDocBucketURL(){
        return Utils::getBaseURL().Constants::DOC_BUCKET_BASE;
    }


    public  static  function getBaseURL() {
        $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
        $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
        $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
        $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port;
        return $url;
    }

    public static function fetchRowAsArray($result)
    {
        $array = array();

        if($result instanceof mysqli_stmt)
        {
            $result->store_result();

            $variables = array();
            $data = array();
            $meta = $result->result_metadata();

            while($field = $meta->fetch_field())
                $variables[] = &$data[$field->name]; // pass by reference

            call_user_func_array(array($result, 'bind_result'), $variables);

            $i=0;
            while($result->fetch())
            {
                $array[$i] = array();
                foreach($data as $k=>$v)
                    $array[$i][$k] = $v;
                $i++;

                // don't know why, but when I tried $array[] = $data, I got the same one result in all rows
            }
        }
        elseif($result instanceof mysqli_result)
        {
            while($row = $result->fetch_assoc())
                $array[] = $row;
        }

        return $array;
    }
}