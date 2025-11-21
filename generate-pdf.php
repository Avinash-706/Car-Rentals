<?php
/**
 * Complete PDF Generation - All 23 Steps, All Fields
 * Ensures NO field is ever missing
 */

// Auto-configure PHP settings
require_once __DIR__ . '/auto-config.php';
// CRITICAL: Support for 500+ image uploads in PDF
@ini_set('memory_limit', '2048M');
@ini_set('max_execution_time', '600');
@ini_set('max_file_uploads', '500');
@ini_set('max_input_vars', '5000');
@set_time_limit(600);

if (!defined('APP_INIT')) {
    define('APP_INIT', true);
    require_once __DIR__ . '/config.php';
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/image-optimizer.php';

function generatePDF($data) {
    try {
        // Compress all images first
        $data = compressAllImages($data);
        
        // Create mPDF with memory-efficient settings
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'tempDir' => __DIR__ . '/tmp',
            'useSubstitutions' => false,
            'simpleTables' => true,
            'dpi' => 96,
            'img_dpi' => 96,
        ]);
        
        $mpdf->use_kwt = false;
        $mpdf->SetTitle('Car Inspection Report');
        $mpdf->SetAuthor('Car Inspection Expert');
        
        // Generate complete HTML
        $html = generateCompleteHTML($data);
        $mpdf->WriteHTML($html);
        
        // Save PDF
        $pdfFilename = 'inspection_' . ($data['booking_id'] ?? 'unknown') . '_' . time() . '.pdf';
        $pdfPath = PDF_DIR . $pdfFilename;
        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);
        
        return $pdfPath;
        
    } catch (Exception $e) {
        error_log('PDF Generation Error: ' . $e->getMessage());
        return false;
    }
}

function generateCompleteHTML($data) {
    $html = generateStyles();
    $html .= generateHeader($data);
    
    // ========================================================================
    // STEP 1: Booking Details
    // ========================================================================
    $html .= generateStepHeader(1, 'Booking Details');
    
    // Mandatory: Booking ID only
    $html .= generateField('Booking ID', $data['booking_id'] ?? '', true);
    
    // Optional fields (print only if filled)
    $html .= generateField('Expert ID', $data['expert_id'] ?? '', false);
    $html .= generateField('Customer Name', $data['customer_name'] ?? '', false);
    $html .= generateField('Customer Phone', $data['customer_phone'] ?? '', false);
    $html .= generateField('Date', $data['inspection_date'] ?? '', false);
    $html .= generateField('Time', $data['inspection_time'] ?? '', false);
    $html .= generateField('Inspection Address', $data['inspection_address'] ?? '', false);
    $html .= generateField('OBD Scanning', $data['obd_scanning'] ?? '', false);
    $html .= generateField('Car', $data['car'] ?? '', false);
    $html .= generateField('Lead Owner', $data['lead_owner'] ?? '', false);
    $html .= generateField('Pending Amount', $data['pending_amount'] ?? '', false);
    
    // ========================================================================
    // STEP 2: Expert Details
    // ========================================================================
    $html .= generateStepHeader(2, 'Expert Details');
    
    // Mandatory fields
    $html .= generateField('Inspection 45 Minutes Delayed?', $data['inspection_delayed'] ?? '', true);
    $html .= generateImage('Your photo with car\'s number plate', $data['car_photo_path'] ?? '', true);
    
    // Location section (all mandatory)
    $html .= '<div class="location-section">';
    $html .= '<div class="field-row"><span class="field-label section-label">Current Location:</span></div>';
    $html .= generateField('Latitude', $data['latitude'] ?? '', true);
    $html .= generateField('Longitude', $data['longitude'] ?? '', true);
    $html .= generateField('Full Location Address', $data['location_address'] ?? '', true);
    $html .= '</div>';
    
    // Optional fields (print only if filled)
    $html .= generateField('Date', $data['expert_date'] ?? '', false);
    $html .= generateField('Time', $data['expert_time'] ?? '', false);
    
    // ========================================================================
    // STEP 3: Car Details
    // ========================================================================
    $html .= generateStepHeader(3, 'Car Details');
    
    // Mandatory fields
    $html .= generateField('Car Company', $data['car_company'] ?? '', true);
    $html .= generateField('Car Registration Number', $data['car_registration_number'] ?? '', true);
    $html .= generateField('Car Registration Year (YYYY)', $data['car_registration_year'] ?? '', true);
    $html .= generateField('Car Variant', $data['car_variant'] ?? '', true);
    $html .= generateField('Car Registered State', $data['car_registered_state'] ?? '', true);
    
    // Optional field (print only if filled)
    $html .= generateField('Car Registered City', $data['car_registered_city'] ?? '', false);
    
    // Mandatory fields continued
    $html .= generateField('Fuel Type', formatArray($data['fuel_type'] ?? []), true);
    $html .= generateField('Engine Capacity (in CC)', $data['engine_capacity'] ?? '', true);
    $html .= generateField('Transmission', $data['transmission'] ?? '', true);
    $html .= generateField('Car Colour', $data['car_colour'] ?? '', true);
    $html .= generateField('Car KM Current Reading', $data['car_km_reading'] ?? '', true);
    $html .= generateImage('Car KM Reading Photo', $data['car_km_photo_path'] ?? '', true);
    $html .= generateField('Number of Car Keys Available', $data['car_keys_available'] ?? '', true);
    $html .= generateField('Chassis Number', $data['chassis_number'] ?? '', true);
    $html .= generateField('Engine Number', $data['engine_number'] ?? '', true);
    $html .= generateImage('Chassis No Plate', $data['chassis_plate_photo_path'] ?? '', true);
    
    // ========================================================================
    // STEP 4: Car Documents
    // ========================================================================
    $html .= generateStepHeader(4, 'Car Documents');
    
    $html .= generateField('Registration Certificate', formatArray($data['registration_certificate'] ?? []), true);
    $html .= generateField('Car Insurance', formatArray($data['car_insurance'] ?? []), true);
    $html .= generateField('Car Finance NOC', formatArray($data['car_finance_noc'] ?? []), true);
    $html .= generateField('Car Purchase Invoice', formatArray($data['car_purchase_invoice'] ?? []), true);
    $html .= generateField('Bi-Fuel Certification', formatArray($data['bifuel_certification'] ?? []), true);
    
    // ========================================================================
    // STEP 5: Body Frame Accidental Checklist
    // ========================================================================
    $html .= generateStepHeader(5, 'Body Frame Accidental Checklist');
    
    // Radiator Core Support
    $html .= generateField('Radiator Core Support', formatArray($data['radiator_core'] ?? []), true);
    $html .= generateImage('Radiator Core Support Image', $data['radiator_core_image_path'] ?? '', true);
    
    // Match Chassis No Plate with Real Body
    $html .= generateField('Match Chassis No Plate with Real Body', $data['match_chassis'] ?? '', true);
    
    // Driver Side Strut Tower Apron
    $html .= generateField('Driver Side Strut Tower Apron', formatArray($data['driver_strut'] ?? []), true);
    $html .= generateImage('Driver Side Strut Tower Apron Image', $data['driver_strut_image_path'] ?? '', true);
    
    // Passenger Strut Tower Apron
    $html .= generateField('Passenger Strut Tower Apron', formatArray($data['passenger_strut'] ?? []), true);
    $html .= generateImage('Passenger Strut Tower Apron Image', $data['passenger_strut_image_path'] ?? '', true);
    
    // Front Bonnet UnderBody
    $html .= generateField('Front Bonnet UnderBody', formatArray($data['front_bonnet'] ?? []), true);
    $html .= generateImage('Front Bonnet UnderBody Image', $data['front_bonnet_image_path'] ?? '', true);
    
    // Boot Floor
    $html .= generateField('Boot Floor', formatArray($data['boot_floor'] ?? []), true);
    $html .= generateImage('Boot Floor Image', $data['boot_floor_image_path'] ?? '', true);
    
    // ========================================================================
    // STEP 6: Exterior Body
    // ========================================================================
    $html .= generateStepHeader(6, 'Exterior Body');
    
    $html .= generateField('Front Bumper', formatArray($data['front_bumper'] ?? []), true);
    $html .= generateField('Rear Bumper', formatArray($data['rear_bumper'] ?? []), true);
    $html .= generateField('Bonnet', formatArray($data['bonnet'] ?? []), true);
    $html .= generateField('Roof', formatArray($data['roof'] ?? []), true);
    $html .= generateField('Windshield', formatArray($data['windshield'] ?? []), true);
    
    // ========================================================================
    // STEP 7: Engine (Before Test Drive)
    // ========================================================================
    $html .= generateStepHeader(7, 'Engine (Before Test Drive)');
    
    // Car Start
    $html .= generateImage('Car Start Image', $data['car_start_image_path'] ?? '', true);
    $html .= generateField('Car Start', formatArray($data['car_start'] ?? []), true);
    
    // Wiring
    $html .= generateImage('Wiring Image', $data['wiring_image_path'] ?? '', true);
    $html .= generateField('Wiring', formatArray($data['wiring'] ?? []), true);
    
    // Engine Oil Quality
    $html .= generateImage('Engine Oil Quality Image', $data['engine_oil_image_path'] ?? '', true);
    $html .= generateField('Engine Oil Quality', formatArray($data['engine_oil'] ?? []), true);
    $html .= generateField('Engine Oil Cap', formatArray($data['engine_oil_cap'] ?? []), true);
    $html .= generateField('Engine Mounting and Components', formatArray($data['engine_mounting'] ?? []), true);
    
    // Coolant
    $html .= generateField('Coolant Level', formatArray($data['coolant_level'] ?? []), true);
    $html .= generateField('Coolant Quality', formatArray($data['coolant_quality'] ?? []), true);
    
    // Smoke Emission
    $html .= generateImage('Smoke Emission Image', $data['smoke_emission_image_path'] ?? '', true);
    $html .= generateField('Smoke Emission', formatArray($data['smoke_emission'] ?? []), true);
    
    // Battery
    $html .= generateField('Battery', formatArray($data['battery'] ?? []), true);
    
    // ========================================================================
    // STEP 8: OBD Scan
    // ========================================================================
    $html .= generateStepHeader(8, 'OBD Scan');
    
    $html .= generateField('Fault Codes', $data['fault_codes'] ?? '', true);
    $html .= generateImage('OBD Scan Photo', $data['obd_scan_photo_path'] ?? '', true);
    
    // ========================================================================
    // STEP 9: Electrical and Interior
    // ========================================================================
    $html .= generateStepHeader(9, 'Electrical and Interior');
    
    // ALL MANDATORY FIELDS - EXACT LABELS FROM FORM
    $html .= generateField('Central Lock Working', $data['central_lock'] ?? '', true);
    $html .= generateField('Ignition Switch / Push Button', $data['ignition_switch'] ?? '', true);
    $html .= generateField('Driver – Front Indicator', $data['driver_front_indicator'] ?? '', true);
    $html .= generateField('Passenger – Front Indicator', $data['passenger_front_indicator'] ?? '', true);
    $html .= generateField('Driver Headlight', $data['driver_headlight'] ?? '', true);
    $html .= generateField('Driver Headlight Highbeam', $data['driver_headlight_highbeam'] ?? '', true);
    $html .= generateField('Passenger Headlight', $data['passenger_headlight'] ?? '', true);
    $html .= generateField('Passenger Headlight Highbeam', $data['passenger_headlight_highbeam'] ?? '', true);
    $html .= generateField('Front Number Plate Light', $data['front_number_plate_light'] ?? '', true);
    $html .= generateField('Driver Back Indicator', $data['driver_back_indicator'] ?? '', true);
    $html .= generateField('Passenger Back Indicator', $data['passenger_back_indicator'] ?? '', true);
    $html .= generateField('Back Number Plate Light', $data['back_number_plate_light'] ?? '', true);
    $html .= generateField('Brake Light Driver', $data['brake_light_driver'] ?? '', true);
    $html .= generateField('Brake Light Passenger', $data['brake_light_passenger'] ?? '', true);
    $html .= generateField('Driver Tail Light', $data['driver_tail_light'] ?? '', true);
    $html .= generateField('Passenger Tail Light', $data['passenger_tail_light'] ?? '', true);
    $html .= generateField('Steering Wheel Condition', $data['steering_wheel_condition'] ?? '', true);
    $html .= generateField('Steering Mounted Controls', $data['steering_mounted_controls'] ?? '', true);
    $html .= generateField('Back Camera', $data['back_camera'] ?? '', true);
    $html .= generateField('Reverse Parking Sensor', $data['reverse_parking_sensor'] ?? '', true);
    $html .= generateField('Multi Function Display', $data['multi_function_display'] ?? '', true);
    
    // MANDATORY IMAGE
    $html .= generateImage('Multi Function Display Image', $data['multi_function_display_image_path'] ?? '', true);
    
    // Optional text field (only if filled)
    $html .= generateField('In Car Start', $data['in_car_start'] ?? '', false);
    
    // Continue mandatory fields
    $html .= generateField('Car Horn', $data['car_horn'] ?? '', true);
    $html .= generateField('Entertainment System', $data['entertainment_system'] ?? '', true);
    $html .= generateField('Cruise Control', $data['cruise_control'] ?? '', true);
    $html .= generateField('Interior Lights', $data['interior_lights'] ?? '', true);
    $html .= generateField('Sun Roof', $data['sun_roof'] ?? '', true);
    $html .= generateField('Bonnet Release Operation', $data['bonnet_release_operation'] ?? '', true);
    $html .= generateField('Fuel Cap Release Operation', $data['fuel_cap_release_operation'] ?? '', true);
    $html .= generateField('Check Onboard Computer AdBlue Level – Diesel Cars', $data['adblue_level'] ?? '', true);
    $html .= generateField('Window Safety Lock', $data['window_safety_lock'] ?? '', true);
    $html .= generateField('Driver ORVM Controls', $data['driver_orvm_controls'] ?? '', true);
    $html .= generateField('Passenger ORVM Controls', $data['passenger_orvm_controls'] ?? '', true);
    $html .= generateField('Glove Box', $data['glove_box'] ?? '', true);
    $html .= generateField('Wiper', $data['wiper'] ?? '', true);
    $html .= generateField('Rear View Mirror', $data['rear_view_mirror'] ?? '', true);
    $html .= generateField('Dashboard Condition', $data['dashboard_condition'] ?? '', true);
    $html .= generateField('Car Roof From Inside', $data['car_roof_from_inside'] ?? '', true);
    
    // MANDATORY IMAGE
    $html .= generateImage('Car Roof From Inside Image', $data['car_roof_inside_image_path'] ?? '', true);
    
    // Continue mandatory fields
    $html .= generateField('Seat Adjustment Driver Side', $data['seat_adjustment_driver'] ?? '', true);
    $html .= generateField('Seat Adjustment Driver Rear Side', $data['seat_adjustment_driver_rear'] ?? '', true);
    $html .= generateField('Front Driver Side Seat Condition', $data['front_driver_seat_condition'] ?? '', true);
    $html .= generateField('Front Driver Side Floor', $data['front_driver_floor'] ?? '', true);
    $html .= generateField('Front Driver Side Seat Belt', $data['front_driver_seat_belt'] ?? '', true);
    $html .= generateField('Front Passenger Side Seat Condition', $data['front_passenger_seat_condition'] ?? '', true);
    $html .= generateField('Front Passenger Side Floor', $data['front_passenger_floor'] ?? '', true);
    $html .= generateField('Front Passenger Side Seat Belt', $data['front_passenger_seat_belt'] ?? '', true);
    $html .= generateField('Seat Adjustment Passenger Side', $data['seat_adjustment_passenger'] ?? '', true);
    $html .= generateField('Window Driver Side', $data['window_driver_side'] ?? '', true);
    $html .= generateField('Window Passenger Side', $data['window_passenger_side'] ?? '', true);
    $html .= generateField('Window Passenger Rear Side', $data['window_passenger_rear'] ?? '', true);
    $html .= generateField('Window Driver Rear Side', $data['window_driver_rear'] ?? '', true);
    $html .= generateField('Back Driver Side Floor', $data['back_driver_floor'] ?? '', true);
    $html .= generateField('Back Driver Side Seat Belt', $data['back_driver_seat_belt'] ?? '', true);
    $html .= generateField('Back Driver Side Seat Condition', $data['back_driver_seat_condition'] ?? '', true);
    $html .= generateField('Seat Adjustment Passenger Rear Side', $data['seat_adjustment_passenger_rear'] ?? '', true);
    $html .= generateField('Back Passenger Side Floor', $data['back_passenger_floor'] ?? '', true);
    $html .= generateField('Back Passenger Side Seat Condition', $data['back_passenger_seat_condition'] ?? '', true);
    $html .= generateField('Back Passenger Side Seat Belt', $data['back_passenger_seat_belt'] ?? '', true);
    $html .= generateField('Rear Side Floor – 7 Seater', $data['rear_side_floor_7seater'] ?? '', true);
    $html .= generateField('Rear Seat Condition – 7 Seater', $data['rear_seat_condition_7seater'] ?? '', true);
    $html .= generateField('Rear Seat Belt – 7 Seater', $data['rear_seat_belt_7seater'] ?? '', true);
    $html .= generateField('Child Safety Lock', $data['child_safety_lock'] ?? '', true);
    
    // NON-MANDATORY TEXT AREA (only if filled)
    $html .= generateField('Check All Buttons', $data['check_all_buttons'] ?? '', false);
    
    // ========================================================================
    // STEP 10: Warning Lights
    // ========================================================================
    $html .= generateStepHeader(10, 'Warning Lights');
    
    // ALL MANDATORY FIELDS - EXACT LABELS FROM FORM
    $html .= generateField('Check Engine', $data['check_engine'] ?? '', true);
    $html .= generateField('Power Steering Problem', $data['power_steering_problem'] ?? '', true);
    $html .= generateField('ABS Sensor Problem', $data['abs_sensor_problem'] ?? '', true);
    $html .= generateField('Airbag Sensor Problem', $data['airbag_sensor_problem'] ?? '', true);
    $html .= generateField('Battery Problem', $data['battery_problem'] ?? '', true);
    $html .= generateField('Low Engine Oil', $data['low_engine_oil'] ?? '', true);
    $html .= generateField('Low Coolant / Overheating', $data['low_coolant_overheating'] ?? '', true);
    $html .= generateField('Brake System Warning', $data['brake_system_warning'] ?? '', true);
    $html .= generateField('Any Other Lights Found?', $data['any_other_lights'] ?? '', true);
    
    // NON-MANDATORY TEXT AREA (only if filled)
    $html .= generateField('Control Lights Present', $data['control_lights_present'] ?? '', false);
    
    // ========================================================================
    // STEP 11: Air Conditioning
    // ========================================================================
    $html .= generateStepHeader(11, 'Air Conditioning');
    
    $html .= generateField('Air Conditioning Turning On', $data['ac_turning_on'] ?? '', true);
    $html .= generateField('AC Cool Temperature', $data['ac_cool_temperature'] ?? '', true);
    $html .= generateField('AC Hot Temperature', $data['ac_hot_temperature'] ?? '', true);
    $html .= generateField('Air Conditioning Direction Mode Working', $data['ac_direction_mode'] ?? '', true);
    $html .= generateField('Defogger Front Vent Working', $data['defogger_front'] ?? '', true);
    $html .= generateField('Defogger Rear Vent Working', $data['defogger_rear'] ?? '', true);
    $html .= generateField('Air Conditioning All Vents', $data['ac_all_vents'] ?? '', true);
    $html .= generateField('AC Abnormal Vibration', $data['ac_vibration'] ?? '', true);
    $html .= generateImage('AC Cool Mode Temperature Image', $data['ac_cool_image_path'] ?? '', true);
    $html .= generateImage('AC Hot Mode Temperature Image', $data['ac_hot_image_path'] ?? '', true);
    
    // ========================================================================
    // STEP 12: Tyres
    // ========================================================================
    $html .= generateStepHeader(12, 'Tyres');
    
    $html .= generateField('Tyre Size', $data['tyre_size'] ?? '', true);
    $html .= generateField('Tyre Type', $data['tyre_type'] ?? '', true);
    $html .= generateField('Rim Type', $data['rim_type'] ?? '', true);
    
    // Driver Front Tyre
    $html .= generateField('Driver Front Tyre Depth Check', $data['driver_front_tyre_depth'] ?? '', true);
    $html .= generateImage('Driver Front Tyre Tread Depth', $data['driver_front_tyre_image_path'] ?? '', true);
    $html .= generateField('Driver Front Tyre Manufacturing Date', $data['driver_front_tyre_date'] ?? '', true);
    $html .= generateField('Driver Front Tyre Shape', $data['driver_front_tyre_shape'] ?? '', true);
    
    // Driver Back Tyre
    $html .= generateField('Driver Back Tyre Depth Check', $data['driver_back_tyre_depth'] ?? '', true);
    $html .= generateImage('Driver Back Tyre Tread Depth', $data['driver_back_tyre_image_path'] ?? '', true);
    $html .= generateField('Driver Back Tyre Manufacturing Date', $data['driver_back_tyre_date'] ?? '', true);
    $html .= generateField('Driver Back Tyre Shape', $data['driver_back_tyre_shape'] ?? '', true);
    
    // Passenger Back Tyre
    $html .= generateField('Passenger Back Tyre Depth Check', $data['passenger_back_tyre_depth'] ?? '', true);
    $html .= generateImage('Passenger Back Tyre Tread Depth', $data['passenger_back_tyre_image_path'] ?? '', true);
    $html .= generateField('Passenger Back Tyre Manufacturing Date', $data['passenger_back_tyre_date'] ?? '', true);
    $html .= generateField('Passenger Back Tyre Shape', $data['passenger_back_tyre_shape'] ?? '', true);
    
    // Passenger Front Tyre
    $html .= generateField('Passenger Front Tyre Depth Check', $data['passenger_front_tyre_depth'] ?? '', true);
    $html .= generateImage('Passenger Front Tyre Tread Depth', $data['passenger_front_tyre_image_path'] ?? '', true);
    $html .= generateField('Passenger Front Tyre Manufacturing Date', $data['passenger_front_tyre_date'] ?? '', true);
    $html .= generateField('Passenger Front Tyre Shape', $data['passenger_front_tyre_shape'] ?? '', true);
    
    // Stepney Tyre
    $html .= generateField('Stepney Tyre Depth Check', $data['stepney_tyre_depth'] ?? '', true);
    $html .= generateImage('Stepney Tyre Tread Depth', $data['stepney_tyre_image_path'] ?? '', true);
    $html .= generateField('Stepney Tyre Manufacturing Date', $data['stepney_tyre_date'] ?? '', true);
    $html .= generateField('Stepney Tyre Shape', $data['stepney_tyre_shape'] ?? '', true);
    
    $html .= generateField('Signs of Camber Issue', $data['camber_issue'] ?? '', true);
    
    // ========================================================================
    // STEP 13: Transmission & Clutch Pedal
    // ========================================================================
    $html .= generateStepHeader(13, 'Transmission & Clutch Pedal');
    
    $html .= generateField('Check Gear Box / Transmission Shifting', $data['gearbox_shifting'] ?? '', true);
    $html .= generateField('Check 4x4 Shifting', $data['check_4x4'] ?? '', true);
    $html .= generateField('Clutch Pedal', $data['clutch_pedal'] ?? '', true);
    $html .= generateField('Clutch Engagement - RPM Downgrade', $data['clutch_rpm_downgrade'] ?? '', true);
    $html .= generateField('Clutch Engagement - RPM Upgrade', $data['clutch_rpm_upgrade'] ?? '', true);
    $html .= generateField('Clutch Engagement - RPM With Acceleration', $data['clutch_rpm_acceleration'] ?? '', true);
    
    // ========================================================================
    // STEP 14: Axle
    // ========================================================================
    $html .= generateStepHeader(14, 'Axle');
    
    $html .= generateField('Left Axle', $data['left_axle'] ?? '', true);
    $html .= generateField('Right Axle', $data['right_axle'] ?? '', true);
    $html .= generateField('Abnormal Noise', $data['abnormal_noise'] ?? '', true);
    
    // ========================================================================
    // STEP 15: Engine (After Test Drive)
    // ========================================================================
    $html .= generateStepHeader(15, 'Engine (After Test Drive)');
    
    $html .= generateField('Check for Oil Leaks Near Engine', $data['oil_leaks_engine'] ?? '', true);
    $html .= generateImage('Oil Leak Near Engine Image', $data['oil_leak_image_path'] ?? '', true);
    $html .= generateField('Radiator Fan', $data['radiator_fan'] ?? '', true);
    $html .= generateField('Radiator Condition', $data['radiator_condition'] ?? '', true);
    $html .= generateField('Smoke from Dipstick Point', $data['smoke_dipstick'] ?? '', true);
    $html .= generateField('Transmission Oil Leakage', $data['transmission_oil_leakage'] ?? '', true);
    $html .= generateField('Engine Noise', $data['engine_noise'] ?? '', true);
    $html .= generateField('Engine Vibration', $data['engine_vibration'] ?? '', true);
    $html .= generateField('Hoses', $data['hoses'] ?? '', true);
    $html .= generateField('Exhaust Sound', $data['exhaust_sound'] ?? '', true);
    
    // ========================================================================
    // STEP 16: Brakes
    // ========================================================================
    $html .= generateStepHeader(16, 'Brakes');
    
    // Front Driver Side
    $html .= generateField('Brake Pads Front Driver Side', $data['brake_pads_front_driver'] ?? '', true);
    $html .= generateField('Brake Discs Front Driver Side', $data['brake_discs_front_driver'] ?? '', true);
    $html .= generateField('Brake Calipers Front Driver Side', $data['brake_calipers_front_driver'] ?? '', true);
    
    // Front Passenger Side
    $html .= generateField('Brake Pads Front Passenger Side', $data['brake_pads_front_passenger'] ?? '', true);
    $html .= generateField('Brake Discs Front Passenger Side', $data['brake_discs_front_passenger'] ?? '', true);
    $html .= generateField('Brake Calipers Front Passenger Side', $data['brake_calipers_front_passenger'] ?? '', true);
    
    // Back Passenger Side
    $html .= generateField('Brake Pads Back Passenger Side', $data['brake_pads_back_passenger'] ?? '', true);
    $html .= generateField('Brake Discs Back Passenger Side', $data['brake_discs_back_passenger'] ?? '', true);
    $html .= generateField('Brake Calipers Back Passenger Side', $data['brake_calipers_back_passenger'] ?? '', true);
    
    // Back Driver Side
    $html .= generateField('Brake Pads Back Driver Side', $data['brake_pads_back_driver'] ?? '', true);
    $html .= generateField('Brake Discs Back Driver Side', $data['brake_discs_back_driver'] ?? '', true);
    $html .= generateField('Brake Calipers Back Driver Side', $data['brake_calipers_back_driver'] ?? '', true);
    
    // Brake System
    $html .= generateField('Brake Fluid Reservoir', $data['brake_fluid_reservoir'] ?? '', true);
    $html .= generateField('Brake Fluid Cap', $data['brake_fluid_cap'] ?? '', true);
    $html .= generateField('Parking Hand Brake', $data['parking_hand_brake'] ?? '', true);
    
    // ========================================================================
    // STEP 17: Suspension
    // ========================================================================
    $html .= generateStepHeader(17, 'Suspension');
    
    $html .= generateField('Car Height', $data['car_height'] ?? '', true);
    $html .= generateField('Shocker Bounce Test', $data['shocker_bounce_test'] ?? '', true);
    $html .= generateField('Driver Side Suspension Assembly', $data['driver_suspension_assembly'] ?? '', true);
    $html .= generateField('Driver Side Shocker Leakage', $data['driver_shocker_leakage'] ?? '', true);
    $html .= generateField('Passenger Side Suspension Assembly', $data['passenger_suspension_assembly'] ?? '', true);
    $html .= generateField('Passenger Side Shocker Leakage', $data['passenger_shocker_leakage'] ?? '', true);
    $html .= generateImage('Driver Side Front Shocker Photo', $data['driver_front_shocker_photo_path'] ?? '', true);
    $html .= generateImage('Passenger Side Front Shocker Photo', $data['passenger_front_shocker_photo_path'] ?? '', true);
    $html .= generateImage('Driver Side Rear Shocker Photo', $data['driver_rear_shocker_photo_path'] ?? '', true);
    $html .= generateImage('Passenger Side Rear Shocker Photo', $data['passenger_rear_shocker_photo_path'] ?? '', true);
    
    // ========================================================================
    // STEP 18: Brakes & Steering (Test Drive)
    // ========================================================================
    $html .= generateStepHeader(18, 'Brakes & Steering (Test Drive)');
    
    $html .= generateField('Brake Pedal', $data['brake_pedal'] ?? '', true);
    $html .= generateField('Steering Rotating Smoothly', $data['steering_rotating_smoothly'] ?? '', true);
    $html .= generateField('Steering Coming Back While Driving', $data['steering_coming_back'] ?? '', true);
    $html .= generateField('Steering Weird Noise', $data['steering_weird_noise'] ?? '', true);
    $html .= generateField('Alignment', $data['alignment'] ?? '', true);
    $html .= generateField('Bubbling', $data['bubbling'] ?? '', true);
    $html .= generateField('Steering Vibration', $data['steering_vibration'] ?? '', true);
    
    // ========================================================================
    // STEP 19: Underbody
    // ========================================================================
    $html .= generateStepHeader(19, 'Underbody');
    
    $html .= generateField('Driver Side Body Chasis', $data['driver_body_chasis'] ?? '', true);
    $html .= generateField('Passenger Side Body Chasis', $data['passenger_body_chasis'] ?? '', true);
    $html .= generateField('Rear Subframe', $data['rear_subframe'] ?? '', true);
    $html .= generateField('Engine Damage UnderBody', $data['engine_damage_underbody'] ?? '', true);
    $html .= generateField('Under Trays', $data['under_trays'] ?? '', true);
    $html .= generateField('Front Protection Cover', $data['front_protection_cover'] ?? '', true);
    $html .= generateField('Rear Protection Cover', $data['rear_protection_cover'] ?? '', true);
    $html .= generateField('Silencer', $data['silencer'] ?? '', true);
    $html .= generateField('Fuel Tank', $data['fuel_tank'] ?? '', true);
    $html .= generateField('Any Fluid Leaks Under Body', $data['fluid_leaks_underbody'] ?? '', true);
    $html .= generateImage('Underbody Left', $data['underbody_left_path'] ?? '', true);
    $html .= generateImage('Underbody Rear', $data['underbody_rear_path'] ?? '', true);
    $html .= generateImage('Underbody Right', $data['underbody_right_path'] ?? '', true);
    $html .= generateImage('Underbody Front', $data['underbody_front_path'] ?? '', true);
    
    // ========================================================================
    // STEP 20: Equipments
    // ========================================================================
    $html .= generateStepHeader(20, 'Equipments');
    
    $html .= generateField('Tool Kit', $data['tool_kit'] ?? '', true);
    $html .= generateImage('Tool Kit Image', $data['tool_kit_image_path'] ?? '', true);
    
    // ========================================================================
    // STEP 21: Final Car Result
    // ========================================================================
    $html .= generateStepHeader(21, 'Final Car Result');
    
    $html .= generateField('Any Issues Found in the Car?', $data['issues_found'] ?? '', true);
    $html .= generateImage('Photos of Issues', $data['issues_photo_path'] ?? '', false);
    
    // ========================================================================
    // STEP 22: Car Images From All Directions
    // ========================================================================
    $html .= generateStepHeader(22, 'Car Images From All Directions');
    
    $html .= generateImage('Front', $data['car_front_path'] ?? '', true);
    $html .= generateImage('Corner Front - Driver', $data['car_corner_front_driver_path'] ?? '', true);
    $html .= generateImage('Driver Side', $data['car_driver_side_path'] ?? '', true);
    $html .= generateImage('Corner Back - Driver', $data['car_corner_back_driver_path'] ?? '', true);
    $html .= generateImage('Back', $data['car_back_path'] ?? '', true);
    $html .= generateImage('Corner Back - Passenger', $data['car_corner_back_passenger_path'] ?? '', true);
    $html .= generateImage('Passenger Side', $data['car_passenger_side_path'] ?? '', true);
    $html .= generateImage('Corner Front - Passenger', $data['car_corner_front_passenger_path'] ?? '', true);
    $html .= generateImage('Front Interior', $data['car_front_interior_path'] ?? '', true);
    $html .= generateImage('Rear Interior', $data['car_rear_interior_path'] ?? '', true);
    $html .= generateImage('4 Way Switch Driver Side', $data['car_4way_switch_path'] ?? '', true);
    $html .= generateImage('Trunk Open', $data['car_trunk_open_path'] ?? '', true);
    $html .= generateImage('Car KM Reading', $data['car_km_reading_final_path'] ?? '', true);
    
    // ========================================================================
    // STEP 23: Payment Details
    // ========================================================================
    $html .= generateStepHeader(23, 'Payment Details');
    
    $html .= generateField('Taking Payment', $data['taking_payment'] ?? '', true);
    
    $html .= generateFooter();
    
    return $html;
}

function generateStyles() {
    return '
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; line-height: 1.4; }
        .header { text-align: center; background: #2196F3; color: white; padding: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .step-header { 
            background: #f0f0f0; 
            padding: 10px; 
            font-size: 13px; 
            font-weight: bold;
            margin: 15px 0 10px 0;
            border-left: 4px solid #2196F3;
            page-break-after: avoid;
        }
        .field-row { 
            margin: 5px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
            page-break-inside: avoid;
        }
        .field-label { font-weight: bold; color: #333; display: inline-block; width: 40%; }
        .field-value { color: #000; display: inline-block; width: 58%; }
        .field-value.missing { color: #d32f2f; font-weight: bold; }
        .image-block { 
            text-align: center; 
            margin: 10px 0;
            page-break-inside: avoid;
        }
        .image-block img { 
            width: 400px !important;
            height: 300px !important;
            border: 1px solid #ddd;
            display: block;
            margin: 0 auto;
        }
        .image-caption { 
            font-size: 9px; 
            color: #666; 
            margin-top: 5px;
            font-weight: bold;
        }
        .location-section {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-left: 3px solid #4CAF50;
        }
        .section-label {
            font-size: 11px;
            color: #4CAF50;
            font-weight: bold;
        }
        .footer { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 2px solid #2196F3; font-size: 9px; color: #666; }
    </style>';
}

function generateHeader($data) {
    return '
    <div class="header">
        <h1>🚗 CAR INSPECTION REPORT</h1>
        <p>' . APP_TITLE . '</p>
        <p>Booking ID: ' . htmlspecialchars($data['booking_id'] ?? 'N/A') . '</p>
        <p>Generated: ' . date('Y-m-d H:i:s') . '</p>
    </div>';
}

function generateStepHeader($stepNumber, $title) {
    return '<div class="step-header">STEP ' . $stepNumber . ' — ' . strtoupper($title) . '</div>';
}

function generateField($label, $value, $required = false) {
    // Handle arrays (checkbox/multi-select fields)
    if (is_array($value)) {
        // Filter out empty values and join with comma
        $filtered = array_filter($value, function($v) {
            return $v !== '' && $v !== null && $v !== false;
        });
        
        if (!empty($filtered)) {
            $value = implode(', ', $filtered);
        } else {
            $value = ''; // Empty array
        }
    }
    
    // Convert value to string for comparison
    $value = (string)$value;
    
    // Check if value is empty
    if ($value === '' || $value === null) {
        if ($required) {
            return '<div class="field-row">
                <span class="field-label">' . htmlspecialchars($label) . ':</span>
                <span class="field-value missing">Not Selected</span>
            </div>';
        } else {
            return ''; // Skip optional empty fields
        }
    }
    
    return '<div class="field-row">
        <span class="field-label">' . htmlspecialchars($label) . ':</span>
        <span class="field-value">' . nl2br(htmlspecialchars($value)) . '</span>
    </div>';
}

function generateImage($label, $path, $required = false) {
    if (empty($path) || !file_exists($path)) {
        if ($required) {
            return '<div class="field-row">
                <span class="field-label">' . htmlspecialchars($label) . ':</span>
                <span class="field-value missing">⚠️ IMAGE MISSING</span>
            </div>';
        } else {
            return ''; // Skip optional missing images
        }
    }
    
    // Resize image to uniform dimensions for consistent PDF layout
    // All images will be exactly 400px × 300px in the PDF
    $uniformPath = ImageOptimizer::resizeToUniform($path, 400, 300, 75);
    
    return '<div class="image-block">
        <div class="image-caption">' . htmlspecialchars($label) . '</div>
        <img src="' . $uniformPath . '" alt="' . htmlspecialchars($label) . '" width="400" height="300">
    </div>';
}

function generateFooter() {
    return '<div class="footer">
        <p><strong>Car Inspection Expert System</strong></p>
        <p>This report contains complete inspection data from all 23 steps.</p>
        <p>Report generated on ' . date('Y-m-d H:i:s') . '</p>
    </div>';
}

function formatArray($value) {
    if (is_array($value)) {
        // Filter out empty values
        $filtered = array_filter($value, function($v) {
            return $v !== '' && $v !== null && $v !== false;
        });
        
        if (!empty($filtered)) {
            return implode(', ', $filtered);
        }
        return ''; // Return empty string if no valid values
    }
    return (string)$value;
}

function compressAllImages($data) {
    foreach ($data as $key => $value) {
        if (strpos($key, '_path') !== false && !empty($value) && file_exists($value)) {
            $data[$key] = ImageOptimizer::compressToFile($value, 1200, 65);
        }
    }
    return $data;
}
