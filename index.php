<?php
// Configurar zona horaria de Colombia
date_default_timezone_set('America/Bogota');

$fecha_actual = date('d/m/Y');
$dia_mes = (int) date('j');
$dia_semana = (int) date('N'); // 1 (Lunes) a 7 (Domingo)

$restriccion = "Sin restricción (Fin de semana/Festivo)";

// Lógica de Pico y Placa (Lunes a Viernes)
if ($dia_semana >= 1 && $dia_semana <= 5) {
    if ($dia_mes % 2 == 0) {
        $restriccion = "1, 2, 3, 4, 5"; // Días pares
    } else {
        $restriccion = "6, 7, 8, 9, 0"; // Días impares
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pico y Placa Bogotá</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f4f4f9; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 400px; margin: auto; }
        h1 { color: #333; margin-bottom: 5px; }
        .info { font-size: 1.2em; margin: 20px 0; color: #555; }
        .restriccion { font-weight: bold; color: #d9534f; font-size: 1.8em; margin: 15px 0; }
        .horario { color: #777; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container">
    <h1>Pico y Placa - Bogotá</h1>
    <p class="info">Hoy es: <strong><?php echo $fecha_actual; ?></strong></p>
    
    <p>Placas con restricción hoy:</p>
    <div class="restriccion">
        <?php echo $restriccion; ?>
    </div>
    
    <p class="horario">Horario: 6:00 AM a 9:00 PM</p>
</div>

</body>
</html>
