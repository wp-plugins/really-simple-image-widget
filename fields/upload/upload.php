<?php
function really_simple_image_widget_upload_enqueue($hook) {

  if( $hook != 'widgets.php' )
      return;

  wp_enqueue_media();
  $js_url = plugins_url( 'upload.js', __FILE__ );
  wp_enqueue_script( 'really-simple-image-widget-upload-script', $js_url , array( 'jquery' ), '1.1', true );

}
add_action( 'admin_enqueue_scripts', 'really_simple_image_widget_upload_enqueue' );
