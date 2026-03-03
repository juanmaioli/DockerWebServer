<?php
// Configuración de seguridad y codificación
session_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Recibir y decodificar el JSON
$input = json_decode(file_get_contents('php://input'), true);
$data = $input['value'] ?? [];

if (empty($data)) {
    die("<h4 class='text-danger'>Error: No se recibieron datos válidos.</h4>");
}

$cmd = $data['cmd'] ?? '';

// Lógica de obtención de credenciales:
if (in_array($cmd, ['list_dbs', 'conn'])) {
    $_SESSION['db_server']     = $data['db_server'] ?? 'localhost';
    $_SESSION['db_user']       = $data['db_user'] ?? 'root';
    $_SESSION['db_pass']       = $data['db_pass'] ?? '';
    $_SESSION['db_serverport'] = $data['db_serverport'] ?? 3306;
    
    if ($cmd === 'conn') {
        $_SESSION['db_name'] = $data['db_name'] ?? '';
    }
}

// Verificar si tenemos datos de conexión en la sesión
if (!isset($_SESSION['db_server'])) {
    die("<h4 class='text-warning'>Sesión expirada o no iniciada. Por favor, ingrese sus datos nuevamente.</h4>");
}

$db_server     = $_SESSION['db_server'];
$db_user       = $_SESSION['db_user'];
$db_pass       = $_SESSION['db_pass'];
$db_name       = $_SESSION['db_name'] ?? '';
$db_serverport = $_SESSION['db_serverport'];

mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

try {
    if ($cmd === 'list_dbs') {
        $conn = new mysqli($db_server, $db_user, $db_pass, "", $db_serverport);
    } else {
        if (empty($db_name)) {
             throw new Exception("Nombre de base de datos no especificado.");
        }
        $conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
    }
    $conn->set_charset('utf8');
} catch (Exception $e) {
    die("<div class='alert alert-danger'>❌ Error de conexión: " . $e->getMessage() . "</div>");
}

switch ($cmd) {
    case 'list_dbs':
        $result = $conn->query("SHOW DATABASES");
        $options = "";
        while ($row = $result->fetch_array()) {
            $db = $row[0];
            if (!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                $options .= "<option value='" . htmlspecialchars($db) . "'>" . htmlspecialchars($db) . "</option>";
            }
        }
        echo $options;
        break;

    case 'conn':
        echo "<div class='alert alert-success py-2 shadow-sm'>✅ Conectado con éxito a <strong>$db_server</strong></div>";
        $sql = "SELECT TABLE_NAME AS nombre FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $db_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $lista_tablas = "<label class='form-label fw-bold'>📊 Selecciona una Tabla para procesar:</label>";
            $lista_tablas .= "<select name='table_name' id='table_name' class='form-select border-primary shadow-sm' onchange='enviaForm(\"formularioData\")'>";
            $lista_tablas .= "<option value=''>--- Listado de Tablas ---</option>";
            while ($row = $result->fetch_assoc()) {
                $nombreTabla = $row["nombre"];
                $lista_tablas .= "<option value='" . htmlspecialchars($nombreTabla) . "'>📁 " . htmlspecialchars($nombreTabla) . "</option>";
            }
            $lista_tablas .= "</select>";
        } else {
            $lista_tablas = "<p class='text-warning'>⚠️ No se encontraron tablas en la base de datos '$db_name'.</p>";
        }

        echo "<div class='card shadow-sm mt-3 border-0'>
                <div class='card-body bg-body-tertiary rounded'>
                    <form method='post' name='formularioData' id='formularioData'>
                        <input type='hidden' name='cmd' id='cmd' value='tabla'>
                        $lista_tablas
                    </form>
                </div>
              </div>";
        break;

    case 'tabla':
        $tableName = $data['table_name'] ?? '';
        if (empty($tableName)) {
            die("<h4 class='text-warning'>⚠️ Seleccione una tabla válida.</h4>");
        }

        $sql = "SELECT COLUMN_NAME as campo, DATA_TYPE as datatype 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $db_name, $tableName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $lista_campos = "<table class='table table-hover table-striped table-sm small align-middle'>";
            $lista_resultados = "<table class='table table-sm table-borderless bg-body-tertiary small text-success rounded'>";
            $lista_php = "<table class='table table-sm table-borderless bg-body-tertiary small text-danger rounded'>";
            $lista_sql = "<table class='table table-sm table-borderless bg-body-tertiary small text-info rounded'>";

            while ($row = $result->fetch_assoc()) {
                $nombreCampo = $row["campo"];
                $datatype = $row["datatype"];
                $listaInput = ["text", "number", "hidden", "password", "checkbox", "radio", "date", "file", "range", "color", "button", "select", "textarea"];

                $lista_campos .= "<tr id='" . htmlspecialchars($nombreCampo) . "Tr' class='border-bottom'>
                    <td class='fw-bold text-primary'>" . htmlspecialchars($nombreCampo) . " <br><span class='text-secondary fw-normal small'>($datatype)</span></td>";
                foreach ($listaInput as $input) {
                    $lista_campos .= "<td><button class='btn btn-link btn-sm p-0 text-decoration-none' id='" . htmlspecialchars($nombreCampo . $input) . "' onclick='selectElement(\"" . htmlspecialchars($nombreCampo) . "\", \"$input\")'>✨ $input</button></td>";
                }
                $lista_campos .= "<td><button class='btn btn-sm btn-outline-danger border-0' onclick='clearElement(\"" . htmlspecialchars($nombreCampo) . "\")'>🗑️</button></td></tr>";

                $lista_resultados .= "<tr><td id='" . htmlspecialchars($nombreCampo) . "' class='font-monospace'></td></tr>";
                $lista_php .= "<tr><td id='" . htmlspecialchars($nombreCampo) . "_php' class='font-monospace'></td></tr>";
                $lista_sql .= "<tr><td id='" . htmlspecialchars($nombreCampo) . "_sql' class='font-monospace'></td></tr>";
            }
            $lista_campos .= "</table>";
            $lista_resultados .= "</table>";
            $lista_php .= "</table>";
            $lista_sql .= "</table>";

            echo "
                <div class='row mt-4'>
                    <div class='col-md-12'>
                        <h2 class='text-center mb-3'>🛠️ Generador para: <span class='badge bg-primary'>" . htmlspecialchars($tableName) . "</span></h2>
                        <div class='d-flex justify-content-center mb-4'>
                            <div class='form-check form-switch'>
                                <input class='form-check-input' type='checkbox' id='bootstrap' checked>
                                <label class='form-check-label fw-bold' for='bootstrap'>🎨 Estilos Bootstrap 5</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row mt-1'>
                    <div class='col-md-12 mb-4'>
                        <div class='card shadow-sm'><div class='card-body p-0'>$lista_campos</div></div>
                    </div>
                </div>
                <div class='row g-4'>
                    <div class='col-md-5'>
                        <div class='card shadow-sm h-100'>
                            <div class='card-header d-flex justify-content-between align-items-center bg-success text-white'>
                                <strong>📄 Código HTML5</strong>
                                <div>
                                    <button class='btn btn-sm btn-light py-0' onclick='copyColumn(\"html\")'>📋</button>
                                    <button class='btn btn-sm btn-light py-0' onclick='downloadColumn(\"html\")'>💾</button>
                                </div>
                            </div>
                            <div class='card-body overflow-auto' style='max-height: 400px;'>$lista_resultados</div>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='card shadow-sm h-100'>
                            <div class='card-header d-flex justify-content-between align-items-center bg-danger text-white'>
                                <strong>🐘 Código PHP</strong>
                                <div>
                                    <button class='btn btn-sm btn-light py-0' onclick='copyColumn(\"php\")'>📋</button>
                                    <button class='btn btn-sm btn-light py-0' onclick='downloadColumn(\"php\")'>💾</button>
                                </div>
                            </div>
                            <div class='card-body overflow-auto' style='max-height: 400px;'>$lista_php</div>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='card shadow-sm h-100'>
                            <div class='card-header d-flex justify-content-between align-items-center bg-info text-white'>
                                <strong>🗄️ SQL Params</strong>
                                <div>
                                    <button class='btn btn-sm btn-light py-0' onclick='copyColumn(\"sql\")'>📋</button>
                                    <button class='btn btn-sm btn-light py-0' onclick='downloadColumn(\"sql\")'>💾</button>
                                </div>
                            </div>
                            <div class='card-body overflow-auto' style='max-height: 400px;'>$lista_sql</div>
                        </div>
                    </div>
                </div>
                <div class='row mt-4 mb-5'>
                    <div class='col-md-12'>
                        <div class='card shadow-sm border-info'>
                            <div class='card-header d-flex justify-content-between align-items-center bg-info-subtle'>
                                <h5 class='mb-0'>📝 SQL Insert Statement Completo:</h5>
                                <button class='btn btn-sm btn-info' onclick='copyToClipboard(\"sql_full\")'>📋 Copiar Todo</button>
                            </div>
                            <div class='card-body p-0'>
                                <textarea id='sql_full' class='form-control border-0 bg-body-secondary text-info font-monospace' rows='3' readonly placeholder='El SQL se generará automáticamente...'></textarea>
                            </div>
                        </div>
                    </div>
                </div>";
        }
        break;

    default:
        echo 'Error: Comando desconocido.';
        break;
}

if (isset($conn)) {
    $conn->close();
}
?>