<?php
$zodiac = isset($_SESSION['zodiac']) ? $_SESSION['zodiac'] : null;
$year = isset($_SESSION['selected_year']) ? $_SESSION['selected_year'] : null;
$isDragon = ($zodiac == 'Dragon');
$zodiacImage = $zodiac ? getZodiacImage($zodiac) : 'rat.png';
?>

<div id="lunar-card" class="card lunar-card">
    <div class="card-header">
        <div class="header-with-icon">
            <img src="Img/lantern.png" alt="Lunar" class="section-icon">
            <h2>Lunar Year Checker</h2>
        </div>
    </div>

    <form method="POST" action="index.php" class="lunar-form">
        <div class="form-group">
            <label for="lunar_year">Select Date:</label>
            <input type="month" name="lunar_year" id="lunar_year" required
                min="1900" max="2100" value="<?php echo date('Y-m'); ?>">
        </div>
        <button type="submit" name="check_year" class="btn btn-secondary">
            <img src="Img/lantern.png" alt="Check" class="btn-icon"> Check Zodiac
        </button>
    </form>

    <?php if ($zodiac): ?>
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
    <?php endif; ?>
</div>