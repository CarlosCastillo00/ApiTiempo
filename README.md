
# Proyecto API del Clima - Carlos Castillo  

## Índice  
1. [Introducción](#id1)  
2. [Objetivo](#id2)  
3. [Implementación de la Instancia EC2](#id3)  
   3.1 [Instalación de dependencias](#3.1)  
   3.2 [Configuración del servidor Apache](#3.2)  
4. [Acceso a la Aplicación](#id4)  

## Introducción <a name="id1"></a>  

Este proyecto consiste en el desarrollo de una aplicación web que permite consultar el estado del tiempo en cualquier ciudad del mundo mediante una API meteorológica. Para su despliegue, se hace uso de los servicios de AWS, concretamente una instancia EC2, donde se alojará la aplicación. En este documento, se detallará el proceso de creación y configuración de la instancia, además de cómo interactuar con la aplicación para obtener información meteorológica.  

## Objetivo <a name="id2"></a>  

El propósito principal de este proyecto es ofrecer a los usuarios una herramienta sencilla para consultar datos meteorológicos actualizados. La aplicación mostrará:  

- Un campo de búsqueda donde se podrá ingresar el nombre de una ciudad.  
  - Si la ciudad no se encuentra en la base de datos, se notificará al usuario.  
- En caso de que la ciudad exista, se presentará:  
  - Información del clima en tiempo real.  
  - Predicción meteorológica para el día en curso.  
  - Pronóstico del clima para los próximos días.  

## Implementación de la Instancia EC2 <a name="id3"></a>  

Para poner en funcionamiento la aplicación, se despliega una instancia EC2 en AWS *(Amazon Web Services)*, la cual recibe automáticamente una IP pública para su acceso. En el futuro, se configurará una IP elástica para asignarle un dominio y facilitar su uso.  

**Nota:** Debido a mi experiencia previa en la configuración de instancias en AWS, no entraré en detalles sobre este proceso. El enfoque de este proyecto está en el desarrollo de la aplicación más que en la infraestructura, por lo que la configuración de seguridad y red no es la más óptima para un entorno de producción.  

### Pasos para la creación de la instancia:  

1. Desde la consola de AWS, accedemos a **EC2** y seleccionamos "Lanzar una instancia".  

2. Seleccionamos el sistema operativo que se usará en la máquina virtual:  
   ![image](https://github.com/user-attachments/assets/e4e722e4-1b22-4be1-b2f5-65bee382ead8)  

3. Elegimos el tipo de instancia y generamos un par de claves para la conexión SSH *(en mi caso, utilizo una clave generada por AWS previamente)*.  
   ![image](https://github.com/user-attachments/assets/8bfa0040-2a8a-40ef-a665-1fcbf23c9666)  

4. En la configuración de red, activamos la asignación automática de una IP pública y dejamos el resto de opciones en sus valores predeterminados. Además, habilitamos el tráfico **HTTP** en el grupo de seguridad.  
   ![image](https://github.com/user-attachments/assets/93c0269f-6ef4-4a67-bee8-f0e3e0741c58)  

Una vez creada la instancia, se puede ver su IP pública en la consola de **Instancias**:  
![image](https://github.com/user-attachments/assets/dac8a10c-bfa7-457b-bd10-05f0704779a7)  

### Instalación de dependencias <a name="3.1"></a>  

Para que la aplicación funcione correctamente, es necesario instalar ciertos paquetes. A continuación, se ejecuta un comando para instalar Apache y PHP:  

```bash
sudo apt install -y apache2 php libapache2-mod-php php-cli php-mysql php-json php-gd php-curl php-mbstring php-xml
```  

### Configuración del servidor Apache <a name="3.2"></a>  

Para que el servidor web pueda mostrar la aplicación correctamente, es necesario modificar el archivo de configuración por defecto de Apache y especificar la ruta donde están alojados los archivos del proyecto. Luego, se aplican los cambios reiniciando el servicio:  

```bash
sudo systemctl reload apache2
sudo systemctl restart apache2
```  

![image](https://github.com/user-attachments/assets/3bef8513-0d42-4a57-90d8-9ac85fc09be7)  

## Acceso a la Aplicación <a name="id4"></a>  

Una vez que la instancia está operativa, podemos acceder a la aplicación a través de una IP elástica que hemos asociado a un dominio. En este caso, la dirección asignada es:  

🔗 **eltiempocarloscast.zapto.org**  

![image](https://github.com/user-attachments/assets/d78c4f29-b441-4276-b856-9b0f103d9590)  

Dentro de la página principal, se encuentra un buscador donde se puede introducir el nombre de una ciudad.  

Si la ciudad es válida, se mostrará su información meteorológica:  

![image](https://github.com/user-attachments/assets/1e7a68ba-9a89-4a72-bd30-33b08e672f79)  

La aplicación también permite consultar pronósticos detallados:  

- **Predicción por horas**:  
  ![image](https://github.com/user-attachments/assets/543ef4a0-23b8-4929-9d17-577e7a97279b)  

- **Pronóstico semanal**:  
  ![image](https://github.com/user-attachments/assets/11d6b734-78b1-45be-9686-4d7cea334649)  


## Conclusión

Este proyecto permite a los usuarios consultar fácilmente el clima de cualquier ciudad del mundo mediante una interfaz sencilla y accesible. Utilizando AWS para el despliegue y la API meteorológica para obtener los datos, la aplicación proporciona tanto la previsión horaria como la semanal de manera clara y eficiente. Aunque el enfoque principal está en el desarrollo de la aplicación, la configuración de la infraestructura en AWS también se ha llevado a cabo correctamente para asegurar que todo funcione de manera óptima.
