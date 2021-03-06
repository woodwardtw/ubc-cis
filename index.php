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
      remove_menu_page( 'edit.php?post_type=portfolio' );   // remove that theme menu addition               
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

//remove course posts from blog page 

function ubc_cis_exclude_courses($query) {
  if ( $query->is_home() ) {
    $query->set('cat', '-###'); //REPLACE WITH CAT ID FOR COURSES and then turn on filter
  }
    return $query;
  }
//add_filter('pre_get_posts', 'ubc_cis_exclude_courses');



//THIS AUTO PUTS NEW POSTS IN THE COURSE CATEGORY IF YOU'RE cis_author
add_filter( 'load-post-new.php', 'ubc_cis_auto_cat_new' );
function ubc_cis_auto_cat_new()
{
    $post_type = 'post';
    if ( isset( $_REQUEST['post_type'] ) ) {
        $post_type = $_REQUEST['post_type'];
    }

    // Only do this for posts
    if ( 'post' != $post_type ) {
        return;
    }

    $user = wp_get_current_user();
   if (in_array( 'cis_author', (array) $user->roles )  ) {
        add_action( 'wp_insert_post', 'update_post_terms' );
        return;
    }
  }



 
function update_post_terms( $post_id ) {
  var_dump($post_id);
    $post = get_post( $post_id );
    if ( $post->post_type != 'post' )
        return;

    // add a category

    $newcat  = get_term_by( 'name', 'Course', 'category' );
    wp_set_post_categories( $post_id, $newcat );
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

/* Filter the single_template with our custom function*/
add_filter('single_template', 'ubc_cis_custom_template');

function ubc_cis_custom_template($single) {

    global $post;
    /* Checks for single template by post type */
    if ( $post->post_type == 'course' ) {

        if ( file_exists( plugin_dir_path( __FILE__) . 'single-course.php' ) ) {
            return plugin_dir_path( __FILE__) . 'single-course.php';
        }
    }

    return $single;

}





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



//course custom post type

// Register Custom Post Type course
// Post Type Key: course

function create_course_cpt() {

  $labels = array(
    'name' => __( 'Courses', 'Post Type General Name', 'textdomain' ),
    'singular_name' => __( 'Course', 'Post Type Singular Name', 'textdomain' ),
    'menu_name' => __( 'Course', 'textdomain' ),
    'name_admin_bar' => __( 'Course', 'textdomain' ),
    'archives' => __( 'Course Archives', 'textdomain' ),
    'attributes' => __( 'Course Attributes', 'textdomain' ),
    'parent_item_colon' => __( 'Course:', 'textdomain' ),
    'all_items' => __( 'All Courses', 'textdomain' ),
    'add_new_item' => __( 'Add New Course', 'textdomain' ),
    'add_new' => __( 'Add New', 'textdomain' ),
    'new_item' => __( 'New Course', 'textdomain' ),
    'edit_item' => __( 'Edit Course', 'textdomain' ),
    'update_item' => __( 'Update Course', 'textdomain' ),
    'view_item' => __( 'View Course', 'textdomain' ),
    'view_items' => __( 'View Courses', 'textdomain' ),
    'search_items' => __( 'Search Courses', 'textdomain' ),
    'not_found' => __( 'Not found', 'textdomain' ),
    'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
    'featured_image' => __( 'Featured Image', 'textdomain' ),
    'set_featured_image' => __( 'Set featured image', 'textdomain' ),
    'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
    'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
    'insert_into_item' => __( 'Insert into course', 'textdomain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this course', 'textdomain' ),
    'items_list' => __( 'Course list', 'textdomain' ),
    'items_list_navigation' => __( 'Course list navigation', 'textdomain' ),
    'filter_items_list' => __( 'Filter Course list', 'textdomain' ),
  );
  $args = array(
    'label' => __( 'course', 'textdomain' ),
    'description' => __( '', 'textdomain' ),
    'labels' => $labels,
    'menu_icon' => '',
    'supports' => array('title', 'editor', 'revisions', 'author', 'trackbacks', 'custom-fields', 'thumbnail',),
    'taxonomies' => array(),
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'can_export' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'exclude_from_search' => false,
    'show_in_rest' => true,
    'publicly_queryable' => true,
    'capability_type' => 'post',
    'menu_icon' => 'dashicons-universal-access-alt',
  );
  register_post_type( 'course', $args );
  
  // flush rewrite rules because we changed the permalink structure
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
add_action( 'init', 'create_course_cpt', 0 );


//only show your own posts/pages in admin land
function ubc_cis_posts_for_current_author($query) {
    global $pagenow;
  
    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;
  
    if( !current_user_can( 'activate_plugins' ) ) {
        global $user_ID;
        $query->set('author', $user_ID );
    }
    return $query;
}
add_filter('pre_get_posts', 'ubc_cis_posts_for_current_author');



//show acf in rest
// Enable the option show in rest
add_filter( 'acf/rest_api/field_settings/show_in_rest', '__return_true' );