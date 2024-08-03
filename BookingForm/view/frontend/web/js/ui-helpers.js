define('Appliancentre_BookingForm/js/ui-helpers', ['jquery'], function($) {
    'use strict';
    var currentStep = 1;
    var totalSteps = 4;

    function populateApplianceSubtypes(applianceSubtypes, applianceId) {
        var $applianceType = $('#applianceType_' + applianceId);
        var $applianceSubtype = $applianceType.closest('.appliance-fields').find('.appliance-subtype');
        var applianceType = $applianceType.val();
        var subtypes = applianceSubtypes[applianceType];
        var subtypeHtml = '';

        if (subtypes) {
            subtypeHtml += '<label>Please select your ' + applianceType.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + ' type to continue</label><br>';
            subtypes.forEach(function(subtype) {
                subtypeHtml += '<label><input type="radio" name="appliances[' + applianceId + '][applianceSubtype]" value="' + subtype.toLowerCase().replace(/ /g, '-') + '" required> ' + subtype + '</label><br>';
            });
            $applianceSubtype.html(subtypeHtml).show();
        } else {
            $applianceSubtype.hide();
        }
    }

    function showError(message) {
        var errorContainer = $('#error-messages');
        errorContainer.html('<p>' + message + '</p>');
        errorContainer.show();
    }

    function showErrors(errors, errorContainer) {
        errorContainer.html('');
        errors.forEach(function(error) {
            errorContainer.append('<p>' + error + '</p>');
        });
        errorContainer.show();
    }

    function hideErrors(errorContainer) {
        errorContainer.hide().html('');
    }

    function toggleLandlordAgentDetails(isLandlord) {
        if (isLandlord) {
            $('#landlordAgentDetails').show().find(':input').prop('disabled', false);
            $('#regularCustomerDetails').hide().find(':input').prop('disabled', true);
        } else {
            $('#landlordAgentDetails').hide().find(':input').prop('disabled', true);
            $('#regularCustomerDetails').show().find(':input').prop('disabled', false);
        }
    }

    function disableHiddenFields() {
        $('#landlordAgentDetails :input').prop('disabled', function() {
            return $(this).closest('#landlordAgentDetails').is(':hidden');
        });
        $('#regularCustomerDetails :input').prop('disabled', function() {
            return $(this).closest('#regularCustomerDetails').is(':hidden');
        });
    }

    function enableAllInputs() {
        $(':input').prop('disabled', false);
    }

    function showStep(step) {
        console.log('Showing step:', step);
        $('.form-step').hide();
        $('#step' + step).show();
        currentStep = step;
        updateNavigationButtons();
        
        if (step === 4) {
            if ($('input[name="landlordAgent"]:checked').val() === 'yes') {
                $('#landlordAgentDetails').show();
                $('#regularCustomerDetails').hide();
            } else {
                $('#landlordAgentDetails').hide();
                $('#regularCustomerDetails').show();
            }
        }
    }

    function updateNavigationButtons() {
        $('.edit-button').toggle(currentStep > 1);
        $('.next-step').toggle(currentStep < totalSteps);
        $('#confirmBooking').toggle(currentStep === totalSteps);
    }

    function getCurrentStep() {
        return currentStep;
    }

    return {
        populateApplianceSubtypes: populateApplianceSubtypes,
        showError: showError,
        showErrors: showErrors,
        hideErrors: hideErrors,
        toggleLandlordAgentDetails: toggleLandlordAgentDetails,
        disableHiddenFields: disableHiddenFields,
        enableAllInputs: enableAllInputs,
        showStep: showStep,
        updateNavigationButtons: updateNavigationButtons,
        getCurrentStep: getCurrentStep
    };
});