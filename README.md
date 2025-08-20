# SendMailByCategory

A Kanboard plugin that sends emails to specific recipients when tasks are moved to a target column (e.g. Finalizado), based on the task category.  
Un plugin para Kanboard que envía correos electrónicos a destinatarios específicos cuando las tareas se mueven a una columna destino (por ejemplo Finalizado), en función de la categoría asignada a la tarea.

---

## ✨ Features / Funcionalidades
- Triggered when a task is moved to a specific column.  
- Checks the category of the task.  
- Sends email notifications to one or multiple recipients depending on that category.  
- Recipients are configurable inside the plugin code ($mapByName or $mapById arrays).  
- Works independently of user notification settings → always sends to the defined addresses.  

---

## 📦 Installation / Instalación
1. Download or clone this repository into your Kanboard `plugins/` directory.  
   Descarga o clona este repositorio dentro del directorio `plugins/` de Kanboard.  

   cd plugins/  
   git clone https://github.com/your-user/SendMailByCategory.git  

2. Ensure the folder structure is correct.  
   Asegúrate de que la estructura de carpetas quede así:  

   plugins/  
     SendMailByCategory/  
       Plugin.php  
       Action/  
         SendMailOnFinalizedByCategory.php  
       README.md  

3. Clear Kanboard cache: remove everything inside `data/cache/`.  
   Limpia la caché de Kanboard: borra todo el contenido de `data/cache/`.  

4. Go to Settings → Plugins and verify SendMailByCategory appears.  
   Ve a Ajustes → Plugins y verifica que aparezca SendMailByCategory.  

---

## ⚙️ Configuration / Configuración
1. In your project, go to Automatic Actions → New Action.  
   En tu proyecto, entra en Acciones automáticas → Nueva acción.  

2. Select:  
   - **Event / Evento:** Move a task to another column  
   - **Action / Acción:** Send email to recipients based on task category when moved to a specific column  

3. Set the **Target column name** (e.g. Finalizado).  
   Define el **nombre de la columna destino** (por ejemplo Finalizado).  

---

## 📝 Mapping categories to recipients / Mapeo de categorías a destinatarios
Edit `Action/SendMailOnFinalizedByCategory.php`:  

$mapByName = [  
    'Compras'   => ['compras@empresa.com'],  
    'Dirección' => ['direccion@empresa.com', 'ceo@empresa.com'],  
    'Sistemas'  => ['sistemas@empresa.com'],  
];  

$mapById = [  
    // Example / Ejemplo:  
    // 12 => ['otro@empresa.com'],  
];  

---

## 📨 Email Requirements / Requisitos de correo
- Kanboard must be configured to send emails (SMTP in config.php).  
- Kanboard debe estar configurado para enviar correos (SMTP en config.php).  

- The constant KANBOARD_URL or application_url should be defined for correct task links.  
- La constante KANBOARD_URL o application_url debe estar definida para que los links de tarea sean correctos.  

---

## 📋 Example Email / Ejemplo de correo
Subject: [My Project] Tarea finalizada por categoría: Compras — #42 Facturas Marzo  

Proyecto: My Project  
Tarea: #42 — Facturas Marzo  
Columna: Finalizado  
Categoría: Compras  
Asignado a: Juan Benitez  

Abrir tarea: https://kanboard.mycompany.com/?controller=TaskViewController&action=show&task_id=42  

---

## 🛠 Compatibility / Compatibilidad
- Kanboard >= 1.2.0  
- PHP >= 7.2  

---

## 📄 License / Licencia
Released under the MIT License.  
Publicado bajo la Licencia MIT.  

---

## 🤝 Contributing / Contribuir
Feel free to fork this repository and submit pull requests.  
Puedes hacer fork del repositorio y enviar pull requests con mejoras.  
