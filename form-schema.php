<?php
/**
 * Form Schema - Source of Truth
 * Complete field mapping for all 23 steps
 */

return [
    // STEP 1: Expert Details
    1 => [
        'title' => 'Expert Details',
        'fields' => [
            'booking_id' => ['label' => 'Booking ID', 'type' => 'text', 'required' => true],
            'expert_id' => ['label' => 'Expert ID', 'type' => 'text', 'required' => false],
            'customer_name' => ['label' => 'Customer Name', 'type' => 'text', 'required' => false],
            'customer_phone' => ['label' => 'Customer Phone', 'type' => 'text', 'required' => false],
            'inspection_date' => ['label' => 'Inspection Date', 'type' => 'date', 'required' => false],
            'inspection_time' => ['label' => 'Inspection Time', 'type' => 'time', 'required' => false],
            'inspection_address' => ['label' => 'Inspection Address', 'type' => 'textarea', 'required' => false],
            'obd_scanning' => ['label' => 'OBD Scanning', 'type' => 'radio', 'required' => false],
            'car' => ['label' => 'Car', 'type' => 'radio', 'required' => false],
            'lead_owner' => ['label' => 'Lead Owner', 'type' => 'text', 'required' => false],
            'pending_amount' => ['label' => 'Pending Amount', 'type' => 'number', 'required' => false],
            'inspection_delayed' => ['label' => 'Inspection Delayed', 'type' => 'radio', 'required' => true],
            'latitude' => ['label' => 'Latitude', 'type' => 'hidden', 'required' => false],
            'longitude' => ['label' => 'Longitude', 'type' => 'hidden', 'required' => false],
            'location_address' => ['label' => 'Location Address', 'type' => 'hidden', 'required' => false],
            'expert_date' => ['label' => 'Expert Date', 'type' => 'hidden', 'required' => false],
            'expert_time' => ['label' => 'Expert Time', 'type' => 'hidden', 'required' => false],
        ]
    ],
    
    // STEP 2: Car Photo
    2 => [
        'title' => 'Car Photo',
        'fields' => [
            'car_photo' => ['label' => 'Your photo with car\'s number plate', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 3: Car Details
    3 => [
        'title' => 'Car Details',
        'fields' => [
            'car_company' => ['label' => 'Car Company', 'type' => 'text', 'required' => true],
            'car_registration_number' => ['label' => 'Car Registration Number', 'type' => 'text', 'required' => true],
            'car_registration_year' => ['label' => 'Car Registration Year', 'type' => 'number', 'required' => true],
            'car_variant' => ['label' => 'Car Variant', 'type' => 'text', 'required' => true],
            'car_registered_state' => ['label' => 'Car Registered State', 'type' => 'text', 'required' => true],
            'car_registered_city' => ['label' => 'Car Registered City', 'type' => 'text', 'required' => true],
            'fuel_type' => ['label' => 'Fuel Type', 'type' => 'checkbox', 'required' => true],
            'engine_capacity' => ['label' => 'Engine Capacity (CC)', 'type' => 'number', 'required' => true],
            'transmission' => ['label' => 'Transmission', 'type' => 'radio', 'required' => true],
            'car_colour' => ['label' => 'Car Colour', 'type' => 'text', 'required' => true],
            'car_km_reading' => ['label' => 'Car KM Reading', 'type' => 'number', 'required' => true],
            'car_km_photo' => ['label' => 'Car KM Reading Photo', 'type' => 'file', 'required' => true],
            'car_keys_available' => ['label' => 'Car Keys Available', 'type' => 'number', 'required' => true],
        ]
    ],
    
    // STEP 4: Chassis & Engine
    4 => [
        'title' => 'Chassis & Engine Numbers',
        'fields' => [
            'chassis_number' => ['label' => 'Chassis Number', 'type' => 'text', 'required' => true],
            'chassis_plate_photo' => ['label' => 'Chassis No Plate', 'type' => 'file', 'required' => true],
            'engine_number' => ['label' => 'Engine Number', 'type' => 'text', 'required' => true],
        ]
    ],
    
    // STEP 5: Documents
    5 => [
        'title' => 'Car Documents',
        'fields' => [
            'registration_certificate' => ['label' => 'Registration Certificate', 'type' => 'checkbox', 'required' => true],
            'car_insurance' => ['label' => 'Car Insurance', 'type' => 'checkbox', 'required' => true],
            'car_finance_noc' => ['label' => 'Car Finance NOC', 'type' => 'checkbox', 'required' => true],
            'car_purchase_invoice' => ['label' => 'Car Purchase Invoice', 'type' => 'checkbox', 'required' => true],
            'bifuel_certification' => ['label' => 'Bifuel Certification', 'type' => 'checkbox', 'required' => true],
        ]
    ],
    
    // STEP 6: Structural Inspection
    6 => [
        'title' => 'Structural Inspection',
        'fields' => [
            'radiator_core' => ['label' => 'Radiator Core Support', 'type' => 'checkbox', 'required' => true],
            'radiator_core_image' => ['label' => 'Radiator Core Support Image', 'type' => 'file', 'required' => true],
            'match_chassis' => ['label' => 'Match Chassis', 'type' => 'radio', 'required' => true],
        ]
    ],
    
    // STEP 7: Strut Towers
    7 => [
        'title' => 'Strut Tower Inspection',
        'fields' => [
            'driver_strut' => ['label' => 'Driver Side Strut Tower Apron', 'type' => 'checkbox', 'required' => true],
            'driver_strut_image' => ['label' => 'Driver Side Strut Tower Apron Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 8: Passenger Strut
    8 => [
        'title' => 'Passenger Strut Tower',
        'fields' => [
            'passenger_strut' => ['label' => 'Passenger Strut Tower Apron', 'type' => 'checkbox', 'required' => true],
            'passenger_strut_image' => ['label' => 'Passenger Strut Tower Apron Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 9: Front Bonnet
    9 => [
        'title' => 'Front Bonnet UnderBody',
        'fields' => [
            'front_bonnet' => ['label' => 'Front Bonnet UnderBody', 'type' => 'checkbox', 'required' => true],
            'front_bonnet_image' => ['label' => 'Front Bonnet UnderBody Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 10: Boot Floor
    10 => [
        'title' => 'Boot Floor',
        'fields' => [
            'boot_floor' => ['label' => 'Boot Floor', 'type' => 'checkbox', 'required' => true],
            'boot_floor_image' => ['label' => 'Boot Floor Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 11: Exterior Body
    11 => [
        'title' => 'Exterior Body Inspection',
        'fields' => [
            'front_bumper' => ['label' => 'Front Bumper', 'type' => 'checkbox', 'required' => true],
            'rear_bumper' => ['label' => 'Rear Bumper', 'type' => 'checkbox', 'required' => true],
            'bonnet' => ['label' => 'Bonnet', 'type' => 'checkbox', 'required' => true],
            'roof' => ['label' => 'Roof', 'type' => 'checkbox', 'required' => true],
            'windshield' => ['label' => 'Windshield', 'type' => 'checkbox', 'required' => true],
        ]
    ],
    
    // STEP 12: Engine Inspection
    12 => [
        'title' => 'Engine Inspection',
        'fields' => [
            'car_start' => ['label' => 'Car Start', 'type' => 'checkbox', 'required' => true],
            'car_start_image' => ['label' => 'Car Start Image', 'type' => 'file', 'required' => true],
            'wiring' => ['label' => 'Wiring', 'type' => 'checkbox', 'required' => true],
            'wiring_image' => ['label' => 'Wiring Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 13: Engine Oil
    13 => [
        'title' => 'Engine Oil Quality',
        'fields' => [
            'engine_oil' => ['label' => 'Engine Oil', 'type' => 'checkbox', 'required' => true],
            'engine_oil_image' => ['label' => 'Engine Oil Quality Image', 'type' => 'file', 'required' => true],
            'engine_oil_cap' => ['label' => 'Engine Oil Cap', 'type' => 'checkbox', 'required' => true],
            'engine_mounting' => ['label' => 'Engine Mounting', 'type' => 'checkbox', 'required' => true],
            'coolant_level' => ['label' => 'Coolant Level', 'type' => 'checkbox', 'required' => true],
            'coolant_quality' => ['label' => 'Coolant Quality', 'type' => 'checkbox', 'required' => true],
        ]
    ],
    
    // STEP 14: Smoke & Battery
    14 => [
        'title' => 'Smoke Emission & Battery',
        'fields' => [
            'smoke_emission' => ['label' => 'Smoke Emission', 'type' => 'checkbox', 'required' => true],
            'smoke_emission_image' => ['label' => 'Smoke Emission Image', 'type' => 'file', 'required' => true],
            'battery' => ['label' => 'Battery', 'type' => 'checkbox', 'required' => true],
        ]
    ],
    
    // STEP 15: OBD Scan
    15 => [
        'title' => 'OBD Scan',
        'fields' => [
            'fault_codes' => ['label' => 'Fault Codes', 'type' => 'radio', 'required' => true],
            'obd_scan_photo' => ['label' => 'OBD Scan Photo', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 16: Electrical & Suspension
    16 => [
        'title' => 'Electrical & Suspension',
        'fields' => [
            'electrical_status' => ['label' => 'Electrical Status', 'type' => 'textarea', 'required' => true],
            'suspension_condition' => ['label' => 'Suspension Condition', 'type' => 'textarea', 'required' => true],
            'transmission_status' => ['label' => 'Transmission Status', 'type' => 'textarea', 'required' => true],
            'ac_heating_status' => ['label' => 'AC/Heating Status', 'type' => 'textarea', 'required' => true],
            'safety_features' => ['label' => 'Safety Features', 'type' => 'textarea', 'required' => true],
            'test_drive_notes' => ['label' => 'Test Drive Notes', 'type' => 'textarea', 'required' => true],
            'overall_assessment' => ['label' => 'Overall Assessment', 'type' => 'textarea', 'required' => true],
            'final_remarks' => ['label' => 'Final Remarks', 'type' => 'textarea', 'required' => true],
            'recommended_price' => ['label' => 'Recommended Price', 'type' => 'number', 'required' => true],
        ]
    ],
    
    // STEP 17: Multi Function Display
    17 => [
        'title' => 'Multi Function Display',
        'fields' => [
            'multi_function_display_image' => ['label' => 'Multi Function Display Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 18: Interior Roof
    18 => [
        'title' => 'Interior Roof',
        'fields' => [
            'car_roof_inside_image' => ['label' => 'Car Roof From Inside Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 19: AC Temperature
    19 => [
        'title' => 'AC Temperature Check',
        'fields' => [
            'ac_cool_image' => ['label' => 'AC Cool Mode Temperature Image', 'type' => 'file', 'required' => true],
            'ac_hot_image' => ['label' => 'AC Hot Mode Temperature Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 20: Tyre Inspection
    20 => [
        'title' => 'Tyre Tread Depth',
        'fields' => [
            'driver_front_tyre_image' => ['label' => 'Driver Front Tyre Tread Depth', 'type' => 'file', 'required' => true],
            'driver_back_tyre_image' => ['label' => 'Driver Back Tyre Tread Depth', 'type' => 'file', 'required' => true],
            'passenger_back_tyre_image' => ['label' => 'Passenger Back Tyre Tread Depth', 'type' => 'file', 'required' => true],
            'passenger_front_tyre_image' => ['label' => 'Passenger Front Tyre Tread Depth', 'type' => 'file', 'required' => true],
            'stepney_tyre_image' => ['label' => 'Stepney Tyre Tread Depth', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 21: Oil Leak
    21 => [
        'title' => 'Oil Leak Inspection',
        'fields' => [
            'oil_leak_image' => ['label' => 'Oil Leak Near Engine Image', 'type' => 'file', 'required' => true],
        ]
    ],
    
    // STEP 22: Additional Images
    22 => [
        'title' => 'Additional Inspection Images',
        'fields' => [
            'additional_image_1' => ['label' => 'Additional Image 1', 'type' => 'file', 'required' => false],
            'additional_image_2' => ['label' => 'Additional Image 2', 'type' => 'file', 'required' => false],
            'additional_image_3' => ['label' => 'Additional Image 3', 'type' => 'file', 'required' => false],
        ]
    ],
    
    // STEP 23: Final Review
    23 => [
        'title' => 'Final Review',
        'fields' => [
            // Review step - no additional fields
        ]
    ],
];
