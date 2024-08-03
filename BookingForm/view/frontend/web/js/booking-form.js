define('Appliancentre_BookingForm/js/booking-form', [
    'jquery',
    'flatpickr',
    'Appliancentre_BookingForm/js/appliance-data',
    'Appliancentre_BookingForm/js/form-validation',
    'Appliancentre_BookingForm/js/quote-calculator',
    'Appliancentre_BookingForm/js/ui-helpers',
    'Appliancentre_BookingForm/js/postcode-validation'
], function($, flatpickr, applianceData, formValidation, quoteCalculator, uiHelpers, postcodeValidation) {
    'use strict';
    return function (config, element) {
        var form = $(element);
        var errorContainer = $('#error-messages');
        var applianceCount = 1;

        // Function to populate initial appliance fields
        function populateInitialApplianceFields() {
            var $applianceType = $('#applianceType_1');
            var $applianceMake = $('#applianceMake_1');

            $applianceType.html(applianceData.getApplianceTypeOptions());
            $applianceMake.html(applianceData.getApplianceMakeOptions());
        }

        // Function to add appliance fields
        function addApplianceFields() {
            applianceCount++;
            var applianceHtml = `
                <div class="appliance-fields" data-appliance-id="${applianceCount}">
                    <h3>Appliance ${applianceCount}</h3>
                    <div class="form-group">
                        <label for="applianceType_${applianceCount}">Appliance Type *</label>
                        <select id="applianceType_${applianceCount}" name="appliances[${applianceCount}][applianceType]" required class="form-control appliance-type">
                            <option value="">Select Appliance Type</option>
                            ${applianceData.getApplianceTypeOptions()}
                        </select>
                    </div>
                    <div class="form-group appliance-subtype" style="display:none;">
                        <!-- This will be populated dynamically -->
                    </div>
                    <div class="form-group">
                        <label for="applianceMake_${applianceCount}">Make of Appliance *</label>
                        <select id="applianceMake_${applianceCount}" name="appliances[${applianceCount}][applianceMake]" required class="form-control">
                            <option value="">Select Make</option>
                            ${applianceData.getApplianceMakeOptions()}
                        </select>
                    </div>
                    <button type="button" class="btn btn-danger remove-appliance">Remove</button>
                </div>
            `;
            $('#appliances-container').append(applianceHtml);
        }

        // Populate initial appliance fields
        populateInitialApplianceFields();

        // Event listener for Add Appliance button
        $('#add-appliance').on('click', function() {
            addApplianceFields();
        });

        // Event listener for Remove Appliance button
        $(document).on('click', '.remove-appliance', function() {
            $(this).closest('.appliance-fields').remove();
            applianceCount--;
        });

        // Event listener for appliance type change
        $(document).on('change', '.appliance-type', function() {
            var applianceId = $(this).closest('.appliance-fields').data('appliance-id');
            uiHelpers.populateApplianceSubtypes(applianceData.applianceSubtypes, applianceId);
        });

        $('#getQuote').on('click', function(e) {
            e.preventDefault();
            var postcode = $('#customer_postcode').val();
            var postcodeValidationResult = postcodeValidation.validatePostcode(postcode);
            if (postcodeValidationResult.valid) {
                if (quoteCalculator.validateQuoteInputs()) {
                    quoteCalculator.calculateMultipleAppliancesQuote(applianceData, uiHelpers.showError, uiHelpers.showStep);
                } else {
                    uiHelpers.showError('Please fill in all required fields');
                }
            } else {
                uiHelpers.showError(postcodeValidationResult.message);
            }
        });

        $('.edit-button').on('click', function(e) {
            e.preventDefault();
            uiHelpers.showStep(uiHelpers.getCurrentStep() - 1);
        });

        $('#changeDetails').on('click', function(e) {
            e.preventDefault();
            uiHelpers.showStep(1);
        });

        $('#bookOnline').on('click', function(e) {
            e.preventDefault();
            uiHelpers.showStep(3);
        });

        $('.next-step').on('click', function(e) {
            e.preventDefault();
            if (formValidation.validateStep(uiHelpers.getCurrentStep(), errorContainer)) {
                uiHelpers.showStep(uiHelpers.getCurrentStep() + 1);
            }
        });

        $('input[name="landlordAgent"]').on('click', function() {
            uiHelpers.toggleLandlordAgentDetails($(this).val() === 'yes');
        });

        form.on('submit', function(e) {
            e.preventDefault();
            
            uiHelpers.disableHiddenFields();
            if (formValidation.validateForm(uiHelpers.getCurrentStep(), errorContainer)) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#booking-form').hide();
                            $('.booking-confirmation')
                                .html(response.confirmationHtml)
                                .show();
                        } else {
                            uiHelpers.showErrors([response.message], errorContainer);
                        }
                    },
                    error: function() {
                        uiHelpers.showErrors(['An error occurred. Please try again.'], errorContainer);
                    }
                });
            }
            uiHelpers.enableAllInputs();
        });

        // Initialize Flatpickr
        flatpickr("#visitDate", {
            minDate: "today",
            disable: [
                function(date) {
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            dateFormat: "Y-m-d"
        });

        // Initialize
        uiHelpers.showStep(1);
        $('input[name="landlordAgent"]:checked').trigger('change');

    };
});