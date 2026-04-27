# PACIS — Sistema de Remisión de Insumos Médicos

Laravel 12 + Livewire 3 + Tailwind CSS. Control de inventario multi-almacén con lotes y caducidades, remisiones con escáner de código de barras, parseo de Constancia de Situación Fiscal (CSF) del SAT y roles de usuario.

---

## Requisitos

- PHP 8.2 o superior con extensiones: `mbstring`, `xml`, `bcmath`, `intl`, `gd`, `pdo_mysql` (o `pdo_pgsql` / `pdo_sqlite`)
- Composer 2.x
- Node 20+ y npm
- MySQL 8.x (recomendado) — también compatible con PostgreSQL 15+ y SQLite 3

---

## Instalación rápida

```bash
# 1. Clonar o descomprimir
unzip pacis.zip && cd pacis

# 2. Dependencias de PHP y JS
composer install
npm install

# 3. Variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar BD en .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
#    y parámetros PACIS_* y FACTURAMA_*

# 5. Migrar y poblar
php artisan migrate --seed

# 6. Storage público (CSF, barcodes)
php artisan storage:link

# 7. Compilar assets
npm run build       # producción
# npm run dev       # desarrollo con HMR

# 8. Servir
php artisan serve
```

Accede a `http://localhost:8000`.

### Credenciales por defecto (solo entorno local)

| Rol         | Email                       | Password             |
| ----------- | --------------------------- | -------------------- |
| admin       | admin@pacis.local           | PacisAdmin#2026      |
| vendedor    | vendedor@pacis.local        | PacisVendedor#2026   |
| facturación | facturacion@pacis.local     | PacisFactura#2026    |
| almacén     | almacen@pacis.local         | PacisAlmacen#2026    |

Cámbialas antes de subir a producción. El seeder `DefaultUsersSeeder` solo corre en `APP_ENV=local`.

---

## Variables de entorno clave (`.env`)

```
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=pacis
DB_USERNAME=root
DB_PASSWORD=

# Configuración PACIS
PACIS_BARCODE_PREFIX=200
PACIS_BARCODE_FORMAT=C128
PACIS_FEFO_ENABLED=true
PACIS_ALERT_DAYS_BEFORE_EXPIRY=60
PACIS_ALLOW_NEGATIVE_STOCK=false
PACIS_DEFAULT_ADMIN_EMAIL=admin@pacis.local

# Facturama (fase 3)
FACTURAMA_USER=
FACTURAMA_PASS=
FACTURAMA_SANDBOX=true
```

---

## Arquitectura

### Módulos (Fase 1 — completa)

- **Autenticación** (Breeze rehecho sobre Livewire): login, registro (solo admin crea usuarios), recuperación de contraseña
- **Usuarios y roles** (`spatie/laravel-permission`): admin, vendedor, facturacion, almacen
- **Almacenes**: CRUD, activar/desactivar
- **Productos**: referencia, clave alterna, descripción, código de barras (capturado por escáner o generado con prefijo), lote y caducidad opcionales por producto, costo y precio, tasa IVA
- **Clientes y proveedores**: alta manual o carga de PDF de CSF (SAT). El parser extrae RFC, razón social, régimen fiscal, CP y domicilio
- **Dashboard**: KPIs básicos y alertas de caducidad

### Módulos (Fase 2 — pendientes de implementar)

- **Órdenes de compra**: alta, autorización, envío a proveedor
- **Recepción de OC**: entrada parcial/total, captura de lotes y caducidades, genera movimiento de stock tipo `reception`
- **Remisiones**: captura con escáner por almacén, descuenta stock siguiendo FEFO, cancelación solo admin con reversión de stock
- **Reportes de inventario**: stock por almacén, caducidades próximas, kardex

### Módulos (Fase 3 — Facturama)

- Integración con Facturama API para timbrado de CFDI 4.0
- Transformación de remisión a factura
- Cancelación de CFDI con motivos SAT

---

## Lógica de negocio crítica

### Inventario FEFO (First-Expire-First-Out)

`App\Services\Inventory\StockService` centraliza todas las mutaciones de stock:

- `increase()` — Recalcula costo promedio ponderado por producto+almacén+lote
- `decrease()` — Rechaza consumo si `PACIS_ALLOW_NEGATIVE_STOCK=false`
- `fefoLots()` — Devuelve stock de un producto ordenado por caducidad ASC (con null al final)
- `consumeFefo()` — Descuenta de varios lotes siguiendo FEFO cuando el lote más próximo a vencer no alcanza

Cada mutación se registra en `stock_movements` con `reference_type` / `reference_id` polimórficos (remisión, recepción, ajuste…).

### Cancelación de remisiones (regla crítica)

- Solo usuarios con rol `admin` pueden cancelar (permiso `remissions.cancel` y `RemissionPolicy::cancel()`)
- La cancelación genera movimientos `remission_cancel` inversos que restauran stock en los mismos lotes originales
- Ver `StockService::revertRemission()` y `tests/Unit/StockServiceTest.php::test_revert_remission_returns_stock`

### Parseo de CSF

`App\Services\Csf\CsfParser` usa `smalot/pdfparser` y regex para extraer campos del PDF emitido por el SAT. Los datos extraídos pueblan `fiscal_profiles` asociados a un `Customer` o `Supplier`. El PDF original se guarda en `storage/app/csf/{uuid}.pdf` y el JSON crudo en `fiscal_profiles.csf_raw` para auditoría.

### Código de barras

`App\Services\Barcode\BarcodeGenerator`:

- Si el producto ya trae barcode: se respeta y `barcode_generated = false`
- Si no: genera `{PACIS_BARCODE_PREFIX}{referencia zero-padded}` y `barcode_generated = true`
- Renderiza PNG/SVG con `picqer/php-barcode-generator` (Code 128 por defecto)

Escaneo en vivo con webcam vía `@zxing/browser` (`resources/js/barcode-scanner.js`, global `window.PacisBarcode`).

---

## Testing

```bash
php artisan test
# o
./vendor/bin/phpunit
```

Tests incluidos (Fase 1):

- `tests/Unit/StockServiceTest.php`
  - `test_increase_creates_stock_row`
  - `test_decrease_throws_when_insufficient`
  - `test_revert_remission_returns_stock`

---

## Roadmap

- [x] **Fase 1** — Auth, roles, catálogos (almacenes, productos, clientes, proveedores), CSF parser, barcode gen+scan
- [ ] **Fase 2** — OC, recepciones, remisiones con FEFO, cancelación admin, kardex
- [ ] **Fase 3** — Integración Facturama (CFDI 4.0), timbrado desde remisión, cancelación con motivo SAT

---

## Estructura de carpetas relevante

```
app/
  Http/           Controllers, Requests, Middleware
  Livewire/       Componentes Livewire por módulo
  Models/         Eloquent models
  Policies/       Autorización (RemissionPolicy crítica)
  Providers/
  Services/
    Barcode/      BarcodeGenerator
    Csf/          CsfParser
    Inventory/    StockService (FEFO, reverts)
config/pacis.php  Configuración del sistema
database/
  factories/
  migrations/
  seeders/        RolesAndPermissionsSeeder, DefaultUsersSeeder
resources/
  css/app.css     Tailwind + componentes
  js/             barcode-scanner.js, app.js
  views/          Blade + Livewire
routes/
  web.php         Rutas principales con middleware role/permission
  auth.php        Flujo Breeze/Livewire
```

---

## Licencia

Uso interno. No redistribuir sin autorización del propietario.
