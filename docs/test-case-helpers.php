<?php

function tc(string $title, string $objective, array $steps, string $expected): string
{
    $steps = array_map('htmlspecialchars', $steps);
    $stepsHtml = implode('', array_map(fn ($s) => "<li>{$s}</li>", $steps));

    return "<h4>{$title}</h4>"
        . "<p><strong>Objective:</strong> {$objective}</p>"
        . '<p><strong>Steps:</strong></p><ol>' . $stepsHtml . '</ol>'
        . '<p><strong>Expected Result:</strong></p>'
        . "<p>{$expected}</p><hr>";
}

function ep(string $method, string $uri, string $name, array $testCases, string $note = ''): string
{
    $html = "<h3>Endpoint: {$method} {$uri}</h3><p><strong>Route name:</strong> {$name}</p>";
    if ($note) {
        $html .= "<p><em>Note: {$note}</em></p>";
    }
    foreach ($testCases as $case) {
        $html .= tc($case[0], $case[1], $case[2], $case[3]);
    }

    return $html;
}

function writeTestCaseDocument(
    string $outputPath,
    string $title,
    string $description,
    array $prerequisites,
    array $tocItems,
    string $body
): void {
    $prereqRows = '';
    foreach ($prerequisites as $item) {
        $prereqRows .= '<tr><td colspan="2">' . htmlspecialchars($item) . '</td></tr>';
    }

    $tocHtml = '';
    foreach ($tocItems as $i => $item) {
        $tocHtml .= '<li>' . htmlspecialchars($item) . '</li>';
    }

    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{$title}</title>
<style>
body { font-family: Arial, sans-serif; line-height: 1.5; max-width: 920px; margin: 40px auto; color: #222; }
h1 { color: #1a365d; border-bottom: 3px solid #1a365d; padding-bottom: 8px; }
h2 { color: #2c5282; margin-top: 44px; border-bottom: 1px solid #ccc; padding-bottom: 6px; }
h3 { color: #2d3748; margin-top: 28px; background: #f7fafc; padding: 10px; border-left: 4px solid #4299e1; }
h4 { color: #4a5568; margin-top: 16px; }
table { border-collapse: collapse; width: 100%; margin: 16px 0; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
th { background: #edf2f7; }
hr { border: none; border-top: 1px solid #e2e8f0; margin: 16px 0; }
.note { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px; margin: 16px 0; }
code { background: #edf2f7; padding: 2px 6px; border-radius: 3px; }
@media print { h2 { page-break-before: always; } h2:first-of-type { page-break-before: avoid; } }
</style>
</head>
<body>
<h1>{$title}</h1>
<p><strong>Version:</strong> 1.0 &nbsp;|&nbsp; <strong>Date:</strong> August 2026 &nbsp;|&nbsp; <strong>Type:</strong> Manual testing (no automation)</p>
<p>{$description}</p>

<h2>Test Prerequisites</h2>
<table>
<tr><th>Requirement</th></tr>
{$prereqRows}
</table>

<div class="note">
<strong>Important:</strong> All test cases are designed for manual execution. Use the mobile app, dashboard, Postman, browser, or Artisan CLI as indicated in each test case. Verify results in the database, API responses, and log files.
</div>

<h2>Table of Contents</h2>
<ol>{$tocHtml}</ol>

{$body}

<p style="margin-top:60px;color:#718096;font-size:0.9em;">— End of Document —</p>
</body>
</html>
HTML;

    file_put_contents($outputPath, $html);
}
