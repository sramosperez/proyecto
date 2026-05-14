<div align="center">
  <h1>Sistema de Gestión de Incidencias</h1>
 

  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" />
  <img src="https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white" />
  <img src="https://img.shields.io/badge/Firebase-FFCA28?style=for-the-badge&logo=firebase&logoColor=black" />
</div>

<br />

## 👤 Sobre el Proyecto y Autoría

<div style="background-color: #f9f9f9; padding: 15px; border-left: 5px solid #2496ED;">
Este proyecto ha sido desarrollado por <b>Sara Ramos</b> como trabajo de final de DAW. <br>
La motivación principal nace de una <b>necesidad real observada en mi entorno laboral.</b><br>
El proyecto esta orientado a un caso real de operativa diaria: consulta de incidencias por código, actualización por perfiles con permisos y conexión desacoplada con sistema externo de incidencias mediante un Proxy.
</div>

## 🛠️ Stack Tecnológico

| Capa | Tecnología |
| :--- | :--- |
| **Backend** | Laravel 11/12 (PHP 8.4) |
| **Frontend** | Blade, Bootstrap 5, Alpine.js, Vite |
| **Persistencia** | MariaDB & Firebase Realtime Database |
| **Infraestructura** | Docker, Laravel Sail & Dokploy |

## 🏗️ Arquitectura (Patrón Proxy)

Se ha implementado una arquitectura desacoplada para garantizar la escalabilidad y el mantenimiento limpio del código:

* **Contrato:** `IssueApiInterface`
* **Implementación:** `IssueApiProxy` (Encapsula la lógica de conexión externa).
* **Transferencia:** `IssueDTO` (Normalización de datos).

> **Propósito:** Separar la lógica de negocio de la tecnología del proveedor, facilitando futuros cambios de proveedor de datos sin afectar al núcleo de la aplicación.

## 🚀 Funcionalidades Principales

<ul>
  <li><b>Gestión de Identidad:</b> Login por <code>employee_id</code> con seguridad por roles (Empleado, Responsable y Dirección).</li>
  <li><b>Operativa de Incidencias:</b> Búsqueda por ID, vista de detalle y actualización dinámica según permisos.</li>
  <li><b>Panel de Dirección:</b> Listado global de incidencias para supervisión de tienda.</li>
  <li><b>Robustez:</b> Middleware de control de acceso y vistas de error personalizadas (403, 404, 419, 500).</li>
</ul>

## 📦 Despliegue

El proyecto está optimizado para producción mediante un **Dockerfile multi-stage**, lo que permite una imagen ligera y segura:
1.  **Build Stage:** Compilación de assets con Vite y Node.
2.  **Vendor Stage:** Gestión de dependencias de Composer optimizadas para producción.
3.  **Runtime Stage:** Entorno final bajo Apache y PHP 8.4.

## 📈 Mejoras Futuras

- [ ] Integración con **SSO Corporativo**.
- [ ] Identificación mediante **Códigos QR**.
- [ ] Panel de **Analytics** y filtros avanzados.

<hr />

<div align="center">
  <p>Desarrollado por Sara Ramos</p>
</div>
