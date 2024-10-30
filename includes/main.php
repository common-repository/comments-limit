<div id='limit_comments_body'>
<?php
if (!is_admin()) {
  echo ("<div id='error'>
  <h3>Permission Denied</h3>
  <p>You dont have the required permission to view this page</p>
  </div>");
  }
else {
  ?>
  
<h1>Limit Comments - Settings</h1>

<p>This plugin is powered by <a href='http://www.userw.com'>Custom Wordpress Plugins</a> and designed by <a href='http://www.skinzee.com'>Custom Wordpress Themes</a></p>

<p>If you like this plugin, please consider donating whatever you can afford.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="9MR6DLSRJ3JGG"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>
</p>


<?php

if ($_POST['submit'] == "UPDATE") {
  $limit_count = $_POST['limit_count'];
  
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
  
  echo ("<div id='success'>
  <h3>Individual Post Settings Update Result</h3>
  <p>Settings update was successful.<br />The selected posts will allow limited no. of comments as specified.</p>
  </div>");
  }
  
/* Update for Search Posts List */
if ($_POST['submit1'] == "UPDATE") {
  $limit_count1 = $_POST['limit_count1'];
  
  foreach ($limit_count1 as $key=>$value) {
    /* See if we already have set a wordpress option */
    $check_option = get_option("limit_comments_{$key}");
    if ($check_option === FALSE) {
      add_option("limit_comments_{$key}", "$value");
    }
      else {
        update_option("limit_comments_{$key}", "$value");
      }
  }
  
  echo ("<div id='success'>
  <h3>Individual Post Settings Update Result</h3>
  <p>Settings update was successful.<br />The selected posts will allow limited no. of comments as specified.</p>
  </div>");
  }  
  
if ($_POST['master_settings'] == "UPDATE") {
/* Check if already have Master comments settings */
$check_option_master = get_option("limit_comments_master");
  if ($check_option_master === FALSE) {
        add_option("limit_comments_master", "$_POST[master_limits_comment]");
    }
      else {
        update_option("limit_comments_master", "$_POST[master_limits_comment]");
      }

  echo ("<div id='success'>
  <h3>Master Comments Limit Update Result</h3>
  <p>Master Comments Limit was successfully updated</p>
  </div>");

}


if ($_POST['post_search'] == "SEARCH") {
/* Display list of Posts by Post Title */  
$post_title = $_POST['post_title'];

global $wpdb;
$query_posts = "SELECT `ID`, `post_title`, `comment_count` FROM " . $wpdb->prefix . "posts WHERE `post_title` LIKE '%$post_title%' AND `post_type` = 'post' AND `post_status` = 'publish' ORDER BY `post_title` ASC $max";
$result_posts = $wpdb->get_results($query_posts, ARRAY_A);
$rows = $wpdb->num_rows;
if ($rows == 0) {
echo ("
<div id='error'>
<h2>No Posts</h2>
<p>No wordpress posts found matching this Post Title.</p></div>");
}
else {

echo ("
<div id=\"admin_table\">
<h2 class='center'>Search Posts - Result</h2>
<form method='post' action='$_SERVER[PHP_SELF]?page=limit-comments-settings-page' name='settings_form1' id='settings_form1'>
<table align='center' border='1'>
<tr>
<th>Post Title</th><th>Total Comments Count</th><th>Max. no. of Comments to allow</th>
</tr>
");

foreach ($result_posts as $row)
{
$post_id = $row['ID'];
$post_title = $row['post_title'];
$comments_count = $row['comment_count'];

if ($_POST['submit1'] == "UPDATE") {
if ($limit_count1[$post_id] > 0) {
  $show_value = $limit_count1[$post_id];  
}
  else {
    $show_value = 0;  
    }
}
else {
  $check_option = get_option("limit_comments_{$post_id}");
  if ($check_option === FALSE) {
      $show_value = 0;
    }
      else {
        $show_value = $check_option;
      }
  
}
echo ("
<tr>
<td>$post_title</td>
<td class='center'>$comments_count</td>
<td class='center'><input type='text' name='limit_count1[$post_id]' size='3' maxlength='4' value='$show_value' /></td>
</tr>
");
}

echo ("</table>
   <p class='center'><input type='submit' name='submit1' value='UPDATE' /></p>
   </form>
</div>
<hr />");
}
}
  
?>




<form method='post' action='<?php echo("$_SERVER[PHP_SELF]"); ?>?page=limit-comments-settings-page' name='search_posts' id='search_posts'>
<p><strong>Search by Post Title:</strong> <input type='text' name='post_title' value='' size='25' maxlength='' /> &nbsp; <input type='submit' name='post_search' value='SEARCH' /><br />
(supports wildcard Search)</p>
</form>
<hr />



<form method='post' action='<?php echo("$_SERVER[PHP_SELF]"); ?>?page=limit-comments-settings-page' name='master_settings_form' id='master_settings_form'>
<?php
$check_option_master = get_option("limit_comments_master");
  if ($check_option_master === FALSE) {
      $show_master_value = 0;
    }
      else {
        $show_master_value = $check_option_master;
      }

echo ("<p><strong>Master Comments Limit: </strong><input type='text' name='master_limits_comment' size='3' maxlength='4' value='$show_master_value' />&nbsp; <input type='submit' name='master_settings' value='UPDATE' /></p>
");
?>
</form>
<hr />

<p>Please enter the maximum number of comments allowed on respective posts.</p>

<p><strong>Note:</strong> The default value of "0" will allow any number of comments.</p>

<?php
#Pagination start
//This checks to see if there is a page number. If not, it will set it to page 1
$pagenum = $_GET['pagenum'];
if (!(isset($pagenum)))
{
$pagenum = 1;
}

//This is the number of results displayed per page
$page_rows = 25;

//This sets the range to display in our query
$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;

global $wpdb;

$query1 = "SELECT `ID`, `post_title`, `comment_count` FROM " . $wpdb->prefix . "posts WHERE `post_status` = 'publish' AND `post_type` = 'post' ORDER BY `post_title` ASC $max";
$result1 = $wpdb->get_results($query1, ARRAY_A);
$rows = $wpdb->num_rows;

$query2 = "SELECT `ID`, `post_title`, `comment_count` FROM " . $wpdb->prefix . "posts WHERE `post_status` = 'publish' AND `post_type` = 'post' ORDER BY `post_title` ASC";
$result2 = $wpdb->get_results($query2, ARRAY_A);
$rows2 = $wpdb->num_rows;

//This tells us the page number of our last page
$last = ceil($rows2/$page_rows);

//this makes sure the page number isn't below one, or more than our maximum pages
if ($pagenum < 1)
{
$pagenum = 1;
}
elseif ($pagenum > $last)
{
$pagenum = $last;
}



if ($rows == 0)
{
echo ("<div id ='error'>
<h2>No Posts</h2>
<p>No wordpress posts found in publish status.</p>
</div>
");
}
else
{
echo ("<p class=\"center\"><strong>Showing Page # $pagenum of $last (Total No. of Posts: $rows2)</strong>
</p>");

// First we check if we are on page one. If we are then we don't need a link to the previous page or the first page so we do nothing. If we aren't then we generate links to the first page, and to the previous page.
if ($pagenum == 1)
{
echo ("<p class=\"center\">");
}
else
{
echo "<p class=\"center\"><a href='{$_SERVER['PHP_SELF']}?page=limit-comments-settings-page&pagenum=1' class='paginate_links'> << First</a> ";
echo " ";
$previous = $pagenum-1;
echo " <a href='{$_SERVER['PHP_SELF']}?page=limit-comments-settings-page&pagenum=$previous' class='paginate_links'> < Previous</a> ";
}

if ($pagenum > 1 && $pagenum != $last)
{
//just a spacer
echo " ---- ";
}

//This does the same as above, only checking if we are on the last page, and then generating the Next and Last links
if ($pagenum == $last)
{
echo ("</p>");
}
else {
$next = $pagenum+1;
echo " <a href='{$_SERVER['PHP_SELF']}?page=limit-comments-settings-page&pagenum=$next' class='paginate_links'>Next ></a> ";
echo " ";
echo " <a href='{$_SERVER['PHP_SELF']}?page=limit-comments-settings-page&pagenum=$last' class='paginate_links'>Last >></a></p> ";
}

echo ("
<div id=\"admin_table\">
<form method='post' action='$_SERVER[PHP_SELF]?page=limit-comments-settings-page' name='settings_form' id='settings_form'>
<table align='center' border='1'>
<tr>
<th>Post Title</th><th>Total Comments Count</th><th>Max. no. of Comments to allow</th>
</tr>
");

foreach ($result1 as $row)
{
$post_id = $row['ID'];
$post_title = $row['post_title'];
$comments_count = $row['comment_count'];

if ($_POST['submit'] == "UPDATE") {
if ($limit_count[$post_id] > 0) {
  $show_value = $limit_count[$post_id];  
}
  else {
    $show_value = 0;  
    }
}
else {
  $check_option = get_option("limit_comments_{$post_id}");
  if ($check_option === FALSE) {
      $show_value = 0;
    }
      else {
        $show_value = $check_option;
      }
  
}
echo ("
<tr>
<td>$post_title</td>
<td class='center'>$comments_count</td>
<td class='center'><input type='text' name='limit_count[$post_id]' size='3' maxlength='4' value='$show_value' /></td>
</tr>
");
}

echo ("</table>
   <p class='center'><input type='submit' name='submit' value='UPDATE' /></p>
   </form>
</div>");
}
?>  
  



  


  
<?php  
}
?>
</div>