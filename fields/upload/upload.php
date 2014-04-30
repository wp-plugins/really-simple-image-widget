<?php
function really_simple_image_widget_upload_enqueue($hook)
{
  if( $hook != 'widgets.php' )
      return;
  wp_enqueue_style('thickbox');
  wp_enqueue_script('media-upload');
  wp_enqueue_script('thickbox');
  $js_url =plugins_url( 'upload.js', __FILE__ );
  wp_enqueue_script('really-simple-image-widget-upload-script', $js_url , null, null, true);
}
add_action('admin_enqueue_scripts', 'really_simple_image_widget_upload_enqueue');
