=== Despacho vía Starken para WooCommerce ===
Contributors: AndresReyesDev
Tags: woocommerce, shipping, chile, starken, turbus, despacho
Donate link: https://andres.reyes.dev
Requires at least: 4.5
Tested up to: 5.6
Stable tag: trunk
License: MIT License
License URI: https://opensource.org/licenses/MIT

Plugin de cálculo de despacho para WooCommerce en línea con  Starken (Ex Turbus Cargo). 

== Description ==

La actualización 2.0 es obligatoria. Sin ella no podrán realizar cálculos de despacho.

Es importante entender que desde la versión 2.0 es requerida una API gratis desde https://www.anyda.xyz que sea válida con el dominio. Si esta no está creada no funcionará la aplicación.

Realiza cálculo de despacho en línea (valor real entregado por Starken) en base al tamaño y peso del envío.

== Installation ==
Instalar plugin e ingresar a https://www.anyda.xyz para generar una API Gratis (para prevenir abusos). 

Posterior configurar en el apartado WooCommerce -> Ajustes -> Envío -> Starken (ex Turbus Cargo).

Dentro seleccione el lugar de Origen (la sucursal más cercana que servirá de base para los cálculos).

OPCIONES DEL PLUGIN

* Activo: Sirve para activar y desactivar el plugin (... duh?... )
* Título: El nombre con el cual se mostrará el despacho visible al cliente (cuando cálcula y paga)
* API: La API generada para el dominio en https://www.anyda.xyz
* Sucursal Origen: Base para el cálculo de despacho.

Todos las opciones son obligatorias.

== Frequently Asked Questions ==
= ¿No calcula bien? =

La API no está correctamente configurada, los productos no tienen peso y/o tamaño correcto.

= ¿La API será siempre gratis? =

Es muy probable, sin embargo si la carga del mismo es muy alta se considerarán opciones como donador para tener una cantidad ilimitada de consultas por usuario y/o dominio.

= ¿Se asegura el funcionamiento? =

Este plugin depende de, entre otras cosas, los sistemas de Starken. Si estos están funcionando todos somos felices. Dicho esto no podemos dar garantía de funcionalidad 100%, pero si haremos nuestro mejor esfuerzo por hacer funcionar la aplicación.

== Screenshots ==
1. Pantalla de Administración
2. Cálculo de Despacho

== Changelog ==

= 2020.12.26 =

Se mejora el sistema de cache de la API para evitar realizar la misma consulta una y otra vez al servidor. Esto mejora significativamente los tiempos de respuesta.

= 2.2.0 =

Se eliminan referencias de código externo.

= 2.0.1 = 

Mejoras en Cálculo de Despacho cortesía de @melvisnap

= 2.1.0 =

Optimización de código

= 2.0.0 = 

Se cambia la manera de conectar a la API, se corrigen errores y fallas. Se hace obligatorio el uso de la API gratis que sea válida para el dominio.

= 1.2.0 =

* Nuevo: Se deja la opción de asignar tamaño y peso por defecto para los productos que no lo tengan. Esto permite que el usuario siempre muestre el despacho independiente si los productos tienen dimensiones y peso. Por defecto viene con un tamaño de 25cm x 25cm x 25cm y 500grs de peso. Es importante que modifiquen estos parámetros o los desactiven.
* Nuevo: Opción de redondear el despacho (si sale $2345 puedes dejarlo en $2400, o $3000).
* Mejoras de código cortesía de @neoixan
* Se realizan mejoras en el código.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
Nada que hacer aún...