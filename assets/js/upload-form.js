jQuery(document).ready(($) => {

  buttonShowHideEvent()

  fileUploadHandler();

  addToCartHandler();

})
const buttonShowHideEvent = () => {
  jQuery('#file_upload_input').on('input', (e) => {
    jQuery('#upload_button').removeClass('hide');
    jQuery('#ads_add_to_cart').addClass('hide');
  });
}

const fileUploadHandler = () => {
  jQuery('#file-upload-form').on('submit', (e) => {
    e.preventDefault();
    const uploadedFile = jQuery(document).find('input[type="file"]')[0].files[0];
    if (uploadedFile === undefined) {
      jQuery('#estimation').html('<p>Please Upload a File!</p>');
    } else {
      jQuery('#upload_button').addClass('hide');
      let stl_form = new FormData();
      stl_form.append("file", uploadedFile);
      stl_form.append('action', 'ads_stl_form_submission_handler');

      jQuery.ajax({
        type: 'POST',
        url: frontend_ajax.ajaxURL, // Use the WordPress AJAX endpoint
        data: stl_form,
        processData: false,
        contentType: false,
        success: function (response) {
          jQuery('#ads_add_to_cart').removeClass('hide');
          jQuery('#stl_estimation').html(response);
        },
        error: function () {
          jQuery('#stl_estimation').html('An error occurred during file upload.');
        }
      });
    }

  });

}

const addToCartHandler = () => {
  jQuery('#ads_add_to_cart').on('click', () => {
    let add_to_cart_form = new FormData();
    add_to_cart_form.append('volume', jQuery('#stl_volume').val())
    add_to_cart_form.append('price', jQuery('#stl_printing_price').val())
    add_to_cart_form.append('file_name', jQuery('#stl_file_name').val())
    add_to_cart_form.append('file_path', jQuery('#stl_file_path').val())
    add_to_cart_form.append('file_url', jQuery('#stl_file_url').val())
    add_to_cart_form.append('product_id', jQuery('#stl_product_id').val())
    add_to_cart_form.append('printing_time', jQuery('#stl_printing_time').val())
    add_to_cart_form.append('action', 'stl_add_to_cart_handler')
    jQuery.ajax({
      type: 'POST',
      url: frontend_ajax.ajaxURL, // Use the WordPress AJAX endpoint
      data: add_to_cart_form,
      processData: false,
      contentType: false,
      success: function (response) {
        jQuery('#ads_add_to_cart').removeClass('hide');
        jQuery('#add_to_cart_response').html(response);
      },
      error: function () {
        jQuery('#add_to_cart_response').html('An error while adding the file to cart.');
      }
    });
  })
}