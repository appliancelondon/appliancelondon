define(['jquery'], function($) {
    'use strict';

    var currentStep = 1;
    var totalSteps = 4;

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
        showStep: showStep,
        updateNavigationButtons: updateNavigationButtons,
        getCurrentStep: getCurrentStep
    };
});