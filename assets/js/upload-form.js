jQuery(document).ready(($) => {
  
  enableFileUpload();

  buttonShowHideEvent()

  let siteConstants = {fileUploadRequest: false};
  fileUploadHandler(siteConstants);

  addToCartHandler();

  densityChangeEvent();
})


/**
 * this method enables the file upload button
 */
const enableFileUpload = () => {
  jQuery('input#file_upload_input').removeAttr('disabled')
}
/**
 * this method resets the file upload form parameters when the file input is changed
 */
const buttonShowHideEvent = () => {
  jQuery('#file_upload_input').on('input', (e) => {
    jQuery('#progress_bar').val(0);
    jQuery('#upload_button').removeClass('hide');

    jQuery('#ads_add_to_cart').addClass('hide');
    jQuery('#stl_estimation').addClass('hide');
    jQuery('#ads_view_cart').addClass('hide');
    jQuery('#file_upload_loader').addClass('hide');
  });
}


/**
 * this method registers an event on the form submission and calls the file upload handler
 * @param siteConstants
 */
const fileUploadHandler = (siteConstants) => {
  jQuery('#file_upload_form').on('submit', (e) => {
    jQuery('#file_upload_loader').removeClass('hide');
    e.preventDefault();
    const uploadedFile = jQuery(document).find('input[type="file"]')[0].files[0];
    if (uploadedFile === undefined) {
      jQuery('#estimation').html('<p>Please Upload a File!</p>');
    } else {
      if (siteConstants.fileUploadRequest) {
        siteConstants.fileUploadRequest.abort();
      }
      jQuery('#upload_button').addClass('hide');

      siteConstants.fileUploadRequest = jQuery.ajax(prepareUploadFileAjaxSettings(uploadedFile));
    }

  });

}

/**
 * this method prepares the settings object to make the file upload ajax call
 * @param uploadedFile
 * @returns {{processData: boolean, xhr: (function(): XMLHttpRequest), data: FormData, success: *, type: string, error: *, contentType: boolean, url: *}}
 */
const prepareUploadFileAjaxSettings = (uploadedFile) => {
  return {
    type: 'POST',
    url: frontend_ajax.ajaxURL, // Use the WordPress AJAX endpoint
    data: getUploadFormInputs(uploadedFile),
    processData: false,
    contentType: false,
    xhr: function () {
      return showFileUploadProgress();
    },
    success: function (response) {
      toggleFieldsOnUploadSuccess(response);
    },
    error: function () {
      jQuery('#stl_estimation').html('An error occurred during file upload.');
    }
  }
}

/**
 * this method prepares the form data object to make the file upload ajax call
 * @param uploadedFile
 * @returns {FormData}
 */
const getUploadFormInputs = (uploadedFile) => {
  let stl_form = new FormData();
  let selectedObject = jQuery('select#infill_density option:selected');

  stl_form.append('file', uploadedFile);
  stl_form.append('action', 'ads_stl_form_submission_handler');
  stl_form.append('infill_density', selectedObject.val() ?? 1);
  stl_form.append('infill_density_label', selectedObject.text() ?? '');
  stl_form.append('layer_height', parseFloat(jQuery('select#layer_height').val()));
  return stl_form;
}


/**
 * this method updates the file upload progress bar
 * @returns {XMLHttpRequest}
 */
const showFileUploadProgress = () => {
  const xhr = new window.XMLHttpRequest();
  xhr.upload.addEventListener('progress', function (e) {
    if (e.lengthComputable) {
      jQuery('#progress_bar').val((e.loaded / e.total) * 100);
    }
  });
  return xhr;
}


/**
 * this method toggles the elements visibility upon the file upload success
 * @param response
 */
const toggleFieldsOnUploadSuccess = (response) => {
  jQuery('#ads_add_to_cart').removeClass('hide');
  jQuery('#stl_estimation').html(response).removeClass('hide');
  jQuery('#file_upload_loader').addClass('hide');
  jQuery('#ads_view_cart').addClass('hide');

}


/**
 * this method registers an event on add to cart button click and makes an ajax call to add the item in the cart
 */
const addToCartHandler = () => {
  jQuery('#ads_add_to_cart').on('click', () => {
    jQuery.ajax(prepareAddToCartAjaxSettings());
  })
}


/**
 * this method prepares the settings object for the ajax call to add the current object in the cart
 * @returns {{processData: boolean, data: FormData, success: *, type: string, error: *, contentType: boolean, url: *}}
 */
const prepareAddToCartAjaxSettings = () => {
  return {
    type: 'POST',
    url: frontend_ajax.ajaxURL, // Use the WordPress AJAX endpoint
    data: prepareAddToCartFormData(),
    processData: false,
    contentType: false,
    success: function (response) {
      jQuery('#ads_add_to_cart').addClass('hide');
      jQuery('#ads_view_cart').removeClass('hide');
      jQuery('#add_to_cart_response').html(response);
    },
    error: function () {
      jQuery('#add_to_cart_response').html('An error occurred while adding the file to cart.');
    }
  }
}

/**
 * this method prepares the form data object for the add to cart ajax call
 * @returns {FormData}
 */
const prepareAddToCartFormData = () => {
  let add_to_cart_form = new FormData();
  add_to_cart_form.append('volume', jQuery('#stl_volume').val());
  add_to_cart_form.append('price', jQuery('#stl_printing_price').val());
  add_to_cart_form.append('file_name', jQuery('#stl_file_name').val());
  add_to_cart_form.append('file_url', jQuery('#stl_file_url').val());
  add_to_cart_form.append('infill_density', jQuery('#stl_infill_density').val());
  add_to_cart_form.append('infill_density_label', jQuery('#stl_infill_density_label').val());
  add_to_cart_form.append('product_id', jQuery('#stl_product_id').val());
  add_to_cart_form.append('printing_time', jQuery('#stl_printing_time').val());
  add_to_cart_form.append('action', 'ads_stl_add_to_cart_handler');
  return add_to_cart_form;
}

/**
 * this method registers an event that triggers on the change in density and makes an ajax call to recalculate
 * the estimate
 */
const densityChangeEvent = () => {
  jQuery('select#infill_density').on('change', () => {
    if (jQuery('#stl_file_name').val() !== undefined) {
      jQuery.ajax(prepareChangeInDensityAjaxSettings());
    }
  })
}


/**
 * this method returns the settings object to make ajax call after change in the density
 * @returns {{processData: boolean, data: FormData, success: *, type: string, error: *, contentType: boolean, url: *}}
 */
const prepareChangeInDensityAjaxSettings = () => {
  return {
    type: 'POST',
    url: frontend_ajax.ajaxURL, // Use the WordPress AJAX endpoint
    data: prepareChangeInDensityFormData(),
    processData: false,
    contentType: false,
    success: function (response) {
      toggleFieldsOnUploadSuccess(response);
    },
    error: function () {
      jQuery('#add_to_cart_response').html('An error occurred during the calculations');
    }
  }
}


/**
 * this method prepares the input array to estimate the price on change of density
 * @returns {FormData}
 */
const prepareChangeInDensityFormData = () => {
  let selectedObject = jQuery('select#infill_density option:selected');
  let add_to_cart_form = new FormData();
  add_to_cart_form.append('file_name', jQuery('#stl_file_name').val());
  add_to_cart_form.append('file_url', jQuery('#stl_file_url').val());
  add_to_cart_form.append('infill_density', selectedObject.val());
  add_to_cart_form.append('infill_density_label', selectedObject.text());
  add_to_cart_form.append('action', 'ads_stl_change_in_density_handler');
  return add_to_cart_form;
}