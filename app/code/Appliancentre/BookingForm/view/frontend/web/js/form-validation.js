define('Appliancentre_BookingForm/js/form-validation', ['jquery'], function($) {
    'use strict';

    function isValidEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    function validateField($field) {
        var isValid = true;
        var errorMessage = '';

        if ($field.prop('required') && !$field.val()) {
            isValid = false;
            errorMessage = getCustomErrorMessage($field, 'required');
        } else if ($field.attr('type') === 'email' && !isValidEmail($field.val())) {
            isValid = false;
            errorMessage = 'Please enter a valid email address.';
        }

        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            $field.next('.invalid-feedback').remove();
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            if (!$field.next('.invalid-feedback').length) {
                $field.after('<div class="invalid-feedback">' + errorMessage + '</div>');
            }
        }

        return isValid;
    }

    function getCustomErrorMessage($field, errorType) {
        var fieldName = $field.attr('name');
        var customMessages = {
            'service': 'Please select a service type.',
            'postcode': 'Please enter your postcode.',
            'applianceType': 'Please select an appliance type.',
            'applianceSubtype': 'Please select an appliance subtype.',
            'applianceMake': 'Please select an appliance make.',
            'visitDate': 'Please select a preferred date.',
            'visitTime': 'Please select a preferred time.',
            'title': 'Please select a title.',
            'firstname': 'Please enter your first name.',
            'lastname': 'Please enter your last name.',
            'email': 'Please enter a valid email address.',
            'phone': 'Please enter your phone number.',
            'address1': 'Please enter your address.',
            'faultDescription': 'Please provide a fault description.',
            'termsConditions': 'Please accept the Terms & Conditions.'
        };

        return customMessages[fieldName] || 'This field is required.';
    }

    function validateStep(currentStep, errorContainer) {
        var isValid = true;
        var errors = [];
        var $fields = $('#step' + currentStep + ' :input:not(:button)');

        $fields.each(function() {
            var $field = $(this);
            if (!validateField($field)) {
                isValid = false;
                errors.push(getCustomErrorMessage($field, 'required'));
            }
        });

        if (!isValid) {
            errorContainer.html('');
            errors.forEach(function(error) {
                errorContainer.append('<p>' + error + '</p>');
            });
            errorContainer.show();
        } else {
            errorContainer.hide().html('');
        }

        return isValid;
    }

    // Add real-time validation
    $('form').on('blur change', ':input', function() {
        validateField($(this));
    });

    return {
        validateStep: validateStep,
        validateForm: validateStep // For the final step, we use the same validation
    };
});
