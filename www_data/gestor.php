<?php
/**
 * 📁 Gestor de Archivos Pro - WebServer Docker
 * Soporta subida de múltiples archivos y carpetas completas.
 */

$directorio_subida = __DIR__ . '/uploads/';
$mensaje = '';
$clase_alerta = '';

// Función para listar archivos de forma recursiva (para mostrar subcarpetas)
function listarArchivos($dir, &$resultados = array()) {
    $archivos = scandir($dir);
    foreach ($archivos as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $resultados[] = str_replace(realpath(__DIR__ . '/uploads/') . DIRECTORY_SEPARATOR, '', $path);
        } else if ($value != "." && $value != "..") {
            listarArchivos($path, $resultados);
        }
    }
    return $resultados;
}

// Procesar la subida (Múltiples archivos o Carpetas)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivos'])) {
    $total_subidos = 0;
    $errores = 0;

    foreach ($_FILES['archivos']['name'] as $i => $name) {
        if ($_FILES['archivos']['error'][$i] === UPLOAD_ERR_OK) {
            // Usar 'full_path' si está disponible (para mantener estructura de carpetas)
            $rel_path = isset($_FILES['archivos']['full_path'][$i]) ? $_FILES['archivos']['full_path'][$i] : $name;
            
            // Limpiar la ruta para seguridad básica pero permitir '/' para subcarpetas
            $rel_path = preg_replace("/[^a-zA-Z0-9\._\-\/]/", "_", $rel_path);
            $ruta_final = $directorio_subida . $rel_path;
            
            // Crear subcarpetas si no existen
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

$lista_archivos = is_dir($directorio_subida) ? listarArchivos($directorio_subida) : [];
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
        h2 { margin-top: 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        .upload-box { border: 2px dashed #444; padding: 15px; border-radius: 8px; text-align: center; transition: 0.3s; }
        .upload-box:hover { border-color: var(--pri); background: #252525; }
        label { display: block; margin-bottom: 10px; font-weight: bold; cursor: pointer; }
        input[type="file"] { width: 100%; box-sizing: border-box; }
        button { background: var(--pri); color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; font-size: 1.1rem; }
        button:hover { background: #2980b9; }
        .alerta { padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid; }
        .exito { background: rgba(46, 204, 113, 0.1); border-color: var(--success); color: var(--success); }
        .error { background: rgba(231, 76, 60, 0.1); border-color: var(--danger); color: var(--danger); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #333; }
        th { color: var(--pri); text-transform: uppercase; font-size: 0.8rem; }
        tr:hover { background: #252525; }
        a { color: var(--pri); text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main>
        <h1>📁 Gestor de Archivos Pro</h1>

        <?php if ($mensaje): ?>
            <div class="alerta <?php echo $clase_alerta; ?>" role="alert"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="gestor.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <section class="card upload-box">
                    <label for="f_archivos">📂 Subir Archivos</label>
                    <input type="file" id="f_archivos" name="archivos[]" multiple aria-label="Seleccionar varios archivos">
                    <p style="font-size: 0.8rem; color: #888;">Podés seleccionar muchos archivos a la vez.</p>
                </section>

                <section class="card upload-box">
                    <label for="f_carpeta">📁 Subir Carpeta Completa</label>
                    <input type="file" id="f_carpeta" name="archivos[]" webkitdirectory directory multiple aria-label="Seleccionar una carpeta completa">
                    <p style="font-size: 0.8rem; color: #888;">Mantiene la estructura de subcarpetas.</p>
                </section>
            </div>
            <button type="submit">🚀 Iniciar Subida Masiva</button>
        </form>

        <section class="card">
            <h2>📜 Explorador de /uploads</h2>
            <table>
                <thead>
                    <tr>
                        <th>Ruta / Nombre</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lista_archivos)): ?>
                        <tr><td colspan="2" style="text-align:center; padding: 30px; color:#666;">No hay archivos en el servidor.</td></tr>
                    <?php else: ?>
                        <?php foreach ($lista_archivos as $archivo): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($archivo); ?></code></td>
                                <td><a href="uploads/<?php echo $archivo; ?>" target="_blank">Abrir 👀</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        
        <p style="text-align: center;"><a href="index.php" style="color: #666;">← Volver al inicio</a></p>
    </main>
</body>
</html>
