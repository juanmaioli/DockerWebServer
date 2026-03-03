<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <title>WebServer Docker - Juan Gabriel Maioli</title>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .container { text-align: center; background: #1e1e1e; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h1 { color: #3498db; }
        .btn { display: inline-block; margin-top: 20px; padding: 15px 30px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; }
        .btn:hover { background: #2980b9; transform: translateY(-2px); }
        .info { margin-top: 30px; font-size: 0.9rem; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐳 WebServer Docker</h1>
        <p>El entorno LAMP está funcionando correctamente.</p>
        
        <a href="gestor.php" class="btn">📂 Abrir Gestor de Archivos</a>
        <br>
        <a href="?info=1" style="color: #555; margin-top: 20px; display: inline-block;">Ver PHP Info</a>

        <?php if (isset($_GET['info'])) phpinfo(); ?>
    </div>
</body>
</html>
