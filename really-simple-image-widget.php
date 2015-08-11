<?php
/*
Plugin Name: Really Simple Image Widget
Plugin URI: http://wordpress.org/plugins/really-simple-image-widget/
Description: Easiest way to add image in your sidebar
Author: Nilambar Sharma
Author URI: http://www.nilambar.net
Version: 1.2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'REALLY_SIMPLE_WIDGET_NAME', 'Really Simple Image Widget' );
define( 'REALLY_SIMPLE_WIDGET_SLUG', 'really-simple-image-widget' );

// For Upload field
require_once( __DIR__ . "/fields/upload/upload.php" );

class Really_Simple_Image_Widget  extends WP_Widget {

    function __construct() {

        // Load textdomain for translation
        load_plugin_textdomain( 'really-simple-image-widget' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Widget Options
        $opts =array(
          'classname'     => 'really_simple_image_widget',
          'description'   => __( 'Easiest way to add image in your sidebar', 'really-simple-image-widget' )
        );

        parent::__construct( 'really-simple-image-widget', __( 'Really Simple Image Widget', 'really-simple-image-widget' ), $opts );
    }


    function widget( $args, $instance ) {

        extract( $args , EXTR_SKIP );

        $title            = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $rsiw_image_url     = ! empty( $instance['rsiw_image_url'] ) ? $instance['rsiw_image_url'] : '';
        $rsiw_image_width   = ! empty( $instance['rsiw_image_width'] ) ? $instance['rsiw_image_width'] : '';
        $rsiw_image_height  = ! empty( $instance['rsiw_image_height'] ) ? $instance['rsiw_image_height'] : '';
        $rsiw_link          = ! empty( $instance['rsiw_link'] ) ? $instance['rsiw_link'] : '';
        $rsiw_alt_text      = ! empty( $instance['rsiw_alt_text'] ) ? $instance['rsiw_alt_text'] : '';
        $rsiw_open_link     = ! empty( $instance['rsiw_open_link'] ) ? $instance['rsiw_open_link'] : false;
        $rsiw_image_caption = ! empty( $instance['rsiw_image_caption'] ) ? $instance['rsiw_image_caption'] : '';
        $rsiw_disable_link_in_title     = ! empty( $instance['rsiw_disable_link_in_title'] ) ? $instance['rsiw_disable_link_in_title'] : false;
        $rsiw_disable_link_in_caption     = ! empty( $instance['rsiw_disable_link_in_caption'] ) ? $instance['rsiw_disable_link_in_caption'] : false;

        $instance['link_open'] = '';
        $instance['link_close'] = '';
        if ( ! empty ( $rsiw_link ) ) {
          $target                 = ( empty( $rsiw_open_link ) ) ? '' : ' target="_blank" ';
          $instance['link_open']  = '<a href="' . esc_url( $rsiw_link ) . '"' . $target . '>';
          $instance['link_close'] = '</a>';
        }

        echo $before_widget;

        if ( $title ) {
          echo $before_title ;
          if ( $rsiw_disable_link_in_title ) {
            echo sprintf( '%s',
              $title
            );
          }
          else{
            echo sprintf( '%s%s%s',
              $instance['link_open'],
              $title,
              $instance['link_close']
            );
          }
          echo $after_title ;
        }

        if (!empty($rsiw_image_url)) {

          $sizes = array();

          $alt_text = ( ! empty( $rsiw_alt_text ) ) ? $rsiw_alt_text : basename($rsiw_image_url);
          $dimension_text = '';
          if ( ! empty( $rsiw_image_width ) ) {
            $dimension_text .= ' width="' . esc_attr( $rsiw_image_width ) . '" ';
          }
          if ( ! empty( $rsiw_image_height ) ) {
            $dimension_text .= ' height="' . esc_attr( $rsiw_image_height ) . '" ';
          }

          $imgtag = '<img src="' . esc_url( $rsiw_image_url ) . '" alt="' . esc_attr( $alt_text ) . '" ' . $dimension_text . ' />';

          echo '<div class="image-wrapper">';
          echo sprintf( '<div class="rsiw-image" %s>%s%s%s</div>',
            ' style="max-width:100%;"',
            $instance['link_open'],
            $imgtag,
            $instance['link_close']
          );
          if (!empty($rsiw_image_caption)) {
            if ( $rsiw_disable_link_in_caption ) {
              echo sprintf( '<div class="rsiw-image-caption">%s</div>',
                $rsiw_image_caption
              );
            }
            else{
              echo sprintf( '<div class="rsiw-image-caption">%s%s%s</div>',
                $instance['link_open'],
                $rsiw_image_caption,
                $instance['link_close']
              );
            }
          }
          echo '</div>';

        } //end if : image is there

        echo $after_widget;

    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']                        = strip_tags( stripslashes($new_instance['title']) );
        $instance['rsiw_image_url']               = esc_url( $new_instance['rsiw_image_url'] );
        $instance['rsiw_image_width']             = esc_attr( $new_instance['rsiw_image_width'] );
        $instance['rsiw_image_height']            = esc_attr( $new_instance['rsiw_image_height'] );
        $instance['rsiw_link']                    = esc_url( $new_instance['rsiw_link'] );
        $instance['rsiw_alt_text']                = esc_attr( $new_instance['rsiw_alt_text'] );
        $instance['rsiw_open_link']               = isset( $new_instance['rsiw_open_link'] );
        $instance['rsiw_disable_link_in_title']   = isset( $new_instance['rsiw_disable_link_in_title'] );
        $instance['rsiw_disable_link_in_caption'] = isset( $new_instance['rsiw_disable_link_in_caption'] );
        if ( current_user_can( 'unfiltered_html' ) ){
          $instance['rsiw_image_caption'] =  $new_instance['rsiw_image_caption'];
        }
        else{
          $instance['rsiw_image_caption'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['rsiw_image_caption']) ) );
        }

        return $instance;
    }

    function form( $instance ) {
        //Defaults
        $instance = wp_parse_args( (array) $instance, array(
          'title'                        =>  '',
          'rsiw_image_url'               =>  '',
          'rsiw_image_width'             =>  '',
          'rsiw_image_height'            =>  '',
          'rsiw_link'                    =>  '',
          'rsiw_alt_text'                =>  '',
          'rsiw_open_link'               =>  0,
          'rsiw_image_caption'           =>  '',
          'rsiw_disable_link_in_title'   =>  0,
          'rsiw_disable_link_in_caption' =>  0,
          ) );

        $title                        = htmlspecialchars( $instance['title'] );
        $rsiw_image_url               = esc_url( $instance['rsiw_image_url'] );
        $rsiw_image_width             = esc_attr( $instance['rsiw_image_width'] );
        $rsiw_image_height            = esc_attr( $instance['rsiw_image_height'] );
        $rsiw_link                    = esc_url( $instance['rsiw_link'] );
        $rsiw_alt_text                = esc_attr( $instance['rsiw_alt_text'] );
        $rsiw_open_link               = esc_attr( $instance['rsiw_open_link'] );
        $rsiw_image_caption           = esc_textarea( $instance[ 'rsiw_image_caption' ] );
        $rsiw_disable_link_in_title   = esc_attr( $instance['rsiw_disable_link_in_title'] );
        $rsiw_disable_link_in_caption = esc_attr( $instance['rsiw_disable_link_in_caption'] );
?>
    <style>
      .rsiw-preview-wrap{
        margin-top: 10px;
      }
    </style>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'really-simple-image-widget' ); ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title ; ?>" />
    </p>

    <div>
      <label for="<?php echo $this->get_field_id( 'rsiw_image_url' ); ?>"><?php _e( 'Image URL', 'really-simple-image-widget' ); ?></label>:<br />
      <input type="text" class="img widefat" name="<?php echo $this->get_field_name( 'rsiw_image_url' ); ?>" id="<?php echo $this->get_field_id( 'rsiw_image_url' ); ?>" value="<?php echo $rsiw_image_url; ?>" /><br />
      <input type="button" class="select-img button button-primary" value="<?php _e( 'Upload', 'really-simple-image-widget' ); ?>" data-uploader_title="<?php _e( 'Select Image', 'really-simple-image-widget' ); ?>" data-uploader_button_text="<?php _e( 'Choose Image', 'really-simple-image-widget' ); ?>" style="margin-top:5px;" />

      <?php
        $full_image_url = '';
        if (! empty( $rsiw_image_url ) ){
          $full_image_url = $rsiw_image_url;
        }
        $wrap_style = '';
        if ( empty( $full_image_url ) ) {
          $wrap_style = ' style="display:none;" ';
        }
      ?>
      <div class="rsiw-preview-wrap" <?php echo $wrap_style; ?>>
        <img src="<?php echo esc_url( $full_image_url ); ?>" alt="<?php _e('Preview', 'really-simple-image-widget'); ?>" style="max-width: 100%;"  />
      </div><!-- .rsiw-preview-wrap -->

    </div>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_image_width' ); ?>"><?php _e( 'Image Width', 'really-simple-image-widget' ); ?>:</label>
        <input id="<?php echo $this->get_field_id( 'rsiw_image_width' ); ?>"
        name="<?php echo $this->get_field_name( 'rsiw_image_width' ); ?>" type="text" value="<?php echo $rsiw_image_width; ?>" style="max-width:60px;"/>&nbsp;<em class="small"><?php _e( 'in pixel', 'really-simple-image-widget' ); ?></em>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_image_height' ); ?>"><?php _e( 'Image Height', 'really-simple-image-widget' ); ?>:</label>
        <input id="<?php echo $this->get_field_id( 'rsiw_image_height' ); ?>"
        name="<?php echo $this->get_field_name( 'rsiw_image_height' ); ?>" type="text" value="<?php echo $rsiw_image_height; ?>" style="max-width:60px;"/>&nbsp;<em class="small"><?php _e( 'in pixel', 'really-simple-image-widget' ); ?></em>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_alt_text' ); ?>"><?php _e( 'Alt Text', 'really-simple-image-widget' ); ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'rsiw_alt_text' ); ?>"
        name="<?php echo $this->get_field_name( 'rsiw_alt_text' ); ?>" type="text" value="<?php echo $rsiw_alt_text; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_link' ); ?>"><?php _e( 'Link', 'really-simple-image-widget' ); ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'rsiw_link' ); ?>"
        name="<?php echo $this->get_field_name( 'rsiw_link' ); ?>" type="text" value="<?php echo $rsiw_link; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_open_link' ); ?>"><?php _e( 'Open in New Window', 'really-simple-image-widget' ); ?>:</label>
      <input id="<?php echo $this->get_field_id( 'rsiw_open_link' ); ?>" name="<?php echo $this->get_field_name( 'rsiw_open_link' ); ?>" type="checkbox" <?php checked( isset( $instance['rsiw_open_link'] ) ? $instance['rsiw_open_link'] : 0 ); ?> />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_disable_link_in_title' ); ?>"><?php _e( 'Disable Link in Title', 'really-simple-image-widget' ); ?>:</label>
      <input id="<?php echo $this->get_field_id( 'rsiw_disable_link_in_title' ); ?>" name="<?php echo $this->get_field_name( 'rsiw_disable_link_in_title' ); ?>" type="checkbox" <?php checked( isset( $instance['rsiw_disable_link_in_title'] ) ? $instance['rsiw_disable_link_in_title'] : 0 ); ?> />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_disable_link_in_caption' ); ?>"><?php _e( 'Disable Link in Caption', 'really-simple-image-widget' ); ?>:</label>
      <input id="<?php echo $this->get_field_id( 'rsiw_disable_link_in_caption' ); ?>" name="<?php echo $this->get_field_name( 'rsiw_disable_link_in_caption' ); ?>" type="checkbox" <?php checked( isset( $instance['rsiw_disable_link_in_caption'] ) ? $instance['rsiw_disable_link_in_caption'] : 0 ); ?> />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'rsiw_image_caption' ); ?>"><?php _e( 'Caption', 'really-simple-image-widget' ); ?>:</label>
      <textarea class="widefat" rows="2" id="<?php echo $this->get_field_id( 'rsiw_image_caption' ); ?>" name="<?php echo $this->get_field_name( 'rsiw_image_caption' ); ?>"><?php echo $rsiw_image_caption; ?></textarea>
    </p>

<?php }

} // end class

/**
  * Register  widget.
  *
  * Calls 'widgets_init' action after widget has been registered.
  */
function really_simple_image_widget_register() {

  register_widget('Really_Simple_Image_Widget');

}
add_action( 'widgets_init', 'really_simple_image_widget_register');
