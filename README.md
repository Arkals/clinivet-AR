# Clinivet Store

Guía rápida de ejecución local, URLs limpias y ofuscación.

## Requisitos
- XAMPP (Apache + PHP >= 7.4)
- Extensiones PHP habituales (pdo_mysql, gd, openssl, etc.)
- Composer (opcional)
- git (solo para ofuscación completa con YAK Pro)

## Ejecutar en local (XAMPP)
- Carpeta del proyecto: `C:\xampp\htdocs\clinivet`.
- Asegúrate que Apache tenga `mod_rewrite` activo y `AllowOverride All`.
- Navega a: `http://localhost/clinivet/home` (URLs limpias habilitadas).

## URLs limpias
- `.htaccess` en la raíz reescribe de forma interna los slugs a `backend/public/index.php`.
- Ejemplos:
  - `/clinivet/home`
  - `/clinivet/productos`
  - `/clinivet/categoria/3`
  - `/clinivet/checkout`
  - `/clinivet/facturas`

## Estructura
- `backend/app` — Controladores y helpers.
- `frontend/app/Views` — Vistas.
- `vendor/` — Dependencias de Composer.
- `storage/` — Archivos generados (facturas, etc.).
- `scripts/` — Utilidades (ofuscación/minificación).

## Ofuscación ligera (incluida)
Minifica PHP/JS/CSS y copia estáticos, evitando `vendor/` y vistas.

```powershell
cd C:\xampp\htdocs\clinivet
php scripts/obfuscate.php
```

Salida: `build/obfuscated/`.

## Ofuscación completa (renombrado de símbolos)
Usa [YAK Pro - Php Obfuscator](https://github.com/pk-fr/yakpro-po) para ofuscar clases, funciones, variables y cadenas.

1) Requisitos: `git` y `php` (CLI) en PATH.

2) Ejecuta en PowerShell:
```powershell
cd C:\xampp\htdocs\clinivet\scripts
./yakpro_run.ps1
```

- El script clona `yakpro-po` (y `nikic/PHP-Parser`) si no existen.
- Usa `yakpro-po.cnf` de la raíz para configuración.
- Salida: `build/yakpro/yakpro-po/`.

3) Despliegue: apunta tu servidor al directorio ofuscado generado.

### Configuración (yakpro-po.cnf)
- Excluye: `vendor/`, `storage/`, `frontend/app/Views/`, `frontend/public/uploads/`, `build/`.
- Activa: ofuscación de cadenas, nombres de funciones, clases, métodos, propiedades, variables, namespaces y shuffle de sentencias.
- Si usas nombres dinámicos (llamadas indirectas), agrega excepciones en `t_ignore_*`.

## Notas
- Los 301 se cachean: usa ventana privada si cambias reglas de reescritura.
- Si algo falla, revisa `apache/error.log` y comparte el mensaje para depurar.
