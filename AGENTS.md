Protocolo de Implementación - OfertasCerca Dinámico
Estado del Proyecto: Migración de Prototipo Estático a Slim Framework 4.
Stack Tecnológico: PHP 8.x, Slim 4, SQLite, Tailwind CSS, Leaflet.js (OpenStreetMap).

🎯 Objetivo General
Convertir el archivo archivo (6).html en una aplicación funcional con persistencia en base de datos, sistema de usuarios y un panel administrativo, manteniendo la estética "Liquid Glass" y "Neon" del administrador. Manteniendo la estructura visual de "objetivo.html". 

📂 Fase 1: Infraestructura y Base de Datos
Tarea 1.1: Preparación del Skeleton

Instalar Slim 4 Skeleton: composer create-project slim/slim-skeleton ..

Crear el directorio database/ y el archivo vacío database/app.sqlite.

Configurar permisos de escritura para logs/, database/ y public/uploads/.

Tarea 1.2: Esquema de Base de Datos (SQLite)

Crear script de migración para las siguientes tablas:

users: id, email, password, role (admin, business, user), business_name, created_at.

offers: id, user_id, category, title, description, image_url, whatsapp, location, lat, lon, status (pending, active, expired), expires_at.

settings: key, value (para labels del sitio y configuración de auto-aprobación).

seo: page_name, title, meta_description, og_image.

🖼️ Fase 2: Desarrollo de Vistas Públicas (Frontend)
Tarea 2.1: Implementación del Layout Exacto

Fragmentar archivo (6).html en templates/layout.php, partials/header.php y partials/footer.php.

Asegurar que el diseño sea idéntico al original utilizando las mismas clases de Tailwind CSS y animaciones.

Tarea 2.2: Vista de Ofertas y Negocios

Página Principal: Listado dinámico de ofertas con filtros por categoría.

Página de Negocios: Directorio de comercios registrados con sus ofertas activas.

Lógica de Timers: Adaptar el script de cuenta regresiva del original para que use la fecha expires_at de la base de datos.

Tarea 2.3: Mapa con OpenStreetMap

Reemplazar el iframe de Google Maps por un contenedor de Leaflet.js.

Markers: Cargar dinámicamente los negocios con ofertas activas.

Interactividad: * Implementar Tooltips con la miniatura de la oferta al pasar el mouse.

Implementar Modales (usando Tailwind) para mostrar los detalles completos al hacer clic en un marcador.

🔐 Fase 3: Autenticación y Gestión de Usuarios
Tarea 3.1: Login y Registro

Desarrollar controladores para GET /login y GET /register.

Validar el registro de negocios (requiere nombre del local y WhatsApp).

Implementar sesión persistente basada en cookies seguras.

Tarea 3.2: Middleware de Seguridad

Crear AuthMiddleware para proteger las rutas de administración.

Restringir el acceso según el rol (admin vs business).

🛠️ Fase 4: Panel Administrativo
Tarea 4.1: Interfaz de Administración

Crear una vista protegida para la gestión del sitio.

Gestión de Ofertas: Tabla para aprobar/rechazar o cambiar el estado de las publicaciones.

Ajustes de Publicación: Switch para alternar entre "Aprobación Automática" y "Revisión Manual".

Tarea 4.2: Editor de Contenido y SEO

Labels: Formulario para modificar los textos principales (H1, Hero, descripciones) almacenados en la tabla settings.

SEO Manager: Panel para actualizar Meta Tags y etiquetas OpenGraph por cada una de las 3 vistas principales.

🚀 Fase 5: Publicación y Lógica de Negocio
Tarea 5.1: Formulario de Creación de Oferta

Manejar la subida de imágenes a public/uploads/ y registrar la ruta en SQLite.

Geocodificar la ubicación ingresada por el usuario (o permitir seleccionar en el mapa) para guardar lat y lon.

Tarea 5.2: Tarea Programada (Cron)

Crear un script/comando para marcar como expired todas las ofertas cuyo expires_at sea menor a la hora actual.

Notas de Implementación para la IA:
Idioma: Todos los textos, mensajes de error y etiquetas del panel deben estar estrictamente en español.

Estilo de Código: Seguir el estándar PSR-12. Utilizar el contenedor de dependencias para la conexión PDO a SQLite.

Assets: Mantener el uso de Lucide Icons para la consistencia visual.