# Muebles Florence Art — Control Financiero y Gestión de Proyectos

Aplicación web responsiva para controlar la rentabilidad real de una mueblería
artesanal: proyectos por cliente, abonos, gastos, mano de obra, proveedores
"al fiado" y gastos operativos generales.

## Stack elegido y por qué

| Capa       | Tecnología                                   |
|------------|-----------------------------------------------|
| Backend    | PHP 8.3 nativo, arquitectura MVC propia (sin framework) |
| Base de datos | MySQL 8 / MariaDB 10.4+ (PDO con consultas preparadas) |
| Frontend   | HTML5 + Tailwind CSS (build local, sin CDN) + Alpine.js (CDN) |

Se optó por **PHP nativo en lugar de un framework pesado (Laravel, Symfony)**
o **Node/Express** por tres razones prácticas para este proyecto:

1. El entorno ya cuenta con PHP 8.3 instalado y no tenía Composer configurado
   como parte del stack — un MVC hecho a mano no depende de instalar
   herramientas adicionales para funcionar.
2. Un negocio pequeño como una mueblería normalmente se despliega en hosting
   compartido con Apache + MySQL (cPanel, etc.). PHP corre ahí de forma
   nativa sin necesidad de un proceso Node persistente.
3. Una arquitectura MVC explícita (sin "magia" de framework) es más fácil de
   auditar y modificar para alguien que dé mantenimiento al sistema sin
   experiencia previa en un framework específico.

Tailwind sí se compila localmente con Node/npm (solo para generar el CSS de
producción, no para el runtime de la app) porque el proyecto tiene Node
disponible; Alpine.js se mantiene por CDN por ser una librería pequeña que no
necesita build.

## Estructura del proyecto

```
Florence Art/
├── app/
│   ├── Core/              # Router, Controller y Model base, conexión PDO, helpers
│   ├── Controllers/       # Un controlador por recurso (Project, Client, Payment, ...)
│   ├── Models/            # Acceso a datos (PDO + consultas preparadas)
│   └── Views/
│       ├── layouts/       # app.php (interno), guest.php (login), print.php (reportes)
│       ├── dashboard/
│       ├── clients/       # index/create/edit/show (ficha con historial de proyectos)
│       ├── projects/      # index/create/edit/show/report (show = detalle con pestañas)
│       ├── suppliers/
│       ├── operating_expenses/
│       ├── errors/        # 404.php, 500.php
│       └── partials/      # pagination.php
├── bin/
│   ├── create_admin.php   # Alta de usuarios por línea de comandos
│   └── backup_db.php      # Respaldo de la base de datos vía mysqldump
├── config/
│   ├── env.php             # Cargador de variables de entorno (.env)
│   └── config.php          # Bootstrap de configuración (timezone, errores)
├── database/
│   └── schema.sql           # Estructura relacional completa con FKs
├── resources/css/input.css  # Fuente de Tailwind (antes de compilar)
├── tests/run.php             # Suite de pruebas de integración (sin PHPUnit)
├── public/                   # Document root — apunta aquí tu servidor web
│   ├── index.php             # Front controller (router de la app)
│   ├── .htaccess              # Reescritura de URLs (Apache)
│   ├── assets/css/tailwind.css # CSS de Tailwind ya compilado (se sube al repo)
│   └── uploads/projects/      # Evidencias/fotos subidas por proyecto
├── router.php                # Router auxiliar solo para `php -S` en desarrollo
├── .env.example
└── README.md
```

## Módulo de proyectos (flujo de datos de ejemplo)

`app/Models/Project.php` es el modelo principal: además del CRUD, expone
`financialSummary($id)`, que calcula en tiempo real:

```
Costo total     = costo acordado + Σ modificaciones (project_extras)
Saldo pendiente = Costo total - Σ abonos (payments)
Utilidad        = Σ abonos - (Σ gastos directos + Σ mano de obra)
```

`app/Controllers/ProjectController.php` orquesta el modelo y sus relaciones
(`ProjectExtra`, `ProjectMedia`, `Payment`, `Expense`, `Labor`, `Supplier`,
`ProjectStatusHistory`) para armar la vista de detalle
(`app/Views/projects/show.php`), que muestra todo el ciclo de vida financiero
de un proyecto en pestañas, incluida su bitácora de cambios de estado.

## Instalación local

1. **Crear la base de datos**
   ```
   mysql -u root -p < database/schema.sql
   ```
   (o importa `database/schema.sql` desde phpMyAdmin/HeidiSQL/DBeaver).

2. **Configurar variables de entorno**
   ```
   copy .env.example .env
   ```
   Edita `.env` con tus credenciales de MySQL.

3. **Compilar el CSS** (solo la primera vez, o si editas clases de Tailwind):
   ```
   npm install
   npm run build:css
   ```
   El repo ya incluye `public/assets/css/tailwind.css` compilado, así que este
   paso es opcional a menos que cambies el HTML/PHP y necesites nuevas clases.
   Durante desarrollo activo puedes dejar `npm run watch:css` corriendo en una
   terminal aparte para recompilar automáticamente.

4. **Levantar el servidor de desarrollo** (no requiere Apache/XAMPP):
   ```
   php -S localhost:8000 -t public router.php
   ```
   Abre `http://localhost:8000`. (`router.php` es necesario porque el
   servidor embebido de PHP, sin él, intercepta URLs con extensión como
   `.csv` y las trata como archivo estático en vez de pasarlas al router de
   la app; un servidor real con el `.htaccess` incluido no tiene ese problema.)

   Alternativa con Apache/XAMPP: configura un Virtual Host cuyo
   `DocumentRoot` apunte a la carpeta `public/` de este proyecto, y ajusta
   `APP_URL` en `.env` acorde. Con Apache no hace falta `router.php`.

5. **Crear el primer usuario administrador** (la app exige sesión iniciada
   en todas las rutas):
   ```
   php bin/create_admin.php "Tu Nombre" tu@correo.com tuContraseña
   ```
   Entra en `http://localhost:8000/login` con esas credenciales.

## Roles

Hay dos roles (columna `users.role`): **admin** y **operador**.

- Ambos pueden crear y editar cualquier registro.
- Solo **admin** puede eliminar (proyectos, clientes, proveedores, abonos,
  gastos, mano de obra, modificaciones, galería, gastos operativos). Un
  operador ni siquiera ve el botón "Eliminar" en la interfaz, y si intentara
  forzar la petición, el backend la rechaza con 403.

Para dar de alta un operador: `php bin/create_admin.php "Nombre" correo pass`
y luego `UPDATE users SET role = 'operador' WHERE email = '...'` (no hay
pantalla de gestión de usuarios todavía, ver "Próximos pasos").

## Respaldos de la base de datos

```
php bin/backup_db.php
```
Genera un `.sql` con fecha en `/backups` usando `mysqldump`. Para
automatizarlo:
- **Windows**: crea una tarea en el Programador de tareas que ejecute ese
  comando diariamente (`schtasks /create ...`).
- **Hosting Linux**: agrega una línea de cron, p. ej.
  `0 3 * * * php /ruta/al/proyecto/bin/backup_db.php`.

## Pruebas automatizadas

```
php tests/run.php
```
Corre una suite de integración contra una base de datos desechable
(`florence_art_test`, se crea y se destruye en cada corrida) que valida los
cálculos financieros clave: utilidad por proyecto, saldo de proveedor,
protección de integridad referencial, paginación/búsqueda y la bitácora de
estados. No requiere PHPUnit ni Composer.

## Qué ya funciona

- **Autenticación y roles**: login/logout con sesión, todas las rutas
  protegidas salvo `/login`. Rol `admin` vs `operador` (ver arriba). Alta de
  usuarios por línea de comandos (`bin/create_admin.php`); no hay pantalla de
  registro pública (correcto para una herramienta interna).
- **Dashboard**: los 4 indicadores clave (proyectos activos, por cobrar, por
  pagar a proveedores, utilidad neta del mes), gráfica de ingresos vs. gastos
  de los últimos 6 meses (Chart.js), y paneles de alerta para entregas
  próximas (7 días) y proyectos con saldo pendiente y entrega ya vencida.
- **Clientes**: alta, edición, eliminación (bloqueada con mensaje claro si el
  cliente tiene proyectos asociados), listado con búsqueda y paginación, y
  ficha de detalle con el historial de sus proyectos y totales acumulados.
- **Proyectos**: alta, edición, eliminación (en cascada), listado con
  filtros y paginación, exportación a CSV (individual o de todo el listado),
  reporte imprimible (conviértelo a PDF con el diálogo de impresión del
  navegador), y detalle con pestañas para abonos, gastos, mano de obra,
  modificaciones, galería de evidencias e historial de cambios de estado —
  cada renglón se puede editar o eliminar individualmente.
- **Proveedores**: alta, edición, eliminación, listado con búsqueda,
  paginación y saldo de fiado calculado, registro de abonos para saldar
  deuda.
- **Gastos operativos generales**: alta, listado y eliminación.
- **Configuración del negocio** (solo admin): nombre, eslogan, teléfono,
  WhatsApp, correo, dirección y sitio web — editables desde `/settings`. El
  logo se sube ahí mismo (JPG/PNG/WEBP) y se refleja automáticamente en el
  sidebar, la pantalla de login, el favicon y los reportes impresos; sin
  logo cargado se usa un emblema por defecto.
- Protección CSRF en todos los formularios POST.
- Páginas de error propias (404 y 500); en producción el 500 nunca expone
  detalles técnicos, solo se registra en el log del servidor.
- Respaldo de base de datos y suite de pruebas de integración (ver arriba).

## Próximos pasos sugeridos

- Pantalla de gestión de usuarios (alta/edición/cambio de rol desde la UI,
  hoy solo por línea de comandos y SQL directo).
- Editar el archivo de un registro de galería (hoy solo se puede eliminar y
  volver a subir).
- Exportar a Excel real (.xlsx) en vez de CSV, si se necesita formato con
  fórmulas o múltiples hojas (requeriría una librería vía Composer).
- Notificaciones push/correo para las alertas del dashboard (hoy son solo
  visuales, no se envían automáticamente).
