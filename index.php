<?php
// ---------- Helper: safely print user input (prevents XSS) ----------
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// ---------- Malaysia time and greeting ----------
$myt = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
$hour = (int)$myt->format('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

// ---------- Available conversions ----------
$conversions = [
    'c_to_f'   => ['label' => 'Celsius to Fahrenheit', 'from' => '°C', 'to' => '°F', 'fn' => fn($v) => $v * 9 / 5 + 32],
    'f_to_c'   => ['label' => 'Fahrenheit to Celsius', 'from' => '°F', 'to' => '°C', 'fn' => fn($v) => ($v - 32) * 5 / 9],
    'km_to_mi' => ['label' => 'Kilometres to Miles',   'from' => 'km', 'to' => 'mi', 'fn' => fn($v) => $v * 0.621371],
    'mi_to_km' => ['label' => 'Miles to Kilometres',   'from' => 'mi', 'to' => 'km', 'fn' => fn($v) => $v / 0.621371],
    'kg_to_lb' => ['label' => 'Kilograms to Pounds',   'from' => 'kg', 'to' => 'lb', 'fn' => fn($v) => $v * 2.20462],
    'lb_to_kg' => ['label' => 'Pounds to Kilograms',   'from' => 'lb', 'to' => 'kg', 'fn' => fn($v) => $v / 2.20462],
];

// ---------- Handle the form ----------
$selected = $_POST['type'] ?? 'c_to_f';
$input    = $_POST['value'] ?? '';
$result   = null;
$error    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($conversions[$selected])) {
        $error = 'Please choose a valid conversion.';
    } elseif (!is_numeric($input)) {
        $error = 'Please enter a valid number.';
    } else {
        $c = $conversions[$selected];
        $answer = round($c['fn']((float)$input), 2);
        $result = e($input) . ' ' . $c['from'] . ' = ' . $answer . ' ' . $c['to'];
    }
}

// ---------- Live server information ----------
$serverInfo = [
    'PHP version'      => PHP_VERSION,
    'Operating system' => php_uname('s') . ' ' . php_uname('r'),
    'Web server'       => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'Host name'        => gethostname(),
    'Server time (UTC)'=> gmdate('Y-m-d H:i:s'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SWE40006 Deployment Portfolio Task 3.3 - PHP Web App</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Segoe UI, Arial, sans-serif; background: #f4f6f9; color: #222; margin: 0; }
        .container { max-width: 960px; margin: 0 auto; padding: 24px; }
        .hero { background: linear-gradient(135deg, #4f5bd5, #7a3fb8); color: #fff; padding: 40px 24px; border-radius: 12px; text-align: center; }
        .hero h1 { margin: 0 0 8px; }
        .hero p { margin: 6px 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin: 24px 0; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); margin-bottom: 16px; }
        .card h2 { font-size: 1.15rem; margin-top: 0; color: #4f5bd5; }
        form { display: flex; flex-wrap: wrap; gap: 8px; }
        input, select, button { padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 6px; }
        input { flex: 1; min-width: 120px; }
        button { background: #4f5bd5; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #3c47b0; }
        .result { background: #e6f6ea; color: #1e6b33; padding: 12px; border-radius: 6px; margin-top: 12px; font-weight: bold; }
        .error { background: #fdeaea; color: #a02727; padding: 12px; border-radius: 6px; margin-top: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        th { width: 40%; color: #555; }
        footer { text-align: center; color: #777; font-size: 0.9rem; padding: 16px; }
    </style>
</head>
<body>
<div class="container">

    <div class="hero">
        <h1>SWE40006 Deployment Portfolio Task 3.3</h1>
        <p>A PHP web app deployed to Microsoft Azure App Service (Linux)</p>
        <p>Student: <strong>Kio Xiao Jie</strong> 106214085</p>
        <p><?= $greeting ?> from Malaysia! It is <?= $myt->format('g:i A') ?> MYT.</p>
    </div>

    <div class="grid">
        <div class="card">
            <h2>About this project</h2>
            <p>This app was developed for Task 3.3 of SWE40006 Software Deployment and Evolution.
               It shows a server-side PHP application built locally and deployed to the cloud.</p>
        </div>
        <div class="card">
            <h2>Technology stack</h2>
            <ul>
                <li>Language: PHP <?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?></li>
                <li>Hosting: Azure App Service (Linux, Free F1)</li>
                <li>Source control: GitHub</li>
            </ul>
        </div>
        <div class="card">
            <h2>Deployment steps</h2>
            <ol>
                <li>Installed PHP and tested with php -v</li>
                <li>Built and tested the app locally</li>
                <li>Pushed the source code to GitHub</li>
                <li>Deployed to Azure App Service</li>
                <li>Verified the live public URL</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <h2>Unit converter</h2>
        <p>Choose a conversion, enter a number, and the PHP code on the server calculates the result.</p>
        <form method="post">
            <select name="type">
                <?php foreach ($conversions as $key => $c): ?>
                    <option value="<?= e($key) ?>" <?= $key === $selected ? 'selected' : '' ?>>
                        <?= e($c['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="value" placeholder="Enter a number" value="<?= e($input) ?>">
            <button type="submit">Convert</button>
        </form>
        <?php if ($result): ?>
            <div class="result"><?= $result ?></div>
        <?php elseif ($error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Live server information</h2>
        <p>These values are read by PHP at runtime, so they change when the app runs on Azure instead of my computer.</p>
        <table>
            <?php foreach ($serverInfo as $label => $value): ?>
                <tr><th><?= e($label) ?></th><td><?= e($value) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <footer>SWE40006 Software Deployment and Evolution &middot; Task 3.3</footer>
</div>
</body>
</html>