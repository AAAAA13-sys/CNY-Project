<?php
$transportEntries = isset($_SESSION['transportEntries']) ? $_SESSION['transportEntries'] : [];
$total_transport_display = array_sum(array_column($transportEntries, 'value'));
?>

<div id="transportation-card" class="card transportation-card">
    <div class="card-header">
        <div class="header-with-icon">
            <img src="Img/cloud.png" alt="Transport" class="section-icon">
            <h2>Transportation Expenses</h2>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Cost (₱)</th>
                <th>Transport Mode</th>
                <th>Destination</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transportEntries)): ?>
                <?php foreach ($transportEntries as $entry): ?>
                    <tr>
                        <td>₱<?php echo number_format($entry['value'], 2); ?></td>
                        <td><?php echo htmlspecialchars($entry['mode']); ?></td>
                        <td><?php echo htmlspecialchars($entry['destination']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='3' style='text-align:center; padding:20px;'>No transportation expenses yet</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form method="POST" action="index.php" class="input-form">
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

    <?php if (!empty($transportEntries)): ?>
        <p class='info-text'>Total Transport: ₱<?php echo number_format($total_transport_display, 2); ?></p>
    <?php endif; ?>
</div>