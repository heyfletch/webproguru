<?php

class sidecar_track{
        
    /**
     * Get the UUID and build the base tracking call
     * @return string
     */
    static function get_tracking_base(){
        $qs_base="v=1&tid=".sidecar_app_config::$tid."&cid=".get_option('install_UUID');
        $qs_base.="&an=".sidecar_app_config::$appname."&av=".sidecar_app_config::$version;
        $qs_base.="&cd1=".urlencode(get_bloginfo('version'));
        
        return $qs_base;
    }

    /**
     * track a screen view
     * @param type $screen_name name of the screen to track
     */
    static function track_screen($screen_name){
        $tracking_call ="t=appview&cd=".urlencode($screen_name);
        self::track_it($tracking_call);
    }
    
    /**
     * track an app event
     * @param type $category of event to track
     * @param type $action action within the category to traack
     */
    static function track_event($category, $action, $label='', $value=''){
        $tracking_call = "t=event&ec=".urlencode($category)."&ea=".urlencode($action);
        if(!empty($label)){
            $tracking_call.="&el=".urlencode($label);
        }
        if(!empty($value)){
            $tracking_call.="&ev=".urlencode($value);
        }
        self::track_it($tracking_call);
    }
    /**
     * fire the tracking call 
     * @param type $qs the query string elements to use for the tracking call
     */
    static function track_it($qs){
        $app_class = sidecar_app_config::$appname;
        if($app_class::get_track_status()){
            file_get_contents("https://ssl.google-analytics.com/collect?".self::get_tracking_base()."&".$qs);
        }
    }
    
}


?>
