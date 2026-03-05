# 🐳 WebServer Docker - Dashboard Unificado

Este proyecto proporciona un entorno de desarrollo web completo basado en Docker (**LAMP**), ahora con un **Dashboard Unificado** que integra exploración de archivos y gestión de subidas en una sola interfaz moderna.

## 🚀 Novedades (v1.3.0)
- **Proxy Inverso SSL:** Centralización de certificados SSL para todos los servicios.
- **phpMyAdmin Seguro:** Accesible en `https://pma.webserver.docker:5443`.
- **OpenVSCode Seguro:** Accesible en `https://code.webserver.docker:5443` con soporte para WebSockets.
- **Infraestructura SSL:** Soporte nativo para HTTPS en el puerto **5443**.
- **Dominio Comodín:** Configurado para responder a `*.webserver.docker`.
- **Dashboard Unificado:** Integración de explorador y gestor de archivos.

## 🛠️ Tecnologías Principales
- **PHP 8.4** con extensiones optimizadas y soporte **SSL**.
- **MariaDB 10.11 LTS**.
- **Apache 2.4** como Proxy Inverso con módulos `ssl`, `proxy`, `proxy_http` y `proxy_wstunnel`.
- **Docker Integration:** Gestión de contenedores desde la aplicación.

## ⚙️ Uso Rápido
1.  **Levantar el entorno:** `docker compose up -d --build`
2.  **Dashboard:** [https://webserver.docker:5443](https://webserver.docker:5443)
3.  **phpMyAdmin:** [https://pma.webserver.docker:5443](https://pma.webserver.docker:5443)
4.  **OpenVSCode:** [https://code.webserver.docker:5443](https://code.webserver.docker:5443)
5.  **Acceso HTTP (Legacy):** [http://localhost:5080](http://localhost:5080)
6.  **Subida de Archivos:** Arrastrá o seleccioná contenido directamente en el Dashboard.

## 📂 Estructura del Proyecto
- `www_data/`: Directorio raíz (Dashboard y Aplicaciones).
- `apache_data/`: Configuración del servidor web.
- `db_data/`: Persistencia de la base de datos.
- `php_data/`: Ajustes de `php.ini`.

---
Desarrollado por **Juan Gabriel Maioli** &bull; 2026
