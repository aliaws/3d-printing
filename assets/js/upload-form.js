jQuery(document).ready(($) => {
  let siteConstants = {fileUploadRequest: false};
  enableFileUpload();
  buttonShowHideEvent()

  fileUploadHandler(siteConstants);

  addToCartHandler();

  densityChangeEvent();
})

const enableFileUpload = () => {
  jQuery('input#file_upload_input').removeAttr('disabled')
}
const buttonShowHideEvent = () => {
  jQuery('#file_upload_input').on('input', (e) => {
    jQuery('#progressBar').val(0);
    jQuery('#upload_button').removeClass('hide');

    jQuery('#ads_add_to_cart').addClass('hide');
    jQuery('#stl_estimation').addClass('hide');
    jQuery('#ads_view_cart').addClass('hide');
    jQuery('#file_upload_loader').addClass('hide');
  });
}

const fileUploadHandler = (siteConstants) => {
  jQuery('#file-upload-form').on('submit', (e) => {
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
const getUploadFormInputs = (uploadedFile) => {
  let stl_form = new FormData();
  let selectedObject = jQuery('select#infill_density option:selected');
  stl_form.append('file', uploadedFile);
  stl_form.append('action', 'ads_stl_form_submission_handler');
  stl_form.append('infill_density', selectedObject.val() ?? 100);
  stl_form.append('infill_density_label', selectedObject.text() ?? 100);
  return stl_form;
}

const showFileUploadProgress = () => {
  const xhr = new window.XMLHttpRequest();
  xhr.upload.addEventListener('progress', function (e) {
    if (e.lengthComputable) {
      jQuery('#progressBar').val((e.loaded / e.total) * 100);
    }
  });
  return xhr;
}

const toggleFieldsOnUploadSuccess = (response) => {
  jQuery('#ads_add_to_cart').removeClass('hide');
  jQuery('#stl_estimation').html(response).removeClass('hide');
  jQuery('#file_upload_loader').addClass('hide');
  jQuery('#ads_view_cart').addClass('hide');

}

const addToCartHandler = () => {
  jQuery('#ads_add_to_cart').on('click', () => {
    jQuery.ajax(prepareAddToCartAjaxSettings());
  })
}

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

const densityChangeEvent = () => {
  jQuery('select#infill_density').on('change', () => {
    if (jQuery('#stl_file_name').val() !== undefined) {
      jQuery.ajax(prepareChangeInDensityAjaxSettings());
    }
  })
}


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