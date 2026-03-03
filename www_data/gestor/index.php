<?php
/**
 * 📁 Gestor de Archivos Pro - WebServer Docker
 * Ubicación: www_data/gestor/index.php
 */

$directorio_subida = realpath(__DIR__ . '/../uploads') . DIRECTORY_SEPARATOR;
$mensaje = '';
$clase_alerta = '';

// Asegurar que uploads existe
if (!is_dir($directorio_subida)) {
    mkdir($directorio_subida, 0775, true);
}

// Función para listar archivos de forma recursiva
function listarArchivos($dir, $base_dir, &$resultados = array()) {
    if (!is_dir($dir)) return [];
    $archivos = scandir($dir);
    foreach ($archivos as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $resultados[] = str_replace($base_dir . DIRECTORY_SEPARATOR, '', $path);
        } else if ($value != "." && $value != "..") {
            listarArchivos($path, $base_dir, $resultados);
        }
    }
    return $resultados;
}

// Procesar la subida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivos'])) {
    $total_subidos = 0;
    $errores = 0;

    foreach ($_FILES['archivos']['name'] as $i => $name) {
        if ($_FILES['archivos']['error'][$i] === UPLOAD_ERR_OK) {
            $rel_path = isset($_FILES['archivos']['full_path'][$i]) ? $_FILES['archivos']['full_path'][$i] : $name;
            $rel_path = preg_replace("/[^a-zA-Z0-9\._\-\/]/", "_", $rel_path);
            $ruta_final = $directorio_subida . $rel_path;
            
            $directorio_padre = dirname($ruta_final);
            if (!is_dir($directorio_padre)) {
                mkdir($directorio_padre, 0775, true);
            }

            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $ruta_final)) {
                $total_subidos++;
            } else {
                $errores++;
            }
        }
    }

    if ($total_subidos > 0) {
        $mensaje = "✅ Se subieron {$total_subidos} archivos correctamente.";
        $clase_alerta = 'exito';
        if ($errores > 0) $mensaje .= " (Hubo {$errores} errores).";
    } else {
        $mensaje = "❌ Error: No se pudo subir ningún archivo.";
        $clase_alerta = 'error';
    }
}

$lista_archivos = listarArchivos($directorio_subida, realpath($directorio_subida));
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Pro - WebServer</title>
    <style>
        :root { --bg: #121212; --txt: #eee; --pri: #3498db; --sec: #1e1e1e; --success: #2ecc71; --danger: #e74c3c; }
        body { font-family: sans-serif; background: var(--bg); color: var(--txt); padding: 20px; line-height: 1.6; }
        main { max-width: 900px; margin: 0 auto; }
        .card { background: var(--sec); padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        h1 { color: var(--pri); border-bottom: 2px solid #333; padding-bottom: 10px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        .upload-box { border: 2px dashed #444; padding: 15px; border-radius: 8px; text-align: center; }
        label { display: block; margin-bottom: 10px; font-weight: bold; cursor: pointer; }
        button { background: var(--pri); color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; font-size: 1.1rem; }
        .alerta { padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid; }
        .exito { background: rgba(46, 204, 113, 0.1); border-color: var(--success); color: var(--success); }
        .error { background: rgba(231, 76, 60, 0.1); border-color: var(--danger); color: var(--danger); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #333; }
        th { color: var(--pri); text-transform: uppercase; font-size: 0.8rem; }
        a { color: var(--pri); text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h1>📁 Gestor de Archivos Pro</h1>

        <?php if ($mensaje): ?>
            <div class="alerta <?php echo $clase_alerta; ?>" role="alert"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="./" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <section class="card upload-box">
                    <label for="f_archivos">📂 Subir Archivos</label>
                    <input type="file" id="f_archivos" name="archivos[]" multiple>
                </section>
                <section class="card upload-box">
                    <label for="f_carpeta">📁 Subir Carpeta Completa</label>
                    <input type="file" id="f_carpeta" name="archivos[]" webkitdirectory directory multiple>
                </section>
            </div>
            <button type="submit">🚀 Iniciar Subida Masiva</button>
        </form>

        <section class="card">
            <h2>📜 Explorador de /uploads</h2>
            <table>
                <thead>
                    <tr><th>Ruta / Nombre</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($lista_archivos)): ?>
                        <tr><td colspan="2" style="text-align:center; padding: 30px; color:#666;">No hay archivos en el servidor.</td></tr>
                    <?php else: ?>
                        <?php foreach ($lista_archivos as $archivo): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($archivo); ?></code></td>
                                <td><a href="../uploads/<?php echo $archivo; ?>" target="_blank">Abrir 👀</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        
        <p style="text-align: center;"><a href="../index.php" style="color: #666;">← Volver al inicio</a></p>
    </main>
</body>
</html>
