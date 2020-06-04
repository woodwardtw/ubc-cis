<?php 
/*
Plugin Name: UBC CIS 
Plugin URI:  https://github.com/
Description: 
Version:     1.0
Author:      Tom Woodward
Author URI:  https://bionicteaching.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'ubc_cis_load_scripts');

function ubc_cis_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('ubc-cis-main-js', plugin_dir_url( __FILE__) . 'js/ubc-cis-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'ubc-cis-main-css', plugin_dir_url( __FILE__) . 'css/ubc-cis-main.css');
}

 add_action( 'admin_enqueue_scripts', 'ubc_cis_load_admin_style' );
 
function ubc_cis_load_admin_style() {
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_style( 'ubc-cis-admin-css', plugin_dir_url( __FILE__ )  . 'css/ubc-cis-admin.css', false, '1.0.0' );
    wp_enqueue_script('ubc-cis-admin-js', plugin_dir_url( __FILE__) . 'js/ubc-cis-admin.js', $deps, $version, $in_footer); 
}

//CLEAN UP BACKEND FOR USERS 
add_action( 'admin_menu', 'ubc_cis_remove_menus', 999 );
 
function  ubc_cis_remove_menus(){
  $roles = ubc_cis_get_current_user_roles();
  if( in_array("cis_author", $roles) ) {      
      remove_menu_page( 'index.php' );                  //Dashboard
      remove_menu_page( 'jetpack' );                    //Jetpack* 
      //remove_menu_page( 'edit.php' );                   //Posts
      remove_menu_page( 'upload.php' );                 //Media
      //remove_menu_page( 'edit.php?post_type=page' );    //Pages
      remove_menu_page( 'edit-comments.php' );          //Comments
      //remove_menu_page( 'themes.php' );                 //Appearance
      //remove_menu_page( 'plugins.php' );                //Plugins
      //remove_menu_page( 'users.php' );                  //Users
      remove_menu_page( 'tools.php' );                  //Tools
      remove_menu_page( 'options-general.php' );        //Settings
    }
}

//create user type 
function ubc_cis_add_roles_on_plugin_activation() {
    add_role( 'cis_author', 'CIS Author', get_role( 'author' )->capabilities  );
}
register_activation_hook( __FILE__, 'ubc_cis_add_roles_on_plugin_activation' );


function ubc_cis_get_current_user_roles() {
 if( is_user_logged_in() ) {
   $user = wp_get_current_user();
   $roles = ( array ) $user->roles;
   return $roles; // This returns an array
   // Use this to return a single value
   // return $roles[0];
 } else {
 return array();
 }
}


//append content to filter
function ubc_cis_add_content($content){
  global $post;
  $post_id = $post->ID;
  $course_dates = get_field('course_dates',$post_id); // 'our_services' is your parent group
  $course_start_date = $course_dates['course_start_date'];

  return $content . $course_start_date;
}

add_filter( 'the_content', 'ubc_cis_add_content', 1);



//ACF SAVE and LOAD JSON
add_filter('acf/settings/save_json', 'ubc_cis_json_save_point');
 
function ubc_cis_json_save_point( $path ) {
    
    // update path
    $path = plugin_dir_path( __FILE__ )  . 'acf-json';
    // return
    return $path;
    
}


add_filter('acf/settings/load_json', 'ubc_cis_json_load_point');

function ubc_cis_json_load_point( $paths ) {
    
    // remove original path (optional)
    unset($paths[0]);
    
    // append path
    $paths[] = plugin_dir_path( __FILE__ )  . 'acf-json';
    
    
    // return
    return $paths;
    
}




//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

  //print("<pre>".print_r($a,true)."</pre>");
