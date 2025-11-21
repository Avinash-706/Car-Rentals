<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inspection Expert - USED CAR 3.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🚗</div>
            <h1>USED CAR 3.0</h1>
        </div>

        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
            <div class="progress-text" id="progressText">Step 1 of 16</div>
        </div>

        <!-- Form Container -->
        <form id="inspectionForm" enctype="multipart/form-data">
            
            <!-- STEP 1: INSPECTION DETAILS -->
            <div class="form-step active" data-step="1">
                <h2>⊙ Inspection Details</h2>
                
                <div class="form-group">
                    <label>Enter Booking ID <span class="required">*</span></label>
                    <input type="text" name="booking_id" required>
                </div>

                <div class="form-group">
                    <label>Expert ID</label>
                    <input type="text" name="expert_id">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name">
                    </div>
                    <div class="form-group">
                        <label>Customer Phone</label>
                        <input type="tel" name="customer_phone">
                    </div>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="inspection_date">
                </div>

                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="inspection_time">
                </div>

                <div class="form-group">
                    <label>Inspection Address</label>
                    <textarea name="inspection_address" rows="3" placeholder="Provide complete address..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>OBD Scanning</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="obd_scanning" value="Yes"> Yes
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="obd_scanning" value="No"> No
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Car</label>
                        <input type="text" name="car">
                    </div>
                </div>

                <div class="form-group">
                    <label>Lead Owner</label>
                    <input type="text" name="lead_owner">
                </div>

                <div class="form-group">
                    <label>Pending Amount</label>
                    <input type="number" name="pending_amount" step="0.01">
                </div>
            </div>

            <!-- STEP 2: EXPERT DETAILS -->
            <div class="form-step" data-step="2">
                <h2>⊙ Expert Details</h2>
                
                <div class="form-group">
                    <label>Inspection 45 Minutes Delayed? <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="inspection_delayed" value="Yes" required> Yes
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="inspection_delayed" value="No"> No
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Your photo with car's number plate <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_photo" id="carPhoto" accept="image/*" required>
                        <label for="carPhoto" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="carPhotoPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Current Location <span class="required">*</span></label>
                    <div class="location-group">
                        <div class="form-row">
                            <input type="text" name="latitude" id="latitude" placeholder="Lat" readonly>
                            <input type="text" name="longitude" id="longitude" placeholder="Long" readonly>
                            <button type="button" class="btn-location" id="fetchLocation">📍</button>
                        </div>
                        <textarea name="location_address" id="locationAddress" rows="2" placeholder="Location where the inspection is happening" readonly></textarea>
                        <div class="location-error" id="locationError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="expert_date" id="expertDate" readonly>
                </div>

                <div class="form-group">
                    <label>Time</label>
                    <input type="text" name="expert_time" id="expertTime" readonly>
                </div>
            </div>

            <!-- STEP 3: CAR DETAILS -->
            <div class="form-step" data-step="3">
                <h2>⊙ Car Details</h2>
                
                <div class="form-group">
                    <label>Car Company <span class="required">*</span></label>
                    <input type="text" name="car_company" required>
                </div>

                <div class="form-group">
                    <label>Car Registration Number <span class="required">*</span></label>
                    <input type="text" name="car_registration_number" required>
                </div>

                <div class="form-group">
                    <label>Car Registration Year (YYYY) <span class="required">*</span></label>
                    <input type="text" name="car_registration_year" required pattern="\d{4}" maxlength="4" placeholder="YYYY">
                    <small>Enter 4-digit year</small>
                </div>

                <div class="form-group">
                    <label>Car Variant <span class="required">*</span></label>
                    <input type="text" name="car_variant" required>
                </div>

                <div class="form-group">
                    <label>Car Registered State <span class="required">*</span></label>
                    <input type="text" name="car_registered_state" required>
                </div>

                <div class="form-group">
                    <label>Car Registered City</label>
                    <input type="text" name="car_registered_city">
                </div>

                <div class="form-group">
                    <label>Fuel Type <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="fuel_type[]" value="Petrol"> Petrol
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fuel_type[]" value="Diesel"> Diesel
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fuel_type[]" value="Electric"> Electric
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fuel_type[]" value="Hybrid"> Hybrid
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fuel_type[]" value="CNG"> CNG
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Capacity (in CC) <span class="required">*</span></label>
                    <input type="number" name="engine_capacity" required min="0">
                </div>

                <div class="form-group">
                    <label>Transmission <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="transmission" value="Manual" required> Manual
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="transmission" value="Automatic"> Automatic
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Colour <span class="required">*</span></label>
                    <input type="text" name="car_colour" required>
                </div>

                <div class="form-group">
                    <label>Car KM Current Reading <span class="required">*</span></label>
                    <input type="number" name="car_km_reading" required min="0">
                </div>

                <div class="form-group">
                    <label>Car KM Reading Photo <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_km_photo" id="carKmPhoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="carKmPhoto" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="carKmPhotoPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Number of Car Keys Available <span class="required">*</span></label>
                    <input type="number" name="car_keys_available" required min="0">
                </div>

                <div class="form-group">
                    <label>Chassis Number <span class="required">*</span></label>
                    <input type="text" name="chassis_number" required>
                </div>

                <div class="form-group">
                    <label>Engine Number <span class="required">*</span></label>
                    <input type="text" name="engine_number" required>
                </div>

                <div class="form-group">
                    <label>Chassis No Plate <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="chassis_plate_photo" id="chassisPlatePhoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="chassisPlatePhoto" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="chassisPlatePhotoPreview"></div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: CAR DOCUMENTS -->
            <div class="form-step" data-step="4">
                <h2>⊙ Car Documents</h2>
                
                <div class="form-group">
                    <label>Registration Certificate <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="registration_certificate[]" value="Available"> Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="registration_certificate[]" value="Expired"> Expired
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="registration_certificate[]" value="Not Available"> Not Available
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Insurance <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_insurance[]" value="Available"> Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_insurance[]" value="Not Available"> Not Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_insurance[]" value="Expired"> Expired
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Finance NOC <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_finance_noc[]" value="Available"> Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_finance_noc[]" value="Not Available"> Not Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_finance_noc[]" value="Not Required"> Not Required
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Purchase Invoice <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_purchase_invoice[]" value="Available"> Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_purchase_invoice[]" value="Not Available"> Not Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_purchase_invoice[]" value="Not Required"> Not Required
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Bi-Fuel Certification <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="bifuel_certification[]" value="Available"> Available
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="bifuel_certification[]" value="Not Required"> Not Required
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="bifuel_certification[]" value="Not Available"> Not Available
                        </label>
                    </div>
                </div>
            </div>

            <!-- STEP 5: BODY FRAME ACCIDENTAL CHECKLIST -->
            <div class="form-step" data-step="5">
                <h2>⊙ Body Frame Accidental Checklist</h2>
                
                <!-- Radiator Core Support -->
                <div class="form-group">
                    <label>Radiator Core Support <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="radiator_core">
                        <label class="checkbox-label">
                            <input type="checkbox" name="radiator_core[]" value="Accidental"> Accidental
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="radiator_core[]" value="OK" data-ok-checkbox> OK
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="radiator_core[]" value="Rusted"> Rusted
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Radiator Core Support Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="radiator_core_image" id="radiatorCoreImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="radiatorCoreImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="radiatorCoreImagePreview"></div>
                    </div>
                </div>

                <!-- Match Chassis No Plate -->
                <div class="form-group">
                    <label>Match Chassis No Plate with Real Body <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="match_chassis" value="Matching" required> Matching
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="match_chassis" value="Not Matching"> Not Matching
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="match_chassis" value="Not Able To Locate"> Not Able To Locate
                        </label>
                    </div>
                </div>

                <!-- Driver Side Strut Tower Apron -->
                <div class="form-group">
                    <label>Driver Side Strut Tower Apron <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="driver_strut">
                        <label class="checkbox-label">
                            <input type="checkbox" name="driver_strut[]" value="Accidental"> Accidental
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="driver_strut[]" value="OK" data-ok-checkbox> OK
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="driver_strut[]" value="Rusted"> Rusted
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Driver Side Strut Tower Apron Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="driver_strut_image" id="driverStrutImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="driverStrutImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="driverStrutImagePreview"></div>
                    </div>
                </div>

                <!-- Passenger Strut Tower Apron -->
                <div class="form-group">
                    <label>Passenger Strut Tower Apron <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="passenger_strut">
                        <label class="checkbox-label">
                            <input type="checkbox" name="passenger_strut[]" value="Accidental"> Accidental
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="passenger_strut[]" value="OK" data-ok-checkbox> OK
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="passenger_strut[]" value="Rusted"> Rusted
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Passenger Strut Tower Apron Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="passenger_strut_image" id="passengerStrutImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="passengerStrutImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="passengerStrutImagePreview"></div>
                    </div>
                </div>

                <!-- Front Bonnet UnderBody -->
                <div class="form-group">
                    <label>Front Bonnet UnderBody <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="front_bonnet">
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bonnet[]" value="Accidental"> Accidental
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bonnet[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bonnet[]" value="Repainted"> Repainted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bonnet[]" value="Rusted"> Rusted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bonnet[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Front Bonnet UnderBody Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="front_bonnet_image" id="frontBonnetImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="frontBonnetImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="frontBonnetImagePreview"></div>
                    </div>
                </div>

                <!-- Boot Floor -->
                <div class="form-group">
                    <label>Boot Floor <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="boot_floor">
                        <label class="checkbox-label">
                            <input type="checkbox" name="boot_floor[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="boot_floor[]" value="Rusted"> Rusted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="boot_floor[]" value="Accidental"> Accidental
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="boot_floor[]" value="Repainted"> Repainted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="boot_floor[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Boot Floor Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="boot_floor_image" id="bootFloorImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="bootFloorImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="bootFloorImagePreview"></div>
                    </div>
                </div>
            </div>

            <!-- STEP 6: EXTERIOR BODY -->
            <div class="form-step" data-step="6">
                <h2>⊙ Exterior Body</h2>
                
                <div class="form-group">
                    <label>Front Bumper <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="front_bumper">
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bumper[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bumper[]" value="Dent"> Dent
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bumper[]" value="Repainted"> Repainted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="front_bumper[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Bumper <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="rear_bumper">
                        <label class="checkbox-label">
                            <input type="checkbox" name="rear_bumper[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="rear_bumper[]" value="Dent"> Dent
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="rear_bumper[]" value="Repainted"> Repainted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="rear_bumper[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Bonnet <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="bonnet">
                        <label class="checkbox-label">
                            <input type="checkbox" name="bonnet[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="bonnet[]" value="Dent"> Dent
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="bonnet[]" value="Repainted"> Repainted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="bonnet[]" value="Rusted"> Rusted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="bonnet[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Roof <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="roof">
                        <label class="checkbox-label">
                            <input type="checkbox" name="roof[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="roof[]" value="Dent"> Dent
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="roof[]" value="Repainted"> Repainted
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="roof[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Windshield <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="windshield">
                        <label class="checkbox-label">
                            <input type="checkbox" name="windshield[]" value="Cracked"> Cracked
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="windshield[]" value="Scratches"> Scratches
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="windshield[]" value="OK" data-ok-checkbox> OK
                        </label>
                    </div>
                </div>
            </div>

            <!-- STEP 7: ENGINE (BEFORE TEST DRIVE) -->
            <div class="form-step" data-step="7">
                <h2>⊙ Engine (Before Test Drive)</h2>
                
                <div class="form-group">
                    <label>Car Start Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_start_image" id="carStartImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="carStartImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="carStartImagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Start <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="car_start">
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_start[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="car_start[]" value="Not Ok"> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wiring Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="wiring_image" id="wiringImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="wiringImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="wiringImagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wiring <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="wiring">
                        <label class="checkbox-label">
                            <input type="checkbox" name="wiring[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="wiring[]" value="Original Wiring Changed"> Original Wiring Changed
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="wiring[]" value="Damaged"> Damaged
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Oil Quality Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="engine_oil_image" id="engineOilImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="engineOilImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="engineOilImagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Oil Quality <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="engine_oil">
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_oil[]" value="Low Level"> Low Level
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_oil[]" value="Oil Change Needed"> Oil Change Needed
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_oil[]" value="Tar Found"> Tar Found
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_oil[]" value="Clean and Complete"> Clean and Complete
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Oil Cap <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="engine_oil_cap">
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_oil_cap[]" value="Clean"> Clean
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_oil_cap[]" value="Sludge Present"> Sludge Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Mounting and Components <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="engine_mounting">
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_mounting[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="engine_mounting[]" value="Not Ok"> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Coolant Level <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="coolant_level">
                        <label class="checkbox-label">
                            <input type="checkbox" name="coolant_level[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="coolant_level[]" value="Not Ok"> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Coolant Quality <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="coolant_quality">
                        <label class="checkbox-label">
                            <input type="checkbox" name="coolant_quality[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="coolant_quality[]" value="Oil Present"> Oil Present
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="coolant_quality[]" value="Water Mixed"> Water Mixed
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Smoke Emission Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="smoke_emission_image" id="smokeEmissionImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="smokeEmissionImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="smokeEmissionImagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Smoke Emission <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="smoke_emission">
                        <label class="checkbox-label">
                            <input type="checkbox" name="smoke_emission[]" value="Present"> Present
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="smoke_emission[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Battery <span class="required">*</span></label>
                    <div class="checkbox-group" data-ok-group="battery">
                        <label class="checkbox-label">
                            <input type="checkbox" name="battery[]" value="Damaged"> Damaged
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="battery[]" value="Acid Leakage"> Acid Leakage
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="battery[]" value="Rusted / Dissolved Tray"> Rusted / Dissolved Tray
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="battery[]" value="Ok" data-ok-checkbox> Ok
                        </label>
                    </div>
                </div>
            </div>

            <!-- STEP 8: OBD SCAN -->
            <div class="form-step" data-step="8">
                <h2>⊙ OBD Scan</h2>
                
                <div class="form-group">
                    <label>Any Fault Codes Present? <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="fault_codes[]" value="Yes"> Yes
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fault_codes[]" value="No"> No
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fault_codes[]" value="Port Not Working"> Port Not Working
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="fault_codes[]" value="Not Checked"> Not Checked
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>OBD Scan Photo <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="obd_scan_photo" id="obdScanPhoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="obdScanPhoto" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="obdScanPhotoPreview"></div>
                    </div>
                </div>
            </div>

            <!-- STEP 9: ELECTRICAL AND INTERIOR -->
            <div class="form-step" data-step="9">
                <h2>⊙ Electrical and Interior</h2>
                
                <div class="form-group">
                    <label>Central Lock Working <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="central_lock" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="central_lock" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ignition Switch / Push Button <span class="required">*</span></label>
                    <small>Start the car engine</small>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="ignition_switch" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="ignition_switch" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver - Front Indicator <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_front_indicator" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_front_indicator" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger - Front Indicator <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_indicator" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_indicator" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Headlight <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_headlight" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_headlight" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Headlight Highbeam <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_headlight_highbeam" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_headlight_highbeam" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Headlight <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_headlight" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_headlight" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Headlight Highbeam <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_headlight_highbeam" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_headlight_highbeam" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Number Plate Light <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_number_plate_light" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_number_plate_light" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_number_plate_light" value="Not Available" required> Not Available
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Back Indicator <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_back_indicator" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_back_indicator" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Back Indicator <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_indicator" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_indicator" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Number Plate Light <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_number_plate_light" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_number_plate_light" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_number_plate_light" value="Not Available" required> Not Available
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Light Driver <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_light_driver" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_light_driver" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Light Passenger <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_light_passenger" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_light_passenger" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Tail Light <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_tail_light" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_tail_light" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Tail Light <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_tail_light" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_tail_light" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Steering Wheel Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="steering_wheel_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_wheel_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Steering Mounted Controls <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="steering_mounted_controls" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_mounted_controls" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_mounted_controls" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Camera <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_camera" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_camera" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_camera" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reverse Parking Sensor <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="reverse_parking_sensor" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="reverse_parking_sensor" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="reverse_parking_sensor" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Multi Function Display <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="multi_function_display" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="multi_function_display" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Multi Function Display Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="multi_function_display_image" id="multiFunctionDisplayImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="multiFunctionDisplayImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="multiFunctionDisplayImagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>In Car Start</label>
                    <input type="text" name="in_car_start">
                </div>

                <div class="form-group">
                    <label>Car Horn <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="car_horn" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="car_horn" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Entertainment System <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="entertainment_system" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="entertainment_system" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="entertainment_system" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cruise Control <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="cruise_control" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="cruise_control" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="cruise_control" value="Not Applicable" required> Not Applicable
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="cruise_control" value="Not Able to Check" required> Not Able to Check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Interior Lights <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="interior_lights" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="interior_lights" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Sun Roof <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="sun_roof" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="sun_roof" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="sun_roof" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Bonnet Release Operation <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="bonnet_release_operation" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="bonnet_release_operation" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="bonnet_release_operation" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Fuel Cap Release Operation <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="fuel_cap_release_operation" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fuel_cap_release_operation" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fuel_cap_release_operation" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Check Onboard Computer AdBlue Level – Diesel Cars <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="adblue_level" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="adblue_level" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="adblue_level" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Window Safety Lock <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="window_safety_lock" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_safety_lock" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver ORVM Controls <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_orvm_controls" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_orvm_controls" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger ORVM Controls <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_orvm_controls" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_orvm_controls" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Glove Box <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="glove_box" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="glove_box" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wiper <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="wiper" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="wiper" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear View Mirror <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rear_view_mirror" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_view_mirror" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dashboard Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="dashboard_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="dashboard_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Roof From Inside <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="car_roof_from_inside" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="car_roof_from_inside" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car Roof From Inside Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_roof_inside_image" id="carRoofInsideImage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="carRoofInsideImage" class="file-label">
                            <span class="camera-icon">📷</span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="carRoofInsideImagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Seat Adjustment Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_driver" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_driver" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Seat Adjustment Driver Rear Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_driver_rear" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_driver_rear" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_driver_rear" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Driver Side Seat Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_driver_seat_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_driver_seat_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Driver Side Floor <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_driver_floor" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_driver_floor" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_driver_floor" value="Covered - Not Able To See" required> Covered - Not Able To See
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Driver Side Seat Belt <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_driver_seat_belt" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_driver_seat_belt" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Passenger Side Seat Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_seat_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_seat_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Passenger Side Floor <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_floor" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_floor" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_floor" value="Covered - Not Able To See" required> Covered - Not Able To See
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Passenger Side Seat Belt <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_seat_belt" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_passenger_seat_belt" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Seat Adjustment Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_passenger" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_passenger" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Window Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="window_driver_side" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_driver_side" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_driver_side" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Window Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="window_passenger_side" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_passenger_side" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_passenger_side" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Window Passenger Rear Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="window_passenger_rear" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_passenger_rear" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_passenger_rear" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Window Driver Rear Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="window_driver_rear" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_driver_rear" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="window_driver_rear" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Driver Side Floor <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_driver_floor" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_driver_floor" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_driver_floor" value="Covered - Not Able To See" required> Covered - Not Able To See
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Driver Side Seat Belt <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_driver_seat_belt" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_driver_seat_belt" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Driver Side Seat Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_driver_seat_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_driver_seat_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Seat Adjustment Passenger Rear Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_passenger_rear" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_passenger_rear" value="Not Working" required> Not Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="seat_adjustment_passenger_rear" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Passenger Side Floor <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_floor" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_floor" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_floor" value="Covered - Not Able To See" required> Covered - Not Able To See
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Passenger Side Seat Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_seat_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_seat_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back Passenger Side Seat Belt <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_seat_belt" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="back_passenger_seat_belt" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Side Floor – 7 Seater <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rear_side_floor_7seater" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_side_floor_7seater" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_side_floor_7seater" value="Covered - Not Able To See" required> Covered - Not Able To See
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_side_floor_7seater" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Seat Condition – 7 Seater <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rear_seat_condition_7seater" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_seat_condition_7seater" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_seat_condition_7seater" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Seat Belt – 7 Seater <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rear_seat_belt_7seater" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_seat_belt_7seater" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_seat_belt_7seater" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Child Safety Lock <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="child_safety_lock" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="child_safety_lock" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Check All Buttons</label>
                    <small>Mention if anything is not working</small>
                    <textarea name="check_all_buttons" rows="3" placeholder="Mention if anything is not working"></textarea>
                </div>
            </div>

            <!-- STEP 10: WARNING LIGHTS -->
            <div class="form-step" data-step="10">
                <h2>⊙ Step 10: Warning Lights</h2>
                
                <div class="form-group">
                    <label>Check Engine <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="check_engine" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="check_engine" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Power Steering Problem <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="power_steering_problem" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="power_steering_problem" value="Not Present" required> Not Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="power_steering_problem" value="NA" required> NA
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>ABS Sensor Problem <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="abs_sensor_problem" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="abs_sensor_problem" value="Not Present" required> Not Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="abs_sensor_problem" value="NA" required> NA
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Airbag Sensor Problem <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="airbag_sensor_problem" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="airbag_sensor_problem" value="Not Present" required> Not Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="airbag_sensor_problem" value="NA" required> NA
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Battery Problem <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="battery_problem" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="battery_problem" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Low Engine Oil <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="low_engine_oil" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="low_engine_oil" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Low Coolant / Overheating <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="low_coolant_overheating" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="low_coolant_overheating" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake System Warning <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_system_warning" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_system_warning" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Any Other Lights Found? <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="any_other_lights" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="any_other_lights" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Control Lights Present</label>
                    <textarea name="control_lights_present" rows="3" placeholder="Not Found"></textarea>
                </div>
            </div>

                        <!-- STEP 11: AIR CONDITIONING -->
            <div class="form-step" data-step="11">
                <h2> Air Conditioning</h2>

                <div class="form-group">
                    <label>Air Conditioning Turning On <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="ac_turning_on" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="ac_turning_on" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>AC Cool Temperature <span class="required">*</span></label>
                    <input type="text" name="ac_cool_temperature" required placeholder="Enter temperature">
                </div>

                <div class="form-group">
                    <label>AC Hot Temperature <span class="required">*</span></label>
                    <input type="text" name="ac_hot_temperature" required placeholder="Enter temperature">
                </div>

                <div class="form-group">
                    <label>Air Conditioning Direction Mode Working <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="ac_direction_mode" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="ac_direction_mode" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Defogger Front Vent Working <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="defogger_front" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="defogger_front" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Defogger Rear Vent Working <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="defogger_rear" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="defogger_rear" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="defogger_rear" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Air Conditioning All Vents <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="ac_all_vents" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="ac_all_vents" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>AC Abnormal Vibration <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="ac_vibration" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="ac_vibration" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>AC Cool Mode Temperature Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="ac_cool_image" id="Accoolimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Accoolimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="AccoolimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>AC Hot Mode Temperature Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="ac_hot_image" id="Achotimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Achotimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="AchotimagePreview"></div>
                    </div>
                </div>

            </div>

            <!-- STEP 12: TYRES -->
            <div class="form-step" data-step="12">
                <h2> Tyres</h2>

                <div class="form-group">
                    <label>Tyre Size <span class="required">*</span></label>
                    <input type="text" name="tyre_size" required>
                </div>

                <div class="form-group">
                    <label>Tyre Type <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="tyre_type" value="With Tube" required> With Tube
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tyre_type" value="Tubeless" required> Tubeless
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rim Type <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rim_type" value="Normal" required> Normal
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rim_type" value="Alloy" required> Alloy
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Front Tyre Depth Check <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_front_tyre_depth" value="Good" required> Good
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_front_tyre_depth" value="Average" required> Average
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_front_tyre_depth" value="Bad" required> Bad
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Front Tyre Tread Depth <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="driver_front_tyre_image" id="Driverfronttyreimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Driverfronttyreimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="DriverfronttyreimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Front Tyre Manufacturing Date <span class="required">*</span></label>
                    <input type="text" name="driver_front_tyre_date" required placeholder="MM/YYYY">
                </div>

                <div class="form-group">
                    <label>Driver Front Tyre Shape <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_front_tyre_shape" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_front_tyre_shape" value="Damaged" required> Damaged
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Back Tyre Depth Check <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_back_tyre_depth" value="Good" required> Good
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_back_tyre_depth" value="Average" required> Average
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_back_tyre_depth" value="Bad" required> Bad
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Back Tyre Tread Depth <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="driver_back_tyre_image" id="Driverbacktyreimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Driverbacktyreimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="DriverbacktyreimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Back Tyre Manufacturing Date <span class="required">*</span></label>
                    <input type="text" name="driver_back_tyre_date" required placeholder="MM/YYYY">
                </div>

                <div class="form-group">
                    <label>Driver Back Tyre Shape <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_back_tyre_shape" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_back_tyre_shape" value="Damaged" required> Damaged
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Back Tyre Depth Check <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_tyre_depth" value="Good" required> Good
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_tyre_depth" value="Average" required> Average
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_tyre_depth" value="Bad" required> Bad
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Back Tyre Tread Depth <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="passenger_back_tyre_image" id="Passengerbacktyreimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Passengerbacktyreimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="PassengerbacktyreimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Back Tyre Manufacturing Date <span class="required">*</span></label>
                    <input type="text" name="passenger_back_tyre_date" required placeholder="MM/YYYY">
                </div>

                <div class="form-group">
                    <label>Passenger Back Tyre Shape <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_tyre_shape" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_back_tyre_shape" value="Damaged" required> Damaged
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Front Tyre Depth Check <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_tyre_depth" value="Good" required> Good
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_tyre_depth" value="Average" required> Average
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_tyre_depth" value="Bad" required> Bad
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Front Tyre Tread Depth <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="passenger_front_tyre_image" id="Passengerfronttyreimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Passengerfronttyreimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="PassengerfronttyreimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Front Tyre Manufacturing Date <span class="required">*</span></label>
                    <input type="text" name="passenger_front_tyre_date" required placeholder="MM/YYYY">
                </div>

                <div class="form-group">
                    <label>Passenger Front Tyre Shape <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_tyre_shape" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_front_tyre_shape" value="Damaged" required> Damaged
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Stepney Tyre Depth Check <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="stepney_tyre_depth" value="Good" required> Good
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="stepney_tyre_depth" value="Average" required> Average
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="stepney_tyre_depth" value="Bad" required> Bad
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="stepney_tyre_depth" value="Stepney Not Available" required> Stepney Not Available
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Stepney Tyre Tread Depth <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="stepney_tyre_image" id="Stepneytyreimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Stepneytyreimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="StepneytyreimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Stepney Tyre Manufacturing Date <span class="required">*</span></label>
                    <input type="text" name="stepney_tyre_date" required placeholder="MM/YYYY">
                </div>

                <div class="form-group">
                    <label>Stepney Tyre Shape <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="stepney_tyre_shape" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="stepney_tyre_shape" value="Damaged" required> Damaged
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Signs of Camber Issue <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="camber_issue" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="camber_issue" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

            </div>

            <!-- STEP 13: TRANSMISSION & CLUTCH PEDAL -->
            <div class="form-step" data-step="13">
                <h2> Transmission & Clutch Pedal</h2>
                <small>During Test Drive</small>

                <div class="form-group">
                    <label>Check Gear Box / Transmission Shifting <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gearbox_shifting" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gearbox_shifting" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gearbox_shifting" value="Not Smooth" required> Not Smooth
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gearbox_shifting" value="Not Able to Check" required> Not Able to Check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Check 4x4 Shifting <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="check_4x4" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="check_4x4" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="check_4x4" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Clutch Pedal <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="clutch_pedal" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="clutch_pedal" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="clutch_pedal" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Clutch Engagement - RPM Downgrade <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="clutch_rpm_downgrade" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="clutch_rpm_downgrade" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Clutch Engagement - RPM Upgrade <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="clutch_rpm_upgrade" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="clutch_rpm_upgrade" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Clutch Engagement - RPM With Acceleration <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="clutch_rpm_acceleration" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="clutch_rpm_acceleration" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

            </div>

            <!-- STEP 14: AXLE -->
            <div class="form-step" data-step="14">
                <h2> Axle</h2>
                <small>During Test Drive</small>

                <div class="form-group">
                    <label>Left Axle <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="left_axle" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="left_axle" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Right Axle <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="right_axle" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="right_axle" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Abnormal Noise <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="abnormal_noise" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="abnormal_noise" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

            </div>

            <!-- STEP 15: ENGINE (AFTER TEST DRIVE) -->
            <div class="form-step" data-step="15">
                <h2> Engine (After Test Drive)</h2>

                <div class="form-group">
                    <label>Check for Oil Leaks Near Engine <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="oil_leaks_engine" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="oil_leaks_engine" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Oil Leak Near Engine Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="oil_leak_image" id="Oilleakimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Oilleakimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="OilleakimagePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Radiator Fan <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="radiator_fan" value="Working" required> Working
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="radiator_fan" value="Not Working" required> Not Working
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Radiator Condition <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="radiator_condition" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="radiator_condition" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Smoke from Dipstick Point <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="smoke_dipstick" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="smoke_dipstick" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Transmission Oil Leakage <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="transmission_oil_leakage" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="transmission_oil_leakage" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Noise <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="engine_noise" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="engine_noise" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Vibration <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="engine_vibration" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="engine_vibration" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Hoses <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="hoses" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="hoses" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Exhaust Sound <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="exhaust_sound" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="exhaust_sound" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

            </div>

                        <!-- STEP 16: BRAKES -->
            <div class="form-step" data-step="16">
                <h2>⊙ Brakes</h2>
                <small>After Test Drive</small>

                <div class="form-group">
                    <label>Brake Pads Front Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_front_driver" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_front_driver" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_front_driver" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Discs Front Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_front_driver" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_front_driver" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_front_driver" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Calipers Front Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_driver" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_driver" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_driver" value="Not Able To Check" required> Not Able To Check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_driver" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Pads Front Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_front_passenger" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_front_passenger" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_front_passenger" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Discs Front Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_front_passenger" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_front_passenger" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_front_passenger" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Calipers Front Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_passenger" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_passenger" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_passenger" value="Not Able To Check" required> Not Able To Check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_front_passenger" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Pads Back Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_back_passenger" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_back_passenger" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_back_passenger" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Discs Back Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_back_passenger" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_back_passenger" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_back_passenger" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Calipers Back Passenger Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_passenger" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_passenger" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_passenger" value="Not Able To Check" required> Not Able To Check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_passenger" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Pads Back Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_back_driver" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_back_driver" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pads_back_driver" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Discs Back Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_back_driver" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_back_driver" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_discs_back_driver" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Calipers Back Driver Side <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_driver" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_driver" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_driver" value="Not Able To Check" required> Not Able To Check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_calipers_back_driver" value="Not Applicable" required> Not Applicable
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Fluid Reservoir <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_fluid_reservoir" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_fluid_reservoir" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Brake Fluid Cap <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_fluid_cap" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_fluid_cap" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Parking Hand Brake <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="parking_hand_brake" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="parking_hand_brake" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

            </div>

            <!-- STEP 17: SUSPENSION -->
            <div class="form-step" data-step="17">
                <h2> Suspension</h2>

                <div class="form-group">
                    <label>Car Height <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="car_height" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="car_height" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Shocker Bounce Test <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="shocker_bounce_test" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="shocker_bounce_test" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Side Suspension Assembly <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_suspension_assembly" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_suspension_assembly" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Side Shocker Leakage <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_shocker_leakage" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_shocker_leakage" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Side Suspension Assembly <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_suspension_assembly" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_suspension_assembly" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Side Shocker Leakage <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_shocker_leakage" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_shocker_leakage" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Side Front Shocker Photo <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="driver_front_shocker_photo" id="Driverfrontshockerphoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Driverfrontshockerphoto" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="DriverfrontshockerphotoPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Side Front Shocker Photo <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="passenger_front_shocker_photo" id="Passengerfrontshockerphoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Passengerfrontshockerphoto" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="PassengerfrontshockerphotoPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Side Rear Shocker Photo <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="driver_rear_shocker_photo" id="Driverrearshockerphoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Driverrearshockerphoto" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="DriverrearshockerphotoPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Side Rear Shocker Photo <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="passenger_rear_shocker_photo" id="Passengerrearshockerphoto" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Passengerrearshockerphoto" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="PassengerrearshockerphotoPreview"></div>
                    </div>
                </div>

            </div>

            <!-- STEP 18: BRAKES & STEERING (TEST DRIVE) -->
            <div class="form-step" data-step="18">
                <h2> Brakes & Steering</h2>
                <small>During Test Drive</small>

                <div class="form-group">
                    <label>Brake Pedal <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="brake_pedal" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="brake_pedal" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Steering Rotating Smoothly <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="steering_rotating_smoothly" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_rotating_smoothly" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Steering Coming Back While Driving <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="steering_coming_back" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_coming_back" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Steering Weird Noise <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="steering_weird_noise" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_weird_noise" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alignment <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="alignment" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="alignment" value="Not Ok" required> Not Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Bubbling <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="bubbling" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="bubbling" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Steering Vibration <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="steering_vibration" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="steering_vibration" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

            </div>

            <!-- STEP 19: UNDERBODY -->
            <div class="form-step" data-step="19">
                <h2> Underbody</h2>

                <div class="form-group">
                    <label>Driver Side Body Chasis <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="driver_body_chasis" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_body_chasis" value="Rusted" required> Rusted
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_body_chasis" value="Accidental" required> Accidental
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_body_chasis" value="Not able to check" required> Not able to check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="driver_body_chasis" value="Ok" required> Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Side Body Chasis <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="passenger_body_chasis" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_body_chasis" value="Rusted" required> Rusted
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_body_chasis" value="Accidental" required> Accidental
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_body_chasis" value="Not able to check" required> Not able to check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="passenger_body_chasis" value="Ok" required> Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Subframe <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rear_subframe" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_subframe" value="Rusted" required> Rusted
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_subframe" value="Accidental" required> Accidental
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_subframe" value="Not able to check" required> Not able to check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_subframe" value="Ok" required> Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Engine Damage UnderBody <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="engine_damage_underbody" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="engine_damage_underbody" value="Rusted" required> Rusted
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="engine_damage_underbody" value="Accidental" required> Accidental
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="engine_damage_underbody" value="Not Able to check" required> Not Able to check
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="engine_damage_underbody" value="Ok" required> Ok
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Under Trays <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="under_trays" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="under_trays" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="under_trays" value="Not Applicable" required> Not Applicable
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="under_trays" value="Not able to check" required> Not able to check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Protection Cover <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="front_protection_cover" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_protection_cover" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_protection_cover" value="Not Applicable" required> Not Applicable
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="front_protection_cover" value="Not able to check" required> Not able to check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Protection Cover <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="rear_protection_cover" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_protection_cover" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_protection_cover" value="Not Applicable" required> Not Applicable
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="rear_protection_cover" value="Not able to check" required> Not able to check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Silencer <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="silencer" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="silencer" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="silencer" value="Rusted" required> Rusted
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="silencer" value="Not Able to check" required> Not Able to check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Fuel Tank <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="fuel_tank" value="Ok" required> Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fuel_tank" value="Not Ok" required> Not Ok
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fuel_tank" value="Rusted" required> Rusted
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fuel_tank" value="Not Able to check" required> Not Able to check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Any Fluid Leaks Under Body <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="fluid_leaks_underbody" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fluid_leaks_underbody" value="Not Present" required> Not Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="fluid_leaks_underbody" value="Not able to check" required> Not able to check
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Underbody Left <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="underbody_left" id="Underbodyleft" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Underbodyleft" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="UnderbodyleftPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Underbody Rear <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="underbody_rear" id="Underbodyrear" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Underbodyrear" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="UnderbodyrearPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Underbody Right <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="underbody_right" id="Underbodyright" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Underbodyright" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="UnderbodyrightPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Underbody Front <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="underbody_front" id="Underbodyfront" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Underbodyfront" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="UnderbodyfrontPreview"></div>
                    </div>
                </div>

            </div>

            <!-- STEP 20: EQUIPMENTS -->
            <div class="form-step" data-step="20">
                <h2> Equipments</h2>

                <div class="form-group">
                    <label>Tool Kit <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="tool_kit" value="Present" required> Present
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tool_kit" value="Not Present" required> Not Present
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tool Kit Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="tool_kit_image" id="Toolkitimage" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Toolkitimage" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="ToolkitimagePreview"></div>
                    </div>
                </div>

            </div>

            <!-- STEP 21: FINAL CAR RESULT -->
            <div class="form-step" data-step="21">
                <h2> Final Car Result</h2>

                <div class="form-group">
                    <label>Any Issues Found in the Car? <span class="required">*</span></label>
                    <textarea name="issues_found" rows="4" required placeholder="Describe any issues found"></textarea>
                </div>

                <div class="form-group">
                    <label>Photos of Issues</label>
                    <div class="file-upload">
                        <input type="file" name="issues_photo" id="Issuesphoto" accept="image/jpeg,image/jpg,image/png">
                        <label for="Issuesphoto" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="IssuesphotoPreview"></div>
                    </div>
                </div>

            </div>

            <!-- STEP 22: CAR IMAGES FROM ALL DIRECTIONS -->
            <div class="form-step" data-step="22">
                <h2> Car Images From All Directions</h2>

                <div class="form-group">
                    <label>Front <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_front" id="Carfront" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carfront" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarfrontPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Corner Front - Driver <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_corner_front_driver" id="Carcornerfrontdriver" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carcornerfrontdriver" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarcornerfrontdriverPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Driver Side <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_driver_side" id="Cardriverside" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Cardriverside" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CardriversidePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Corner Back - Driver <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_corner_back_driver" id="Carcornerbackdriver" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carcornerbackdriver" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarcornerbackdriverPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Back <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_back" id="Carback" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carback" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarbackPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Corner Back - Passenger <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_corner_back_passenger" id="Carcornerbackpassenger" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carcornerbackpassenger" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarcornerbackpassengerPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Passenger Side <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_passenger_side" id="Carpassengerside" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carpassengerside" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarpassengersidePreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Corner Front - Passenger <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_corner_front_passenger" id="Carcornerfrontpassenger" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carcornerfrontpassenger" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarcornerfrontpassengerPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Front Interior <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_front_interior" id="Carfrontinterior" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carfrontinterior" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarfrontinteriorPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rear Interior <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_rear_interior" id="Carrearinterior" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carrearinterior" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarrearinteriorPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>4 Way Switch Driver Side <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_4way_switch" id="Car4wayswitch" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Car4wayswitch" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="Car4wayswitchPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trunk Open <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_trunk_open" id="Cartrunkopen" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Cartrunkopen" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CartrunkopenPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Car KM Reading <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="car_km_reading_final" id="Carkmreadingfinal" accept="image/jpeg,image/jpg,image/png" required>
                        <label for="Carkmreadingfinal" class="file-label">
                            <span class="camera-icon"></span>
                            <span class="file-text">Choose Image</span>
                        </label>
                        <div class="file-preview" id="CarkmreadingfinalPreview"></div>
                    </div>
                </div>

            </div>

            <!-- STEP 23: PAYMENT DETAILS -->
            <div class="form-step" data-step="23">
                <h2> Payment Details</h2>

                <div class="form-group">
                    <label>Taking Payment <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="taking_payment" value="Yes" required> Yes
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="taking_payment" value="No" required> No
                        </label>
                    </div>
                </div>

            </div>


            <!-- Navigation Buttons -->
            <div class="form-navigation">
                <button type="button" class="btn btn-secondary" id="prevBtn">Previous</button>
                <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">Submit</button>
                <button type="button" class="btn btn-info" id="saveDraftBtn">Save Draft</button>
                <button type="button" class="btn btn-secondary" id="discardDraftBtn">Discard Draft</button>
                <button type="button" class="btn btn-warning" id="tSubmitBtn" style="background: #ff9800; color: white; font-weight: bold;">🔍 T-SUBMIT (Test PDF)</button>
            </div>

            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
                <p>Processing your submission...</p>
            </div>

            <!-- Success Message -->
            <div class="success-message" id="successMessage">
                <div class="success-content">
                    <div class="success-icon">✓</div>
                    <h3>Inspection Submitted Successfully!</h3>
                    <p>PDF has been generated and sent to the email.</p>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">New Inspection</button>
                </div>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
