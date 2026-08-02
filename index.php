<?php
/**
 * Página informativa de Pico y Placa Bogotá
 * Compatible con PHP 8.0+
 *
 * Antes de publicar:
 * 1. Cambia $canonicalUrl por la URL definitiva.
 * 2. Cambia los enlaces /privacidad, /terminos y /contacto.
 * 3. Inserta tu código de Google Ads/AdSense únicamente en los espacios señalados.
 * 4. Revisa periódicamente las tarifas y cambios normativos en fuentes oficiales.
 */

declare(strict_types=1);

date_default_timezone_set('America/Bogota');

$siteName      = 'Movilidad Bogotá Hoy';
$pageTitle     = 'Pico y Placa Bogotá hoy 2026: horario, placas, calendario y tarifas';
$metaDescription = 'Consulta el Pico y Placa de Bogotá hoy: placas que pueden circular, horario 6 a. m. a 9 p. m., calendario, multa, Pico y Placa Solidario y regional.';
$canonicalUrl  = 'https://www.tudominio.com/pico-y-placa-bogota.php';
$dataVersion   = '2026-08-02';
$officialMain  = 'https://www.movilidadbogota.gov.co/pico-y-placa';
$officialSolidario = 'https://picoyplacasolidario.movilidadbogota.gov.co/';

/**
 * Imagen social autogenerada para mantener la página en un solo archivo.
 * URL: pico-y-placa-bogota.php?asset=og
 */
if (isset($_GET['asset']) && $_GET['asset'] === 'og') {
    if (extension_loaded('gd')) {
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=86400');

        $img = imagecreatetruecolor(1200, 630);
        $navy = imagecolorallocate($img, 7, 31, 73);
        $blue = imagecolorallocate($img, 18, 96, 214);
        $cyan = imagecolorallocate($img, 48, 207, 208);
        $white = imagecolorallocate($img, 255, 255, 255);
        $soft = imagecolorallocate($img, 220, 235, 255);
        $yellow = imagecolorallocate($img, 255, 205, 50);

        imagefilledrectangle($img, 0, 0, 1200, 630, $navy);
        imagefilledellipse($img, 1070, 80, 430, 430, $blue);
        imagefilledellipse($img, 1040, 560, 520, 260, $cyan);
        imagefilledrectangle($img, 0, 510, 1200, 630, $blue);

        imagestring($img, 5, 80, 90, 'PICO Y PLACA', $cyan);
        imagestring($img, 5, 80, 145, 'BOGOTA 2026', $white);
        imagestring($img, 4, 82, 220, 'Horario, placas, calendario y tarifas', $soft);

        imagefilledrectangle($img, 80, 315, 610, 445, $white);
        imagestring($img, 5, 115, 345, '6:00 A. M. - 9:00 P. M.', $navy);
        imagestring($img, 4, 115, 392, 'Lunes a viernes', $blue);

        imagefilledellipse($img, 900, 300, 240, 240, $yellow);
        imagefilledellipse($img, 900, 300, 180, 180, $white);
        imagestring($img, 5, 855, 275, '1-5', $navy);
        imagestring($img, 3, 840, 320, 'IMPAR', $blue);

        imagepng($img);
        imagedestroy($img);
        exit;
    }

    header('Content-Type: image/svg+xml; charset=utf-8');
    header('Cache-Control: public, max-age=86400');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630"><rect width="1200" height="630" fill="#071f49"/><circle cx="1040" cy="120" r="230" fill="#1260d6"/><rect y="500" width="1200" height="130" fill="#1260d6"/><text x="80" y="150" fill="#30cfd0" font-family="Arial" font-size="52" font-weight="700">PICO Y PLACA</text><text x="80" y="225" fill="#fff" font-family="Arial" font-size="68" font-weight="800">BOGOTÁ 2026</text><text x="80" y="285" fill="#dcebff" font-family="Arial" font-size="30">Horario, placas, calendario y tarifas</text><rect x="80" y="340" rx="25" width="530" height="120" fill="#fff"/><text x="115" y="405" fill="#071f49" font-family="Arial" font-size="34" font-weight="700">6:00 A. M. – 9:00 P. M.</text><text x="115" y="442" fill="#1260d6" font-family="Arial" font-size="25">Lunes a viernes</text></svg>';
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function spanishMonth(int $month): string
{
    $months = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];
    return $months[$month] ?? '';
}

function spanishWeekday(int $isoDay): string
{
    $days = [1 => 'lunes', 2 => 'martes', 3 => 'miércoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sábado', 7 => 'domingo'];
    return $days[$isoDay] ?? '';
}

function longDateEs(DateTimeInterface $date): string
{
    return spanishWeekday((int)$date->format('N')) . ' ' . $date->format('j') . ' de ' . spanishMonth((int)$date->format('n')) . ' de ' . $date->format('Y');
}

function nextMonday(DateTimeImmutable $date): DateTimeImmutable
{
    if ((int)$date->format('N') === 1) {
        return $date;
    }
    return $date->modify('next monday');
}

/** Algoritmo gregoriano de Meeus/Jones/Butcher. */
function easterSunday(int $year): DateTimeImmutable
{
    $a = $year % 19;
    $b = intdiv($year, 100);
    $c = $year % 100;
    $d = intdiv($b, 4);
    $e = $b % 4;
    $f = intdiv($b + 8, 25);
    $g = intdiv($b - $f + 1, 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $month = intdiv($h + $l - 7 * $m + 114, 31);
    $day = (($h + $l - 7 * $m + 114) % 31) + 1;

    return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day), new DateTimeZone('America/Bogota'));
}

/** Festivos nacionales de Colombia, incluyendo traslados de la Ley Emiliani. */
function colombianHolidays(int $year): array
{
    $tz = new DateTimeZone('America/Bogota');
    $easter = easterSunday($year);
    $holidays = [];

    $add = static function (DateTimeImmutable $date, string $name) use (&$holidays): void {
        $holidays[$date->format('Y-m-d')] = $name;
    };

    $add(new DateTimeImmutable("$year-01-01", $tz), 'Año Nuevo');
    $add(nextMonday(new DateTimeImmutable("$year-01-06", $tz)), 'Día de los Reyes Magos');
    $add(nextMonday(new DateTimeImmutable("$year-03-19", $tz)), 'Día de San José');
    $add($easter->modify('-3 days'), 'Jueves Santo');
    $add($easter->modify('-2 days'), 'Viernes Santo');
    $add(new DateTimeImmutable("$year-05-01", $tz), 'Día del Trabajo');
    $add($easter->modify('+43 days'), 'Ascensión del Señor');
    $add($easter->modify('+64 days'), 'Corpus Christi');
    $add($easter->modify('+71 days'), 'Sagrado Corazón');
    $add(nextMonday(new DateTimeImmutable("$year-06-29", $tz)), 'San Pedro y San Pablo');
    $add(new DateTimeImmutable("$year-07-20", $tz), 'Día de la Independencia');
    $add(new DateTimeImmutable("$year-08-07", $tz), 'Batalla de Boyacá');
    $add(nextMonday(new DateTimeImmutable("$year-08-15", $tz)), 'Asunción de la Virgen');
    $add(nextMonday(new DateTimeImmutable("$year-10-12", $tz)), 'Día de la Raza');
    $add(nextMonday(new DateTimeImmutable("$year-11-01", $tz)), 'Todos los Santos');
    $add(nextMonday(new DateTimeImmutable("$year-11-11", $tz)), 'Independencia de Cartagena');
    $add(new DateTimeImmutable("$year-12-08", $tz), 'Inmaculada Concepción');
    $add(new DateTimeImmutable("$year-12-25", $tz), 'Navidad');

    ksort($holidays);
    return $holidays;
}

function parseDateSafe(string $raw, DateTimeImmutable $fallback): DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw, new DateTimeZone('America/Bogota'));
    $errors = DateTimeImmutable::getLastErrors();
    if (!$date || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        return $fallback;
    }
    return $date;
}

function parseMonthSafe(string $raw, DateTimeImmutable $fallback): DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw . '-01', new DateTimeZone('America/Bogota'));
    $errors = DateTimeImmutable::getLastErrors();
    if (!$date || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        return $fallback->modify('first day of this month');
    }
    return $date;
}

function lastDigitFromPlate(string $plate): ?int
{
    if (preg_match_all('/\d/', $plate, $matches) && !empty($matches[0])) {
        return (int)end($matches[0]);
    }
    return null;
}

function allowedDigits(DateTimeInterface $date): array
{
    return ((int)$date->format('j') % 2 === 0)
        ? [6, 7, 8, 9, 0]
        : [1, 2, 3, 4, 5];
}

function restrictedDigits(DateTimeInterface $date): array
{
    return ((int)$date->format('j') % 2 === 0)
        ? [1, 2, 3, 4, 5]
        : [6, 7, 8, 9, 0];
}

function evaluateDay(DateTimeImmutable $date, ?int $digit, DateTimeImmutable $now): array
{
    $holidays = colombianHolidays((int)$date->format('Y'));
    $key = $date->format('Y-m-d');
    $isoDay = (int)$date->format('N');
    $isWeekend = $isoDay >= 6;
    $holidayName = $holidays[$key] ?? null;
    $isRegularDay = !$isWeekend && $holidayName === null;
    $allowed = allowedDigits($date);
    $restricted = restrictedDigits($date);
    $isToday = $date->format('Y-m-d') === $now->format('Y-m-d');
    $minutes = ((int)$now->format('G') * 60) + (int)$now->format('i');
    $insideHours = $minutes >= 360 && $minutes < 1260;

    if (!$isRegularDay) {
        return [
            'type' => 'free',
            'title' => 'No aplica el Pico y Placa ordinario',
            'message' => $holidayName
                ? "La fecha seleccionada es festivo: {$holidayName}. Revisa si el Distrito anunció Pico y Placa Regional o una medida especial."
                : 'Es fin de semana. Para vehículos particulares no aplica la restricción ordinaria; verifica medidas especiales o regionales.',
            'allowed' => $allowed,
            'restricted' => $restricted,
            'holiday' => $holidayName,
            'is_regular_day' => false,
            'inside_hours' => false,
            'plate_restricted' => false,
        ];
    }

    if ($digit === null) {
        return [
            'type' => 'info',
            'title' => 'Consulta una placa',
            'message' => 'Ingresa la placa o su último dígito para saber si puede circular durante el horario de 6:00 a. m. a 9:00 p. m.',
            'allowed' => $allowed,
            'restricted' => $restricted,
            'holiday' => null,
            'is_regular_day' => true,
            'inside_hours' => $insideHours,
            'plate_restricted' => null,
        ];
    }

    $plateRestricted = in_array($digit, $restricted, true);

    if ($isToday && !$insideHours) {
        return [
            'type' => 'free',
            'title' => 'Puede circular en este momento',
            'message' => $plateRestricted
                ? 'La placa sí tiene restricción hoy, pero en este momento está fuera del horario de 6:00 a. m. a 9:00 p. m.'
                : 'La placa puede circular hoy y, además, actualmente está fuera del horario de restricción.',
            'allowed' => $allowed,
            'restricted' => $restricted,
            'holiday' => null,
            'is_regular_day' => true,
            'inside_hours' => false,
            'plate_restricted' => $plateRestricted,
        ];
    }

    if ($plateRestricted) {
        return [
            'type' => 'danger',
            'title' => $isToday ? 'No puede circular en el horario restringido' : 'La placa tendrá restricción',
            'message' => 'La terminación ' . $digit . ' está restringida entre las 6:00 a. m. y las 9:00 p. m. para la fecha seleccionada.',
            'allowed' => $allowed,
            'restricted' => $restricted,
            'holiday' => null,
            'is_regular_day' => true,
            'inside_hours' => $insideHours,
            'plate_restricted' => true,
        ];
    }

    return [
        'type' => 'success',
        'title' => $isToday ? 'Sí puede circular hoy' : 'La placa podrá circular',
        'message' => 'La terminación ' . $digit . ' está autorizada para circular durante la fecha seleccionada, según la rotación ordinaria.',
        'allowed' => $allowed,
        'restricted' => $restricted,
        'holiday' => null,
        'is_regular_day' => true,
        'inside_hours' => $insideHours,
        'plate_restricted' => false,
    ];
}

function digitList(array $digits): string
{
    return implode(', ', $digits);
}

$now = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));
$today = new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));

$plateInput = strtoupper(trim((string)($_GET['placa'] ?? '')));
$plateInput = preg_replace('/[^A-Z0-9-]/', '', $plateInput) ?? '';
$plateInput = substr($plateInput, 0, 10);
$plateDigit = lastDigitFromPlate($plateInput);

$selectedDate = parseDateSafe((string)($_GET['fecha'] ?? $today->format('Y-m-d')), $today);
$selectedMonth = parseMonthSafe((string)($_GET['mes'] ?? $selectedDate->format('Y-m')), $selectedDate);
$result = evaluateDay($selectedDate, $plateDigit, $now);
$todayResult = evaluateDay($today, null, $now);
$todayAllowed = allowedDigits($today);
$todayRestricted = restrictedDigits($today);
$todayHolidays = colombianHolidays((int)$today->format('Y'));
$todayHoliday = $todayHolidays[$today->format('Y-m-d')] ?? null;
$todayIsRegular = (int)$today->format('N') <= 5 && $todayHoliday === null;

$firstDay = $selectedMonth->modify('first day of this month');
$daysInMonth = (int)$selectedMonth->format('t');
$startOffset = (int)$firstDay->format('N') - 1;
$monthHolidays = colombianHolidays((int)$selectedMonth->format('Y'));
$previousMonth = $selectedMonth->modify('-1 month')->format('Y-m');
$nextMonth = $selectedMonth->modify('+1 month')->format('Y-m');

$baseQuery = [];
if ($plateInput !== '') $baseQuery['placa'] = $plateInput;
$baseQuery['fecha'] = $selectedDate->format('Y-m-d');

$faqItems = [
    [
        'q' => '¿Cuál es el horario del Pico y Placa para particulares en Bogotá?',
        'a' => 'La restricción ordinaria para vehículos particulares aplica de lunes a viernes, entre las 6:00 a. m. y las 9:00 p. m., salvo festivos o modificaciones especiales anunciadas por el Distrito.'
    ],
    [
        'q' => '¿Qué placas pueden circular los días impares?',
        'a' => 'En los días impares pueden circular los vehículos particulares cuya placa termina en 1, 2, 3, 4 o 5.'
    ],
    [
        'q' => '¿Qué placas pueden circular los días pares?',
        'a' => 'En los días pares pueden circular los vehículos particulares cuya placa termina en 6, 7, 8, 9 o 0.'
    ],
    [
        'q' => '¿Cuánto cuesta la multa por incumplir el Pico y Placa en 2026?',
        'a' => 'La sanción informada por la Secretaría Distrital de Movilidad para 2026 es de $633.200, además de la posible inmovilización del vehículo y costos asociados.'
    ],
    [
        'q' => '¿Cuánto cuesta el Pico y Placa Solidario en 2026?',
        'a' => 'Los valores base de 2026 son $70.294 por un día, $561.808 por un mes y $2.809.311 por seis meses. El valor final cambia según avalúo, impacto ambiental y municipio de matrícula.'
    ],
    [
        'q' => '¿El Pico y Placa aplica los sábados, domingos y festivos?',
        'a' => 'La restricción ordinaria para vehículos particulares no aplica en fines de semana ni festivos. Sin embargo, puede existir Pico y Placa Regional, Día sin Carro u otra medida especial.'
    ],
];

$faqSchema = [];
foreach ($faqItems as $item) {
    $faqSchema[] = [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['a'],
        ],
    ];
}
?>
<!doctype html>
<html lang="es-CO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071f49">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">

    <meta property="og:locale" content="es_CO">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:image" content="<?= e($canonicalUrl) ?>?asset=og">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDescription) ?>">
    <meta name="twitter:image" content="<?= e($canonicalUrl) ?>?asset=og">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $pageTitle,
        'description' => $metaDescription,
        'url' => $canonicalUrl,
        'inLanguage' => 'es-CO',
        'dateModified' => $dataVersion,
        'about' => [
            '@type' => 'Thing',
            'name' => 'Pico y Placa de Bogotá',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteName,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqSchema,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <style>
        :root {
            --navy: #071f49;
            --navy-2: #0b2c64;
            --blue: #1260d6;
            --blue-2: #2f7ff0;
            --cyan: #30cfd0;
            --sky: #eef6ff;
            --ink: #15243b;
            --muted: #63718a;
            --line: #dfe8f4;
            --white: #ffffff;
            --green: #0c9b67;
            --green-bg: #e9fbf4;
            --red: #cf3448;
            --red-bg: #fff0f2;
            --amber: #b46a00;
            --amber-bg: #fff7e5;
            --shadow: 0 20px 60px rgba(7, 31, 73, .12);
            --shadow-soft: 0 10px 30px rgba(7, 31, 73, .08);
            --radius: 24px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            color: var(--ink);
            background: #f7faff;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }
        img, svg { max-width: 100%; }
        a { color: var(--blue); text-decoration: none; }
        a:hover { text-decoration: underline; }
        button, input, select { font: inherit; }
        .container { width: min(1180px, calc(100% - 36px)); margin-inline: auto; }
        .section { padding: 84px 0; }
        .section-sm { padding: 52px 0; }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--blue);
            font-weight: 800;
            font-size: .78rem;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        h1, h2, h3 { line-height: 1.14; margin-top: 0; color: var(--navy); }
        h1 { font-size: clamp(2.4rem, 6vw, 5.3rem); letter-spacing: -.055em; margin-bottom: 22px; }
        h2 { font-size: clamp(2rem, 4vw, 3.25rem); letter-spacing: -.035em; margin-bottom: 18px; }
        h3 { font-size: 1.25rem; }
        p { margin-top: 0; }
        .lead { font-size: clamp(1.05rem, 2vw, 1.25rem); color: #dbe8ff; max-width: 680px; }
        .section-intro { max-width: 760px; color: var(--muted); font-size: 1.08rem; }
        .muted { color: var(--muted); }
        .small { font-size: .9rem; }
        .text-center { text-align: center; }

        .topbar {
            background: #03142f;
            color: #dceaff;
            font-size: .86rem;
            padding: 9px 0;
        }
        .topbar-inner { display: flex; justify-content: space-between; gap: 20px; align-items: center; }
        .topbar a { color: var(--cyan); font-weight: 700; }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(7, 31, 73, .92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,.09);
        }
        .nav-inner { min-height: 72px; display: flex; align-items: center; justify-content: space-between; gap: 18px; }
        .brand { display: flex; align-items: center; gap: 12px; color: #fff; font-weight: 900; letter-spacing: -.02em; }
        .brand:hover { text-decoration: none; }
        .brand-mark {
            width: 42px; height: 42px; border-radius: 14px;
            display: grid; place-items: center;
            color: var(--navy); background: linear-gradient(135deg, var(--cyan), #79f2df);
            box-shadow: 0 8px 24px rgba(48,207,208,.25);
        }
        .nav-links { display: flex; align-items: center; gap: 24px; }
        .nav-links a { color: #eaf2ff; font-size: .93rem; font-weight: 700; }
        .nav-links a:hover { color: var(--cyan); text-decoration: none; }
        .nav-cta {
            padding: 10px 16px; border-radius: 999px; background: var(--cyan); color: var(--navy)!important;
        }

        .hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 85% 18%, rgba(48,207,208,.25), transparent 24%),
                radial-gradient(circle at 12% 82%, rgba(47,127,240,.25), transparent 28%),
                linear-gradient(135deg, #061b40 0%, #0b3476 58%, #1260d6 100%);
            color: #fff;
            padding: 82px 0 70px;
        }
        .hero::before {
            content: ""; position: absolute; inset: 0; opacity: .18;
            background-image: linear-gradient(rgba(255,255,255,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, #000, transparent 85%);
        }
        .hero-grid { position: relative; display: grid; grid-template-columns: 1.08fr .92fr; gap: 54px; align-items: center; }
        .hero h1 { color: #fff; }
        .hero .eyebrow { color: #89fff1; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 13px; margin: 30px 0 34px; }
        .btn {
            border: 0; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 9px;
            min-height: 50px; padding: 13px 20px; border-radius: 14px; font-weight: 850;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .btn:hover { transform: translateY(-2px); text-decoration: none; }
        .btn-primary { color: var(--navy); background: var(--cyan); box-shadow: 0 14px 36px rgba(48,207,208,.26); }
        .btn-light { color: #fff; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22); }
        .btn-blue { color: #fff; background: var(--blue); box-shadow: 0 12px 30px rgba(18,96,214,.24); }
        .btn-outline { color: var(--navy); background: #fff; border: 1px solid var(--line); }
        .hero-pills { display: flex; flex-wrap: wrap; gap: 10px; }
        .hero-pill {
            display: inline-flex; gap: 8px; align-items: center; padding: 9px 13px; border-radius: 999px;
            color: #e6f0ff; background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.13); font-size: .9rem;
        }
        .hero-card {
            position: relative; padding: 24px; border-radius: 32px; background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18); backdrop-filter: blur(14px); box-shadow: 0 30px 80px rgba(0,0,0,.22);
        }
        .hero-status {
            position: absolute; left: -18px; bottom: 28px; z-index: 2; width: min(320px, 88%);
            padding: 16px 18px; border-radius: 18px; color: var(--navy); background: #fff; box-shadow: var(--shadow);
        }
        .hero-status strong { display: block; font-size: 1.05rem; }
        .hero-status span { color: var(--muted); font-size: .88rem; }

        .quick-strip { margin-top: -34px; position: relative; z-index: 8; }
        .quick-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); overflow: hidden;
            background: #fff; border: 1px solid var(--line); border-radius: 22px; box-shadow: var(--shadow);
        }
        .quick-item { padding: 24px; border-right: 1px solid var(--line); }
        .quick-item:last-child { border-right: 0; }
        .quick-icon { width: 44px; height: 44px; border-radius: 14px; display: grid; place-items: center; background: var(--sky); color: var(--blue); font-size: 1.25rem; margin-bottom: 12px; }
        .quick-item strong { display: block; color: var(--navy); font-size: 1.05rem; }
        .quick-item span { color: var(--muted); font-size: .9rem; }

        .checker-wrap { display: grid; grid-template-columns: .92fr 1.08fr; gap: 32px; align-items: stretch; }
        .card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow-soft);
        }
        .checker-card { padding: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.full { grid-column: 1 / -1; }
        label { font-weight: 800; color: var(--navy); font-size: .92rem; }
        input[type="text"], input[type="date"], input[type="month"] {
            width: 100%; min-height: 52px; padding: 12px 14px; color: var(--ink); background: #fbfdff;
            border: 1px solid #cfdbea; border-radius: 13px; outline: none;
        }
        input:focus { border-color: var(--blue); box-shadow: 0 0 0 4px rgba(18,96,214,.1); }
        .plate-input { text-transform: uppercase; font-weight: 900; letter-spacing: .18em; font-size: 1.15rem; }
        .helper { color: var(--muted); font-size: .83rem; }
        .form-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; }

        .result-card { padding: 30px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; }
        .result-card::after { content: ""; position: absolute; width: 190px; height: 190px; border-radius: 50%; right: -70px; top: -70px; opacity: .14; background: currentColor; }
        .result-card.success, .result-card.free { color: var(--green); background: linear-gradient(145deg, #fff 35%, var(--green-bg)); border-color: #b8ead8; }
        .result-card.danger { color: var(--red); background: linear-gradient(145deg, #fff 35%, var(--red-bg)); border-color: #ffc9d0; }
        .result-card.info { color: var(--blue); background: linear-gradient(145deg, #fff 35%, var(--sky)); }
        .result-icon { width: 64px; height: 64px; border-radius: 20px; display: grid; place-items: center; font-size: 1.8rem; background: currentColor; margin-bottom: 20px; }
        .result-icon i { color: #fff; }
        .result-card h3 { font-size: 1.65rem; color: var(--navy); margin-bottom: 10px; }
        .result-card p { color: var(--ink); }
        .result-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
        .tag { display: inline-flex; align-items: center; gap: 7px; padding: 8px 11px; border-radius: 999px; background: rgba(255,255,255,.8); border: 1px solid var(--line); color: var(--navy); font-size: .85rem; font-weight: 750; }

        .ad-slot {
            min-height: 110px; display: grid; place-items: center; text-align: center; padding: 24px; margin: 34px auto;
            border: 1px dashed #b7c8dc; border-radius: 18px; color: #8090a6; background: #f2f7fd; font-size: .86rem;
        }

        .rule-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 34px; }
        .rule-card { padding: 30px; position: relative; overflow: hidden; }
        .rule-number { display: flex; gap: 10px; flex-wrap: wrap; margin: 22px 0; }
        .digit { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 16px; font-weight: 950; font-size: 1.15rem; }
        .rule-card.odd .digit { background: #eaf8ff; color: #0879b8; }
        .rule-card.even .digit { background: #eaf0ff; color: #274dc9; }
        .rule-card::after { content: ""; position: absolute; width: 160px; height: 160px; border-radius: 50%; right: -85px; bottom: -85px; background: var(--blue); opacity: .08; }

        .calendar-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin: 30px 0 18px; }
        .month-nav { display: flex; align-items: center; gap: 10px; }
        .icon-btn { width: 44px; height: 44px; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 13px; background: #fff; color: var(--navy); }
        .icon-btn:hover { text-decoration: none; border-color: var(--blue); }
        .calendar-scroll { overflow-x: auto; padding-bottom: 4px; }
        .calendar { min-width: 820px; display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
        .calendar-head { padding: 10px; text-align: center; font-weight: 850; color: var(--muted); font-size: .82rem; text-transform: uppercase; letter-spacing: .08em; }
        .calendar-day {
            min-height: 116px; border: 1px solid var(--line); border-radius: 16px; padding: 12px; background: #fff; position: relative;
        }
        .calendar-day.blank { background: transparent; border-color: transparent; }
        .calendar-day.today { outline: 3px solid rgba(18,96,214,.17); border-color: var(--blue); }
        .calendar-day.allowed { background: #effbf7; border-color: #bdebd9; }
        .calendar-day.restricted { background: #fff3f4; border-color: #ffd1d7; }
        .calendar-day.free { background: #f5f8fc; color: var(--muted); }
        .day-num { font-weight: 950; color: var(--navy); font-size: 1.05rem; }
        .day-state { display: block; margin-top: 12px; font-size: .78rem; font-weight: 850; line-height: 1.25; }
        .calendar-day.allowed .day-state { color: var(--green); }
        .calendar-day.restricted .day-state { color: var(--red); }
        .calendar-day.free .day-state { color: var(--muted); }
        .day-holiday { display: block; margin-top: 5px; font-size: .68rem; line-height: 1.2; color: var(--amber); }
        .legend { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 16px; color: var(--muted); font-size: .86rem; }
        .legend-item { display: inline-flex; align-items: center; gap: 7px; }
        .dot { width: 11px; height: 11px; border-radius: 50%; }
        .dot.green { background: var(--green); }
        .dot.red { background: var(--red); }
        .dot.gray { background: #9daabc; }

        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-top: 34px; }
        .price-card { padding: 30px; position: relative; overflow: hidden; }
        .price-card.featured { border: 2px solid var(--blue); transform: translateY(-8px); }
        .price-label { color: var(--blue); text-transform: uppercase; letter-spacing: .1em; font-size: .75rem; font-weight: 900; }
        .price { margin: 10px 0 4px; font-size: clamp(2rem, 4vw, 3rem); line-height: 1; letter-spacing: -.04em; font-weight: 950; color: var(--navy); }
        .price-note { color: var(--muted); font-size: .87rem; }
        .price-list { list-style: none; padding: 0; margin: 22px 0; }
        .price-list li { display: flex; gap: 10px; margin: 10px 0; color: var(--muted); }
        .price-list i { color: var(--green); }
        .badge-popular { position: absolute; right: 18px; top: 18px; padding: 7px 10px; border-radius: 999px; background: var(--blue); color: #fff; font-size: .7rem; font-weight: 900; }

        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-top: 32px; }
        .step { padding: 24px; }
        .step-no { width: 40px; height: 40px; display: grid; place-items: center; border-radius: 13px; color: #fff; background: var(--blue); font-weight: 950; margin-bottom: 16px; }

        .fine-banner {
            display: grid; grid-template-columns: auto 1fr auto; gap: 24px; align-items: center;
            padding: 30px; color: #fff; background: linear-gradient(135deg, #811b2a, #cf3448); border-radius: 26px; box-shadow: 0 24px 60px rgba(207,52,72,.24);
        }
        .fine-icon { width: 68px; height: 68px; border-radius: 20px; display: grid; place-items: center; background: rgba(255,255,255,.14); font-size: 2rem; }
        .fine-banner h2 { color: #fff; margin-bottom: 6px; font-size: clamp(1.7rem, 3vw, 2.5rem); }
        .fine-amount { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 950; white-space: nowrap; }

        .regional-grid { display: grid; grid-template-columns: .85fr 1.15fr; gap: 30px; align-items: start; }
        .regional-box { padding: 30px; background: linear-gradient(145deg, #fff, #eef6ff); }
        .time-band { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 22px; }
        .time-card { padding: 18px; border: 1px solid var(--line); border-radius: 16px; background: #fff; }
        .time-card strong { color: var(--navy); display: block; }
        .corridors { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .corridor { display: flex; gap: 10px; align-items: center; padding: 13px 15px; border: 1px solid var(--line); border-radius: 14px; background: #fff; }
        .corridor i { color: var(--blue); }

        .exemption-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 30px; }
        .exemption { padding: 22px; }
        .exemption-icon { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 15px; color: var(--blue); background: var(--sky); font-size: 1.25rem; margin-bottom: 14px; }

        .content-grid { display: grid; grid-template-columns: 1fr 340px; gap: 34px; align-items: start; }
        .article-card { padding: 34px; }
        .article-card h3 { margin-top: 28px; }
        .sidebar { position: sticky; top: 96px; display: grid; gap: 18px; }
        .sidebar-card { padding: 22px; }
        .sidebar-card ul { margin: 0; padding-left: 18px; color: var(--muted); }
        .sidebar-card li { margin: 9px 0; }

        .faq { display: grid; gap: 12px; margin-top: 30px; }
        details { border: 1px solid var(--line); border-radius: 16px; background: #fff; overflow: hidden; }
        summary { cursor: pointer; list-style: none; padding: 20px 22px; font-weight: 850; color: var(--navy); display: flex; justify-content: space-between; gap: 16px; }
        summary::-webkit-details-marker { display: none; }
        summary::after { content: "+"; color: var(--blue); font-size: 1.3rem; }
        details[open] summary::after { content: "–"; }
        details p { padding: 0 22px 22px; margin: 0; color: var(--muted); }

        .source-list { display: grid; gap: 12px; margin-top: 24px; }
        .source-link { display: flex; align-items: flex-start; gap: 13px; padding: 16px; border: 1px solid var(--line); border-radius: 15px; background: #fff; }
        .source-link i { color: var(--blue); margin-top: 3px; }
        .source-link strong { display: block; color: var(--navy); }
        .source-link span { color: var(--muted); font-size: .87rem; }

        .notice { padding: 18px 20px; border-radius: 16px; background: var(--amber-bg); color: #6d4a0c; border: 1px solid #f0d89a; }
        .notice strong { color: #5b3c06; }

        footer { color: #cbd9ee; background: #03142f; padding: 60px 0 26px; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr repeat(3, 1fr); gap: 34px; }
        footer h3 { color: #fff; font-size: 1rem; }
        footer a { color: #cbd9ee; }
        .footer-links { display: grid; gap: 9px; }
        .footer-bottom { margin-top: 40px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.1); display: flex; justify-content: space-between; gap: 18px; color: #92a5c1; font-size: .84rem; }

        @media (max-width: 980px) {
            .nav-links a:not(.nav-cta) { display: none; }
            .hero-grid, .checker-wrap, .regional-grid, .content-grid { grid-template-columns: 1fr; }
            .hero-card { max-width: 720px; }
            .quick-grid { grid-template-columns: repeat(2, 1fr); }
            .quick-item:nth-child(2) { border-right: 0; }
            .quick-item:nth-child(-n+2) { border-bottom: 1px solid var(--line); }
            .pricing-grid, .exemption-grid { grid-template-columns: repeat(2, 1fr); }
            .steps { grid-template-columns: repeat(2, 1fr); }
            .sidebar { position: static; grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 680px) {
            .container { width: min(100% - 24px, 1180px); }
            .section { padding: 64px 0; }
            .hero { padding-top: 58px; }
            .hero-status { position: static; width: 100%; margin-top: 12px; }
            .topbar-inner { flex-direction: column; align-items: flex-start; gap: 4px; }
            .quick-grid, .rule-grid, .pricing-grid, .steps, .exemption-grid, .sidebar, .footer-grid { grid-template-columns: 1fr; }
            .quick-item { border-right: 0; border-bottom: 1px solid var(--line); }
            .quick-item:last-child { border-bottom: 0; }
            .form-grid, .time-band, .corridors { grid-template-columns: 1fr; }
            .fine-banner { grid-template-columns: 1fr; }
            .fine-amount { white-space: normal; }
            .footer-bottom { flex-direction: column; }
            .checker-card, .result-card, .article-card { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <span><i class="bi bi-shield-check"></i> Información revisada con fuentes oficiales · Actualización: 2 de agosto de 2026</span>
            <a href="#fuentes">Ver fuentes oficiales</a>
        </div>
    </div>

    <nav class="navbar" aria-label="Navegación principal">
        <div class="container nav-inner">
            <a class="brand" href="#inicio" aria-label="Inicio">
                <span class="brand-mark"><i class="bi bi-car-front-fill"></i></span>
                <span><?= e($siteName) ?></span>
            </a>
            <div class="nav-links">
                <a href="#consultar">Consultar placa</a>
                <a href="#calendario">Calendario</a>
                <a href="#tarifas">Tarifas</a>
                <a href="#regional">Regional</a>
                <a class="nav-cta" href="<?= e($officialSolidario) ?>" target="_blank" rel="noopener noreferrer">Permiso Solidario</a>
            </div>
        </div>
    </nav>

    <header id="inicio" class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow"><i class="bi bi-geo-alt-fill"></i> Bogotá, Colombia</span>
                <h1>Pico y Placa Bogotá hoy</h1>
                <p class="lead">Consulta en segundos si tu vehículo particular puede circular, revisa el calendario mensual, las tarifas del permiso solidario, la multa y el Pico y Placa Regional.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="#consultar"><i class="bi bi-search"></i> Consultar mi placa</a>
                    <a class="btn btn-light" href="#regla"><i class="bi bi-calendar3"></i> Ver cómo funciona</a>
                </div>
                <div class="hero-pills">
                    <span class="hero-pill"><i class="bi bi-clock-fill"></i> 6:00 a. m. a 9:00 p. m.</span>
                    <span class="hero-pill"><i class="bi bi-calendar-week-fill"></i> Lunes a viernes</span>
                    <span class="hero-pill"><i class="bi bi-car-front-fill"></i> Vehículos particulares</span>
                </div>
            </div>

            <div class="hero-card" aria-label="Ilustración de movilidad en Bogotá">
                <svg viewBox="0 0 680 520" role="img" aria-labelledby="svgTitle svgDesc">
                    <title id="svgTitle">Ilustración de tráfico y Pico y Placa en Bogotá</title>
                    <desc id="svgDesc">Montañas, edificios, carretera, automóvil y señal vial.</desc>
                    <defs>
                        <linearGradient id="skyGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#8deff0"/>
                            <stop offset="1" stop-color="#dff8ff"/>
                        </linearGradient>
                        <linearGradient id="roadGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#415574"/>
                            <stop offset="1" stop-color="#162944"/>
                        </linearGradient>
                        <filter id="shadowCar" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="14" stdDeviation="12" flood-color="#071f49" flood-opacity=".28"/>
                        </filter>
                    </defs>
                    <rect x="12" y="12" width="656" height="496" rx="32" fill="url(#skyGrad)"/>
                    <circle cx="550" cy="92" r="42" fill="#ffd05a" opacity=".95"/>
                    <path d="M12 282 L112 172 L185 250 L283 142 L400 278 L500 185 L668 302 L668 508 L12 508 Z" fill="#74a998" opacity=".68"/>
                    <path d="M12 316 L126 228 L207 293 L323 190 L448 320 L568 242 L668 320 L668 508 L12 508 Z" fill="#326f73" opacity=".9"/>
                    <g opacity=".95">
                        <rect x="55" y="250" width="62" height="130" rx="4" fill="#f0f5fb"/>
                        <rect x="125" y="286" width="54" height="94" rx="4" fill="#cbd8e9"/>
                        <rect x="190" y="230" width="72" height="150" rx="4" fill="#e8eff8"/>
                        <rect x="272" y="275" width="55" height="105" rx="4" fill="#b8c9dc"/>
                        <rect x="337" y="238" width="82" height="142" rx="4" fill="#f3f6fb"/>
                        <rect x="430" y="290" width="57" height="90" rx="4" fill="#c9d6e6"/>
                    </g>
                    <path d="M145 508 L268 328 L412 328 L548 508 Z" fill="url(#roadGrad)"/>
                    <path d="M334 338 L346 338 L354 385 L328 385 Z" fill="#fff" opacity=".9"/>
                    <path d="M320 409 L362 409 L380 478 L300 478 Z" fill="#fff" opacity=".9"/>
                    <g transform="translate(240 333)" filter="url(#shadowCar)">
                        <path d="M45 70 L75 25 Q85 10 105 10 H215 Q235 10 247 28 L274 70 Z" fill="#1260d6"/>
                        <path d="M92 26 H207 Q218 26 225 38 L244 68 H67 L84 38 Q88 26 92 26Z" fill="#d9f4ff"/>
                        <rect x="26" y="65" width="270" height="90" rx="30" fill="#2f7ff0"/>
                        <rect x="48" y="90" width="200" height="28" rx="10" fill="#0a367b" opacity=".45"/>
                        <circle cx="80" cy="152" r="27" fill="#132a48"/><circle cx="80" cy="152" r="12" fill="#a7bed8"/>
                        <circle cx="244" cy="152" r="27" fill="#132a48"/><circle cx="244" cy="152" r="12" fill="#a7bed8"/>
                        <rect x="134" y="124" width="58" height="24" rx="5" fill="#fff"/>
                        <text x="142" y="141" fill="#071f49" font-size="13" font-family="Arial" font-weight="700">BOG 26</text>
                        <circle cx="56" cy="94" r="10" fill="#fff4a2"/><circle cx="266" cy="94" r="10" fill="#ff7777"/>
                    </g>
                    <g transform="translate(502 116)">
                        <rect x="51" y="140" width="14" height="150" rx="7" fill="#53657d"/>
                        <circle cx="58" cy="85" r="76" fill="#fff" stroke="#cf3448" stroke-width="14"/>
                        <text x="58" y="72" text-anchor="middle" fill="#071f49" font-size="29" font-family="Arial" font-weight="900">PICO</text>
                        <text x="58" y="108" text-anchor="middle" fill="#071f49" font-size="29" font-family="Arial" font-weight="900">Y PLACA</text>
                    </g>
                </svg>

                <div class="hero-status">
                    <strong><?= e(ucfirst(longDateEs($today))) ?></strong>
                    <span>
                        <?php if (!$todayIsRegular): ?>
                            Sin restricción ordinaria<?= $todayHoliday ? ' · ' . e($todayHoliday) : '' ?>
                        <?php else: ?>
                            Circulan: <?= e(digitList($todayAllowed)) ?> · Restringidas: <?= e(digitList($todayRestricted)) ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </header>

    <div class="quick-strip">
        <div class="container quick-grid">
            <div class="quick-item">
                <span class="quick-icon"><i class="bi bi-clock-history"></i></span>
                <strong>Horario</strong>
                <span>6:00 a. m. a 9:00 p. m.</span>
            </div>
            <div class="quick-item">
                <span class="quick-icon"><i class="bi bi-calendar2-week"></i></span>
                <strong>Días</strong>
                <span>Lunes a viernes no festivos</span>
            </div>
            <div class="quick-item">
                <span class="quick-icon"><i class="bi bi-cash-coin"></i></span>
                <strong>Multa 2026</strong>
                <span>$633.200 + posible inmovilización</span>
            </div>
            <div class="quick-item">
                <span class="quick-icon"><i class="bi bi-ticket-perforated"></i></span>
                <strong>Permiso diario</strong>
                <span>Valor base desde $70.294</span>
            </div>
        </div>
    </div>

    <main>
        <section id="consultar" class="section">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-search"></i> Consulta personalizada</span>
                <h2>¿Tu placa puede circular?</h2>
                <p class="section-intro">Escribe la placa completa o únicamente su último número. El sistema tiene en cuenta fines de semana y festivos nacionales de Colombia.</p>

                <div class="checker-wrap">
                    <form class="card checker-card" method="get" action="#consultar">
                        <div class="form-grid">
                            <div class="field">
                                <label for="placa">Placa o último dígito</label>
                                <input class="plate-input" id="placa" name="placa" type="text" value="<?= e($plateInput) ?>" placeholder="ABC123" maxlength="10" autocomplete="off" inputmode="text" aria-describedby="plateHelp">
                                <span id="plateHelp" class="helper">Ejemplos: ABC123, 123 o simplemente 3.</span>
                            </div>
                            <div class="field">
                                <label for="fecha">Fecha de consulta</label>
                                <input id="fecha" name="fecha" type="date" value="<?= e($selectedDate->format('Y-m-d')) ?>">
                            </div>
                            <div class="field full">
                                <label for="mes">Mes del calendario</label>
                                <input id="mes" name="mes" type="month" value="<?= e($selectedMonth->format('Y-m')) ?>">
                            </div>
                            <div class="field full form-actions">
                                <button class="btn btn-blue" type="submit"><i class="bi bi-check2-circle"></i> Consultar ahora</button>
                                <a class="btn btn-outline" href="<?= e(strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: 'pico-y-placa-bogota.php') ?>#consultar"><i class="bi bi-arrow-counterclockwise"></i> Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <article class="card result-card <?= e($result['type']) ?>" aria-live="polite">
                        <span class="result-icon">
                            <?php if ($result['type'] === 'danger'): ?>
                                <i class="bi bi-x-octagon-fill"></i>
                            <?php elseif ($result['type'] === 'success' || $result['type'] === 'free'): ?>
                                <i class="bi bi-check-circle-fill"></i>
                            <?php else: ?>
                                <i class="bi bi-info-circle-fill"></i>
                            <?php endif; ?>
                        </span>
                        <span class="eyebrow"><?= e(ucfirst(longDateEs($selectedDate))) ?></span>
                        <h3><?= e($result['title']) ?></h3>
                        <p><?= e($result['message']) ?></p>
                        <div class="result-meta">
                            <span class="tag"><i class="bi bi-check2"></i> Circulan: <?= e(digitList($result['allowed'])) ?></span>
                            <span class="tag"><i class="bi bi-slash-circle"></i> Restringidas: <?= e(digitList($result['restricted'])) ?></span>
                            <?php if ($plateDigit !== null): ?>
                                <span class="tag"><i class="bi bi-car-front"></i> Terminación: <?= e((string)$plateDigit) ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>

                <div class="notice" style="margin-top:22px">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i> Importante:</strong>
                    esta herramienta informa la rotación ordinaria de vehículos particulares. No reemplaza las comunicaciones oficiales sobre Pico y Placa Regional, Día sin Carro, cierres, emergencias o cambios temporales.
                </div>

                <!-- ESPACIO PUBLICITARIO: inserta aquí un bloque responsive de Google AdSense/Google Ad Manager. -->
                <div class="ad-slot" aria-label="Espacio publicitario">
                    <div><i class="bi bi-badge-ad" style="font-size:1.35rem"></i><br>Espacio recomendado para anuncio responsive<br><small>Evita colocar anuncios pegados al botón de consulta.</small></div>
                </div>
            </div>
        </section>

        <section id="regla" class="section" style="background:#eef5fd">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-sign-turn-right-fill"></i> Regla vigente</span>
                <h2>¿Cómo funciona el Pico y Placa en Bogotá?</h2>
                <p class="section-intro">La rotación se define por el número del día del calendario y el último dígito de la placa. La regla indica cuáles placas <strong>sí pueden circular</strong>.</p>

                <div class="rule-grid">
                    <article class="card rule-card odd">
                        <span class="eyebrow">Días impares</span>
                        <h3>Circulan las placas terminadas en 1, 2, 3, 4 y 5</h3>
                        <div class="rule-number" aria-label="Placas autorizadas en días impares">
                            <?php foreach ([1,2,3,4,5] as $digit): ?><span class="digit"><?= $digit ?></span><?php endforeach; ?>
                        </div>
                        <p class="muted">Ejemplo: el lunes 3 de agosto de 2026 es día impar; durante el horario de restricción pueden circular las terminaciones 1 a 5.</p>
                    </article>

                    <article class="card rule-card even">
                        <span class="eyebrow">Días pares</span>
                        <h3>Circulan las placas terminadas en 6, 7, 8, 9 y 0</h3>
                        <div class="rule-number" aria-label="Placas autorizadas en días pares">
                            <?php foreach ([6,7,8,9,0] as $digit): ?><span class="digit"><?= $digit ?></span><?php endforeach; ?>
                        </div>
                        <p class="muted">Ejemplo: el martes 4 de agosto de 2026 es día par; durante el horario de restricción pueden circular las terminaciones 6 a 0.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="calendario" class="section">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-calendar3"></i> Planea tus recorridos</span>
                <h2>Calendario de <?= e(spanishMonth((int)$selectedMonth->format('n'))) ?> de <?= e($selectedMonth->format('Y')) ?></h2>
                <p class="section-intro">
                    <?php if ($plateDigit !== null): ?>
                        Calendario personalizado para placas terminadas en <strong><?= e((string)$plateDigit) ?></strong>. Verde significa que puede circular; rojo, que tiene restricción entre 6:00 a. m. y 9:00 p. m.
                    <?php else: ?>
                        Ingresa una placa en el formulario para ver un calendario personalizado. Sin placa, cada día muestra las terminaciones autorizadas.
                    <?php endif; ?>
                </p>

                <div class="calendar-toolbar">
                    <div class="month-nav">
                        <a class="icon-btn" aria-label="Mes anterior" href="?<?= e(http_build_query(array_merge($baseQuery, ['mes' => $previousMonth]))) ?>#calendario"><i class="bi bi-chevron-left"></i></a>
                        <strong><?= e(ucfirst(spanishMonth((int)$selectedMonth->format('n')))) ?> <?= e($selectedMonth->format('Y')) ?></strong>
                        <a class="icon-btn" aria-label="Mes siguiente" href="?<?= e(http_build_query(array_merge($baseQuery, ['mes' => $nextMonth]))) ?>#calendario"><i class="bi bi-chevron-right"></i></a>
                    </div>
                    <a class="btn btn-outline" href="?<?= e(http_build_query(array_merge($baseQuery, ['mes' => $today->format('Y-m')]))) ?>#calendario"><i class="bi bi-calendar-check"></i> Ir al mes actual</a>
                </div>

                <div class="calendar-scroll">
                    <div class="calendar" role="grid" aria-label="Calendario mensual de Pico y Placa">
                        <?php foreach (['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dayName): ?>
                            <div class="calendar-head" role="columnheader"><?= e($dayName) ?></div>
                        <?php endforeach; ?>

                        <?php for ($blank = 0; $blank < $startOffset; $blank++): ?>
                            <div class="calendar-day blank" aria-hidden="true"></div>
                        <?php endfor; ?>

                        <?php for ($day = 1; $day <= $daysInMonth; $day++):
                            $date = $selectedMonth->setDate((int)$selectedMonth->format('Y'), (int)$selectedMonth->format('n'), $day);
                            $key = $date->format('Y-m-d');
                            $isWeekend = (int)$date->format('N') >= 6;
                            $holiday = $monthHolidays[$key] ?? null;
                            $isFree = $isWeekend || $holiday !== null;
                            $allowed = allowedDigits($date);
                            $restricted = restrictedDigits($date);
                            $isTodayCell = $key === $today->format('Y-m-d');

                            if ($isFree) {
                                $cellClass = 'free';
                                $stateText = 'Sin restricción ordinaria';
                            } elseif ($plateDigit === null) {
                                $cellClass = 'allowed';
                                $stateText = 'Circulan: ' . digitList($allowed);
                            } elseif (in_array($plateDigit, $restricted, true)) {
                                $cellClass = 'restricted';
                                $stateText = 'Tu placa no circula';
                            } else {
                                $cellClass = 'allowed';
                                $stateText = 'Tu placa sí circula';
                            }
                        ?>
                            <div class="calendar-day <?= e($cellClass) ?><?= $isTodayCell ? ' today' : '' ?>" role="gridcell" aria-label="<?= e(longDateEs($date) . ': ' . $stateText) ?>">
                                <span class="day-num"><?= $day ?></span>
                                <span class="day-state"><?= e($stateText) ?></span>
                                <?php if ($holiday): ?><span class="day-holiday"><?= e($holiday) ?></span><?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="legend">
                    <span class="legend-item"><span class="dot green"></span> Puede circular / placas autorizadas</span>
                    <span class="legend-item"><span class="dot red"></span> Restricción para tu placa</span>
                    <span class="legend-item"><span class="dot gray"></span> Fin de semana o festivo</span>
                </div>
            </div>
        </section>

        <section id="tarifas" class="section" style="background:#eef5fd">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-credit-card-2-front-fill"></i> Pico y Placa Solidario</span>
                <h2>Tarifas base para circular en días restringidos</h2>
                <p class="section-intro">El permiso permite circular voluntariamente durante la restricción ordinaria. Los siguientes son valores base de 2026; la plataforma oficial calcula el precio final según avalúo, impacto ambiental y municipio de matrícula.</p>

                <div class="pricing-grid">
                    <article class="card price-card">
                        <span class="price-label">Permiso diario</span>
                        <div class="price">$70.294</div>
                        <span class="price-note">Valor base 2026</span>
                        <ul class="price-list">
                            <li><i class="bi bi-check-circle-fill"></i><span>Para una fecha específica.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>Ideal para diligencias puntuales.</span></li>
                            <li><i class="bi bi-info-circle-fill"></i><span>Debe quedar aprobado antes de circular.</span></li>
                        </ul>
                    </article>

                    <article class="card price-card featured">
                        <span class="badge-popular">MÁS CONSULTADO</span>
                        <span class="price-label">Permiso mensual</span>
                        <div class="price">$561.808</div>
                        <span class="price-note">Valor base 2026</span>
                        <ul class="price-list">
                            <li><i class="bi bi-check-circle-fill"></i><span>Vigencia aproximada de un mes.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>Útil para uso frecuente.</span></li>
                            <li><i class="bi bi-info-circle-fill"></i><span>El valor final puede aumentar.</span></li>
                        </ul>
                    </article>

                    <article class="card price-card">
                        <span class="price-label">Permiso semestral</span>
                        <div class="price">$2.809.311</div>
                        <span class="price-note">Valor base 2026</span>
                        <ul class="price-list">
                            <li><i class="bi bi-check-circle-fill"></i><span>Vigencia de seis meses.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>Para desplazamientos continuos.</span></li>
                            <li><i class="bi bi-info-circle-fill"></i><span>No sustituye otras restricciones especiales.</span></li>
                        </ul>
                    </article>
                </div>

                <div class="notice" style="margin-top:26px">
                    <strong>Vehículos matriculados fuera de Bogotá:</strong> en 2026 el factor territorial informado por el Distrito pasó de 1,2 a 1,5. Por eso el total puede ser mayor que los valores base mostrados.
                </div>

                <div class="text-center" style="margin-top:28px">
                    <a class="btn btn-blue" href="<?= e($officialSolidario) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Tramitar en el sitio oficial</a>
                </div>

                <div class="steps">
                    <article class="card step"><span class="step-no">1</span><h3>Simula la tarifa</h3><p class="muted">Selecciona duración, datos del vehículo y municipio de matrícula.</p></article>
                    <article class="card step"><span class="step-no">2</span><h3>Registra la solicitud</h3><p class="muted">Completa los datos del propietario, vehículo y fecha de inicio.</p></article>
                    <article class="card step"><span class="step-no">3</span><h3>Realiza el pago</h3><p class="muted">Utiliza únicamente PSE y los canales habilitados por la Secretaría.</p></article>
                    <article class="card step"><span class="step-no">4</span><h3>Verifica la aprobación</h3><p class="muted">No circules hasta confirmar que el permiso está activo en el sistema.</p></article>
                </div>
            </div>
        </section>

        <section class="section-sm">
            <div class="container">
                <div class="fine-banner">
                    <span class="fine-icon"><i class="bi bi-exclamation-octagon-fill"></i></span>
                    <div>
                        <h2>Multa por incumplir en 2026</h2>
                        <p style="margin:0;color:#ffe9ec">La infracción puede incluir inmovilización del vehículo, patios, grúa y otros costos adicionales.</p>
                    </div>
                    <div class="fine-amount">$633.200</div>
                </div>
            </div>
        </section>

        <section id="regional" class="section">
            <div class="container regional-grid">
                <article class="card regional-box">
                    <span class="eyebrow"><i class="bi bi-signpost-split-fill"></i> Retorno a Bogotá</span>
                    <h2>Pico y Placa Regional</h2>
                    <p class="muted">Opera normalmente el último día de los puentes festivos o cuando el Distrito lo anuncie, exclusivamente en corredores de ingreso a la ciudad.</p>
                    <div class="time-band">
                        <div class="time-card">
                            <strong>12:00 m. a 4:00 p. m.</strong>
                            <span class="muted">Ingresan placas pares: 0, 2, 4, 6 y 8.</span>
                        </div>
                        <div class="time-card">
                            <strong>4:00 p. m. a 8:00 p. m.</strong>
                            <span class="muted">Ingresan placas impares: 1, 3, 5, 7 y 9.</span>
                        </div>
                    </div>
                    <div class="notice" style="margin-top:20px">
                        Antes de las 12:00 m. y después de las 8:00 p. m. suele permitirse el ingreso sin esta restricción. Confirma siempre el anuncio de cada puente.
                    </div>
                </article>

                <div>
                    <span class="eyebrow"><i class="bi bi-map-fill"></i> Corredores de ingreso</span>
                    <h2>Nueve accesos donde puede aplicar</h2>
                    <div class="corridors">
                        <?php foreach ([
                            'Autopista Norte', 'Autopista Sur', 'Avenida Calle 13', 'Avenida Calle 80',
                            'Carrera Séptima', 'Vía al Llano', 'Suba–Cota', 'Vía La Calera', 'Vía a Choachí'
                        ] as $corridor): ?>
                            <div class="corridor"><i class="bi bi-geo-alt-fill"></i><span><?= e($corridor) ?></span></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" style="background:#eef5fd">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-patch-check-fill"></i> Excepciones</span>
                <h2>Vehículos que pueden estar exceptuados</h2>
                <p class="section-intro">Estas son categorías frecuentes. Algunas requieren inscripción previa, validación o condiciones específicas. Consulta siempre el registro oficial de exceptuados.</p>

                <div class="exemption-grid">
                    <article class="card exemption"><span class="exemption-icon"><i class="bi bi-ev-front-fill"></i></span><h3>Eléctricos y cero emisiones</h3><p class="muted">Vehículos eléctricos y de cero emisiones; algunas categorías deben estar registradas.</p></article>
                    <article class="card exemption"><span class="exemption-icon"><i class="bi bi-lightning-charge-fill"></i></span><h3>Vehículos híbridos</h3><p class="muted">Los híbridos contemplados por la normativa vigente pueden solicitar o validar la excepción.</p></article>
                    <article class="card exemption"><span class="exemption-icon"><i class="bi bi-universal-access-circle"></i></span><h3>Personas con discapacidad</h3><p class="muted">Vehículos destinados al transporte de personas con discapacidad, con registro y requisitos aplicables.</p></article>
                    <article class="card exemption"><span class="exemption-icon"><i class="bi bi-hospital-fill"></i></span><h3>Emergencias y salud</h3><p class="muted">Ambulancias, bomberos y vehículos de atención de emergencias debidamente identificados.</p></article>
                    <article class="card exemption"><span class="exemption-icon"><i class="bi bi-bus-front-fill"></i></span><h3>Transporte escolar</h3><p class="muted">Vehículos escolares autorizados y que cumplen las condiciones de operación.</p></article>
                    <article class="card exemption"><span class="exemption-icon"><i class="bi bi-shield-fill-check"></i></span><h3>Autoridades y seguridad</h3><p class="muted">Fuerza Pública, organismos de seguridad y vehículos de control del tránsito.</p></article>
                </div>

                <div class="text-center" style="margin-top:28px">
                    <a class="btn btn-outline" href="https://www.movilidadbogota.gov.co/preguntas-frecuentes/cuales-son-los-tipos-de-vehiculos-exceptuados-de-la-restriccion-de-pico-y" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Consultar excepciones oficiales</a>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container content-grid">
                <article class="card article-card">
                    <span class="eyebrow"><i class="bi bi-journal-text"></i> Guía completa</span>
                    <h2>Todo lo que debes saber antes de salir</h2>

                    <h3>1. La regla se basa en el día del mes</h3>
                    <p>La rotación no depende del día de la semana sino de si el número de la fecha es par o impar. Por ejemplo, un martes 4 funciona como día par y un miércoles 5 como día impar. Siempre debes verificar el último dígito numérico de la placa.</p>

                    <h3>2. La restricción dura quince horas</h3>
                    <p>En días hábiles ordinarios, el horario va desde las 6:00 a. m. hasta las 9:00 p. m. Antes de las 6:00 a. m. y después de las 9:00 p. m. puedes circular bajo la regla general, salvo que exista otra restricción especial.</p>

                    <h3>3. Los festivos requieren atención especial</h3>
                    <p>El Pico y Placa ordinario no aplica en festivos. Sin embargo, el último día de un puente puede operar el Pico y Placa Regional en los accesos a Bogotá. Esa medida utiliza pares e impares tradicionales y horarios diferentes.</p>

                    <h3>4. El permiso solidario debe estar activo</h3>
                    <p>Pagar no es suficiente si el permiso aún aparece pendiente. Verifica la aprobación y fecha de inicio antes de circular. La Secretaría ha advertido que el trámite se realiza únicamente por su canal oficial y no existen intermediarios autorizados.</p>

                    <h3>5. No confundas particulares, taxis y carga</h3>
                    <p>Esta página se centra en vehículos particulares. Los taxis, vehículos de transporte especial y vehículos de carga tienen calendarios, placas y condiciones distintas. Si conduces uno de esos vehículos, consulta el calendario específico de la Secretaría Distrital de Movilidad.</p>

                    <h3>6. Recomendaciones prácticas</h3>
                    <p>Revisa la placa antes de encender el vehículo, planea rutas alternativas, conserva evidencia del permiso solidario y confirma novedades cuando haya eventos masivos, emergencias ambientales, jornadas de Día sin Carro o puentes festivos.</p>

                    <div class="notice">
                        <strong>Sobre los sábados para vehículos matriculados fuera de Bogotá:</strong> hubo anuncios de una medida futura durante 2026, pero su aplicación depende de reglamentación y comunicación oficial. Esta página no la marca como activa automáticamente sin un calendario oficial vigente.
                    </div>
                </article>

                <aside class="sidebar" aria-label="Información complementaria">
                    <div class="card sidebar-card">
                        <h3><i class="bi bi-list-check"></i> Resumen rápido</h3>
                        <ul>
                            <li>Lunes a viernes.</li>
                            <li>6:00 a. m. a 9:00 p. m.</li>
                            <li>Impares: circulan 1–5.</li>
                            <li>Pares: circulan 6–0.</li>
                            <li>Multa 2026: $633.200.</li>
                        </ul>
                    </div>

                    <div class="card sidebar-card">
                        <h3><i class="bi bi-link-45deg"></i> Enlaces útiles</h3>
                        <div class="footer-links">
                            <a href="<?= e($officialMain) ?>" target="_blank" rel="noopener noreferrer">Calendario oficial</a>
                            <a href="<?= e($officialSolidario) ?>" target="_blank" rel="noopener noreferrer">Pico y Placa Solidario</a>
                            <a href="https://portalmimovilidad.movilidadbogota.gov.co/" target="_blank" rel="noopener noreferrer">Portal Mi Movilidad</a>
                        </div>
                    </div>

                    <!-- ESPACIO PUBLICITARIO LATERAL -->
                    <div class="ad-slot" style="margin:0;min-height:280px">Anuncio lateral<br>300 × 250 o responsive</div>
                </aside>
            </div>
        </section>

        <section id="preguntas" class="section" style="background:#eef5fd">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-question-circle-fill"></i> Preguntas frecuentes</span>
                <h2>Respuestas rápidas sobre Pico y Placa</h2>
                <div class="faq">
                    <?php foreach ($faqItems as $index => $item): ?>
                        <details<?= $index === 0 ? ' open' : '' ?>>
                            <summary><?= e($item['q']) ?></summary>
                            <p><?= e($item['a']) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="fuentes" class="section">
            <div class="container">
                <span class="eyebrow"><i class="bi bi-building-check"></i> Transparencia</span>
                <h2>Fuentes oficiales consultadas</h2>
                <p class="section-intro">La información principal de esta página se apoya en publicaciones de la Secretaría Distrital de Movilidad y la Alcaldía de Bogotá. Revisa estas fuentes ante cualquier cambio reciente.</p>

                <div class="source-list">
                    <a class="source-link" href="<?= e($officialMain) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right"></i><span><strong>Secretaría Distrital de Movilidad — Pico y Placa</strong><span>Calendarios, horarios, tipos de vehículo y novedades.</span></span>
                    </a>
                    <a class="source-link" href="https://www.movilidadbogota.gov.co/asi-quedarian-las-tarifas-de-los-servicios-de-movilidad-para-el-2026" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right"></i><span><strong>Tarifas de movilidad para 2026</strong><span>Valores base del permiso Pico y Placa Solidario.</span></span>
                    </a>
                    <a class="source-link" href="<?= e($officialSolidario) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right"></i><span><strong>Portal oficial de Pico y Placa Solidario</strong><span>Simulación, registro, pago y consulta de solicitudes.</span></span>
                    </a>
                    <a class="source-link" href="https://www.alcaldiabogota.gov.co/sisjur/normas/Norma1.jsp?i=191872" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right"></i><span><strong>Decreto Único del Sector Movilidad 652 de 2025</strong><span>Marco normativo distrital de restricciones y excepciones.</span></span>
                    </a>
                </div>

                <div class="notice" style="margin-top:24px">
                    <strong>Aviso legal:</strong> este sitio es informativo, no pertenece a la Secretaría Distrital de Movilidad y no tramita permisos. Las decisiones de circulación deben confirmarse en canales oficiales.
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a class="brand" href="#inicio"><span class="brand-mark"><i class="bi bi-car-front-fill"></i></span><span><?= e($siteName) ?></span></a>
                    <p style="margin-top:18px;max-width:430px">Información clara para que conductores y visitantes planeen sus desplazamientos en Bogotá. Verifica siempre las novedades oficiales antes de viajar.</p>
                </div>
                <div>
                    <h3>Consultas</h3>
                    <div class="footer-links"><a href="#consultar">Consultar placa</a><a href="#calendario">Calendario</a><a href="#tarifas">Tarifas</a><a href="#regional">Pico y Placa Regional</a></div>
                </div>
                <div>
                    <h3>Información</h3>
                    <div class="footer-links"><a href="#preguntas">Preguntas frecuentes</a><a href="#fuentes">Fuentes</a><a href="/contacto">Contacto</a><a href="/sobre-nosotros">Sobre nosotros</a></div>
                </div>
                <div>
                    <h3>Legal</h3>
                    <div class="footer-links"><a href="/privacidad">Política de privacidad</a><a href="/terminos">Términos de uso</a><a href="/cookies">Política de cookies</a><a href="/correcciones">Política de correcciones</a></div>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© <?= e($now->format('Y')) ?> <?= e($siteName) ?>. Sitio informativo independiente.</span>
                <span>Datos verificados al 2 de agosto de 2026.</span>
            </div>
        </div>
    </footer>

    <script>
        (() => {
            const plate = document.getElementById('placa');
            if (plate) {
                plate.addEventListener('input', () => {
                    plate.value = plate.value.toUpperCase().replace(/[^A-Z0-9-]/g, '').slice(0, 10);
                });
            }

            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', event => {
                    const target = document.querySelector(link.getAttribute('href'));
                    if (target) {
                        event.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        })();
    </script>

    <!--
    EJEMPLO DE INSERCIÓN DE GOOGLE ADSENSE
    Reemplaza ca-pub-XXXXXXXXXXXXXXXX y el data-ad-slot por tus datos reales.
    No publiques este bloque sin consentimiento de cookies cuando sea obligatorio.

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX" crossorigin="anonymous"></script>
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-XXXXXXXXXXXXXXXX"
         data-ad-slot="1234567890"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    -->
</body>
</html>
