# Retail Support - Sistema Interno de Incidencias

Aplicacion web interna desarrollada con Laravel para la gestion de incidencias en tienda.

El proyecto esta orientado a un caso real de operativa diaria: consulta de incidencias por codigo, actualizacion por perfiles con permisos y conexion desacoplada con sistema externo de incidencias mediante un Proxy.

## Stack Tecnologico

- Backend: Laravel 12, PHP 8.2+
- Frontend: Blade, Bootstrap 5, Alpine.js, Vite
- Base de datos local: MariaDB (Sail)
- Sistema externo de incidencias: Firebase Realtime Database (Kreait)
- Contenedores: Docker / Laravel Sail
- Despliegue: Docker multi-stage (compatible con Dokploy)

## Funcionalidades Principales

- Login por `employee_id` y contrasena
- Gestion de roles: Empleado, Responsable y Direccion
- Busqueda de incidencias por ID
- Vista de detalle y actualizacion de incidencia (segun rol)
- Listado de incidencias de tienda para Direccion
- Control de acceso con middleware de autenticacion y rol
- Vistas de error personalizadas: 403, 404, 419, 500
- Manejo de fallos del sistema externo con excepcion personalizada

## Arquitectura (Resumen)

- Contrato: `IssueApiInterface`
- Implementacion: `IssueApiProxy`
- Normalizacion de datos externos: `IssueDTO`
- Objetivo: desacoplar la logica de negocio de la tecnologia concreta del proveedor externo

## Despliegue

El repositorio incluye un `Dockerfile` multi-stage orientado a produccion:

- Stage 1: build de assets (Node + Vite)
- Stage 2: dependencias PHP (Composer, sin dev)
- Stage 3: runtime final (PHP 8.4 + Apache)

Tambien elimina `public/hot`, copia solo artefactos necesarios (`vendor`, `public/build`) y prepara permisos de `storage` y `bootstrap/cache`.

## Estado del Proyecto

Proyecto academico con enfoque practico de entorno real.

Posibles mejoras futuras:

- Integracion con SSO corporativo real
- Filtros avanzados en listado de incidencias (fecha, empleado, estado)
- Flujo de identificacion mediante QR para cliente/incidencia

## Licencia

Este proyecto se distribuye bajo licencia MIT.
