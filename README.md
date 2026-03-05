# 🐳 WebServer Docker - Dashboard Unificado

Este proyecto proporciona un entorno de desarrollo web completo basado en Docker (**LAMP**), ahora con un **Dashboard Unificado** que integra exploración de archivos y gestión de subidas en una sola interfaz moderna.

## 🚀 Novedades (v1.2.0)
- **Infraestructura SSL:** Soporte nativo para HTTPS en el puerto **5443**.
- **Dominio Comodín:** Configurado para responder a `*.webserver.docker`.
- **Dashboard Unificado:** Se han fusionado el explorador y el gestor de archivos en `index.php`.
- **Interfaz Moderna:** Potenciado con **Bootstrap 5.3** y soporte nativo para **Modo Oscuro**.
- **Gestión de Archivos Pro:** Soporte para subida masiva de archivos y **carpetas completas** (preservando la estructura).
- **PHP Info Integrado:** Visualización rápida de la configuración del servidor sin salir del dashboard.

## 🛠️ Tecnologías Principales
- **PHP 8.4** con extensiones optimizadas y soporte **SSL**.
- **MariaDB 10.11 LTS**.
- **Apache 2.4** configurado para alto rendimiento con módulos `ssl`, `rewrite` y `headers`.
- **Docker Integration:** Acceso al socket del host para gestión de contenedores.

## ⚙️ Uso Rápido
1.  **Levantar el entorno:** `docker compose up -d`
2.  **Acceso HTTP:** [http://localhost:5080](http://localhost:5080)
3.  **Acceso HTTPS:** [https://localhost:5443](https://localhost:5443) (Dominio: `*.webserver.docker`)
4.  **Subida de Archivos:** Arrastrá o seleccioná archivos/carpetas directamente en el Dashboard.

## 📂 Estructura del Proyecto
- `www_data/`: Directorio raíz (Dashboard y Aplicaciones).
- `apache_data/`: Configuración del servidor web.
- `db_data/`: Persistencia de la base de datos.
- `php_data/`: Ajustes de `php.ini`.

---
Desarrollado por **Juan Gabriel Maioli** &bull; 2026
