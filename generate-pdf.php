<?php
/**
 * Complete PDF Generation - All 23 Steps, All Fields
 * Ensures NO field is ever missing
 */

// Auto-configure PHP settings
require_once __DIR__ . '/auto-config.php';
require_once __DIR__ . '/init-directories.php';

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
        // Compress all images first (optimized for speed)
        $data = compressAllImages($data);
        
        // Get temp directory
        $tmpDir = DirectoryManager::getAbsolutePath('tmp');
        
        // Create mPDF with OPTIMIZED settings for speed
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'tempDir' => $tmpDir,
            'useSubstitutions' => false,
            'simpleTables' => true,
            'packTableData' => true,
            'dpi' => 72,  // Reduced for faster processing
            'img_dpi' => 72,  // Reduced for faster processing
            'compress' => true,  // Enable PDF compression
            'autoScriptToLang' => false,
            'autoLangToFont' => false,
        ]);
        
        $mpdf->use_kwt = false;
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->SetTitle('Car Inspection Report');
        $mpdf->SetAuthor('Car Inspection Expert');
        $mpdf->SetCompression(true);  // Enable compression
        
        // Generate complete HTML
        $html = generateCompleteHTML($data);
        $mpdf->WriteHTML($html);
        
        // Save PDF
        $pdfFilename = 'inspection_' . ($data['booking_id'] ?? 'unknown') . '_' . time() . '.pdf';
        $pdfPath = DirectoryManager::getAbsolutePath('pdfs/' . $pdfFilename);
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
    $html .= generateField('Engineer Name', $data['engineer_name'] ?? '', false);
    $html .= generateField('Customer Name', $data['customer_name'] ?? '', false);
    $html .= generateField('Customer Phone', $data['customer_phone'] ?? '', false);
    $html .= generateField('Time', $data['inspection_time'] ?? '', false);
    $html .= generateField('Inspection Address', $data['inspection_address'] ?? '', false);
    $html .= generateField('OBD Scanning', $data['obd_scanning'] ?? '', false);
    $html .= generateField('Car', $data['car'] ?? '', false);
    $html .= generateField('Lead Owner', $data['lead_owner'] ?? '', false);
    
    // ========================================================================
    // STEP 2: Expert Details
    // ========================================================================
    $html .= generateStepHeader(2, 'Expert Details');
    
    // Taking Payment field
    $html .= generateField('Taking Payment', $data['taking_payment'] ?? '', true);
    
    // Payment Screenshot (if payment was made)
    if (isset($data['taking_payment']) && $data['taking_payment'] === 'Yes') {
        if (!empty($data['payment_screenshot_path'])) {
            $paymentImages = [];
            $paymentImages[] = generateImage('Payment Screenshot', $data['payment_screenshot_path'], false);
            $html .= '<div style="margin-top: 15px;">';
            $html .= generateImageGrid($paymentImages);
            $html .= '</div>';
        }
    }
    
    // Image in grid
    $images = [];
    $images[] = generateImage('Your photo with car\'s number plate', $data['car_photo_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // Location fields (all mandatory) - Normal fields without special styling
    $html .= generateField('Latitude', $data['latitude'] ?? '', true);
    $html .= generateField('Longitude', $data['longitude'] ?? '', true);
    $html .= generateField('Full Location Address', $data['location_address'] ?? '', true);
    
    // Optional Date and Time fields
    $dateValue = $data['expert_date'] ?? '';
    $timeValue = $data['expert_time'] ?? '';
    if (!empty($dateValue)) {
        $html .= generateField('Date', $dateValue, false);
    }
    if (!empty($timeValue)) {
        $html .= generateField('Time', $timeValue, false);
    }
    
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
    
    // Mandatory fields continued
    $html .= generateField('Fuel Type', formatArray($data['fuel_type'] ?? []), true);
    $html .= generateField('Engine Capacity (in CC)', $data['engine_capacity'] ?? '', true);
    $html .= generateField('Transmission', $data['transmission'] ?? '', true);
    $html .= generateField('Car Colour', $data['car_colour'] ?? '', true);
    $html .= generateField('Car KM Current Reading', $data['car_km_reading'] ?? '', true);
    $html .= generateField('Number of Car Keys Available', $data['car_keys_available'] ?? '', true);
    $html .= generateField('Chassis Number', $data['chassis_number'] ?? '', true);
    $html .= generateField('Engine Number', $data['engine_number'] ?? '', true);
    
    // Images in grid
    $images = [];
    $images[] = generateImage('Car KM Reading Photo', $data['car_km_photo_path'] ?? '', true);
    $images[] = generateImage('Chassis No Plate', $data['chassis_plate_photo_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // ========================================================================
    // STEP 4: Car Documents
    // ========================================================================
    $html .= generateStepHeader(4, 'Car Documents');
    
    $html .= generateField('Registration Certificate', formatArray($data['registration_certificate'] ?? []), true);
    $html .= generateField('Car Insurance', formatArray($data['car_insurance'] ?? []), true);
    $html .= generateField('Car Finance NOC', formatArray($data['car_finance_noc'] ?? []), true);
    
    // ========================================================================
    // STEP 5: Body Frame Accidental Checklist
    // ========================================================================
    $html .= generateStepHeader(5, 'Body Frame Accidental Checklist');
    
    // Text fields first
    $html .= generateField('Radiator Core Support', formatArray($data['radiator_core'] ?? []), true);
    $html .= generateField('Match Chassis No Plate with Real Body', $data['match_chassis'] ?? '', true);
    $html .= generateField('Driver Side Strut Tower Apron', formatArray($data['driver_strut'] ?? []), true);
    $html .= generateField('Passenger Strut Tower Apron', formatArray($data['passenger_strut'] ?? []), true);
    $html .= generateField('Front Bonnet UnderBody', formatArray($data['front_bonnet'] ?? []), true);
    $html .= generateField('Boot Floor', formatArray($data['boot_floor'] ?? []), true);
    
    // Images in grid
    $images = [];
    $images[] = generateImage('Radiator Core Support', $data['radiator_core_image_path'] ?? '', true);
    $images[] = generateImage('Driver Side Strut Tower Apron', $data['driver_strut_image_path'] ?? '', true);
    $images[] = generateImage('Passenger Strut Tower Apron', $data['passenger_strut_image_path'] ?? '', true);
    $images[] = generateImage('Front Bonnet UnderBody', $data['front_bonnet_image_path'] ?? '', true);
    $images[] = generateImage('Boot Floor', $data['boot_floor_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // ========================================================================
    // STEP 6: Exterior Body
    // ========================================================================
    $html .= generateStepHeader(6, 'Exterior Body');
    
    // Front Bumper
    $html .= generateField('Front Bumper', formatArray($data['front_bumper'] ?? []), true);
    if (!isOkSelected($data['front_bumper'] ?? [])) {
        $images = [];
        $images[] = generateImage('Front Bumper', $data['front_bumper_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // Rear Bumper
    $html .= generateField('Rear Bumper', formatArray($data['rear_bumper'] ?? []), true);
    if (!isOkSelected($data['rear_bumper'] ?? [])) {
        $images = [];
        $images[] = generateImage('Rear Bumper', $data['rear_bumper_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // Bonnet
    $html .= generateField('Bonnet', formatArray($data['bonnet'] ?? []), true);
    if (!isOkSelected($data['bonnet'] ?? [])) {
        $images = [];
        $images[] = generateImage('Bonnet', $data['bonnet_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // Roof
    $html .= generateField('Roof', formatArray($data['roof'] ?? []), true);
    if (!isOkSelected($data['roof'] ?? [])) {
        $images = [];
        $images[] = generateImage('Roof', $data['roof_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // Windshield
    $html .= generateField('Windshield', formatArray($data['windshield'] ?? []), true);
    if (!isOkSelected($data['windshield'] ?? [])) {
        $images = [];
        $images[] = generateImage('Windshield', $data['windshield_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // ========================================================================
    // STEP 7: Engine (Before Test Drive)
    // ========================================================================
    $html .= generateStepHeader(7, 'Engine (Before Test Drive)');
    
    // Car Start - conditional image
    $html .= generateField('Car Start', formatArray($data['car_start'] ?? []), true);
    if (!isOkSelected($data['car_start'] ?? [])) {
        $images = [];
        $images[] = generateImage('Car Start', $data['car_start_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // Wiring
    $html .= generateField('Wiring', formatArray($data['wiring'] ?? []), true);
    $images = [];
    $images[] = generateImage('Wiring', $data['wiring_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // Engine Oil Quality
    $html .= generateField('Engine Oil Quality', formatArray($data['engine_oil'] ?? []), true);
    $images = [];
    $images[] = generateImage('Engine Oil Quality', $data['engine_oil_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    $html .= generateField('Engine Oil Cap', formatArray($data['engine_oil_cap'] ?? []), true);
    
    // Engine Mounting and Components - conditional image
    $html .= generateField('Engine Mounting and Components', formatArray($data['engine_mounting'] ?? []), true);
    if (!isOkSelected($data['engine_mounting'] ?? [])) {
        $images = [];
        $images[] = generateImage('Engine Mounting and Components', $data['engine_mounting_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // Coolant Level - conditional image
    $html .= generateField('Coolant Level', formatArray($data['coolant_level'] ?? []), true);
    if (!isOkSelected($data['coolant_level'] ?? [])) {
        $images = [];
        $images[] = generateImage('Coolant Level', $data['coolant_level_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    $html .= generateField('Coolant Quality', formatArray($data['coolant_quality'] ?? []), true);
    
    // Smoke Emission
    $html .= generateField('Smoke Emission', formatArray($data['smoke_emission'] ?? []), true);
    $images = [];
    $images[] = generateImage('Smoke Emission', $data['smoke_emission_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // Battery
    $html .= generateField('Battery', formatArray($data['battery'] ?? []), true);
    
    // ========================================================================
    // STEP 8: OBD Scan
    // ========================================================================
    $html .= generateStepHeader(8, 'OBD Scan');
    
    // Any Fault Codes Present? - conditional image
    $html .= generateField('Any Fault Codes Present?', formatArray($data['fault_codes'] ?? []), true);
    if (!isNotCheckedSelected($data['fault_codes'] ?? [])) {
        $images = [];
        $images[] = generateImage('Fault Codes', $data['fault_codes_image_path'] ?? '', false);
        $html .= generateImageGrid($images);
    }
    
    // OBD Scan Photo (always shown)
    $images = [];
    $images[] = generateImage('OBD Scan Photo', $data['obd_scan_photo_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
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
    
    // Image in grid
    $images = [];
    $images[] = generateImage('Multi Function Display', $data['multi_function_display_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
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
    
    // Image in grid
    $images = [];
    $images[] = generateImage('Car Roof From Inside', $data['car_roof_inside_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
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
    
    // Images in grid
    $images = [];
    $images[] = generateImage('AC Cool Mode Temperature', $data['ac_cool_image_path'] ?? '', true);
    $images[] = generateImage('AC Hot Mode Temperature', $data['ac_hot_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // ========================================================================
    // STEP 12: Tyres
    // ========================================================================
    $html .= generateStepHeader(12, 'Tyres');
    
    $html .= generateField('Tyre Size', $data['tyre_size'] ?? '', true);
    $html .= generateField('Tyre Type', $data['tyre_type'] ?? '', true);
    $html .= generateField('Rim Type', $data['rim_type'] ?? '', true);
    
    // Text fields first
    $html .= generateField('Driver Front Tyre Depth Check', $data['driver_front_tyre_depth'] ?? '', true);
    $html .= generateField('Driver Front Tyre Manufacturing Date', $data['driver_front_tyre_date'] ?? '', true);
    $html .= generateField('Driver Front Tyre Shape', $data['driver_front_tyre_shape'] ?? '', true);
    
    $html .= generateField('Driver Back Tyre Depth Check', $data['driver_back_tyre_depth'] ?? '', true);
    $html .= generateField('Driver Back Tyre Manufacturing Date', $data['driver_back_tyre_date'] ?? '', true);
    $html .= generateField('Driver Back Tyre Shape', $data['driver_back_tyre_shape'] ?? '', true);
    
    $html .= generateField('Passenger Back Tyre Depth Check', $data['passenger_back_tyre_depth'] ?? '', true);
    $html .= generateField('Passenger Back Tyre Manufacturing Date', $data['passenger_back_tyre_date'] ?? '', true);
    $html .= generateField('Passenger Back Tyre Shape', $data['passenger_back_tyre_shape'] ?? '', true);
    
    $html .= generateField('Passenger Front Tyre Depth Check', $data['passenger_front_tyre_depth'] ?? '', true);
    $html .= generateField('Passenger Front Tyre Manufacturing Date', $data['passenger_front_tyre_date'] ?? '', true);
    $html .= generateField('Passenger Front Tyre Shape', $data['passenger_front_tyre_shape'] ?? '', true);
    
    $html .= generateField('Stepney Tyre Depth Check', $data['stepney_tyre_depth'] ?? '', true);
    $html .= generateField('Stepney Tyre Manufacturing Date', $data['stepney_tyre_date'] ?? '', true);
    $html .= generateField('Stepney Tyre Shape', $data['stepney_tyre_shape'] ?? '', true);
    
    // Images in grid
    $images = [];
    $images[] = generateImage('Driver Front Tyre', $data['driver_front_tyre_image_path'] ?? '', true);
    $images[] = generateImage('Driver Back Tyre', $data['driver_back_tyre_image_path'] ?? '', true);
    $images[] = generateImage('Passenger Back Tyre', $data['passenger_back_tyre_image_path'] ?? '', true);
    $images[] = generateImage('Passenger Front Tyre', $data['passenger_front_tyre_image_path'] ?? '', true);
    $images[] = generateImage('Stepney Tyre', $data['stepney_tyre_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
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
    
    // Image in grid
    $images = [];
    $images[] = generateImage('Oil Leak Near Engine', $data['oil_leak_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
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
    
    // Front Passenger Side
    $html .= generateField('Brake Pads Front Passenger Side', $data['brake_pads_front_passenger'] ?? '', true);
    $html .= generateField('Brake Discs Front Passenger Side', $data['brake_discs_front_passenger'] ?? '', true);
    
    // Back Passenger Side
    $html .= generateField('Brake Pads Back Passenger Side', $data['brake_pads_back_passenger'] ?? '', true);
    $html .= generateField('Brake Discs Back Passenger Side', $data['brake_discs_back_passenger'] ?? '', true);
    
    // Back Driver Side
    $html .= generateField('Brake Pads Back Driver Side', $data['brake_pads_back_driver'] ?? '', true);
    $html .= generateField('Brake Discs Back Driver Side', $data['brake_discs_back_driver'] ?? '', true);
    
    // Brake System
    $html .= generateField('Brake Fluid Reservoir', $data['brake_fluid_reservoir'] ?? '', true);
    $html .= generateField('Brake Fluid Cap', $data['brake_fluid_cap'] ?? '', true);
    $html .= generateField('Parking Hand Brake', $data['parking_hand_brake'] ?? '', true);
    
    // ========================================================================
    // STEP 17: Suspension
    // ========================================================================
    $html .= generateStepHeader(17, 'Suspension');
    
    $html .= generateField('Shocker Bounce Test', $data['shocker_bounce_test'] ?? '', true);
    $html .= generateField('Driver Side Suspension Assembly', $data['driver_suspension_assembly'] ?? '', true);
    $html .= generateField('Driver Side Shocker Leakage', $data['driver_shocker_leakage'] ?? '', true);
    $html .= generateField('Passenger Side Suspension Assembly', $data['passenger_suspension_assembly'] ?? '', true);
    $html .= generateField('Passenger Side Shocker Leakage', $data['passenger_shocker_leakage'] ?? '', true);
    
    // Images in grid
    $images = [];
    $images[] = generateImage('Driver Side Front Shocker', $data['driver_front_shocker_photo_path'] ?? '', true);
    $images[] = generateImage('Passenger Side Front Shocker', $data['passenger_front_shocker_photo_path'] ?? '', true);
    $images[] = generateImage('Driver Side Rear Shocker', $data['driver_rear_shocker_photo_path'] ?? '', true);
    $images[] = generateImage('Passenger Side Rear Shocker', $data['passenger_rear_shocker_photo_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
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
    
    // Images in grid
    $images = [];
    $images[] = generateImage('Underbody Left', $data['underbody_left_path'] ?? '', true);
    $images[] = generateImage('Underbody Rear', $data['underbody_rear_path'] ?? '', true);
    $images[] = generateImage('Underbody Right', $data['underbody_right_path'] ?? '', true);
    $images[] = generateImage('Underbody Front', $data['underbody_front_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // ========================================================================
    // STEP 20: Equipments
    // ========================================================================
    $html .= generateStepHeader(20, 'Equipments');
    
    $html .= generateField('Tool Kit', $data['tool_kit'] ?? '', true);
    
    // Image in grid
    $images = [];
    $images[] = generateImage('Tool Kit', $data['tool_kit_image_path'] ?? '', true);
    $html .= generateImageGrid($images);
    
    // ========================================================================
    // STEP 21: Final Car Result
    // ========================================================================
    $html .= generateStepHeader(21, 'Final Car Result');
    
    $html .= generateField('Any Issues Found in the Car?', $data['issues_found'] ?? '', true);
    
    // Image in grid (optional)
    $images = [];
    $issueImage = generateImage('Photos of Issues', $data['issues_photo_path'] ?? '', false);
    if (!empty($issueImage)) {
        $images[] = $issueImage;
        $html .= generateImageGrid($images);
    }
    
    // ========================================================================
    // STEP 22: Car Images From All Directions
    // ========================================================================
    $html .= generateStepHeader(22, 'Car Images From All Directions');
    
    // Collect all images for this step
    $images = [];
    $images[] = generateImage('Front', $data['car_front_path'] ?? '', true);
    $images[] = generateImage('Corner Front - Driver', $data['car_corner_front_driver_path'] ?? '', true);
    $images[] = generateImage('Driver Side', $data['car_driver_side_path'] ?? '', true);
    $images[] = generateImage('Corner Back - Driver', $data['car_corner_back_driver_path'] ?? '', true);
    $images[] = generateImage('Back', $data['car_back_path'] ?? '', true);
    $images[] = generateImage('Corner Back - Passenger', $data['car_corner_back_passenger_path'] ?? '', true);
    $images[] = generateImage('Passenger Side', $data['car_passenger_side_path'] ?? '', true);
    $images[] = generateImage('Corner Front - Passenger', $data['car_corner_front_passenger_path'] ?? '', true);
    $images[] = generateImage('Front Interior', $data['car_front_interior_path'] ?? '', true);
    $images[] = generateImage('Rear Interior', $data['car_rear_interior_path'] ?? '', true);
    $images[] = generateImage('4 Way Switch Driver Side', $data['car_4way_switch_path'] ?? '', true);
    $images[] = generateImage('Trunk Open', $data['car_trunk_open_path'] ?? '', true);
    $images[] = generateImage('Car KM Reading', $data['car_km_reading_final_path'] ?? '', true);
    
    // Generate image grid
    $html .= generateImageGrid($images);
    
    // ========================================================================
    // STEP 23: Other Images
    // ========================================================================
    // Only show this step if at least one other image exists
    $otherImages = [];
    for ($i = 1; $i <= 5; $i++) {
        $fieldName = 'other_image_' . $i . '_path';
        if (!empty($data[$fieldName])) {
            $otherImages[] = generateImage('Other Image ' . $i, $data[$fieldName], false);
        }
    }
    
    // Only display STEP 23 if at least one image exists
    if (!empty($otherImages)) {
        $html .= generateStepHeader(23, 'Other Images');
        $html .= generateImageGrid($otherImages);
    }
    
    $html .= generateFooter();
    
    return $html;
}

/**
 * Generate PDF styles with UNIVERSAL LAYOUT RULES
 * 
 * MANDATORY SPECIFICATIONS:
 * 1. All images: EXACTLY 300x225 pixels (no exceptions)
 * 2. Flex-box grid: 3 images per row (all steps)
 * 3. Gap: 12px between images (horizontal & vertical)
 * 4. Text: 20% larger than original (except header)
 * 5. Spacing: Consistent margins and padding throughout
 * 6. Alignment: Perfect horizontal and vertical alignment
 * 
 * Applied to ALL 23 steps uniformly.
 */
function generateStyles() {
    return '
    <style>
        /* Base styles - 20% larger text */
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 12px; 
            line-height: 1.6; 
            margin: 0;
            padding: 0;
        }
        
        /* Step header - 20% larger with RED theme - WHITE BACKGROUND to prevent bleeding */
        .step-header { 
            background: #ffffff; 
            padding: 10px 12px; 
            font-size: 12.6px; 
            font-weight: bold;
            margin: 20px 0 15px 0;
            border-left: 5px solid #D32F2F;
            page-break-after: avoid;
            color: #c62828;
        }
        
        /* Field rows - White background to prevent bleeding */
        .field-row { 
            background-color: #ffffff;
            margin: 6px 0;
            padding: 6px 0;
            border-bottom: 1px solid #e0e0e0;
            page-break-inside: avoid;
        }
        .field-label { 
            font-weight: bold; 
            color: #333; 
            display: inline-block; 
            width: 40%; 
            font-size: 10px;
        }
        .field-value { 
            color: #000; 
            display: inline-block; 
            width: 58%; 
            font-size: 10px;
        }
        .field-value.missing { 
            color: #d32f2f; 
            font-weight: bold; 
        }
        
        /* Image grid table - Universal 3-column layout */
        .image-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin: 20px 0;
        }
        
        /* Individual image cell - Consistent 3-column structure */
        .image-grid td {
            width: 33.333%;
            vertical-align: top;
            text-align: center;
            padding: 5px;
        }
        
        /* Image label - Bold and emphasized with RED theme */
        .image-label {
            font-size: 11px;
            font-weight: bold;
            color: #c62828;
            margin-bottom: 8px;
            text-align: center;
            line-height: 1.4;
            min-height: 32px;
            display: block;
        }
        
        /* Image styling - LARGER uniform dimensions, clean and professional */
        .image-grid img {
            width: 300px !important;
            height: 225px !important;
            border: none;
            display: block;
            margin: 0 auto;
        }
        
        /* Footer with RED theme */
        .footer { 
            text-align: center; 
            margin-top: 25px; 
            padding-top: 18px; 
            border-top: 2px solid #D32F2F; 
            font-size: 10.8px; 
            color: #666; 
        }
    </style>';
}

function generateHeader($data) {
    $booking_id = htmlspecialchars($data['booking_id'] ?? 'N/A');
    $engineer_name = htmlspecialchars($data['engineer_name'] ?? 'N/A');
    $customer_name = htmlspecialchars($data['customer_name'] ?? $data['booking_id'] ?? 'N/A');
    
    $headerHTML = '
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #D32F2F; padding: 25px 30px; margin: 0 0 0 0;">
        <tr>
            <td width="30%" style="vertical-align: middle; padding: 0; margin: 0;">
                <img src="logo.png" style="width: 230px; height: 160px; display: block;" />
            </td>
            <td width="70%" style="vertical-align: middle; text-align: right; padding: 0 0 0 20px; margin: 0;">
                <div style="color: #ffffff; font-family: Arial, Helvetica, sans-serif; line-height: 1.8;">
                    <div style="font-size: 16pt; font-weight: bold; margin-bottom: 10px; letter-spacing: 0.5px;">Used Car Inspection Report</div>
                    <div style="font-size: 11pt; margin-bottom: 5px;">ID: ' . $booking_id . '</div>
                    <div style="font-size: 11pt; margin-bottom: 5px;">Engineer: ' . $engineer_name . '</div>
                    <div style="font-size: 11pt;">Customer Name: ' . $customer_name . '</div>
                </div>
            </td>
        </tr>
    </table>
    <div style="text-align: center; padding: 15px 0; margin: 0 0 30px 0; background-color: #f5f5f5; border-bottom: 2px solid #D32F2F;">
        <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #666;">Generated: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    ';
    
    return $headerHTML;
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

/**
 * Generate image with OPTIMIZED UNIFORM DIMENSIONS
 * 
 * UNIVERSAL IMAGE PROCESSING RULE:
 * - ALL images are resized to EXACTLY 300x225 pixels (larger for clarity)
 * - Compressed to 70% quality for fast PDF generation
 * - Maintains aspect ratio with letterboxing/cropping
 * - Ensures perfect alignment in 3-column table grid
 * - Applied to ALL 23 steps without exception
 * 
 * @param string $label Image label/caption
 * @param string $path Path to image file
 * @param bool $required Whether image is mandatory
 * @return string HTML for image block or error message
 */
function generateImage($label, $path, $required = false) {
    // Convert to absolute path if relative
    $absolutePath = DirectoryManager::getAbsolutePath($path);
    
    if (empty($absolutePath) || !file_exists($absolutePath)) {
        if ($required) {
            return '<div class="field-row">
                <span class="field-label">' . htmlspecialchars($label) . ':</span>
                <span class="field-value missing">⚠️ IMAGE MISSING</span>
            </div>';
        } else {
            return null; // Skip optional missing images
        }
    }
    
    // OPTIMIZED DIMENSIONS: 300x225 pixels (larger, 70% quality for speed)
    // This ensures clear visibility while maintaining fast PDF generation
    $uniformPath = ImageOptimizer::resizeToUniform($absolutePath, 300, 225, 70);
    
    // Return array for table-based grid
    return [
        'label' => htmlspecialchars($label),
        'path' => $uniformPath
    ];
}

/**
 * Generate uniform 3-column table grid for images
 * 
 * UNIVERSAL LAYOUT RULE:
 * - 3 images per row (consistent across ALL steps)
 * - Table-based layout for perfect mPDF compatibility
 * - Automatic row wrapping for any number of images
 * - Applied to ALL steps (1-23) without exception
 * 
 * @param array $images Array of image HTML blocks
 * @return string HTML for table grid container
 */
function generateImageGrid($images) {
    // Filter out null/empty images
    $images = array_filter($images, function($img) {
        return !empty($img) && is_array($img);
    });
    
    if (empty($images)) {
        return '';
    }
    
    // Build HTML table with 3 columns
    $html = '<table class="image-grid" cellpadding="0" cellspacing="0" border="0">';
    
    // Split images into rows of 3
    $chunks = array_chunk($images, 3);
    
    foreach ($chunks as $row) {
        $html .= '<tr>';
        
        foreach ($row as $image) {
            $html .= '<td>';
            $html .= '<div class="image-label">' . $image['label'] . '</div>';
            $html .= '<img src="' . $image['path'] . '" alt="' . $image['label'] . '" width="300" height="225">';
            $html .= '</td>';
        }
        
        // Fill empty cells if row has less than 3 images
        $remaining = 3 - count($row);
        for ($i = 0; $i < $remaining; $i++) {
            $html .= '<td></td>';
        }
        
        $html .= '</tr>';
    }
    
    $html .= '</table>';
    
    return $html;
}

function generateFooter() {
    return '<div class="footer">
        <p><strong>Car Inspection Expert System</strong></p>
        <div style="background: #fff3e0; border: 2px solid #ff9800; border-radius: 6px; padding: 15px; margin: 15px 0;">
            <p style="font-weight: bold; font-size: 13.1px; color: #e65100; margin: 0; line-height: 1.6; text-align: left;">
                ⚠️ <span style="text-decoration: underline;">IMPORTANT NOTE:</span><br>
                While inspecting the vehicle, our boy will be responsible as long as he is at the inspection site, after that he will not be responsible even if anything happens to the vehicle.
            </p>
        </div>
        <p>Report generated on ' . date('Y-m-d H:i:s') . '</p>
    </div>';
}

/**
 * Check if "OK" is selected in an array (case-insensitive)
 */
function isOkSelected($values) {
    if (!is_array($values)) {
        return false;
    }
    
    foreach ($values as $value) {
        if (strtolower(trim($value)) === 'ok') {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if "Not Checked" is selected in an array (case-insensitive)
 */
function isNotCheckedSelected($values) {
    if (!is_array($values)) {
        return false;
    }
    
    foreach ($values as $value) {
        if (strtolower(trim($value)) === 'not checked') {
            return true;
        }
    }
    
    return false;
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
        if (strpos($key, '_path') !== false && !empty($value)) {
            // Convert to absolute path
            $absolutePath = DirectoryManager::getAbsolutePath($value);
            
            if (file_exists($absolutePath)) {
                $data[$key] = ImageOptimizer::compressToFile($absolutePath, 1200, 65);
            }
        }
    }
    return $data;
}
