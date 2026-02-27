<?php
$foodEntries = isset($_SESSION['foodEntries']) ? $_SESSION['foodEntries'] : [];
$total_food = array_sum(array_column($foodEntries, 'value'));
?>

<div id="food-card" class="card food-card">
    <div class="card-header">
        <div class="header-with-icon">
            <img src="Img/gift-bag.png" alt="Food" class="section-icon">
            <h2>Food Expenses</h2>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Cost (₱)</th>
                <th>Food Name</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($foodEntries)): ?>
                <?php foreach ($foodEntries as $entry): ?>
                    <tr>
                        <td>₱<?php echo number_format($entry['value'], 2); ?></td>
                        <td><?php echo htmlspecialchars($entry['food_name']); ?></td>
                        <td><?php echo htmlspecialchars($entry['location']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='3' style='text-align:center; padding:20px;'>No food expenses yet</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form method="POST" action="index.php" class="input-form">
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

    <?php if (!empty($foodEntries)): ?>
        <p class='info-text'>Total Food: ₱<?php echo number_format($total_food, 2); ?></p>
    <?php endif; ?>
</div>