<?php
/*
Plugin Name: Really Simple Image Widget
Plugin URI: http://www.nilambar.net
Description: Easiest way to add image in your sidebar
Author: Nilambar Sharma
Version: 1.0.0
Author URI: http://www.nilambar.net
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('REALLY_SIMPLE_WIDGET_NAME','Really Simple Image Widget');
define('REALLY_SIMPLE_WIDGET_SLUG','really-simple-image-widget');

// For Upload field
require_once(__DIR__ . "/fields/upload/upload.php");

class Really_Simple_Image_Widget  extends WP_Widget {

    function __construct() {

        // Load textdomain for translation
        load_plugin_textdomain( 'really-simple-image-widget' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Widget Options
        $opts =array(
                    'classname'     => 'really_simple_image_widget',
                    'description'   => __( 'Easiest way to add image in your sidebar', 'really-simple-image-widget' )
                );

        // Control Options
        $control_options = array(
          'width' => '250' //default is 250
          );

        parent::__construct('really-simple-image-widget', __('Really Simple Image Widget', 'really-simple-image-widget'), $opts, $control_options);
    }


    function widget( $args, $instance ) {

        extract( $args , EXTR_SKIP );

        $title            = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $wi_image_url     = ! empty( $instance['wi_image_url'] ) ? $instance['wi_image_url'] : '' ;
        $wi_link          = ! empty( $instance['wi_link'] ) ? $instance['wi_link'] : '' ;
        $wi_open_link     = ! empty( $instance['wi_open_link'] ) ? $instance['wi_open_link'] : false ;
        $wi_image_caption = ! empty( $instance['wi_image_caption'] ) ? $instance['wi_image_caption'] : '';

        $instance['link_open'] = '';
        $instance['link_close'] = '';
        if ( ! empty ( $wi_link ) ) {
          $target                 = ( empty( $wi_open_link ) ) ? '' : ' target="_blank" ';
          $instance['link_open']  = '<a href="' . esc_url( $wi_link ) . '"' . $target . '>';
          $instance['link_close'] = '</a>';
        }

        echo $before_widget;

        if ( $title ) {
          echo $before_title ;
          echo sprintf( '%s%s%s',
            $instance['link_open'],
            $title,
            $instance['link_close']
          );
          echo $after_title ;
        }

        if (!empty($wi_image_url)) {

          $sizes = array();
          $width_text ='';

          if (function_exists('getimagesize')) {
            $sizes = getimagesize($wi_image_url);
          }
          if (!empty($sizes)) {
            if ( isset($sizes[3]) && '' != $sizes[3] ) {
              $width_text = $sizes[3];
            }
          }

          $imgtag = '<img src="' . esc_url( $wi_image_url ) . '" alt="" '.$width_text.' />';

          echo '<div class="image-wrapper">';
          echo sprintf( '<div class="rsiw-image" %s>%s%s%s</div>',
            ' style="max-width:100%"',
            $instance['link_open'],
            $imgtag,
            $instance['link_close']
          );
          if (!empty($wi_image_caption)) {
            echo sprintf( '<div class="rsiw-image-caption">%s%s%s</div>',
              $instance['link_open'],
              $wi_image_caption,
              $instance['link_close']
            );
          }
          echo '</div>';

        } //end if : image is there

        echo $after_widget;

    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']        = strip_tags( stripslashes($new_instance['title']) );
        $instance['wi_image_url'] = esc_url($new_instance['wi_image_url']);
        $instance['wi_link']      = esc_url($new_instance['wi_link']);
        $instance['wi_open_link'] = isset($new_instance['wi_open_link']);
        if ( current_user_can('unfiltered_html') ){
          $instance['wi_image_caption'] =  $new_instance['wi_image_caption'];
        }
        else{
          $instance['wi_image_caption'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['wi_image_caption']) ) );
        }

        return $instance;
    }

    function form( $instance ) {
        //Defaults
        $instance = wp_parse_args( (array) $instance, array(
          'title'            =>  '',
          'wi_image_url'     =>  '',
          'wi_link'          =>  '',
          'wi_open_link'     =>  0,
          'wi_image_caption' =>  '',
          ) );
        $title            =   htmlspecialchars($instance['title']);
        $wi_image_url     =   esc_url($instance['wi_image_url']);
        $wi_link          =   esc_url($instance['wi_link']);
        $wi_open_link     =   esc_attr($instance['wi_open_link']);
        $wi_image_caption =   esc_textarea( $instance[ 'wi_image_caption' ] );
?>
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'really-simple-image-widget'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title ; ?>" />
    </p>

      <p>
          <label for="<?php echo $this->get_field_id('wi_image_url'); ?>"><?php _e('Image:', 'really-simple-image-widget'); ?></label><br />
          <input type="text" class="img" name="<?php echo $this->get_field_name('wi_image_url'); ?>" id="<?php echo $this->get_field_id('wi_image_url'); ?>" value="<?php echo $instance['wi_image_url']; ?>" />
          <input type="button" class="select-img button" value="<?php _e('Upload', 'really-simple-image-widget'); ?>" />
          <?php if (! empty( $wi_image_url ) ): ?>
            <img src="<?php echo $wi_image_url; ?>" alt="<?php _e('Preview', 'really-simple-image-widget'); ?>" style="width:100%; margin-top:5px;" title="<?php _e('Preview', 'really-simple-image-widget'); ?>" />
          <?php endif ?>
      </p>
    <p>
      <label for="<?php echo $this->get_field_id('wi_link'); ?>">
        <?php _e('Link:', 'really-simple-image-widget'); ?>
        <input class="widefat" id="<?php echo $this->get_field_id('wi_link'); ?>"
        name="<?php echo $this->get_field_name('wi_link'); ?>" type="text" value="<?php echo $wi_link; ?>" />
      </label>

    </p>
    <p>
      <label for="<?php echo $this->get_field_id('wi_open_link'); ?>"><?php _e('Open in new window', 'really-simple-image-widget'); ?>
      <input id="<?php echo $this->get_field_id('wi_open_link'); ?>" name="<?php echo $this->get_field_name('wi_open_link'); ?>" type="checkbox" <?php checked(isset($instance['wi_open_link']) ? $instance['wi_open_link'] : 0); ?> />&nbsp;</label>
      </p>

    <p>
      <label for="<?php echo $this->get_field_id('wi_image_caption'); ?>">
      <?php _e('Caption:', 'ns-sample-widget'); ?>
      <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('wi_image_caption'); ?>" name="<?php echo $this->get_field_name('wi_image_caption'); ?>"><?php echo $wi_image_caption; ?></textarea>
      </label>
    </p>



<?php }

}



/**
  * Register  widget.
  *
  * Calls 'widgets_init' action after widget has been registered.
  */
function really_simple_image_widget_register() {
  register_widget('Really_Simple_Image_Widget');
}
add_action( 'widgets_init', 'really_simple_image_widget_register');
