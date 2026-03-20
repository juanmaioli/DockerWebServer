# 🌐 Docker WebServer
[Documentación Oficial](README.md)

Este proyecto proporciona un entorno de desarrollo web completo basado en Docker (**LAMP**), con un **Dashboard Unificado** que integra exploración de archivos, gestión de subidas y visualización del sistema en una sola interfaz moderna y eficiente.

## 1. 🚀 Descripción General
El sistema está diseñado para ofrecer un entorno de desarrollo robusto, versionado y persistente para aplicaciones PHP modernas (v8.4), facilitando la gestión de archivos y la base de datos de forma local.

### ✨ Características Principales:
- **Infraestructura SSL:** Soporte nativo para HTTPS en el puerto **5443**.
- **Dashboard Unificado:** Explorador, gestor de archivos y `phpinfo` integrados en `index.php`.
- **Interfaz Moderna:** Potenciado con **Bootstrap 5.3** y soporte nativo para **Modo Oscuro**.
- **Gestión de Archivos Pro:** Soporte para subida masiva de archivos y **carpetas completas** (preservando la estructura).
- **IDE Integrado:** Acceso a **OpenVSCode Server** para editar código directamente en el navegador.

## 2. 🛠️ Tecnologías Principales
| Componente | Versión / Detalle | Descripción |
| :--- | :--- | :--- |
| 🐳 **Docker** | Compose V2 | Orquestación completa de contenedores. |
| 🐘 **PHP** | 8.4 (Apache) | Con extensiones `mysqli`, `gd` y `zip`. |
| 🗄️ **MariaDB** | 10.11 LTS | Base de datos relacional persistente. |
| 🚀 **Apache** | 2.4 | Configurado con `ssl`, `rewrite` y `headers`. |
| 🛡️ **Docker CLI** | v27.3.1 | Acceso al socket del host desde el contenedor. |

## 3. ⚙️ Comandos de Ejecución
Gestión del ciclo de vida de los contenedores mediante Docker Compose:

### Levantar el entorno:
```bash
docker compose up -d
```

### Detener el entorno:
```bash
docker compose down
```

### Ver logs en tiempo real:
```bash
docker compose logs -f
```

## 4. 🌐 Acceso a Servicios
| Servicio | URL Local | Puerto Host |
| :--- | :--- | :--- |
| **Aplicación Web (HTTP)** | [http://localhost:5080](http://localhost:5080) | `5080` |
| **Aplicación Web (HTTPS)** | [https://localhost:5443](https://localhost:5443) | `5443` |
| **Random Image Gallery** | [https://localhost:5443/rnd_img/](https://localhost:5443/rnd_img/) | `5443` |
| **Speedtest App** | [https://localhost:5443/speedtest/](https://localhost:5443/speedtest/) | `5443` |
| **phpMyAdmin** | [http://localhost:5088](http://localhost:5088) | `5088` |
| **OpenVSCode Server** | [http://localhost:5000](http://localhost:5000) | `5000` |

> **Nota:** El dominio comodín configurado internamente es `*.webserver.docker`.

## 5. 📂 Estructura del Proyecto
| Directorio | Descripción |
| :--- | :--- |
| `www_data/` | Directorio raíz del servidor web (`/var/www/html`). Código fuente. |
| `apache_data/` | Configuraciones completas de Apache (sitios, módulos, logs, SSL). |
| `php_data/` | Archivo `php.ini` para ajustes de configuración de PHP. |
| `db_data/` | Datos persistentes de la base de datos MariaDB. |

## 6. 📝 Convenciones de Desarrollo
Para mantener la consistencia en el proyecto, se aplican los siguientes estándares:

- **Identación:** 2 espacios.
- **Comillas:** Simples (`'`) para strings.
- **JavaScript:** Sin puntos y coma (`never`), uso de `const` y `let`.
- **Archivos:** Saltos de línea estilo Windows (`CRLF`).
- **Seguridad:** Los secretos se gestionan vía archivo `.env` (no incluido en el repositorio).

---
Desarrollado por **Juan Gabriel Maioli** &bull; 2026
