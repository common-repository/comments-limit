<?php
/*
Plugin Name: Limit Comments
Plugin URI: http://www.userw.com/
Description: Limit Comments helps to limit the no. of comments that appear on wordpress posts.
Version: 2.0
Author: Wordpress Plugins
Author URI: http://www.userw.com/
*/

/*
All functions are named with the prefix "Limit Comments"
*/

global $wpdb;

 /*
 
/* Limit Comments CONSTANTS */
if (!defined('LIMIT_COMMENTS_VERSION')) {
  define('LIMIT_COMMENTS_VERSION', "1.0");  
}

if (!defined('LIMIT_COMMENTS_DIR')) {
	define('LIMIT_COMMENTS_DIR', WP_PLUGIN_DIR . '/' . "comments-limit/");
	}


/* During plugin installation store Limit Comments version */
if (!function_exists(limit_comments_install)) {
    function limit_comments_install() {
      /* Store Limit Comments Version */
      add_option("limit_comments_version", LIMIT_COMMENTS_VERSION);
      }
    }
    
    
/* Remove all data stored by Limit Comments during plugin deactivation */

if (!function_exists(limit_comments_uninstall)) {
    function limit_comments_uninstall() {
    /* Remove Limit Comments Version stored in wp options */
     delete_option("limit_comments_version");
     
     /* Delete option used for storing Master Comments Limit */
     delete_option("limit_comments_master");
     
     
     /* Delete Post ID options */
     global $wpdb;
     $query2 = "SELECT `ID`, `post_title`, `comment_count` FROM " . $wpdb->prefix . "posts WHERE `post_status` = 'publish' AND `post_type` = 'post' ORDER BY `post_title` ASC";
$result2 = $wpdb->get_results($query2, ARRAY_A);
$rows2 = $wpdb->num_rows;
      if ($rows2 > 0) {
      foreach ($result2 as $row) {
      $post_id = $row['ID'];
      delete_option("limit_comments_{$post_id}");
      }
      }
     
      }
    }
	
/* Add Admin Settings page as Menu */
if (!function_exists(limit_comments_settings)) {
    function limit_comments_settings() {
     add_menu_page("Limit Comments Settings", "Limit Comments", "administrator", "limit-comments-settings-page", "limit_comments_show_settings");
      }
    }
    
/* Settings Page for Limit Comments Plugin */
if (!function_exists(limit_comments_show_settings)) {
    function limit_comments_show_settings() {
      require(LIMIT_COMMENTS_DIR . "includes/main.php");
      }
    }
    
/* Limit the no. of comments as specified by the Admin */
if (!function_exists(limit_comments_mod)) {
    function limit_comments_mod($posts) {
    
    $i = 0;
    foreach ($posts as $post_info) {
      
      /* Find if there is a limit for this Post */
      $get_limit4comment = get_option("limit_comments_{$post_info->ID}");
      $get_master_limit_comment = get_option("limit_comments_master");
      
      if ($get_limit4comment === FALSE || $get_limit4comment < 1) {
      
      /* Apply Master Limit Comment */
      if (($posts[$i]->comment_count >= $get_master_limit_comment) && ($get_master_limit_comment !== FALSE || $get_master_limit_comment > 0)) {
        $posts[$i]->comment_status = 'closed';
      
        }
      }
        elseif($posts[$i]->comment_count < $get_limit4comment) {
        /* do nothing */
        }
        elseif($posts[$i]->comment_count >= $get_limit4comment) {
        $posts[$i]->comment_status = 'closed';
        }
        else {
        // do nothing
        }
        
           $i++;
        return $posts;
      
      }
    
    
    }        
      }
        

/* Check Comment posting */
if (!function_exists(comment_posting_checker)) {
    function comment_posting_checker($commentdata) {
      
      /* Find if there is a limit for this Post */
      $get_limit4comment = get_option("limit_comments_{$commentdata[comment_post_ID]}");
      $get_master_limit_comment = get_option("limit_comments_master");
      
      /* Find no. of comments for this Post ID */
      $current_comment_count = get_comments_number($commentdata[comment_post_ID]); 
      
      if ($get_limit4comment === FALSE || $get_limit4comment < 1) {
      
      /* Apply Master Limit Comment */
      if (($current_comment_count >= $get_master_limit_comment) && ($get_master_limit_comment !== FALSE || $get_master_limit_comment > 0)) {
        wp_die("Comments are closed for this post");
      
        }
      }
        elseif($current_comment_count < $get_limit4comment) {
        /* do nothing */
        }
        elseif($current_comment_count >= $get_limit4comment) {
          wp_die("Comments are closed for this post");
        }
        else {
        // do nothing
        }
        
          
        return $commentdata;
    }
  }

        
/* Add meta box for Edit post panel */
if (!function_exists(limit_comments_add_custom_box)) {
    function limit_comments_add_custom_box() {
      if (!empty($_GET['post'])) {
      add_meta_box('limit_comments_meta_box', 'Limit Comments', 'limit_comments_meta_box_show', 'post', 'normal', 'high');
      }
    }
  }


/* Meta Box Display */
if (!function_exists(limit_comments_meta_box_show)) {
    function limit_comments_meta_box_show($post) {
      require(LIMIT_COMMENTS_DIR . "includes/show-meta-box.php");
      }
    }
    
/* Capture Post Data */
if (!function_exists(limit_comments_save_postdata)) {
    function limit_comments_save_postdata($post_id) {
      
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
      # echo ("doing autosave");
      return;
      }

    if (!wp_verify_nonce($_POST['limit_comments_noncename'], LIMIT_COMMENTS_DIR)) {
      # echo ("Nonce key failed");
      return;
      }

    if ('post' == $_POST['post_type']) 
    {
    if (!current_user_can('edit_post', $post_id)) {
        # echo ("User does not have proper permission to edit this post");
        return;
        }
    }

    
    $limit_count = $_POST['limit_count'];
    # echo ("Limit count is<br />");
    
  
    foreach ($limit_count as $key=>$value) {
    /* See if we already have set a wordpress option */
    $check_option = get_option("limit_comments_{$key}");
    if ($check_option === FALSE) {
      add_option("limit_comments_{$key}", "$value");
    }
      else {
        update_option("limit_comments_{$key}", "$value");
      }
    }

  
      }
    }    

        
/* Enqueue CSS File */

if (!function_exists(limit_comments_add_styles)) {
    function limit_comments_add_styles() {
      if ("limit-comments-settings-page" != $_GET['page']) {
      return;
      }
      else {
        $style_handle = "limit-comments-css";
        $style_src = WP_PLUGIN_URL . "/comments-limit/css/default.css";
        wp_register_style($style_handle, $style_src);
        wp_enqueue_style($style_handle);
        }
      }
    }    
    
/* Installation and deinstallation hooks */
register_activation_hook(__FILE__, 'limit_comments_install');

register_deactivation_hook(__FILE__, 'limit_comments_uninstall');

/*
Hook for adding Admin menu
*/
add_action('admin_menu', 'limit_comments_settings');

/* Hook for including Stylesheet */

add_action('admin_print_styles', 'limit_comments_add_styles');

/* Hook for limiting the comments count */
add_filter('the_posts', 'limit_comments_mod', 1, 1);

/* Hook for Meta Box in edit post panel */
add_action('add_meta_boxes', 'limit_comments_add_custom_box');

// backwards compatible (before WP 3.0)
add_action('admin_init', 'limit_comments_add_custom_box', 1);


/* Capture Limit Comments value when post is updated */
add_action('save_post', 'limit_comments_save_postdata');

/* Spammers still post comments even if the Add Comment/ Leave Reply Box is not shown hence check all comments during comment posting */
add_filter('preprocess_comment', 	'comment_posting_checker', 10, 1);

?>