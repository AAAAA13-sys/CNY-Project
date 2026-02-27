<?php
$results = $calculation_results;
?>

<div id="results-section" class="card calculation-card">
    <form method="POST" action="index.php">
        <button type="submit" name="calculate" class="btn btn-calculate">
            <img src="Img/chinese-coin.png" alt="Calculate" class="btn-icon-large"> CALCULATE FORTUNE
        </button>
    </form>

    <?php if ($results && !empty($results['result_messages'])): ?>
        <div class="results" id="results">
            <div class="results-header">
                <img src="Img/money.png" alt="Results" class="results-icon">
                <h3>RESULTS</h3>
                <img src="Img/chinese-coin.png" alt="Results" class="results-icon">
            </div>

            <div class="result-messages">
                <?php foreach ($results['result_messages'] as $msg): ?>
                    <div class="result-line"><?php echo $msg; ?></div>
                <?php endforeach; ?>
            </div>

            <!-- FINAL COMPUTATION -->
            <div class="final-computation">
                <h4>FINAL COMPUTATION</h4>

                <div class="computation-grid">
                    <p>
                        <span>Ang Pao 1:</span>
                        <strong>₱<?php echo number_format($results['angpao1'] ?? 0, 2); ?></strong>
                    </p>

                    <p>
                        <span>Ang Pao 2:</span>
                        <strong>₱<?php echo number_format($results['angpao2'] ?? 0, 2); ?></strong>
                    </p>

                    <p>
                        <span>Ang Pao 3:</span>
                        <strong>₱<?php echo number_format($results['angpao3'] ?? 0, 2); ?></strong>
                    </p>

                    <p>
                        <span>Total Ang Pao (+):</span>
                        <strong>₱<?php echo $results['total_angpao_display'] ?? '0.00'; ?></strong>
                    </p>

                    <p>
                        <span>Food Expenses (-):</span>
                        <strong>₱<?php echo isset($results['foodExpenses']) ? number_format($results['foodExpenses'] + 1, 2) : '0.00'; ?></strong>
                    </p>

                    <p>
                        <span>Balance after Food:</span>
                        <strong>₱<?php echo number_format(($results['total_angpao'] ?? 0) - (($results['foodExpenses'] ?? 0) + 1), 2); ?></strong>
                    </p>

                    <?php if ($results['isDragonYear']): ?>
                        <p style="color: #FFD700;">
                            <span>Dragon Bonus (×2 + ₱500):</span>
                            <strong>₱<?php echo number_format((($results['total_angpao'] - ($results['foodExpenses'] + 1)) * 2) + 500, 2); ?></strong>
                        </p>
                    <?php endif; ?>

                    <p>
                        <span>Transportation (-):</span>
                        <strong>₱<?php echo isset($results['total_transport']) ? number_format($results['total_transport'], 2) : '0.00'; ?></strong>
                    </p>

                    <p style="border-top: 2px solid rgba(255,255,255,0.3); padding-top: 10px; margin-top: 5px;">
                        <span style="font-weight: bold;">FINAL BALANCE:</span>
                        <strong style="font-size: 1.2rem; <?php echo ($results['remaining_money'] ?? 0) < 0 ? 'color: #ff6b6b;' : 'color: #51cf66;'; ?>">
                            ₱<?php echo $results['remaining_money_display'] ?? '0.00'; ?>
                        </strong>
                    </p>
                </div>

                <div style="text-align: center; margin-top: 15px; font-size: 0.85rem; opacity: 0.8;">
                    (Sum of Ang Pao → Subtract Food → Dragon Bonus if applicable → Subtract Transport)
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>