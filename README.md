# Proyecto API Meteorológica - Carlos Castillo

## Índice
1. [Introducción](#id1)
2. [Objetivo](#id2)
3. [Despliegue de la Instancia EC2](#id3)  
  3.1 [Instalación de herramientas en la instancia](#3.1)  
  3.2 [Configuración de Apache](#3.2)
4. [Accediendo a la Aplicación](#id4)

## Introducción <a name="id1"></a>

Este proyecto tiene como objetivo crear una aplicación web para consultar el clima de cualquier ciudad del mundo a través de una API meteorológica. Para ello, se utilizarán servicios de AWS y una instancia EC2 para alojar la aplicación. A lo largo de este documento se explicará el proceso de creación y configuración de la instancia, así como la interacción con la aplicación y la consulta del clima.

## Objetivo <a name="id2"></a>

El propósito de este proyecto es implementar una aplicación web que permita a los usuarios consultar el clima actual y las previsiones meteorológicas de cualquier ciudad en el mundo. Los principales elementos que se mostrarán en la página son:

- Un formulario de búsqueda para encontrar el clima de una ciudad.
  - En caso de que la ciudad no exista, se mostrará un mensaje informativo.
- Si la ciudad es válida, se mostrará:
  - El clima actual.
  - La previsión del clima para el día.
  - La previsión del clima para la semana.

## Despliegue de la Instancia EC2 <a name="id3"></a>

El despliegue de la aplicación se realizará en una instancia EC2 de AWS *(Amazon Web Services)*, y se accederá a ella mediante una IP pública que AWS asigna automáticamente. Para mayor flexibilidad, más adelante asignaré una IP elástica para poder acceder a la instancia a través de una URL más amigable.

**Nota:** Como ya tengo experiencia en el despliegue de instancias, no me centraré demasiado en los detalles de esta parte. El foco principal de este proyecto está en el desarrollo de la aplicación en lugar de la configuración de la infraestructura, por lo que la configuración de la instancia no es la más adecuada para un entorno de producción.

A continuación, se describen los pasos para crear la instancia:

* Desde la consola de AWS, accedemos a **EC2** y seleccionamos "Lanzar una instancia".

1. Primero, elegimos el sistema operativo que deseamos usar:
   ![image](https://github.com/user-attachments/assets/e4e722e4-1b22-4be1-b2f5-65bee382ead8)

2. Luego, seleccionamos el tipo de instancia y generamos un par de claves (en mi caso, ya existe uno por defecto proporcionado por AWS) para poder conectarnos a la instancia mediante SSH:
   ![image](https://github.com/user-attachments/assets/8bfa0040-2a8a-40ef-a665-1fcbf23c9666)

3. En las configuraciones de red, lo único necesario es permitir la asignación automática de una IP pública. Lo demás se deja por defecto, y también se configura el grupo de seguridad para permitir el tráfico **HTTP**:
   ![image](https://github.com/user-attachments/assets/93c0269f-6ef4-4a67-bee8-f0e3e0741c58)

Una vez la instancia está creada, se puede ver en la consola de **Instancias** junto con su IP pública asignada:
![image](https://github.com/user-attachments/assets/dac8a10c-bfa7-457b-bd10-05f0704779a7)

### Instalación de herramientas en la instancia <a name="3.1"></a>

Para poder visualizar y ejecutar la aplicación, es necesario instalar ciertos programas. He utilizado el siguiente comando para instalar Apache y PHP:

```bash
sudo apt install -y apache2 php libapache2-mod-php php-cli php-mysql php-json php-gd php-curl php-mbstring php-xml
```

### Configuración de Apache <a name="3.2"></a>

La configuración de Apache es bastante sencilla. Solo se debe modificar la ruta del archivo **default** para que apunte a la ubicación de los archivos de la aplicación. Una vez realizada la modificación, es necesario reiniciar Apache para aplicar los cambios:

```bash
sudo systemctl reload apache2
sudo systemctl restart apache2
```

![image](https://github.com/user-attachments/assets/3bef8513-0d42-4a57-90d8-9ac85fc09be7)

## Accediendo a la Aplicación <a name="id4"></a>

Una vez la instancia esté en funcionamiento, podemos acceder a la aplicación mediante una IP elástica que hemos creado en Amazon. Esta IP la hemos asociado a un dominio.
El dominio es eltiempocarloscast.zapto.org

![image](https://github.com/user-attachments/assets/d78c4f29-b441-4276-b856-9b0f103d9590)


Después de hacer clic en "Buscar", la página mostrará la información meteorológica de la ciudad introducida.

![image](https://github.com/user-attachments/assets/1e7a68ba-9a89-4a72-bd30-33b08e672f79)

Además, la aplicación ofrece las siguientes opciones:

- **Previsión horaria**:
  ![image](https://github.com/user-attachments/assets/543ef4a0-23b8-4929-9d17-577e7a97279b)

- **Previsión semanal**:
  ![image](https://github.com/user-attachments/assets/11d6b734-78b1-45be-9686-4d7cea334649)

## Conclusión

Este proyecto permite a los usuarios consultar fácilmente el clima de cualquier ciudad del mundo mediante una interfaz sencilla y accesible. Utilizando AWS para el despliegue y la API meteorológica para obtener los datos, la aplicación proporciona tanto la previsión horaria como la semanal de manera clara y eficiente. Aunque el enfoque principal está en el desarrollo de la aplicación, la configuración de la infraestructura en AWS también se ha llevado a cabo correctamente para asegurar que todo funcione de manera óptima.
