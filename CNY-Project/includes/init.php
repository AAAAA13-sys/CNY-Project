<?php
// Start session for data persistence
session_start();

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
?>