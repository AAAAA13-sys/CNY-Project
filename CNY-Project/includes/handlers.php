<?php
// ============================================
// FORM HANDLERS
// ============================================

// Handle Ang Pao submission (max 3 entries)
if (isset($_POST['add_angpao'])) {
    $current_count = isset($_SESSION['angpaoEntries']) ? count($_SESSION['angpaoEntries']) : 0;
    
    if ($current_count >= 3) {
        $_SESSION['error_message'] = "⚠️ MAXIMUM LIMIT REACHED! You can only add up to 3 Ang Pao entries.";
        $_SESSION['error_type'] = 'angpao_limit';
        $_SESSION['scroll_to_results'] = true;
        
        header('Location: index.php#angpao-card');
        exit;
    }
    
    $value = filter_var($_POST['angpao_value'], FILTER_VALIDATE_FLOAT);
    $origin = htmlspecialchars($_POST['angpao_origin']);
    $notes = htmlspecialchars($_POST['angpao_notes']);

    if ($value !== false && $value >= 0) {
        if (!isset($_SESSION['angpaoEntries'])) {
            $_SESSION['angpaoEntries'] = [];
        }
        $_SESSION['angpaoEntries'][] = [
            'value' => $value,
            'origin' => $origin,
            'notes' => $notes
        ];

        $luckyNumber = 8 + (count($_SESSION['angpaoEntries']) * 2);
        $_SESSION['luckyNumber'] = $luckyNumber;
        
        unset($_SESSION['error_message']);
    }
    
    header('Location: index.php#angpao-card');
    exit;
}

// Handle Food Expense submission
if (isset($_POST['add_food'])) {
    $value = filter_var($_POST['food_value'], FILTER_VALIDATE_FLOAT);
    $food_name = htmlspecialchars($_POST['food_name']);
    $location = htmlspecialchars($_POST['food_location']);

    if ($value !== false && $value >= 0) {
        if (!isset($_SESSION['foodEntries'])) {
            $_SESSION['foodEntries'] = [];
        }
        $_SESSION['foodEntries'][] = [
            'value' => $value,
            'food_name' => $food_name,
            'location' => $location
        ];
    }
    
    header('Location: index.php#food-card');
    exit;
}

// Handle Transport Expense submission
if (isset($_POST['add_transport'])) {
    $value = filter_var($_POST['transport_value'], FILTER_VALIDATE_FLOAT);
    $mode = htmlspecialchars($_POST['transport_mode']);
    $destination = htmlspecialchars($_POST['transport_destination']);

    if ($value !== false && $value >= 0) {
        if (!isset($_SESSION['transportEntries'])) {
            $_SESSION['transportEntries'] = [];
        }
        $_SESSION['transportEntries'][] = [
            'value' => $value,
            'mode' => $mode,
            'destination' => $destination
        ];
    }
    
    header('Location: index.php#transportation-card');
    exit;
}

// Handle Lunar Year check
if (isset($_POST['check_year'])) {
    $selected_year = intval($_POST['lunar_year']);
    $zodiac = getChineseZodiac($selected_year);
    $_SESSION['zodiac'] = $zodiac;
    $_SESSION['selected_year'] = $selected_year;
    
    header('Location: index.php#lunar-card');
    exit;
}

// Handle Calculation
if (isset($_POST['calculate'])) {
    $angpaoEntries = isset($_SESSION['angpaoEntries']) ? $_SESSION['angpaoEntries'] : [];
    
    if (empty($angpaoEntries)) {
        $_SESSION['error_message'] = "⚠️ Please add at least one Ang Pao entry first!";
        $_SESSION['scroll_to_results'] = true;
        header('Location: index.php#angpao-card');
        exit;
    }
    
    $foodEntries = isset($_SESSION['foodEntries']) ? $_SESSION['foodEntries'] : [];
    $transportEntries = isset($_SESSION['transportEntries']) ? $_SESSION['transportEntries'] : [];
    $zodiac = isset($_SESSION['zodiac']) ? $_SESSION['zodiac'] : 'Unknown';

    $results = calculateFortune($angpaoEntries, $foodEntries, $transportEntries, $zodiac);
    
    $_SESSION['calculation_results'] = $results;
    $_SESSION['scroll_to_results'] = true;
    
    header('Location: index.php#results-section');
    exit;
}

// Handle Reset
if (isset($_POST['reset'])) {
    session_destroy();
    session_start();
    header('Location: index.php');
    exit;
}
?>