# 🐳 WebServer Docker (LAMP Stack)

¡Bienvenido al entorno de desarrollo **WebServer**! Este proyecto proporciona un ecosistema completo basado en contenedores Docker para el desarrollo de aplicaciones web modernas utilizando la pila **LAMP**.

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/MariaDB-10.11-003545?style=for-the-badge&logo=mariadb&logoColor=white)](https://mariadb.org/)

---

## 🚀 1. Características Principales

Este entorno está preconfigurado para ser robusto, persistente y fácil de usar:

- **PHP 8.4:** La versión más reciente con soporte para `mysqli` y `gd`.
- **Apache 2.4:** Configurado con `mod_rewrite` y soporte para HTTPS.
- **MariaDB 10.11:** Motor de base de datos estable y de alto rendimiento.
- **phpMyAdmin:** Interfaz web intuitiva para la gestión de bases de datos.
- **Docker CLI Integration:** El contenedor de Apache tiene acceso al socket de Docker del host, permitiendo interactuar con otros contenedores directamente desde PHP.

---

## 🛠️ 2. Estructura del Proyecto

El proyecto utiliza volúmenes locales para asegurar que tus cambios sean persistentes y fáciles de editar:

| Carpeta | Descripción |
| :--- | :--- |
| `www_data/` | 💻 **Código Fuente:** Directorio raíz del servidor web (`/var/www/html`). |
| `apache_data/` | ⚙️ **Apache:** Configuraciones de sitios, módulos y logs. |
| `php_data/` | 🐘 **PHP:** Configuración personalizada de `php.ini`. |
| `db_data/` | 🗄️ **Base de Datos:** Almacenamiento persistente de MariaDB (ignorado en Git). |

---

## ⚙️ 3. Guía de Inicio Rápido

### Levantando el Servidor
Para iniciar todos los servicios, simplemente ejecutá:
```bash
docker compose up -d
```

### Deteniendo el Entorno
Si necesitás apagar los contenedores:
```bash
docker compose down
```

### Monitoreo de Logs
Visualizá lo que ocurre en tiempo real:
```bash
docker compose logs -f
```

---

## 🌐 4. Acceso a los Servicios

| Servicio | URL |
| :--- | :--- |
| **Sitio Web (HTTP)** | [http://localhost:5080](http://localhost:5080) |
| **Sitio Web (HTTPS)** | [https://localhost:5443](https://localhost:5443) |
| **phpMyAdmin** | [http://localhost:5088](http://localhost:5088) |

---

## 🐘 5. Base de Datos
La configuración por defecto utiliza las siguientes credenciales (definidas en tu `.env`):

- **Host:** `web_db`
- **Puerto:** `3306`
- **Base de datos:** `admin_webserver`

---

## 📝 6. Convenciones de Código (JavaScript)
Este proyecto utiliza **ESLint** para mantener un estándar de calidad:
- **Indentación:** 2 espacios.
- **Semicolons:** No se utilizan.
- **Comillas:** Simples (`'`).
- **Variables:** Preferencia por `const` y `let`.

---

> [!IMPORTANT]
> **Seguridad:** Nunca subas el archivo `.env` o la carpeta `db_data/` al control de versiones. Están configurados en el `.gitignore` por defecto.

Desarrollado con ❤️ por **Juan Gabriel Maioli**.
