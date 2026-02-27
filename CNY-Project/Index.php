<?php
// ============================================
// ANG PAO CALCULATOR - LUNAR NEW YEAR EDITION
// ============================================

// Initialize variables
$angpao1 = $angpao2 = $angpao3 = 0;
$foodExpenses = 0;
$total_transport = 0;
$isDragonYear = false;
$luckyNumber = 8;

// Initialize result arrays
$result_messages = [];
$total_angpao = 0;
$remaining_money = 0;
$bonus = 0;
$lucky_status = "";
$final_computation = "";
$error_message = "";

// Start session for data persistence
session_start();

// ============================================
// SCROLL TO RESULTS ENFORCEMENT
// ============================================
$scroll_to_results = isset($_SESSION['scroll_to_results']) ? $_SESSION['scroll_to_results'] : false;
if ($scroll_to_results) {
    unset($_SESSION['scroll_to_results']); // Clear after use
}

// ============================================
// FORM HANDLERS
// ============================================

// Handle Ang Pao submission (max 3 entries)
if (isset($_POST['add_angpao'])) {
    $current_count = isset($_SESSION['angpaoEntries']) ? count($_SESSION['angpaoEntries']) : 0;

    if ($current_count >= 3) {
        $error_message = "Maximum of 3 Ang Pao entries only! Please reset to add new ones.";
    } else {
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

            // Update Ang Pao variables (first 3 only)
            $entries = $_SESSION['angpaoEntries'];
            for ($i = 0; $i < min(3, count($entries)); $i++) {
                ${"angpao" . ($i + 1)} = $entries[$i]['value'];
            }

            // Increment lucky number based on entries
            $luckyNumber += (count($entries) * 2);
            $_SESSION['luckyNumber'] = $luckyNumber;
        }
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
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
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
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
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Lunar Year check
if (isset($_POST['check_year'])) {
    $selected_year = intval($_POST['lunar_year']);
    $zodiac = getChineseZodiac($selected_year);
    $_SESSION['zodiac'] = $zodiac;
    $_SESSION['selected_year'] = $selected_year;
    $isDragonYear = ($zodiac == 'Dragon');
    $_SESSION['isDragonYear'] = $isDragonYear;
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Calculation
if (isset($_POST['calculate'])) {
    // Get data from session
    $angpaoEntries = isset($_SESSION['angpaoEntries']) ? $_SESSION['angpaoEntries'] : [];
    $foodEntries = isset($_SESSION['foodEntries']) ? $_SESSION['foodEntries'] : [];
    $transportEntries = isset($_SESSION['transportEntries']) ? $_SESSION['transportEntries'] : [];

    // Get first 3 Ang Pao values only
    $angpao1 = isset($angpaoEntries[0]['value']) ? $angpaoEntries[0]['value'] : 0;
    $angpao2 = isset($angpaoEntries[1]['value']) ? $angpaoEntries[1]['value'] : 0;
    $angpao3 = isset($angpaoEntries[2]['value']) ? $angpaoEntries[2]['value'] : 0;

    // Calculate totals
    $foodExpenses = array_sum(array_column($foodEntries, 'value'));
    $total_transport = array_sum(array_column($transportEntries, 'value'));
    $luckyNumber = 8 + (count($angpaoEntries) * 2);
    $isDragonYear = (isset($_SESSION['zodiac']) && $_SESSION['zodiac'] == 'Dragon');

    // ============================================
    // PHP OPERATORS DEMONSTRATION
    // ============================================

    // ARITHMETIC OPERATORS (+ -)
    $total_angpao = $angpao1 + $angpao2 + $angpao3;
    $remaining_money = $total_angpao - $foodExpenses;

    // ASSIGNMENT OPERATORS (= += -=)
    $bonus = 0;
    if ($isDragonYear) {
        $remaining_money = $remaining_money * 2; // Multiplication
        $bonus += 500; // Addition assignment
        $remaining_money += $bonus; // Addition assignment
    }

    // Use dynamic transport total instead of fixed 200
    $remaining_money -= $total_transport; // Subtract actual transport costs

    // COMPARISON OPERATORS (> == >)
    $isMoneyGreaterThan5000 = ($total_angpao > 5000);
    $isLuckyEight = ($luckyNumber == 8);
    $areExpensesHigher = ($foodExpenses > $total_angpao);

    // LOGICAL OPERATORS (&& || !)
    $luckyAndRich = ($total_angpao > 5000) && ($luckyNumber == 8);
    $specialWin = ($total_angpao > 5000) || ($isDragonYear);
    $isNotDragonYear = !$isDragonYear;

    // INCREMENT/DECREMENT OPERATORS (++ --)
    $luckyNumber++; // Post-increment
    $foodExpenses--; // Post-decrement

    // ============================================
    // GENERATE RESULT MESSAGES
    // ============================================
    $result_messages = [];

    if ($isMoneyGreaterThan5000) {
        $result_messages[] = "Total Ang Pao: ₱" . number_format($total_angpao, 2) . " (Above ₱5,000)";
    } else {
        $result_messages[] = "Total Ang Pao: ₱" . number_format($total_angpao, 2) . " (Below ₱5,000)";
    }

    if ($isLuckyEight) {
        $result_messages[] = "Lucky Number: $luckyNumber (Lucky 8!)";
    } else {
        $result_messages[] = "Lucky Number: $luckyNumber";
    }

    if ($areExpensesHigher) {
        $result_messages[] = "Food Expenses: ₱" . number_format($foodExpenses + 1, 2) . " (Exceeded Ang Pao)";
    } else {
        $result_messages[] = "Food Expenses: ₱" . number_format($foodExpenses + 1, 2) . " (Within budget)";
    }

    if ($luckyAndRich) {
        $result_messages[] = "Jackpot! Rich & Lucky!";
    }

    if ($specialWin) {
        $result_messages[] = "Special Condition Met";
    }

    if ($isDragonYear) {
        $result_messages[] = "DRAGON YEAR: Money doubled + ₱500 bonus applied!";
    } else {
        $result_messages[] = "Year of the " . $_SESSION['zodiac'] . " (No Dragon bonus)";
    }

    $result_messages[] = "Total Transport: -₱" . number_format($total_transport, 2);
    $result_messages[] = "Lucky Number: $luckyNumber (+1 increment)";
    $result_messages[] = "Food balance: ₱" . number_format($foodExpenses, 2) . " (-1 decrement)";

    // Format for display
    $total_angpao_display = number_format($total_angpao, 2);
    $remaining_money_display = number_format($remaining_money, 2);
    $bonus_display = $bonus > 0 ? number_format($bonus, 2) : "0";
    $lucky_status = $luckyNumber;
    
    // Store results in session
    $_SESSION['calculation_results'] = [
        'result_messages' => $result_messages,
        'angpao1' => $angpao1,
        'angpao2' => $angpao2,
        'angpao3' => $angpao3,
        'total_angpao_display' => $total_angpao_display,
        'foodExpenses' => $foodExpenses,
        'total_transport' => $total_transport,
        'total_angpao' => $total_angpao,
        'isDragonYear' => $isDragonYear,
        'remaining_money_display' => $remaining_money_display,
        'remaining_money' => $remaining_money
    ];
    
    // Set scroll flag
    $_SESSION['scroll_to_results'] = true;
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Retrieve calculation results from session if they exist
if (isset($_SESSION['calculation_results'])) {
    $result_messages = $_SESSION['calculation_results']['result_messages'];
    $angpao1 = $_SESSION['calculation_results']['angpao1'];
    $angpao2 = $_SESSION['calculation_results']['angpao2'];
    $angpao3 = $_SESSION['calculation_results']['angpao3'];
    $total_angpao_display = $_SESSION['calculation_results']['total_angpao_display'];
    $foodExpenses = $_SESSION['calculation_results']['foodExpenses'];
    $total_transport = $_SESSION['calculation_results']['total_transport'];
    $total_angpao = $_SESSION['calculation_results']['total_angpao'];
    $isDragonYear = $_SESSION['calculation_results']['isDragonYear'];
    $remaining_money_display = $_SESSION['calculation_results']['remaining_money_display'];
    $remaining_money = $_SESSION['calculation_results']['remaining_money'];
    
    // Clear from session after retrieving
    unset($_SESSION['calculation_results']);
}

// ============================================
// HELPER FUNCTIONS
// ============================================

// Get Chinese Zodiac based on year
function getChineseZodiac($year)
{
    $zodiacs = [
        'Rat',
        'Ox',
        'Tiger',
        'Rabbit',
        'Dragon',
        'Snake',
        'Horse',
        'Goat',
        'Monkey',
        'Rooster',
        'Dog',
        'Pig'
    ];
    $offset = ($year - 4) % 12;
    return $zodiacs[$offset];
}

// Get zodiac image filename
function getZodiacImage($zodiac)
{
    $images = [
        'Rat' => 'rat.png',
        'Ox' => 'ox.png',
        'Tiger' => 'tiger.png',
        'Rabbit' => 'rabbit.png',
        'Dragon' => 'dragon.png',
        'Snake' => 'snake.png',
        'Horse' => 'horse.png',
        'Goat' => 'goat.png',
        'Monkey' => 'monkey.png',
        'Rooster' => 'rooster.png',
        'Dog' => 'dog.png',
        'Pig' => 'pig.png'
    ];
    return isset($images[$zodiac]) ? $images[$zodiac] : 'rat.png';
}

// Handle reset
if (isset($_POST['reset'])) {
    session_destroy();
    session_start();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ang Pao Calculator - Lunar New Year</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- ============================================ -->
    <!-- DECORATIVE ELEMENTS (LEFT SIDE) -->
    <!-- ============================================ -->
    <div class="decor-left">
        <img src="Img/lantern.png" alt="Lantern" class="lantern-left">
        <img src="Img/cloud.png" alt="Cloud" class="cloud-left">
        <img src="Img/flower.png" alt="Flower" class="flower-left">
    </div>

    <!-- ============================================ -->
    <!-- DECORATIVE ELEMENTS (RIGHT SIDE) -->
    <!-- ============================================ -->
    <div class="decor-right">
        <img src="Img/lantern.png" alt="Lantern" class="lantern-right">
        <img src="Img/cloud.png" alt="Cloud" class="cloud-right">
        <img src="Img/flower.png" alt="Flower" class="flower-right">
    </div>

    <!-- ============================================ -->
    <!-- MAIN CONTAINER -->
    <!-- ============================================ -->
    <div class="container">
        <!-- Header Section -->
        <header class="header">
            <div class="header-images">
                <img src="Img/money.png" alt="Money" class="header-money">
                <img src="Img/gift-bag.png" alt="Gift Bag" class="header-gift">
                <img src="Img/chinese-coin.png" alt="Coin" class="header-coin">
            </div>
            <h1>ANG PAO CALCULATOR</h1>
            <p class="subtitle">Lunar New Year Fortune Manager</p>
        </header>

        <!-- ============================================ -->
        <!-- ANG PAO CONTAINER (MAX 3 ENTRIES) -->
        <!-- ============================================ -->
        <div class="card angpao-card">
            <div class="card-header">
                <div class="header-with-icon">
                    <img src="Img/money.png" alt="Money" class="section-icon">
                    <h2>Ang Pao Container</h2>
                </div>
                <span class="badge">Max 3 Entries</span>
            </div>

            <!-- Error Message Display -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Ang Pao Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Value (₱)</th>
                        <th>Origin</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['angpaoEntries']) && !empty($_SESSION['angpaoEntries'])) {
                        foreach ($_SESSION['angpaoEntries'] as $index => $entry) {
                            echo "<tr>";
                            echo "<td>" . ($index + 1) . "</td>";
                            echo "<td>₱" . number_format($entry['value'], 2) . "</td>";
                            echo "<td>" . $entry['origin'] . "</td>";
                            echo "<td>" . $entry['notes'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No Ang Pao entries yet</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Ang Pao Input Form -->
            <form method="POST" class="input-form">
                <div class="form-group">
                    <input type="number" step="0.01" name="angpao_value" placeholder="Amount (₱)" required
                        onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46">
                    <input type="text" name="angpao_origin" placeholder="From who? (e.g., Lolo, Tita)" required maxlength="50">
                    <input type="text" name="angpao_notes" placeholder="Note (optional)" maxlength="100">
                </div>
                <button type="submit" name="add_angpao" class="btn btn-primary">
                    <img src="Img/gift-bag.png" alt="Add" class="btn-icon"> Add Ang Pao
                </button>
            </form>

            <!-- Info Summary -->
            <?php
            if (isset($_SESSION['angpaoEntries'])) {
                $count = count($_SESSION['angpaoEntries']);
                $current_lucky = 8 + ($count * 2);
                $first3 = array_slice($_SESSION['angpaoEntries'], 0, 3);
                $first3_sum = array_sum(array_column($first3, 'value'));
                echo "<p class='info-text'>Entries: $count/3 | Total: ₱" . number_format($first3_sum, 2) . " | Lucky #: $current_lucky</p>";
            }
            ?>
        </div>

        <!-- ============================================ -->
        <!-- FOOD EXPENSE CONTAINER -->
        <!-- ============================================ -->
        <div class="card food-card">
            <div class="card-header">
                <div class="header-with-icon">
                    <img src="Img/gift-bag.png" alt="Food" class="section-icon">
                    <h2>Food Expenses</h2>
                </div>
            </div>

            <!-- Food Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cost (₱)</th>
                        <th>Food Name</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['foodEntries']) && !empty($_SESSION['foodEntries'])) {
                        foreach ($_SESSION['foodEntries'] as $entry) {
                            echo "<tr>";
                            echo "<td>₱" . number_format($entry['value'], 2) . "</td>";
                            echo "<td>" . $entry['food_name'] . "</td>";
                            echo "<td>" . $entry['location'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding:20px;'>No food expenses yet</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Food Input Form -->
            <form method="POST" class="input-form">
                <div class="form-group">
                    <input type="number" step="0.01" name="food_value" placeholder="Amount (₱)" required
                        onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46">
                    <input type="text" name="food_name" placeholder="Food name (e.g., Noodles)" required maxlength="50">
                    <input type="text" name="food_location" placeholder="Where? (e.g., Restaurant)" maxlength="100">
                </div>
                <button type="submit" name="add_food" class="btn btn-primary">
                    <img src="Img/gift-bag.png" alt="Add" class="btn-icon"> Add Food Expense
                </button>
            </form>

            <!-- Food Total -->
            <?php
            if (isset($_SESSION['foodEntries'])) {
                $total_food = array_sum(array_column($_SESSION['foodEntries'], 'value'));
                echo "<p class='info-text'>Total Food: ₱" . number_format($total_food, 2) . "</p>";
            }
            ?>
        </div>

        <!-- ============================================ -->
        <!-- TRANSPORTATION EXPENSE CONTAINER -->
        <!-- ============================================ -->
        <div class="card transportation-card">
            <div class="card-header">
                <div class="header-with-icon">
                    <img src="Img/cloud.png" alt="Transport" class="section-icon">
                    <h2>Transportation Expenses</h2>
                </div>
            </div>

            <!-- Transport Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cost (₱)</th>
                        <th>Transport Mode</th>
                        <th>Destination</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['transportEntries']) && !empty($_SESSION['transportEntries'])) {
                        foreach ($_SESSION['transportEntries'] as $entry) {
                            echo "<tr>";
                            echo "<td>₱" . number_format($entry['value'], 2) . "</td>";
                            echo "<td>" . $entry['mode'] . "</td>";
                            echo "<td>" . $entry['destination'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding:20px;'>No transportation expenses yet</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Transport Input Form -->
            <form method="POST" class="input-form">
                <div class="form-group">
                    <input type="number" step="0.01" name="transport_value" placeholder="Amount (₱)" required
                        onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46">
                    <input type="text" name="transport_mode" placeholder="Mode (e.g., Taxi, Jeep)" required maxlength="50">
                    <input type="text" name="transport_destination" placeholder="Destination" maxlength="100">
                </div>
                <button type="submit" name="add_transport" class="btn btn-primary">
                    <img src="Img/cloud.png" alt="Add" class="btn-icon"> Add Transport Expense
                </button>
            </form>

            <!-- Transport Total -->
            <?php
            if (isset($_SESSION['transportEntries'])) {
                $total_transport_display = array_sum(array_column($_SESSION['transportEntries'], 'value'));
                echo "<p class='info-text'>Total Transport: ₱" . number_format($total_transport_display, 2) . "</p>";
            }
            ?>
        </div>

        <!-- ============================================ -->
        <!-- LUNAR YEAR CHECKER -->
        <!-- ============================================ -->
        <div class="card lunar-card">
            <div class="card-header">
                <div class="header-with-icon">
                    <img src="Img/lantern.png" alt="Lunar" class="section-icon">
                    <h2>Lunar Year Checker</h2>
                </div>
            </div>

            <form method="POST" class="lunar-form">
                <div class="form-group">
                    <label for="lunar_year">Select Date:</label>
                    <input type="month" name="lunar_year" id="lunar_year" required
                        min="1900" max="2100" value="<?php echo date('Y-m'); ?>">
                </div>
                <button type="submit" name="check_year" class="btn btn-secondary">
                    <img src="Img/lantern.png" alt="Check" class="btn-icon"> Check Zodiac
                </button>
            </form>

            <!-- Zodiac Result Display -->
            <?php
            if (isset($_SESSION['zodiac'])) {
                $zodiac = $_SESSION['zodiac'];
                $year = $_SESSION['selected_year'];
                $isDragon = ($zodiac == 'Dragon');
                $zodiacImage = getZodiacImage($zodiac);
            ?>
                <div class="zodiac-result <?php echo $isDragon ? 'dragon-year' : ''; ?>">
                    <div class="zodiac-image-container">
                        <img src="Img/<?php echo $zodiacImage; ?>" alt="<?php echo $zodiac; ?>" class="zodiac-image">
                    </div>
                    <h3><?php echo $year; ?> is Year of the <?php echo $zodiac; ?>!</h3>
                    <?php if ($isDragon): ?>
                        <div class="bonus-alert">
                            <img src="Img/dragon.png" alt="Dragon" class="bonus-icon">
                            <p>DRAGON YEAR DETECTED!</p>
                            <p class="bonus-text">Money will be DOUBLED + ₱500 Bonus!</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- ============================================ -->
        <!-- CALCULATION SECTION with ID for scrolling -->
        <!-- ============================================ -->
        <div id="results-section" class="card calculation-card">
            <form method="POST">
                <button type="submit" name="calculate" class="btn btn-calculate">
                    <img src="Img/chinese-coin.png" alt="Calculate" class="btn-icon-large"> CALCULATE FORTUNE
                </button>
            </form>

            <!-- Display Results -->
            <?php if (!empty($result_messages)): ?>
                <div class="results" id="results">
                    <div class="results-header">
                        <img src="Img/money.png" alt="Results" class="results-icon">
                        <h3>RESULTS</h3>
                        <img src="Img/chinese-coin.png" alt="Results" class="results-icon">
                    </div>

                    <!-- Result Messages -->
                    <div class="result-messages">
                        <?php foreach ($result_messages as $msg): ?>
                            <div class="result-line"><?php echo $msg; ?></div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ============================================ -->
                    <!-- GRID-STYLE COMPUTATION -->
                    <!-- ============================================ -->
                    <div class="final-computation">
                        <h4>FINAL COMPUTATION</h4>

                        <!-- Computation Grid -->
                        <div class="computation-grid">
                            <!-- Ang Pao 1 -->
                            <p>
                                <span>Ang Pao 1:</span>
                                <strong>₱<?php echo number_format($angpao1 ?? 0, 2); ?></strong>
                            </p>

                            <!-- Ang Pao 2 -->
                            <p>
                                <span>Ang Pao 2:</span>
                                <strong>₱<?php echo number_format($angpao2 ?? 0, 2); ?></strong>
                            </p>

                            <!-- Ang Pao 3 -->
                            <p>
                                <span>Ang Pao 3:</span>
                                <strong>₱<?php echo number_format($angpao3 ?? 0, 2); ?></strong>
                            </p>

                            <!-- Total Ang Pao -->
                            <p>
                                <span>Total Ang Pao (+):</span>
                                <strong>₱<?php echo $total_angpao_display ?? '0.00'; ?></strong>
                            </p>

                            <!-- Food Expenses -->
                            <p>
                                <span>Food Expenses (-):</span>
                                <strong>₱<?php echo isset($foodExpenses) ? number_format($foodExpenses + 1, 2) : '0.00'; ?></strong>
                            </p>

                            <!-- Balance after Food -->
                            <p>
                                <span>Balance after Food:</span>
                                <strong>₱<?php echo number_format(($total_angpao ?? 0) - (($foodExpenses ?? 0) + 1), 2); ?></strong>
                            </p>

                            <!-- Dragon Bonus (if applicable) -->
                            <?php if ($isDragonYear): ?>
                                <p style="color: #FFD700;">
                                    <span>Dragon Bonus (×2 + ₱500):</span>
                                    <strong>₱<?php echo number_format((($total_angpao - ($foodExpenses + 1)) * 2) + 500, 2); ?></strong>
                                </p>
                            <?php endif; ?>

                            <!-- Transportation (now dynamic) -->
                            <p>
                                <span>Transportation (-):</span>
                                <strong>₱<?php echo isset($total_transport) ? number_format($total_transport, 2) : '0.00'; ?></strong>
                            </p>

                            <!-- Final Balance -->
                            <p style="border-top: 2px solid rgba(255,255,255,0.3); padding-top: 10px; margin-top: 5px;">
                                <span style="font-weight: bold;">FINAL BALANCE:</span>
                                <strong style="font-size: 1.2rem; <?php echo ($remaining_money ?? 0) < 0 ? 'color: #ff6b6b;' : 'color: #51cf66;'; ?>">
                                    ₱<?php echo $remaining_money_display ?? '0.00'; ?>
                                </strong>
                            </p>
                        </div>

                        <!-- Helper Text -->
                        <div style="text-align: center; margin-top: 15px; font-size: 0.85rem; opacity: 0.8;">
                            (Sum of Ang Pao → Subtract Food → Dragon Bonus if applicable → Subtract Transport)
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============================================ -->
        <!-- RESET BUTTON -->
        <!-- ============================================ -->
        <div class="reset-section">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button type="submit" name="reset" class="btn btn-reset"
                    onclick="return confirm('Reset all data? This cannot be undone!')">
                    <img src="Img/cloud.png" alt="Reset" class="btn-icon"> Reset All Data
                </button>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- BOTTOM DECORATIVE ELEMENTS -->
    <!-- ============================================ -->
    <div class="decor-bottom">
        <img src="Img/flower.png" alt="Flower" class="flower-bottom">
        <img src="Img/cloud.png" alt="Cloud" class="cloud-bottom">
        <img src="Img/chinese-coin.png" alt="Coin" class="coin-bottom">
    </div>

    <!-- ============================================ -->
    <!-- SCROLL TO RESULTS SCRIPT -->
    <!-- ============================================ -->
    <?php if ($scroll_to_results): ?>
    <script>
        window.onload = function() {
            setTimeout(function() {
                var resultsSection = document.getElementById('results-section');
                if (resultsSection) {
                    resultsSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 100);
        };
    </script>
    <?php endif; ?>
</body>

</html>