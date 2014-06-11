<?php
/*
Plugin Name: Decision Tree
Plugin URI: http://sidecar.tv/decision_tree
Description: A Decision Tree Builder
Version: 1.0.2
Author: SideCar Apps
Author URI: http://sidecar.tv/
License: Commerical
*/
/*
 * Keep version above in sync with version in app_config.php
 */

require_once 'app_config.php';
require_once 'sidecar/sidecar_track.php';

class DecisionTree {
        
    static function create_post_type() {
  
        if($GLOBALS['wp_version'] >= 3.8){
            $menu_icon = "dashicons-networking";
        }
        else{
            $menu_icon = "";
        }
        
        register_post_type( 'decision_tree',
                array(
                    'labels' => array(
                                'menu_name' => 'Trees',
                                'name' => __( 'Decision Trees' ),
                                'singular_name' => __( 'Decision Tree' ),
                                'add_new_item' => __( 'Add New Tree' ),
                                'all_items' => 'All Trees'

                        ),
                    'public' => false,
                    'show_ui' => true,
                    'show_in_menu' => self::get_plugin_path().'/decision_tree_dashboard.php',
                    'query_var'=>false,
                    'has_archive' => false,
                    'supports' => array( 'title' ),
                    'register_meta_box_cb' => array('DecisionTree', 'add_metabox'),
                     'exclude_from_search' => 'true',
                      'publicly_queryable' => 'false'
                    )
            );
    }
  
    /**
     * This method along with register_buttons below it, add DT insert buttons to the visual editor
     */
    static function add_buttons($plugin_array){
        $plugin_array['decision_tree_insert_button'] = plugins_url('/dt_tinymce_plugin.js',__file__);
        return $plugin_array;
        
    }
    static function register_buttons($buttons){
        array_push($buttons, 'decision_tree_insert_button');
        return $buttons;
    }
    
    
    
    /**
     * This method adds the insert button to the text editor
     */
    static function add_quicktags(){
        if (wp_script_is('quicktags')){
            ?>
            <script type="text/javascript">
            QTags.addButton( 'dt_insert_tree', 'Decision Tree', dt_insert_prompt, 
                null, null, 'Decision Tree', 500 );
            function dt_insert_prompt(e, c, ed){
                Decision_Tree_Insert.decision_tree_init(ed, e, c);
                Decision_Tree_Insert.decision_tree_prompt_and_insert('text');
            }
            </script>
            <?php
        }
    }
    
    /**
     * This Adds the meta-box, i.e. the form area to the Decision Tree Custom Post Type Amdin Page
     */
    static function add_metabox(){
        add_meta_box('decision_tree_metabox', 'Tree Box', array('DecisionTree', 'print_metabox_form'), 
                    'decision_tree', 'normal');
    }

    /**
    * TODO: Take the saved data from the serialized blob in the DB and output it to the page as JSON(?)
     * This prints out the form on the Decision Tree Admin Page
     */
    static function print_metabox_form($post){
         $donated = self::get_donation_status();
        //the JS included will place the form in this div
        print "
            <div id='dtree_entry_block_errors' >Oopsie!
                <div id='dtree_entry_block_errors_hide'>X</div>
                <ul id='dtree_entry_block_error_msg' /></ul>
            </div>
            <div id='dtree_entry_preview_outer' >
                <input type='submit' name='dt_preview_button' id='dt_preview_button' class='button 
                    button-primary button-large' value='Preview Decision Tree' >
                <div id='dtree_entry_preview_inner' ></div>
            </div>
            <div id='dtree_entry_block'></div>
            <input type='hidden' name='dtree_blob' id='dtree_blob' value=''/>
            <input type='button' class='button decision_tree_add_step' value='Add Step'>
            <br>
            <script type='text/javascript'>
                jQuery(document).ready(function($) {
                    DTree_field_set = new DTree('".$donated."');
                    DTree_field_set.initializeForm();
                });
            </script>
";
        
        // attempt to get blog from DB and print it if we got it
        $dtree_blob = get_post_meta($post->ID, 'dtree_blob', true);
        if (!empty($dtree_blob)){
            print "
            <script type='text/javascript'>
                raw_DB_value = '$dtree_blob';
                var DTree_object_restored = JSON.parse(raw_DB_value);
            </script>
            ";
        }
        
        print self::get_html_comment_shoutout();
   }

   /**
    * save the DT JSON object to the DB
    * @param type $id id of the post as provided by wp
    */
   static function save_metabox($id){
       
        if (isset($_POST['dtree_blob'])){
            update_post_meta( $id, 'dtree_blob', wp_slash($_POST['dtree_blob']));
        }
   }


    /**
     * TODO: 
     * This returns the decision tree contents to the front end to be rendered and displayed to the user
     */
   static function decision_tree_shortcode( $atts ) {
        //add_action( 'wp_enqueue_scripts',  array('DecisionTree', 'load_scripts') );
        self::load_scripts();
        extract( shortcode_atts( array(
                'id' => '0',
        ), $atts ) );
        
        $donated = self::get_donation_status();    
        $dtree_blob = get_post_meta($atts['id'], 'dtree_blob', true);
        $dtree_post = get_post($atts['id']);
        $dt_id = $atts['id'];
        $dt_title = $dtree_post->post_title;
        
        //output the JSON DT Blob and have JS on DOM ready turn it into an object
        //then on the client side call process on the first question
        $output = "<script type='text/javascript'>";
        $output.="jQuery(document).ready(function($) {\n";
        $output.= "     dt_tree_$dt_id = jQuery.parseJSON('$dtree_blob'); \n";
        $output.= "     if (dt_tree_$dt_id != undefined){ \n";
        $output.= "         dt_tree_$dt_id.donated = '$donated'; \n";
        $output.= "         dt_tree_$dt_id.title = '$dt_title'; \n";
        $output.= "         process_question($dt_id);\n";
        $output.= "     } \n";
        $output.= "});\n";
        $output.= "</script>\n";
        $output.= "<form id='decision_tree_area_".$atts['id']."' class='decision_tree_area'></form>\n";
        return $output.self::get_html_comment_shoutout();
    }

    
    /**
     * returns a list of the decision trees via ajax
     */
    static function list_trees() {
        $args = array( 'post_type' => 'decision_tree', 'orderby'=> 'title', 'posts_per_page'=>-1);
        $myposts = get_posts( $args );
        foreach($myposts as $post){
            $option_list[$post->ID]=$post->post_title;
        }
        print json_encode($option_list);
        die(); // this is required to return a proper result
    }

    
    /*
     * Load scripts required for the visitor facing side
     */
    static function load_scripts(){
        wp_enqueue_script('decision_tree_js', 
            plugins_url( '/decision_tree_wpg.js' , __FILE__ ), 
            array('jquery', 'jquery-ui-core'));
    }

    /*
     * Load scripts required for the admin facing side
     */
    static function load_admin_scripts(){
        wp_enqueue_script('decision_tree_js', 
            plugins_url( '/decision_tree_wpg.js' , __FILE__ ), 
            array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('decision_tree_insert_tag_js', 
            plugins_url( '/decision_tree_insert_tag.js' , __FILE__ ), 
            array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('decision_tree_admin_js', 
            plugins_url( '/decision_tree_admin.js' , __FILE__ ), 
            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'));
        wp_enqueue_style( 'decision_tree_admin_style', 
            plugins_url( 'decision_tree_admin.css' , __FILE__ ) , false ); 
        
        //admin css for pre 3.8 use
        if($GLOBALS['wp_version'] < 3.8){
            wp_enqueue_style( 'decision_tree_admin_pre38_style', 
                plugins_url( 'decision_tree_admin_pre38.css' , __FILE__ ) , false ); 
        }
        wp_enqueue_style( 'decision_tree_jquery_ui', 
            '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', false ); 
        wp_enqueue_style( 'prefix-font-awesome', 
            '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css', 
            array(), '4.0.3' );
    	
        wp_enqueue_style( 'decisiontree_local_style', 
            plugins_url( 'decisiontree_local_style.css' , __FILE__ ) , false );
        
        add_settings_section(
		'dt_setting_section',
		'',
		array('DecisionTree', 'dt_setting_section_callback_function'),
		'dt_options_group'
	);
        add_settings_field(
		'dt_idonated_setting',
		'I donated! Hide Public Attribution',
		array('DecisionTree', 'dt_idonated_setting_callback_function'),
		'dt_options_group',
		'dt_setting_section'
	);
 	
        
        //register option page
        register_setting( 'dt_options_group', 'dt_idonated_setting'); 
        
        //tracking options
        add_settings_section(
		'dt_track_setting_section',
		'',
		array('DecisionTree', 'dt_setting_section_callback_function'),
		'dt_track_options_group'
	);
        add_settings_field(
		'dt_track_setting',
		'Please Track Me Nicely',
		array('DecisionTree', 'dt_track_setting_callback_function'),
		'dt_track_options_group',
		'dt_track_setting_section'
	);
 	
        
        //register option page
        register_setting( 'dt_track_options_group', 'dt_track_setting');         
        
        
        
    }

     static function dt_setting_section_callback_function() {
 	//echo '<p>Click here if you donated and want to turn off the DecisionTree attribution:</p>';
    }
    static function dt_idonated_setting_callback_function() {
           echo '<input name="dt_idonated_setting" id="gv_thumbnails_insert_into_excerpt" 
               type="checkbox" value="1" class="code" ' 
        .checked( 1, get_option( 'dt_idonated_setting' ), false ) . ' />';
    }
    
    static function dt_track_setting_callback_function() {
           echo '<input name="dt_track_setting" id="gv_thumbnails_insert_into_excerpt" 
               type="checkbox" value="1" class="code" ' 
        .checked( 1, get_option( 'dt_track_setting' ), false ) . ' />';
    }    
    /*
     * Load styles
     */
    static function load_styles(){
            wp_enqueue_style( 'decision_tree_jquery_ui', 
                    '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', false ); 
            wp_enqueue_style( 'decisiontree_local_style', 
                    plugins_url( 'decisiontree_local_style.css' , __FILE__ ) , false );
            wp_enqueue_style( 'prefix-font-awesome', 
                    '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css', array(), '4.0.3' );
    }
    
    static function register_menu_items(){
        if($GLOBALS['wp_version'] >= 3.8){
            $menu_icon = "dashicons-networking";
        }
        else{
            $menu_icon = "none";
        }
        add_menu_page('Decision Trees', 'Decision Trees', 'edit_posts', 
                self::get_plugin_path().'/decision_tree_dashboard.php', '', $menu_icon, 25.123);
        
        //add the dashboard page
        add_submenu_page(self::get_plugin_path().'/decision_tree_dashboard.php', 'Dashboard', 
                'Dashboard', 'edit_posts', self::get_plugin_path().'/decision_tree_dashboard.php' );
        
        //add the add new page
        add_submenu_page(self::get_plugin_path().'/decision_tree_dashboard.php', 'Add New', 
                'Add New', 'edit_posts', 'post-new.php?post_type=decision_tree' );
        
        //add the instructions page
        add_submenu_page(self::get_plugin_path().'/decision_tree_dashboard.php', 'Instructions', 
                'Instructions', 'edit_posts', self::get_plugin_path().'/decision_tree_instructions.php' );      
        }
    
   /**
     * light wrapper to get donation setting from DB.  
     * @return string of donation status of "1" or "0"
     */
    static function get_donation_status(){
        $status = get_option('dt_idonated_setting');

        // make sure we have a "0" or a "1"
        if ( $status !== '1'){
            $status = '0';
        }
        
        return $status;
    }
    
   /**
     * light wrapper to get track setting from DB.  
     * @return string of track status of "1" or "0"
     */
    static function get_track_status(){
        $status = get_option('dt_track_setting');

        // make sure we have a "0" or a "1"
        if ( $status !== '1'){
            $status = '0';
        }
        
        return $status;
    }    
    
    static function get_html_comment_shoutout(){
        return "
        <!--
            __            __       __                  __                         __                     __    __                         __         
        .--|  .-----.----|__.-----|__.-----.-----.    |  |_.----.-----.-----.    |  |--.--.--.    .-----|__.--|  .-----.----.---.-.----. |  |_.--.--.
        |  _  |  -__|  __|  |__ --|  |  _  |     |    |   _|   _|  -__|  -__|    |  _  |  |  |    |__ --|  |  _  |  -__|  __|  _  |   ___|   _|  |  |
        |_____|_____|____|__|_____|__|_____|__|__|    |____|__| |_____|_____|    |_____|___  |    |_____|__|_____|_____|____|___._|__||__|____||___/ 
                                                                                       |_____|                                                       
                    ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||                         
                    ||||This is decision tree! A plugin for WordPress.  Find out more at http://sidecar.tv/decision_tree||||                         
                    ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||                                -->";
    }
    
    /**
      * Runs on activation of the plugin in the UL
      */
    static function on_activation() {
         if(!get_option('install_UUID')){
            update_option('install_UUID', self::generate_UUID() );
         }
         sidecar_track::track_event('installation', 'activate');
     }    
     
    /**
      * Runs on delete of the plugin from the UI
      */
     static function on_uninstall(){
         sidecar_track::track_event('installation', 'uninstall');
     }
     
     /**
      * Runs on deativation of the plugin
      */
     static function on_deactivate(){
         sidecar_track::track_event('installation', 'deactivate');
     }
     
    /**
      * Runs on change to the i donated setting
      */
     static function on_update_idonated($new_val, $old_val){
         if($new_val=='1'){
            sidecar_track::track_event('donation_attribution', 'deactivated');
         }
         if($new_val=='0'){
           sidecar_track::track_event('donation_attribution', 'activated');
         }
         return $new_val;
      }
        
          
     /**
      * generate a UUID for this installation of the app
      * @via Andrew Moore http://www.php.net/manual/en/function.uniqid.php#94959
      */
    static function generate_UUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
     
    /**
     * Set the decision tree columns
     */
     static function list_dt_list_columns( $defaults ) {
        $defaults['author'] = 'Added By';
        return $defaults;
     }
     
     /**
      * Runs on admin page view, currently just fires for listing of all trees
      */
     static function admin_page_notice(){
        $screen = get_current_screen();
        if( 'decision_tree' == $screen->post_type
            && 'edit' == $screen->base ){
                $post_count = wp_count_posts('decision_tree');
                sidecar_track::track_event('list_tree', 'count', 'published', $post_count->publish);
        }
     }
     
     static function get_plugin_path(){
         return dirname(plugin_basename(__FILE__));
     }
}
add_action('admin_menu',   array('DecisionTree', 'register_menu_items'), 8);
add_action('init', array('DecisionTree', 'create_post_type'));
add_shortcode( 'decisiontree',  array('DecisionTree', 'decision_tree_shortcode'));
add_action( 'wp_enqueue_scripts', array('DecisionTree', 'load_styles') );
add_action( 'admin_init', array('DecisionTree', 'load_admin_scripts') );
add_action( 'save_post', array('DecisionTree', 'save_metabox') );
add_filter( 'mce_external_plugins', array('DecisionTree', 'add_buttons') );
add_filter( 'mce_buttons', array('DecisionTree', 'register_buttons') );
add_action( 'admin_print_footer_scripts', array('DecisionTree','add_quicktags' ));
add_action( 'wp_ajax_decision_tree_list_trees',  array('DecisionTree', 'list_trees'));
register_activation_hook( __FILE__, array( 'DecisionTree', 'on_activation' ) );
register_uninstall_hook( __FILE__, array( 'DecisionTree', 'on_uninstall' ) );
register_deactivation_hook( __FILE__, array( 'DecisionTree', 'on_deactivate' ) );
add_filter( 'pre_update_option_dt_idonated_setting', array( 'DecisionTree', 'on_update_idonated' ) );
add_filter('manage_decision_tree_posts_columns',  array( 'DecisionTree', 'list_dt_list_columns' ));
add_action( 'admin_notices', array( 'DecisionTree', 'admin_page_notice' ));



?>
