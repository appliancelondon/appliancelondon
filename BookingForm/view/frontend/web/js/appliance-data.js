define('Appliancentre_BookingForm/js/appliance-data', [], function() {
    'use strict';

    var applianceData = {
        applianceSubtypes: {
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
        },
        basePrice: {
            'repair': 40,
            'install': 50
        },
        appliancePrice: {
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
        },
        makePrice: {
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
        },
        getApplianceTypeOptions: function() {
            return Object.keys(this.applianceSubtypes).map(function(type) {
                return '<option value="' + type + '">' + type.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';
            }).join('');
        },
        getApplianceMakeOptions: function() {
            return Object.keys(this.makePrice).map(function(make) {
                return '<option value="' + make + '">' + make.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';
            }).join('');
        }
    };

    return applianceData;
});