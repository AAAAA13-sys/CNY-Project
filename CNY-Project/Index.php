<?php
// Initialize variables
$angpao1 = $angpao2 = $angpao3 = $angpao4 = $angpao5 = 0;
$foodExpenses = 0;
$isDragonYear = false;
$luckyNumber = 8;

// Initialize result messages
$result_messages = [];
$total_angpao = 0;
$remaining_money = 0;
$bonus = 0;
$lucky_status = "";
$final_computation = "";

// Process Ang Pao form submission
if (isset($_POST['add_angpao'])) {
    $value = filter_var($_POST['angpao_value'], FILTER_VALIDATE_FLOAT);
    $description = htmlspecialchars($_POST['angpao_description']);
    $notes = htmlspecialchars($_POST['angpao_notes']);
    
    // Validate: not negative, not scientific notation
    if ($value !== false && $value >= 0 && is_numeric($_POST['angpao_value']) && 
        !preg_match('/[eE]/', $_POST['angpao_value'])) {
        
        session_start();
        if (!isset($_SESSION['angpaoEntries'])) {
            $_SESSION['angpaoEntries'] = [];
        }
        $_SESSION['angpaoEntries'][] = [
            'value' => $value,
            'description' => $description,
            'notes' => $notes
        ];
        
        // Update Ang Pao variables (first 5 entries only)
        $entries = $_SESSION['angpaoEntries'];
        for ($i = 0; $i < min(5, count($entries)); $i++) {
            ${"angpao" . ($i + 1)} = $entries[$i]['value'];
        }
        
        // Increment lucky number by 2 for each Ang Pao
        $luckyNumber += (count($entries) * 2);
    }
}

// Process Food Expense form submission
if (isset($_POST['add_food'])) {
    $value = filter_var($_POST['food_value'], FILTER_VALIDATE_FLOAT);
    $description = htmlspecialchars($_POST['food_description']);
    $notes = htmlspecialchars($_POST['food_notes']);
    
    if ($value !== false && $value >= 0 && is_numeric($_POST['food_value']) && 
        !preg_match('/[eE]/', $_POST['food_value'])) {
        
        session_start();
        if (!isset($_SESSION['foodEntries'])) {
            $_SESSION['foodEntries'] = [];
        }
        $_SESSION['foodEntries'][] = [
            'value' => $value,
            'description' => $description,
            'notes' => $notes
        ];
        
        // Update food expenses
        $foodExpenses = array_sum(array_column($_SESSION['foodEntries'], 'value'));
    }
}

// Process Lunar Year check
if (isset($_POST['check_year'])) {
    session_start();
    $selected_year = intval($_POST['lunar_year']);
    $zodiac = getChineseZodiac($selected_year);
    $_SESSION['zodiac'] = $zodiac;
    $_SESSION['selected_year'] = $selected_year;
    $isDragonYear = ($zodiac == 'Dragon');
}

// Process Calculation
if (isset($_POST['calculate'])) {
    session_start();
    
    // Get all entries
    $angpaoEntries = isset($_SESSION['angpaoEntries']) ? $_SESSION['angpaoEntries'] : [];
    $foodEntries = isset($_SESSION['foodEntries']) ? $_SESSION['foodEntries'] : [];
    
    // Calculate totals
    $total_angpao = array_sum(array_column($angpaoEntries, 'value'));
    $foodExpenses = array_sum(array_column($foodEntries, 'value'));
    
    // Get lucky number
    $luckyNumber = 8 + (count($angpaoEntries) * 2);
    
    // Check if Dragon Year
    $isDragonYear = (isset($_SESSION['zodiac']) && $_SESSION['zodiac'] == 'Dragon');
    
    // Transportation expense (RNG 20-70) multiplied by number of Ang Pao
    $transpo_expense = rand(20, 70) * count($angpaoEntries);
    
    // Start calculations
    $remaining_money = $total_angpao;
    
    // Deduct expenses
    $remaining_money -= $foodExpenses;
    $remaining_money -= $transpo_expense;
    $result_messages[] = "Transportation expense: ₱" . number_format($transpo_expense, 2);
    
    // Dragon Year bonus
    $bonus = 0;
    if ($isDragonYear) {
        $luckyNumber += 2;
        $remaining_money *= 2;
        $remaining_money += 500;
        $bonus = 500;
        $result_messages[] = "🐉 DRAGON YEAR BONUS! Money doubled + ₱500! 🐉";
    }
    
    // Check conditions
    if ($total_angpao > 5000) {
        $result_messages[] = "💰 Paldooooo!!!!! 💰";
    } else {
        $result_messages[] = "😢 awit. 😢";
    }
    
    if ($luckyNumber == 8) {
        $result_messages[] = "✨ Humaharurot nanaman si manoy! ✨";
    } else {
        $result_messages[] = "🤬 MGA BOBO!! 🤬";
    }
    
    if ($foodExpenses > $total_angpao) {
        $result_messages[] = "💸 Gastos AMP. 💸";
    } else {
        $result_messages[] = "🍑 pa pwet yern 🍑";
    }
    
    if ($remaining_money > 5000 && $luckyNumber == 8) {
        $result_messages[] = "🔥 DAMNNN!!!! 🔥";
    } else {
        $result_messages[] = "😞 :(( 😞";
    }
    
    if ($remaining_money > 5000 || $isDragonYear) {
        $result_messages[] = "⚠️ DO NOT REDEEM IT!!! ⚠️";
    } else {
        $result_messages[] = "😐 :( 😐";
    }
    
    if (!$isDragonYear) {
        $result_messages[] = "❌ NOT Dragon Year - No double bonus ❌";
    }
    
    // Final values for display
    $total_angpao = number_format($total_angpao, 2);
    $remaining_money = number_format($remaining_money, 2);
    $lucky_status = $luckyNumber;
    $final_computation = "Total Ang Pao: ₱$total_angpao | Remaining: ₱$remaining_money";
}

// Function to get Chinese Zodiac
function getChineseZodiac($year) {
    $zodiacs = ['Rat', 'Ox', 'Tiger', 'Rabbit', 'Dragon', 'Snake', 
                'Horse', 'Goat', 'Monkey', 'Rooster', 'Dog', 'Pig'];
    $offset = ($year - 4) % 12;
    return $zodiacs[$offset];
}

// Function to get zodiac image filename
function getZodiacImage($zodiac) {
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

// Start session
session_start();
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
    <!-- Decorative elements using images -->
    <div class="decor-left">
        <img src="Img/lantern.png" alt="Lantern" class="lantern-left">
        <img src="Img/cloud.png" alt="Cloud" class="cloud-left">
        <img src="Img/flower.png" alt="Flower" class="flower-left">
    </div>
    
    <div class="decor-right">
        <img src="Img/lantern.png" alt="Lantern" class="lantern-right">
        <img src="Img/cloud.png" alt="Cloud" class="cloud-right">
        <img src="Img/flower.png" alt="Flower" class="flower-right">
    </div>

    <div class="container">
        <!-- Header with money and gift bag images -->
        <header class="header">
            <div class="header-images">
                <img src="Img/money.png" alt="Money" class="header-money">
                <img src="Img/gift-bag.png" alt="Gift Bag" class="header-gift">
                <img src="Img/chinese-coin.png" alt="Coin" class="header-coin">
            </div>
            <h1>ANG PAO CALCULATOR</h1>
            <p class="subtitle">Lunar New Year Fortune Manager</p>
        </header>

        <!-- Ang Pao Container -->
        <div class="card angpao-card">
            <div class="card-header">
                <div class="header-with-icon">
                    <img src="Img/money.png" alt="Money" class="section-icon">
                    <h2>Ang Pao Container</h2>
                </div>
                <span class="badge">5 Variables Max</span>
            </div>
            
            <!-- Ang Pao Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Value (₱)</th>
                        <th>Description</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['angpaoEntries']) && !empty($_SESSION['angpaoEntries'])) {
                        foreach ($_SESSION['angpaoEntries'] as $entry) {
                            echo "<tr>";
                            echo "<td>₱" . number_format($entry['value'], 2) . "</td>";
                            echo "<td>" . $entry['description'] . "</td>";
                            echo "<td>" . $entry['notes'] . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- Ang Pao Input Form -->
            <form method="POST" class="input-form">
                <div class="form-group">
                    <input type="number" step="0.01" name="angpao_value" placeholder="Amount (₱)" required 
                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46">
                    <input type="text" name="angpao_description" placeholder="From who?" required maxlength="50">
                    <input type="text" name="angpao_notes" placeholder="Note (optional)" maxlength="100">
                </div>
                <button type="submit" name="add_angpao" class="btn btn-primary">
                    <img src="Img/gift-bag.png" alt="Add" class="btn-icon"> Add Ang Pao
                </button>
            </form>
            
            <?php
            if (isset($_SESSION['angpaoEntries'])) {
                $count = count($_SESSION['angpaoEntries']);
                $current_lucky = 8 + ($count * 2);
                echo "<p class='info-text'>📊 Total Ang Pao: $count | Lucky Number: $current_lucky 📊</p>";
            }
            ?>
        </div>

        <!-- Food Expense Container -->
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
                        <th>Value (₱)</th>
                        <th>Description</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['foodEntries']) && !empty($_SESSION['foodEntries'])) {
                        foreach ($_SESSION['foodEntries'] as $entry) {
                            echo "<tr>";
                            echo "<td>₱" . number_format($entry['value'], 2) . "</td>";
                            echo "<td>" . $entry['description'] . "</td>";
                            echo "<td>" . $entry['notes'] . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- Food Input Form -->
            <form method="POST" class="input-form">
                <div class="form-group">
                    <input type="number" step="0.01" name="food_value" placeholder="Amount (₱)" required
                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46">
                    <input type="text" name="food_description" placeholder="What food?" required maxlength="50">
                    <input type="text" name="food_notes" placeholder="Where?" maxlength="100">
                </div>
                <button type="submit" name="add_food" class="btn btn-primary">
                    <img src="Img/gift-bag.png" alt="Add" class="btn-icon"> Add Food Expense
                </button>
            </form>
            
            <?php
            if (isset($_SESSION['foodEntries'])) {
                $total_food = array_sum(array_column($_SESSION['foodEntries'], 'value'));
                echo "<p class='info-text'>🍽️ Total Food: ₱" . number_format($total_food, 2) . " 🍽️</p>";
            }
            ?>
        </div>

        <!-- Lunar Year Container -->
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

        <!-- Calculation Section -->
        <div class="card calculation-card">
            <form method="POST">
                <button type="submit" name="calculate" class="btn btn-calculate">
                    <img src="Img/chinese-coin.png" alt="Calculate" class="btn-icon-large"> CALCULATE FORTUNE
                </button>
            </form>
            
            <?php if (!empty($result_messages)): ?>
                <div class="results">
                    <div class="results-header">
                        <img src="Img/money.png" alt="Results" class="results-icon">
                        <h3>RESULTS</h3>
                        <img src="Img/chinese-coin.png" alt="Results" class="results-icon">
                    </div>
                    <div class="result-messages">
                        <?php foreach ($result_messages as $msg): ?>
                            <div class="result-line"><?php echo $msg; ?></div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="final-computation">
                        <h4>FINAL COMPUTATION</h4>
                        <div class="computation-grid">
                            <p><span>Total Ang Pao:</span> <strong>₱<?php echo $total_angpao; ?></strong></p>
                            <p><span>Remaining Money:</span> <strong>₱<?php echo $remaining_money; ?></strong></p>
                            <p><span>Bonus:</span> <strong><?php echo $bonus > 0 ? '₱'.number_format($bonus, 2) : 'None'; ?></strong></p>
                            <p><span>Lucky Number:</span> <strong class="<?php echo $lucky_status == 8 ? 'lucky-eight' : ''; ?>"><?php echo $lucky_status; ?></strong></p>
                            <p><span>Final Status:</span> <strong><?php echo $final_computation; ?></strong></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Reset Button -->
        <div class="reset-section">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button type="submit" name="reset" class="btn btn-reset" 
                        onclick="return confirm('Reset all data? This cannot be undone!')">
                    <img src="Img/cloud.png" alt="Reset" class="btn-icon"> Reset All Data
                </button>
            </form>
        </div>
    </div>

    <!-- Bottom decorative elements -->
    <div class="decor-bottom">
        <img src="Img/flower.png" alt="Flower" class="flower-bottom">
        <img src="Img/cloud.png" alt="Cloud" class="cloud-bottom">
        <img src="Img/chinese-coin.png" alt="Coin" class="coin-bottom">
    </div>
</body>
</html>

<?php
// Handle reset
if (isset($_POST['reset'])) {
    session_destroy();
    session_start();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>