<?php
// ============================================
// HELPER FUNCTIONS
// ============================================

function getChineseZodiac($year)
{
    $zodiacs = [
        'Rat', 'Ox', 'Tiger', 'Rabbit', 'Dragon', 'Snake',
        'Horse', 'Goat', 'Monkey', 'Rooster', 'Dog', 'Pig'
    ];
    $offset = ($year - 4) % 12;
    return $zodiacs[$offset];
}

function getZodiacImage($zodiac)
{
    $images = [
        'Rat' => 'rat.png', 'Ox' => 'ox.png', 'Tiger' => 'tiger.png',
        'Rabbit' => 'rabbit.png', 'Dragon' => 'dragon.png', 'Snake' => 'snake.png',
        'Horse' => 'horse.png', 'Goat' => 'goat.png', 'Monkey' => 'monkey.png',
        'Rooster' => 'rooster.png', 'Dog' => 'dog.png', 'Pig' => 'pig.png'
    ];
    return isset($images[$zodiac]) ? $images[$zodiac] : 'rat.png';
}

function getCalculationResults()
{
    if (isset($_SESSION['calculation_results'])) {
        $results = $_SESSION['calculation_results'];
        unset($_SESSION['calculation_results']);
        return $results;
    }
    return null;
}

function getErrorMessage()
{
    if (isset($_SESSION['error_message'])) {
        $error = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        unset($_SESSION['error_type']);
        return $error;
    }
    return '';
}

function calculateFortune($angpaoEntries, $foodEntries, $transportEntries, $zodiac)
{
    $angpao1 = isset($angpaoEntries[0]['value']) ? $angpaoEntries[0]['value'] : 0;
    $angpao2 = isset($angpaoEntries[1]['value']) ? $angpaoEntries[1]['value'] : 0;
    $angpao3 = isset($angpaoEntries[2]['value']) ? $angpaoEntries[2]['value'] : 0;

    $foodExpenses = array_sum(array_column($foodEntries, 'value'));
    $total_transport = array_sum(array_column($transportEntries, 'value'));
    $luckyNumber = 8 + (count($angpaoEntries) * 2);
    $isDragonYear = ($zodiac == 'Dragon');

    // PHP OPERATORS DEMONSTRATION
    $total_angpao = $angpao1 + $angpao2 + $angpao3;
    $remaining_money = $total_angpao - $foodExpenses;

    $bonus = 0;
    if ($isDragonYear) {
        $remaining_money = $remaining_money * 2;
        $bonus += 500;
        $remaining_money += $bonus;
    }

    $remaining_money -= $total_transport;

    $isMoneyGreaterThan5000 = ($total_angpao > 5000);
    $isLuckyEight = ($luckyNumber == 8);
    $areExpensesHigher = ($foodExpenses > $total_angpao);

    $luckyAndRich = ($total_angpao > 5000) && ($luckyNumber == 8);
    $specialWin = ($total_angpao > 5000) || ($isDragonYear);

    $luckyNumber++;
    $foodExpenses--;

    // GENERATE RESULT MESSAGES
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
        $result_messages[] = "Year of the " . $zodiac . " (No Dragon bonus)";
    }

    $result_messages[] = "Total Transport: -₱" . number_format($total_transport, 2);
    $result_messages[] = "Lucky Number: $luckyNumber (+1 increment)";
    $result_messages[] = "Food balance: ₱" . number_format($foodExpenses, 2) . " (-1 decrement)";

    return [
        'result_messages' => $result_messages,
        'angpao1' => $angpao1,
        'angpao2' => $angpao2,
        'angpao3' => $angpao3,
        'total_angpao_display' => number_format($total_angpao, 2),
        'foodExpenses' => $foodExpenses,
        'total_transport' => $total_transport,
        'total_angpao' => $total_angpao,
        'isDragonYear' => $isDragonYear,
        'remaining_money_display' => number_format($remaining_money, 2),
        'remaining_money' => $remaining_money,
        'bonus' => $bonus,
        'luckyNumber' => $luckyNumber
    ];
}
?>