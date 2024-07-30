define([
    'jquery',
    'flatpickr'
], function($, flatpickr) {
    'use strict';

    return function (config, element) {
        var form = $(element);
        var currentStep = 1;
        var totalSteps = 4;
        var errorContainer = $('#error-messages');

        // Appliance subtypes data
        var applianceSubtypes = {
            'built-in-oven': ['Double Ovens', 'Single Ovens'],
            'cooker-oven': ['Range', 'Duel Fuel', 'Electric', 'Gas'],
            'dishwasher': ['Integrated', 'Freestanding'],
            'extractor-cooker-hood': ['Canopy', 'Chimney', 'Visor', 'Integrated', 'Island'],
            'freezer': ['Freestanding', 'Integrated', 'Under Counter', 'Chest'],
            'fridge-freezer': ['American Style', 'Freestanding', 'Integrated'],
            'hob': ['Gas', 'Ceramic', 'Induction', 'Solid Plate'],
            'refrigerator': ['Freestanding', 'Integrated', 'Under Counter'],
            'tumble-dryer': ['Vented', 'Heatpump', 'Condenser'],
            'washer-dryer': ['Freestanding', 'Integrated'],
            'washing-machine': ['Freestanding', 'Integrated'],
            'wine-chiller': ['Freestanding', 'Integrated']
        };

        function showStep(step) {
            console.log('Showing step:', step);
            $('.form-step').hide();
            $('#step' + step).show();
            currentStep = step;
            updateNavigationButtons();
            
            if (step === 4) {
                toggleLandlordAgentFields();
            }
        }

        function updateNavigationButtons() {
            $('.edit-button').toggle(currentStep > 1);
            $('.next-step').toggle(currentStep < totalSteps);
            $('#confirmBooking').toggle(currentStep === totalSteps);
        }

        function populateApplianceSubtypes() {
            var applianceType = $('#applianceType').val();
            var subtypes = applianceSubtypes[applianceType];
            var subtypeHtml = '';

            if (subtypes) {
                subtypeHtml += '<label>Please select your ' + applianceType + ' type to continue</label><br>';
                subtypes.forEach(function(subtype) {
                    subtypeHtml += '<label><input type="radio" name="applianceSubtype" value="' + subtype.toLowerCase().replace(/ /g, '-') + '" required> ' + subtype + '</label><br>';
                });
                $('#applianceSubtype').html(subtypeHtml).show();
            } else {
                $('#applianceSubtype').hide();
            }
        }

        function showError(message) {
            errorContainer.html('<p>' + message + '</p>');
            errorContainer.show();
        }

        function calculateQuote() {
            var $getQuoteBtn = $('#getQuote');
            $getQuoteBtn.prop('disabled', true).text('Calculating...');

            var service = $('input[name="service"]:checked').val();
            var postcode = $('#customer_postcode').val();
            var applianceType = $('#applianceType').val();
            var applianceSubtype = $('input[name="applianceSubtype"]:checked').val();
            var applianceMake = $('#applianceMake').val();

            if (!service || !postcode || !applianceType || !applianceSubtype || !applianceMake) {
                showError('Please fill in all required fields');
                $getQuoteBtn.prop('disabled', false).text('Get a Quote');
                return;
            }

            // Define price structures
            var basePrice = {
                'repair': 30,
                'install': 40
            };

            var appliancePrice = {
                'wine-chiller_integrated': 35, 'wine-chiller_freestanding': 25,
                'washing-machine_integrated': 25, 'washing-machine_freestanding': 15,
                'washer-dryer_integrated': 25, 'washer-dryer_freestanding': 15,
                'tumble-dryer_condenser': 20, 'tumble-dryer_heatpump': 30, 'tumble-dryer_vented': 15,
                'refrigerator_under-counter': 15, 'refrigerator_integrated': 35, 'refrigerator_freestanding': 25,
                'hob_solid-plate': 15, 'hob_induction': 35, 'hob_ceramic': 25, 'hob_gas': 45,
                'fridge-freezer_integrated': 25, 'fridge-freezer_freestanding': 15, 'fridge-freezer_american-style': 25,
                'freezer_chest': 25, 'freezer_under-counter': 15, 'freezer_integrated': 35, 'freezer_freestanding': 25,
                'extractor-cooker-hood_island': 45, 'extractor-cooker-hood_integrated': 25, 'extractor-cooker-hood_visor': 45,
                'built-in-oven_single-ovens': 15, 'built-in-oven_double-ovens': 25,
                'cooker-oven_gas': 35, 'cooker-oven_electric': 25, 'cooker-oven_duel-fuel': 45, 'cooker-oven_range': 55,
                'dishwasher_freestanding': 15, 'dishwasher_integrated': 25,
                'extractor-cooker-hood_chimney': 35, 'extractor-cooker-hood_canopy': 45
            };

            var makePrice = {
                'zanussi': 10, 'zenith': 10, 'whirlpool': 10, 'tricity-bendix': 10, 'haden': 20,
                'stoves': 10, 'smeg': 10, 'siemens': 10, 'sharp': 10, 'samsung': 10, 'rosires': 30,
                'redfyre': 20, 'rangemaster': 20, 'ilve': 30, 'panasonic': 10, 'other': 20,
                'new-world': 10, 'neff': 10, 'amica': 10, 'miele': 20, 'mercury': 30, 'maytag': 10,
                'liebherr': 20, 'lg': 20, 'leisure': 20, 'lec': 10, 'lamona': 10, 'lacanche': 30,
                'kenwood': 10, 'indesit': 10, 'hygena': 10, 'husky': 10, 'hotpoint': 10, 'hoover': 10,
                'haier': 10, 'grundig': 10, 'gorenje': 10, 'hisense': 10, 'frigidaire': 10,
                'fridgemaster': 10, 'flavel': 10, 'fisher-paykel': 20, 'falcon': 30, 'elica': 20,
                'electrolux': 10, 'montpellier': 10, 'delonghi': 10, 'de-dietrich': 10, 'daewoo': 10,
                'currys-essentials': 10, 'creda': 10, 'cda': 10, 'caple': 10, 'cannon': 10, 'candy': 10,
                'bush': 10, 'britannia': 20, 'bosch': 10, 'blomberg': 10, 'bertazzoni': 30, 'belling': 10,
                'beko': 10, 'baumatic': 10, 'bauknecht': 10, 'ariston': 10, 'aeg': 10
            };

            // Calculate total price
            var total = basePrice[service];
            total += appliancePrice[applianceType + '_' + applianceSubtype.toLowerCase()] || 0;
            total += makePrice[applianceMake.toLowerCase()] || 0;

            $('#quotePostcode').text(postcode);
            $('#quoteAppliance').text(applianceType);
            $('#quoteApplianceType').text(applianceSubtype);
            $('#quoteApplianceMake').text(applianceMake);
            $('#quotePrice').text(total.toFixed(2));

            showStep(2);
            $getQuoteBtn.prop('disabled', false).text('Get a Quote');
        }

        function validateStep() {
            hideErrors();
            var errors = [];

            console.log('Validating step:', currentStep);

            if (currentStep === 3) {
                console.log('Visit Date:', $('#visitDate').val());
                console.log('Visit Time:', $('#visitTime').val());

                if (!$('#visitDate').val()) {
                    errors.push('Please select a preferred date.');
                }
                if (!$('#visitTime').val()) {
                    errors.push('Please select a preferred time.');
                }
            } else {
                // For other steps, use the general validation
                return validateForm();
            }

            if (errors.length > 0) {
                showErrors(errors);
                return false;
            }

            return true;
        }

        function validateForm() {
            var errors = [];
            form.find(':input:visible:not(:disabled)').each(function() {
                if ($(this).prop('required') && !$(this).val()) {
                    errors.push('Please fill in all required fields.');
                    return false; // exit the loop
                }
            });

            if (errors.length > 0) {
                console.log('Validation errors:', errors);
                showErrors(errors);
                return false;
            }
            return true;
        }

        function showErrors(errors) {
            errorContainer.html('');
            errors.forEach(function(error) {
                errorContainer.append('<p>' + error + '</p>');
            });
            errorContainer.show();
        }

        function hideErrors() {
            errorContainer.hide().html('');
        }

        function isValidEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        function toggleLandlordAgentFields() {
            var isLandlord = $('input[name="landlordAgent"]:checked').val() === 'yes';
            $('#landlordAgentDetails').toggle(isLandlord);
            $('#regularCustomerDetails').toggle(!isLandlord);
            
            if (isLandlord) {
                $('#regularCustomerDetails :input').prop('disabled', true);
                $('#landlordAgentDetails :input').prop('disabled', false);
            } else {
                $('#regularCustomerDetails :input').prop('disabled', false);
                $('#landlordAgentDetails :input').prop('disabled', true);
            }
        }

        // Event listeners
        $('#applianceType').on('change', populateApplianceSubtypes);

        $('#getQuote').on('click', function(e) {
            e.preventDefault();
            calculateQuote();
        });

        $('.edit-button').on('click', function(e) {
            e.preventDefault();
            showStep(currentStep - 1);
        });

        $('#changeDetails').on('click', function(e) {
            e.preventDefault();
            showStep(1);
        });

        $('#bookOnline').on('click', function(e) {
            e.preventDefault();
            showStep(3);
        });

        $('.next-step').on('click', function(e) {
            e.preventDefault();
            console.log('Next step button clicked');
            if (validateStep()) {
                showStep(currentStep + 1);
            }
        });

        $('input[name="landlordAgent"]').on('change', toggleLandlordAgentFields);

        form.on('submit', function(e) {
            e.preventDefault();
            
            // Disable validation for hidden fields
            $('#landlordAgentDetails :input').prop('disabled', function() {
                return $(this).closest('#landlordAgentDetails').is(':hidden');
            });
            $('#regularCustomerDetails :input').prop('disabled', function() {
                return $(this).closest('#regularCustomerDetails').is(':hidden');
            });

            if (validateForm()) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#booking-form').hide();
                            $('.booking-confirmation').html(response.confirmationHtml).show();
                        } else {
                            showErrors([response.message]);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        var errorMessage = 'An error occurred. Please try again.';
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }
                        showErrors([errorMessage]);
                    }
                });
            }

            // Re-enable all inputs after form submission
            $(':input').prop('disabled', false);
        });

        // Initialize Flatpickr
        flatpickr("#visitDate", {
            minDate: "today",
            disable: [
                function(date) {
                    // Disable Sundays (0) and Saturdays (6)
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            dateFormat: "Y-m-d"
        });

        // Initialize
        showStep(1);
        toggleLandlordAgentFields();

        // Debug: Log current step when it changes
        $('body').on('DOMSubtreeModified', '.form-step', function() {
            console.log('Current step:', currentStep);
        });
    };
});