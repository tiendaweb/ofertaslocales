# Slim Framework 4 Skeleton Application

[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Use this skeleton application to quickly setup and start working on a new Slim Framework 4 application. This application uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application. You will require PHP 7.4 or newer.

```bash
composer create-project slim/slim-skeleton [my-app-name]
```

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.

To run the application in development, you can run these commands 

```bash
cd [my-app-name]
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd [my-app-name]
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

## Plantillas activas por ruta

Para evitar regresiones visuales, estas son las plantillas actualmente activas:

| Ruta | Action | Template principal | Parciales/layout |
| --- | --- | --- | --- |
| `/` | `HomeAction` | `templates/pages/index.php` | `templates/layout.php`, `templates/partials/header.php`, `templates/partials/footer.php`, `templates/partials/navigation.php` |
| `/ofertas` | `OffersAction` | `templates/pages/ofertas.php` | `templates/layout.php`, parciales globales |
| `/negocios` | `BusinessesAction` | `templates/pages/negocios.php` | `templates/layout.php`, parciales globales |
| `/mapa` | `MapAction` | `templates/pages/mapa.php` | `templates/layout.php`, parciales globales |
| `/negocios/{id}` | `BusinessDetailAction` | `templates/pages/negocio-detalle.php` | `templates/layout.php`, parciales globales |
| `/login` | `LoginPageAction` | `templates/pages/auth/login.php` | `templates/layout.php` |
| `/register` | `RegisterPageAction` | `templates/pages/auth/register.php` | `templates/layout.php` |
| `/panel` | `BusinessDashboardAction` | `templates/pages/admin/panel.php` | `templates/layout.php` |
| `/admin` | `AdminDashboardAction` | `templates/pages/admin/admin.php` | `templates/layout.php` |




Verificación de AGENTS y estrategia dinámica propuesta
Revisé el AGENTS.md aplicable en /workspace/ofertaslocales y lo tomé como base. Las reglas más relevantes para tu pedido son:

La app debe migrar a Slim 4 + SQLite + Tailwind + Leaflet.

La UI pública debe mantener la estructura visual de OBJETIVO.html.

Todos los textos deben quedar en español.

El dominio funcional principal son ofertas que duran 24 horas.

Roles iniciales: usuario, negocio, admin.

Diagnóstico del estado actual
Hoy el repo todavía está lejos del objetivo funcional:

app/routes.php sigue exponiendo solo:

/ → "Hello world!"

/users

public/index.php contiene una app paralela mínima de tareas:

usa tabla tasks

sirve public/ui.html

define /api/tasks

public/ui.html no corresponde a OfertasLocales; es una UI de “Task Manager”.

database/app.sqlite actualmente solo tiene:

tasks

sqlite_sequence

OBJETIVO.html sí representa la base visual/UX correcta para el frontend público.

Qué significa esto para tu objetivo
Tu necesidad no es “retocar una pantalla”: es una reestructuración del skeleton actual para convertirlo en una app orientada a:

Index

Ofertas

Negocios

Mapa

…con una UI idéntica a OBJETIVO.html, pero separada en vistas dinámicas, conectada a SQLite, con expiración real a 24 hs, y con navegación tipo app mediante footer menu con iconos.

Estrategia dinámica recomendada
1) Definir primero el “contrato funcional” de la app
Antes de tocar vistas, conviene fijar qué datos y qué reglas gobiernan la experiencia.

Modelo funcional mínimo
Usuario

visitante

registrado

Negocio

publica ofertas

administra sus ofertas

Admin

aprueba/modera

configura labels/SEO

Regla central
Toda oferta nace con:

created_at

expires_at = created_at + 24 horas

Solo se muestra en páginas públicas si:

status = active

expires_at > now()

Resultado
Esto permite que la UI “se vea igual” pero ya responda a datos reales.

2) Separar la UI objetivo en layout reutilizable
OBJETIVO.html no debería copiarse como una sola página estática. Conviene dividirlo en bloques.

Estructura sugerida
templates/layout.php

templates/partials/header.php

templates/partials/footer-nav.php

templates/partials/footer.php

Qué conservar exactamente
De OBJETIVO.html:

hero

stats

filtros por categoría

cards de ofertas

CTA a WhatsApp

estilo visual general

iconografía Lucide

Qué cambiar
reemplazar los arrays JS hardcodeados por datos del backend

reemplazar iframe fijo por Leaflet

mover el formulario de alta a flujo autenticado de negocio, o dejarlo visible solo si la estrategia comercial lo requiere

3) Diseñar las páginas públicas como vistas hermanas, no como una sola landing
Tu pedido menciona 4 páginas. Para que se sienta app móvil/web-app, lo mejor es que compartan layout y cambien solo el contenido central.

Páginas objetivo
/
Index

Hero exactamente como OBJETIVO.html

resumen de estadísticas reales

preview de ofertas activas

CTA de navegación

/ofertas
grilla completa

filtros por categoría

orden opcional:

más recientes

por vencer

más cercanas

timer por tarjeta

/negocios
listado de comercios

cada negocio con:

nombre

categoría principal

barrio/zona

cantidad de ofertas activas

acceso a detalle

/mapa
Leaflet + OpenStreetMap

markers de negocios con ofertas activas

tooltip con miniatura

modal o drawer con detalle de oferta

Navegación inferior
Un footer menu fijo con iconos debería existir en todas las páginas públicas:

Inicio

Ofertas

Negocios

Mapa

quizá “Cuenta” o “Publicar” según login

Esto es importante porque cambia la percepción del producto: deja de verse como landing y pasa a verse como app.

4) Rediseñar el esquema SQLite alrededor del negocio real
La tabla tasks es solo un placeholder. Para tu caso, el esquema debe quedar centrado en usuarios, negocios y ofertas.

Núcleo de tablas
users
id

email

password

role (admin, business, user)

business_name

whatsapp

created_at

offers
id

user_id

category

title

description

image_url

whatsapp

location

lat

lon

status (pending, active, expired)

created_at

expires_at

settings
key

value

seo
page_name

title

meta_description

og_image

Recomendación dinámica extra
Si querés que “Negocios” tenga más identidad, conviene sumar más adelante:

business_slug

business_logo

business_description

Pero no es imprescindible para el MVP.

5) Pasar de JS demo a render dinámico por servidor + JS liviano
Hoy OBJETIVO.html tiene arrays en JS con offers y countdown local. Eso sirve para demo, pero no para producción.

Estrategia correcta
El backend entrega las ofertas activas ya filtradas

La vista renderiza el HTML inicial

JS solo se usa para:

countdown visual

filtros interactivos

mapa

modal

mejoras UX

Ventaja
SEO mejor

carga inicial mejor

menos acoplamiento

más simple de depurar

6) Resolver el timer de 24 horas como regla de negocio, no solo como animación
Este es el corazón de tu producto.

Comportamiento recomendado
Al crear una oferta
created_at = now

expires_at = now + 24h

En backend
Toda consulta pública debe filtrar:

status = active

expires_at > now

En frontend
Cada card calcula:

expires_at - now

si llega a cero, puede:

ocultarse en próximo refresh

o marcarse como finalizada y desaparecer con polling o recarga

En cron
Un comando debe actualizar:

expired cuando expires_at <= now

Importante
No dependas solo del cron.
La validez pública debe salir siempre del filtro SQL actual.

7) Introducir roles sin complicar de más el MVP
Tu set inicial es correcto:

Usuario

Negocio

Admin

Recomendación de alcance
MVP público
visitante puede navegar sin login

Negocio
registro/login

crear oferta

ver sus ofertas

editar o desactivar

Admin
aprobar pendientes

activar/desactivar

cambiar textos del sitio

editar SEO

Consejo práctico
No mezclar de entrada panel business y panel admin en la misma vista.
Conviene separar:

/panel para negocio

/admin para administrador

8) Orden de implementación recomendado
Para llegar más rápido al objetivo visual + funcional, este sería el mejor orden:

Fase A — Base real del producto
eliminar la lógica conceptual de tasks

crear esquema SQLite real

registrar PDO en el contenedor

crear repositorios/servicios de ofertas y usuarios

Fase B — Front público exacto
fragmentar OBJETIVO.html

montar /, /ofertas, /negocios, /mapa

agregar footer menu fijo con iconos

cargar ofertas desde base

Fase C — Regla 24 hs
persistir created_at y expires_at

renderizar countdown

ocultar expiradas en consultas

sumar comando cron

Fase D — Negocios
registro/login

publicación de oferta

upload de imagen

vista de mis ofertas

Fase E — Admin
moderación

auto-aprobación/manual

settings del home

SEO por página

Ese orden minimiza retrabajo y permite ver valor real temprano.

Estrategia “dinámica” concreta por página
Index
Debe ser la página emocional/comercial:

Hero idéntico a OBJETIVO.html

métricas reales

carrusel o grilla resumida de ofertas activas

acceso rápido al mapa y a publicar

Ofertas
Debe ser la página de conversión:

filtros

countdown

CTA WhatsApp

badges tipo:

“Últimas horas”

“Nuevo”

“Más visto”

Negocios
Debe ser la página de confianza:

listado de locales

reputación visual

ubicación

cantidad de ofertas activas

Mapa
Debe ser la página de descubrimiento:

Leaflet

foco en cercanía

popup + modal

acceso inmediato al WhatsApp del negocio

Footer menu
Debe ser la pieza que unifique todo:

fijo abajo

iconos Lucide

estado activo por ruta

diseño mobile-first

Riesgos actuales a evitar
1. Mantener dos apps en paralelo
Ahora conviven:

skeleton Slim

mini app tasks

Eso va a generar confusión si no se unifica rápido.

2. Copiar OBJETIVO.html sin modularizar
Si queda todo en un solo archivo, después va a costar mucho hacer:

filtros reales

SEO por página

navegación activa

mantenimiento

3. Hacer el timer solo en frontend
Eso rompe la consistencia cuando:

el usuario abre tarde

cambia la hora del sistema

hay varias sesiones

4. No definir pronto la navegación inferior
Como querés “que se vea como aplicación”, el footer menu no es un detalle: es parte de la arquitectura UX.

Recomendación final de enfoque
Si tu prioridad es:

“Que se vea exactamente igual a OBJETIVO.html”
Entonces el primer entregable debería ser:

layout exacto

4 páginas públicas

footer menu fijo

datos fake pero ya estructurados como si vinieran de DB

Y enseguida después:

“Que funcione de verdad”
migración SQLite

modelos reales

filtros SQL

expiración 24 hs

mapa dinámico

login negocio/admin

Ese enfoque reduce riesgo porque primero clavás la experiencia visual y de navegación, y después conectás persistencia sin romper la UI.

Resumen ejecutivo
Hoy el repo está en estado de skeleton/demo, no en estado “OfertasLocales”.
La estrategia correcta para lo que necesitás es:

Tomar OBJETIVO.html como fuente visual exacta

Dividirlo en layout + páginas públicas

Agregar navegación inferior tipo app

Reemplazar tasks por users/offers/settings/seo

Implementar expiración real a 24 hs

Cargar todo dinámicamente desde SQLite

Separar panel negocio y admin

Si querés, en el siguiente paso puedo hacerte una hoja de ruta técnica ultra concreta archivo por archivo para este repo, por ejemplo:

qué reemplazar en app/routes.php

qué borrar conceptualmente de public/index.php

qué carpetas crear para vistas/controladores/repositorios

cómo mapear cada sección de OBJETIVO.html a rutas dinámicas

```bash
composer test
```

That's it! Now go build something cool.
