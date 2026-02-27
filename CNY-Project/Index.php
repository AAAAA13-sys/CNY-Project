<?php
require_once 'includes/init.php';
require_once 'includes/functions.php';
require_once 'includes/handlers.php';

// Get data for display
$current_angpao_count = isset($_SESSION['angpaoEntries']) ? count($_SESSION['angpaoEntries']) : 0;
$angpao_limit_reached = ($current_angpao_count >= 3);
$scroll_to_results = isset($_SESSION['scroll_to_results']) ? $_SESSION['scroll_to_results'] : false;

// Get calculation results if they exist
$calculation_results = getCalculationResults();

// Get error message if exists
$error_message = getErrorMessage();

// Include the HTML template
include 'index.html';
?>