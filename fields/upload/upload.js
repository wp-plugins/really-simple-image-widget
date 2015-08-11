var rsiw_file_frame;

jQuery(function($){
  ////////////////////////////////

      // Uploads

      jQuery(document).on('click', 'input.select-img', function( event ){

        var $this = $(this);

        event.preventDefault();

        var RSIWImage = wp.media.controller.Library.extend({
            defaults :  _.defaults({
                    id:        'rsiw-insert-image',
                    title:      $this.data( 'uploader_title' ),
                    allowLocalEdits: false,
                    displaySettings: true,
                    displayUserSettings: false,
                    multiple : false,
                    library: wp.media.query( { type: 'image' } )
              }, wp.media.controller.Library.prototype.defaults )
        });

        // Create the media frame.
        rsiw_file_frame = wp.media.frames.rsiw_file_frame = wp.media({
          button: {
            text: jQuery( this ).data( 'uploader_button_text' ),
          },
          state : 'rsiw-insert-image',
              states : [
                  new RSIWImage()
              ],
          multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        rsiw_file_frame.on( 'select', function() {

          var state = rsiw_file_frame.state('rsiw-insert-image');
          var selection = state.get('selection');
          var display = state.display( selection.first() ).toJSON();
          var obj_attachment = selection.first().toJSON();
          display = wp.media.string.props( display, obj_attachment );

          var image_field = $this.siblings('.img');
          var imgurl = display.src;

          // Copy image URL
          image_field.val(imgurl);
          // Show in preview
          var image_preview_wrap = $this.siblings('.rsiw-preview-wrap');
          image_preview_wrap.show();
          image_preview_wrap.find('img').attr('src',imgurl);

        });

        // Finally, open the modal
        rsiw_file_frame.open();
      });

  ////////////////////////////////
});
