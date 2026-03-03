<?php
/**
 * 📁 Gestor de Archivos - WebServer Docker
 * Desarrollado con accesibilidad y seguridad en mente.
 */

// Configuración de directorios
$directorio_subida = __DIR__ . '/uploads/';
$mensaje = '';
$clase_alerta = '';

// Procesar la subida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $nombre_limpio = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($archivo['name']));
    $ruta_final = $directorio_subida . $nombre_limpio;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
        $mensaje = "✅ Archivo '{$nombre_limpio}' subido correctamente.";
        $clase_alerta = 'exito';
    } else {
        $mensaje = "❌ Error al subir el archivo.";
        $clase_alerta = 'error';
    }
}

// Listar archivos existentes
$archivos = array_diff(scandir($directorio_subida), array('.', '..'));
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Archivos - WebServer</title>
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #ffffff;
            --primary: #3498db;
            --success: #2ecc71;
            --danger: #e74c3c;
            --card-bg: #1e1e1e;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        main {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 { border-bottom: 2px solid var(--primary); padding-bottom: 10px; }
        .card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        .alerta {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .exito { background-color: rgba(46, 204, 113, 0.2); border: 1px solid var(--success); color: var(--success); }
        .error { background-color: rgba(231, 76, 60, 0.2); border: 1px solid var(--danger); color: var(--danger); }
        
        /* Estilos de formulario accesibles */
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 1.1rem; }
        input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            background: #2a2a2a;
            border: 1px solid #444;
            color: white;
            border-radius: 4px;
        }
        button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover, button:focus { background-color: #2980b9; outline: 3px solid white; }

        /* Tabla de archivos */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #444; }
        th { background-color: #2a2a2a; color: var(--primary); }
        tr:hover { background-color: #252525; }
        .empty-msg { text-align: center; color: #888; padding: 20px; }
    </style>
</head>
<body>
    <main role="main">
        <h1>📁 Panel de Gestión de Archivos</h1>

        <?php if ($mensaje): ?>
            <div role="alert" class="alerta <?php echo $clase_alerta; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <h2>📤 Subir nuevo archivo</h2>
            <form action="gestor.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="archivo">Seleccionar archivo para el servidor:</label>
                    <input type="file" id="archivo" name="archivo" required aria-required="true">
                </div>
                <button type="submit">🚀 Iniciar Subida</button>
            </form>
        </section>

        <section class="card">
            <h2>📜 Archivos en el servidor (/uploads)</h2>
            <table aria-label="Lista de archivos subidos">
                <thead>
                    <tr>
                        <th scope="col">Nombre del Archivo</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($archivos)): ?>
                        <tr>
                            <td colspan="2" class="empty-msg">No hay archivos subidos todavía.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($archivos as $archivo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($archivo); ?></td>
                                <td>
                                    <a href="uploads/<?php echo urlencode($archivo); ?>" 
                                       target="_blank" 
                                       style="color: var(--primary); font-weight: bold;"
                                       aria-label="Ver archivo <?php echo htmlspecialchars($archivo); ?>">
                                        👀 Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
