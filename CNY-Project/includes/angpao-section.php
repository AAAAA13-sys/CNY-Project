<?php
$angpaoEntries = isset($_SESSION['angpaoEntries']) ? $_SESSION['angpaoEntries'] : [];
$count = count($angpaoEntries);
$current_lucky = 8 + ($count * 2);
$first3 = array_slice($angpaoEntries, 0, 3);
$first3_sum = array_sum(array_column($first3, 'value'));
?>

<div id="angpao-card" class="card angpao-card">
    <div class="card-header">
        <div class="header-with-icon">
            <img src="Img/money.png" alt="Money" class="section-icon">
            <h2>Ang Pao</h2>
        </div>
        <span class="badge">Max 3 Entries 
            <?php if ($angpao_limit_reached): ?>
                <span class="limit-badge">FULL</span>
            <?php endif; ?>
        </span>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #c62828; font-weight: bold;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

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
            <?php if (!empty($angpaoEntries)): ?>
                <?php foreach ($first3 as $index => $entry): ?>
                    <tr>
                        <td><?php echo ($index + 1); ?></td>
                        <td>₱<?php echo number_format($entry['value'], 2); ?></td>
                        <td><?php echo htmlspecialchars($entry['origin']); ?></td>
                        <td><?php echo htmlspecialchars($entry['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if ($count > 3): ?>
                    <tr><td colspan='4' style='text-align:center; color:#c62828; font-style:italic; padding:10px;'>Note: Only first 3 entries are used in calculations</td></tr>
                <?php endif; ?>
            <?php else: ?>
                <tr><td colspan='4' style='text-align:center; padding:20px;'>No Ang Pao entries yet</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form method="POST" action="index.php" class="input-form <?php echo $angpao_limit_reached ? 'disabled-form' : ''; ?>">
        <div class="form-group">
            <input type="number" step="0.01" name="angpao_value" placeholder="Amount (₱)" required
                onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46"
                <?php echo $angpao_limit_reached ? 'disabled' : ''; ?>>
            <input type="text" name="angpao_origin" placeholder="From who? (e.g., Lolo, Tita)" required maxlength="50"
                <?php echo $angpao_limit_reached ? 'disabled' : ''; ?>>
            <input type="text" name="angpao_notes" placeholder="Note (optional)" maxlength="100"
                <?php echo $angpao_limit_reached ? 'disabled' : ''; ?>>
        </div>

        <button type="submit" name="add_angpao" class="btn btn-primary" 
            <?php echo $angpao_limit_reached ? 'disabled' : ''; ?>
            style="<?php echo $angpao_limit_reached ? 'opacity:0.5; cursor:not-allowed;' : ''; ?>">
            <img src="Img/gift-bag.png" alt="Add" class="btn-icon"> 
            <?php echo $angpao_limit_reached ? 'Limit Reached (3/3)' : 'Add Ang Pao'; ?>
        </button>
    </form>

    <?php if (!empty($angpaoEntries)): ?>
        <p class='info-text'>Entries: <?php echo $count; ?>/3 | Total (first 3): ₱<?php echo number_format($first3_sum, 2); ?> | Lucky #: <?php echo $current_lucky; ?></p>
        <?php if ($count > 3): ?>
            <p class='info-text' style='color:#c62828;'>⚠️ <?php echo ($count - 3); ?> extra entry(s) not counted in calculations</p>
        <?php endif; ?>
    <?php endif; ?>
</div>