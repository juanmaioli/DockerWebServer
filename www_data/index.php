<?php
/**
 * 🐳 WebServer Docker - Dashboard Unificado & Gestor
 * Desarrollado por: Juan Gabriel Maioli
 */

$directorio_raiz = realpath(__DIR__) . DIRECTORY_SEPARATOR;
$mensaje = '';
$clase_alerta = '';

// --- Lógica del Gestor de Archivos (Subida) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivos'])) {
    $total_subidos = 0;
    $errores_detalles = [];

    foreach ($_FILES['archivos']['name'] as $i => $name) {
        if ($_FILES['archivos']['error'][$i] === UPLOAD_ERR_OK) {
            // Soporte para webkitdirectory (full_path) o nombre simple
            $rel_path = isset($_FILES['archivos']['full_path'][$i]) ? $_FILES['archivos']['full_path'][$i] : $name;
            // Limpieza básica de ruta
            $rel_path = preg_replace("/[^a-zA-Z0-9\._\-\/]/", "_", $rel_path);
            $ruta_final = $directorio_raiz . $rel_path;
            
            $directorio_padre = dirname($ruta_final);
            if (!is_dir($directorio_padre)) {
                @mkdir($directorio_padre, 0777, true);
            }

            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $ruta_final)) {
                $total_subidos++;
            } else {
                $errores_detalles[] = "Error al mover: {$name}";
            }
        }
    }

    if ($total_subidos > 0) {
        $mensaje = "✅ Se subieron {$total_subidos} archivos correctamente.";
        $clase_alerta = 'alert-success';
    }
    if (!empty($errores_detalles)) {
        $mensaje .= ($mensaje ? "<br>" : "") . "⚠️ Errores: " . count($errores_detalles);
        $clase_alerta = $total_subidos > 0 ? 'alert-warning' : 'alert-danger';
    }
}

// --- Lógica del Explorador ---
$files = scandir($directorio_raiz);
$folders = [];
$otherFiles = [];
$exclude = [".", "..", "index.php", "index2.php", ".eslintrc.json", ".git", "php_data", "db_data", "apache_data", ".cache", ".openvscode-server", "gestor"];

foreach ($files as $file) {
    if (in_array($file, $exclude)) continue;
    if (is_dir($directorio_raiz . $file)) {
        $folders[] = $file;
    } else {
        $otherFiles[] = $file;
    }
}
?>
<!DOCTYPE html>
<html lang="es-AR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐳 WebServer Docker - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%); padding: 60px 0; border-bottom: 1px solid #34495e; }
        .file-icon { width: 30px; text-align: center; margin-right: 12px; font-size: 1.3rem; }
        .list-group-item:hover { background-color: var(--bs-tertiary-bg); transition: 0.2s; }
        .card-gestor { border: 2px dashed var(--bs-border-color); transition: 0.3s; }
        .card-gestor:hover { border-color: var(--bs-primary); }
        .btn-upload { font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .php-info-container table { width: 100% !important; border-collapse: collapse; }
    </style>
</head>
<body class="bg-body-tertiary">

<!-- Header Hero -->
<div class="hero-section text-center text-white shadow">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3">🐳 WebServer Docker</h1>
        <p class="lead opacity-75">Entorno LAMP de Alto Rendimiento (PHP 8.4 + MariaDB + Apache)</p>
        <div class="mt-4">
            <span class="badge bg-primary px-3 py-2 me-2">PHP 8.4</span>
            <span class="badge bg-info px-3 py-2 me-2 text-dark">MariaDB 10.11</span>
            <span class="badge bg-success px-3 py-2">Apache 2.4</span>
        </div>
        <hr class="text-primary">
        <div class="text-center mb-2">
        <a href="?info=1" class="btn btn-outline-secondary btn-sm">ℹ️ Ver Información del Servidor (phpinfo)</a>
        </div>

    <!-- PHP Info Overlay -->
    <?php if (isset($_GET['info'])): ?>
        <div class="row mb-2">
            <div class="col-12">
                <div class="p-4 bg-white text-dark rounded shadow-sm border overflow-auto" style="max-height: 500px;">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h4 class="mb-0 fw-bold">System Information</h4>
                        <a href="./" class="btn-close"></a>
                    </div>
                    <div class="php-info-container">
                        <?php
                            ob_start(); phpinfo(); $pinfo = ob_get_contents(); ob_end_clean();
                            echo preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>


<div class="container mt-3 pb-2">
    <?php if ($mensaje): ?>
        <div class="alert <?php echo $clase_alerta; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Explorador de Archivos (Ancho completo) -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">📜 Explorador de Archivos</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="themeSelector" checked>
                        <label class="form-check-label small" for="themeSelector">🌙 Modo</label>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 800px; overflow-y: auto;">
                        <?php
                        // Carpetas
                        foreach ($folders as $folder) {
                            echo '<a href="' . $folder . '" class="list-group-item list-group-item-action py-1 d-flex align-items-center">
                                    <span class="file-icon">📁</span>
                                    <span class="">' . $folder . '</span>
                                    <span class="ms-auto badge bg-secondary rounded-pill">DIR</span>
                                </a>';
                        }
                        // Archivos
                        foreach ($otherFiles as $file) {
                            echo '<a href="' . $file . '" target="_blank" class="list-group-item list-group-item-action py-1 d-flex align-items-center">
                                    <span class="file-icon">📄</span>
                                    <span>' . $file . '</span>
                                    <span class="ms-auto text-muted small">Ver 👀</span>
                                </a>';
                        }
                        if (empty($folders) && empty($otherFiles)) {
                            echo '<div class="p-5 text-center text-muted italic">Directorio vacío.</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="card-footer bg-body-tertiary text-center py-2">
                    <small class="text-muted">Directorio actual: <strong><?php echo basename(getcwd()); ?></strong></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestor de Subidas (Ancho completo) -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header text-start">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-cloud-upload"></i> 📤 Cargar Contenido al Servidor</h5>
                </div>
                <div class="card-body p-4">
                    <form action="./" method="POST" enctype="multipart/form-data">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">📂 Seleccionar Archivos</label>
                                <div class="card card-gestor p-3 text-center bg-body-tertiary">
                                    <input type="file" class="form-control" name="archivos[]" multiple>
                                    <small class="text-muted mt-2">Podés elegir varios archivos simultáneamente.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">📁 Seleccionar Carpeta</label>
                                <div class="card card-gestor p-3 text-center bg-body-tertiary">
                                    <input type="file" class="form-control" name="archivos[]" webkitdirectory directory multiple>
                                    <small class="text-muted mt-2">Se preservará la estructura de carpetas original.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary px-2 py-1 btn-upload shadow">
                                🚀 Iniciar Subida Masiva
                            </button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <a href="?info=1" class="btn btn-outline-secondary btn-sm">ℹ️ Ver Información del Servidor (phpinfo)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<footer class="text-center py-4 mt-auto">
    <p class="text-muted mb-0">Desarrollado con ❤️ por <strong>Juan Gabriel Maioli</strong> &bull; 2026</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const themeSelector = document.getElementById('themeSelector');
    const htmlElement = document.documentElement;

    const savedTheme = localStorage.getItem('theme') || 'dark';
    htmlElement.setAttribute('data-bs-theme', savedTheme);
    themeSelector.checked = savedTheme === 'dark';

    themeSelector.addEventListener('change', () => {
        const theme = themeSelector.checked ? 'dark' : 'light';
        htmlElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
    });
</script>
</body>
</html>
