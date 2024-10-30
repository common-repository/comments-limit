<?php
$check_option = get_option("limit_comments_{$post->ID}");
  if ($check_option === FALSE) {
      $show_value = 0;
    }
      else {
        $show_value = $check_option;
      }
// Using nonce for verification
wp_nonce_field(LIMIT_COMMENTS_DIR, 'limit_comments_noncename');


echo ("<strong>Max. no. of Comments to allow: </strong><input type='text' name='limit_count[$post->ID]' size='3' maxlength='4' value='$show_value' />");

?>