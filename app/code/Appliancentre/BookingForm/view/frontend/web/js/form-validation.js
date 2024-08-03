define('Appliancentre_BookingForm/js/form-validation', ['jquery'], function($) {
    'use strict';

    function isValidEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    function validateStep(currentStep, errorContainer) {
        var errors = [];
        var isLandlord = $('input[name="landlordAgent"]:checked').val() === 'yes';

        if (currentStep === 1) {
            if (!$('input[name="service"]:checked').val()) errors.push('Please select a service type.');
            if (!$('#customer_postcode').val()) errors.push('Please enter your postcode.');
            if (!$('#applianceType').val()) errors.push('Please select an appliance type.');
            if (!$('input[name="applianceSubtype"]:checked').val()) errors.push('Please select an appliance subtype.');
            if (!$('#applianceMake').val()) errors.push('Please select an appliance make.');
        } else if (currentStep === 3) {
            if (!$('#visitDate').val()) errors.push('Please select a preferred date.');
            if (!$('#visitTime').val()) errors.push('Please select a preferred time.');
        } else if (currentStep === 4) {
            if (isLandlord) {
                // Validate Tenant details
                if (!$('#tenant_title').val()) errors.push('Please select a title for the tenant.');
                if (!$('#tenant_firstname').val()) errors.push('Please enter the tenant\'s first name.');
                if (!$('#tenant_lastname').val()) errors.push('Please enter the tenant\'s last name.');
                if (!$('#tenant_email').val() || !isValidEmail($('#tenant_email').val())) errors.push('Please enter a valid email address for the tenant.');
                if (!$('#tenant_phone').val()) errors.push('Please enter the tenant\'s phone number.');
                if (!$('#tenant_postcode').val()) errors.push('Please enter the tenant\'s postcode.');
                if (!$('#tenant_address1').val()) errors.push('Please enter the tenant\'s address.');

                // Validate Landlord details
                if (!$('#landlord_title').val()) errors.push('Please select a title for the landlord.');
                if (!$('#landlord_firstname').val()) errors.push('Please enter the landlord\'s first name.');
                if (!$('#landlord_lastname').val()) errors.push('Please enter the landlord\'s last name.');
                if (!$('#landlord_email').val() || !isValidEmail($('#landlord_email').val())) errors.push('Please enter a valid email address for the landlord.');
                if (!$('#landlord_phone').val()) errors.push('Please enter the landlord\'s phone number.');
                if (!$('#landlord_postcode').val()) errors.push('Please enter the landlord\'s postcode.');
                if (!$('#landlord_address1').val()) errors.push('Please enter the landlord\'s address.');
            } else {
                // Validate regular customer details
                if (!$('#title').val()) errors.push('Please select a title.');
                if (!$('#firstname').val()) errors.push('Please enter your first name.');
                if (!$('#lastname').val()) errors.push('Please enter your last name.');
                if (!$('#email').val() || !isValidEmail($('#email').val())) errors.push('Please enter a valid email address.');
                if (!$('#phone').val()) errors.push('Please enter your phone number.');
                if (!$('#postcode').val()) errors.push('Please enter your postcode.');
                if (!$('#address1').val()) errors.push('Please enter your address.');
            }

            if (!$('#faultDescription').val()) errors.push('Please provide a fault description.');
            if (!$('#termsConditions').is(':checked')) errors.push('Please accept the Terms & Conditions.');
        }

        if (errors.length > 0) {
            errorContainer.html('');
            errors.forEach(function(error) {
                errorContainer.append('<p>' + error + '</p>');
            });
            errorContainer.show();
            return false;
        }

        errorContainer.hide().html('');
        return true;
    }

    return {
        validateStep: validateStep,
        validateForm: validateStep // For the final step, we use the same validation
    };
});