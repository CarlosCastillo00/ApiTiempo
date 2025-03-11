# Proyecto API del Clima - Carlos Castillo

## Índice
1. [Introducción](#id1)
2. [Objetivo](#id2)
3. [Implementación de la Instancia EC2](#id3)
   3.1 [Instalación de dependencias](#3.1)
   3.2 [Configuración del servidor Apache](#3.2)
4. [Acceso a la Aplicación](#id4)
5. [Cambios y Mejoras de la Aplicación](#id5)

## Introducción <a name="id1"></a>

Este proyecto consiste en el desarrollo de una aplicación web que permite consultar el estado del tiempo en cualquier ciudad del mundo mediante una API meteorológica. Se hace uso de AWS para desplegar la aplicación en una instancia EC2, donde se aloja el código en PHP. En este documento se explica tanto el proceso de configuración de la infraestructura en AWS como las mejoras implementadas en la aplicación.

## Objetivo <a name="id2"></a>

El propósito del proyecto es ofrecer a los usuarios una herramienta sencilla y eficaz para consultar datos meteorológicos actualizados. La aplicación permite:
- Ingresar el nombre de una ciudad para buscar su clima.
  - Si la ciudad no existe en la base de datos, notificará al usuario.
- Mostrar la información del clima en tiempo real.
- Presentar la predicción meteorológica para el día en curso.
- Ofrecer un pronóstico del clima para las próximas horas y días.

## Implementación de la Instancia EC2 <a name="id3"></a>

Para desplegar la aplicación, se utiliza una instancia EC2 en AWS que recibe una IP pública de manera automática. Posteriormente, se configurará una IP elástica para asociarla a un dominio y facilitar el acceso.

**Nota:** Dado mi conocimiento en la configuración de instancias AWS, este documento se centra en el desarrollo y mejoras de la aplicación, sin profundizar en aspectos avanzados de seguridad y red, los cuales no son los más óptimos para un entorno de producción.

### Pasos para la creación de la instancia:

1. Desde la consola de AWS, accedemos a **EC2** y seleccionamos "Lanzar una instancia".

2. Seleccionamos el sistema operativo que se utilizará en la máquina virtual:  
   ![image](https://github.com/user-attachments/assets/e4e722e4-1b22-4be1-b2f5-65bee382ead8)

3. Elegimos el tipo de instancia y generamos un par de claves para la conexión SSH *(en mi caso, utilizo una clave generada previamente por AWS)*.  
   ![image](https://github.com/user-attachments/assets/8bfa0040-2a8a-40ef-a665-1fcbf23c9666)

4. En la configuración de red, activamos la asignación automática de una IP pública y dejamos el resto de opciones en sus valores predeterminados. Además, habilitamos el tráfico **HTTP** en el grupo de seguridad.  
   ![image](https://github.com/user-attachments/assets/93c0269f-6ef4-4a67-bee8-f0e3e0741c58)

Una vez creada la instancia, se puede visualizar la IP pública en la consola de **Instancias**:  
![image](https://github.com/user-attachments/assets/dac8a10c-bfa7-457b-bd10-05f0704779a7)

### Instalación de dependencias <a name="3.1"></a>

Para el correcto funcionamiento de la aplicación es necesario instalar ciertos paquetes. Se utiliza el siguiente comando para instalar Apache, PHP y las extensiones requeridas:

```bash
sudo apt install -y apache2 php libapache2-mod-php php-cli php-mysql php-json php-gd php-curl php-mbstring php-xml
```

### Configuración del servidor Apache <a name="3.2"></a>

Se debe modificar el archivo de configuración de Apache por defecto para establecer la ruta de los archivos del proyecto. Luego, se reinicia el servicio para aplicar los cambios:

```bash
sudo systemctl reload apache2
sudo systemctl restart apache2
```

![image](https://github.com/user-attachments/assets/3bef8513-0d42-4a57-90d8-9ac85fc09be7)

## Acceso a la Aplicación <a name="id4"></a>

Una vez que la instancia está operativa, se puede acceder a la aplicación a través de una IP elástica asociada a un dominio. En este caso, la dirección asignada es:

🔗 **eltiempocarloscast.zapto.org**

![image](https://github.com/user-attachments/assets/d78c4f29-b441-4276-b856-9b0f103d9590)

En la página principal se encuentra un buscador para ingresar el nombre de una ciudad y consultar su clima.

Si la ciudad es válida, se mostrarán los siguientes datos:
- Información del clima en tiempo real, incluyendo un icono representativo obtenido de la API.
- Pronóstico por horas con un gráfico que combina líneas y barras, y una sección de iconos para indicar el clima en cada intervalo.
- Pronóstico semanal que agrupa datos diarios (temperaturas mínimas y máximas, acumulación de lluvia) y muestra un icono representativo para cada día.

## Cambios y Mejoras de la Aplicación <a name="id5"></a>

A lo largo del desarrollo se han implementado las siguientes mejoras y se han actualizado los archivos correspondientes para facilitar la colaboración del equipo:


1. **Almacenamiento de la Última Ciudad Buscada:**
   - En `index.php` se ha implementado código JavaScript utilizando `localStorage` para guardar la última ciudad consultada. De esta manera, al recargar la página, el campo de búsqueda se prellena automáticamente con la última ciudad ingresada.

2. **Integración de Iconos del Clima:**
   - Se han añadido bloques de código en `index.php`, `horas.php` y `semanal.php` para mostrar iconos meteorológicos usando el código de icono devuelto por la API de OpenWeatherMap (por ejemplo, `http://openweathermap.org/img/wn/{icon}@2x.png`).

3. **Graficación con Chart.js:**
   - Se han implementado gráficos en `horas.php` y `semanal.php` utilizando Chart.js para mostrar predicciones horarias y semanales. En ambos casos se combinan gráficos de barras y líneas para representar datos de temperatura y lluvia de manera visual y clara.

## Conclusión
Este proyecto ha sido una gran oportunidad para desarrollar una aplicación sencilla y práctica que permite consultar el clima en tiempo real, alojada en una instancia EC2 de AWS. Durante el proceso, se añadieron mejoras como el guardado de la última ciudad buscada, la integración de iconos meteorológicos y la visualización de datos con gráficos interactivos.

Gracias a tecnologías como Apache, PHP y Chart.js, la aplicación ofrece una interfaz fácil de usar y bastante visual. Además, al estar en AWS, tiene la ventaja de ser escalable y adaptable a futuras mejoras.

En resumen, este proyecto no solo cumple su propósito, sino que también ha sido una experiencia valiosa para aprender sobre despliegue y optimización de aplicaciones web en la nube.
