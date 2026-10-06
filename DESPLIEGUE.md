# Guía de Despliegue en la Nube y Ejecución Local - TechFix

Esta guía detalla los pasos requeridos para ejecutar el proyecto en entorno local (XAMPP / Docker) y publicarlo gratis en plataformas cloud host como **Railway**, **Render** o **Fly.io**.

---

## 🚀 1. Ejecución en Entorno Local

### Opción A: Con XAMPP / WAMP / MAMP (Tradicional)
1. Copia la carpeta del proyecto a `htdocs` (en XAMPP) o `www` (en WAMP).
2. Abre **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Crea una base de datos llamada `techfix_db`.
4. Importa el archivo `database.sql` incluido en el proyecto.
5. Abre el navegador en: `http://localhost/SH1U2/` (o el nombre de la carpeta).

*Nota:* Si tienes MySQL corriendo localmente con usuario `root` y sin contraseña, el script `includes/db.php` creará e importará la base de datos automáticamente en la primera visita.

### Opción B: Con Docker Compose (Recomendado)
Si tienes instalado Docker Desktop:
```bash
docker-compose up -d --build
```
Abre tu navegador en: `http://localhost:8000`

---

## ☁️ 2. Despliegue en Railway (Opción Más Rápida)

[Railway](https://railway.app) permite desplegar tanto la aplicación PHP como la base de datos MySQL en minutos.

### Pasos:
1. **Subir a GitHub:**
   Asegúrate de subir este repositorio a tu cuenta de GitHub:
   ```bash
   git add .
   git commit -m "Proyecto TechFix completado con MySQL y Docker"
   git push origin main
   ```

2. **Crear Proyecto en Railway:**
   - Inicia sesión en [railway.app](https://railway.app).
   - Haz clic en **"New Project"** -> **"Provision MySQL"**.
   - Haz clic en el servicio MySQL recién creado, ve a la pestaña **Data** y ejecuta la consulta del archivo `database.sql` o usa los datos de conexión para conectarte desde DBeaver/TablePlus/phpMyAdmin.

3. **Desplegar la Aplicación Web PHP:**
   - En el mismo proyecto de Railway, haz clic en **"New"** -> **"GitHub Repo"** y selecciona este repositorio.
   - Railway detectará el `Dockerfile` automáticamente y construirá el contenedor.

4. **Vincular Variables de Entorno en Railway:**
   - Entra al servicio de tu Web App -> Pestaña **Variables**.
   - Agrega las siguientes variables haciendo referencia a la BD MySQL de Railway:
     - `DB_HOST`: `${{MySQL.MYSQLHOST}}`
     - `DB_PORT`: `${{MySQL.MYSQLPORT}}`
     - `DB_NAME`: `${{MySQL.MYSQLDATABASE}}`
     - `DB_USER`: `${{MySQL.MYSQLUSER}}`
     - `DB_PASS`: `${{MySQL.MYSQLPASSWORD}}`

5. **Generar Dominio Público:**
   - Ve a **Settings** -> **Networking** -> **Generate Domain**.
   - ¡Listo! Tu aplicación estará en línea con HTTPS habilitado.

---

## 🌐 3. Despliegue en Render (Render.com)

1. **Subir cambios a GitHub.**
2. **Crear Web Service en Render:**
   - Entra a [Render Dashboard](https://dashboard.render.com/).
   - Selecciona **"New +"** -> **"Web Service"**.
   - Conecta tu repositorio de GitHub.
   - En **Environment**, selecciona **Docker**.
3. **Configurar Variables de Entorno en Render:**
   - En la sección **Environment Variables**, añade:
     - `DB_HOST`: Host de tu servidor MySQL (Ej. de Aiven.io, Railway MySQL, o PlanetScale).
     - `DB_PORT`: `3306`
     - `DB_NAME`: `techfix_db`
     - `DB_USER`: Tu usuario MySQL
     - `DB_PASS`: Tu contraseña MySQL
4. **Importar Esquema SQL:**
   - Importa `database.sql` en tu servidor MySQL en la nube.
5. **Haz clic en "Create Web Service".**

---

## 🛠️ Estructura de la Base de Datos (`techfix_db`)

- **`clientes`**:
  - `id` (PK, AUTO_INCREMENT)
  - `nombre` (VARCHAR 100)
  - `email` (VARCHAR 100)
  - `telefono` (VARCHAR 20)
  - `ciudad` (VARCHAR 100)
  - `created_at` (TIMESTAMP)

- **`servicios`**:
  - `id` (PK, AUTO_INCREMENT - Folio)
  - `cliente_id` (FK -> `clientes.id` con ON DELETE CASCADE)
  - `equipo` (VARCHAR 150)
  - `servicio` (TEXT)
  - `precio` (DECIMAL 10,2)
  - `estado` ENUM('Pendiente', 'En proceso', 'Completado', 'Cancelado')
  - `fecha_ingreso` (DATE)
  - `created_at` (TIMESTAMP)
