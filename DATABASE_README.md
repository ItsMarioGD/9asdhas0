# 🏥 Medi-Agenda AI - Sistema Híbrido de Base de Datos

## 🎯 Características Principales

### 📊 **Sistema Dual de Base de Datos**

Este sistema puede funcionar con **DOS tipos de base de datos**:

#### 1️⃣ **MySQL (Servidor)**
- Para entornos con XAMPP, WAMP, o servidores
- Requiere configuración en `.env`
- Mejor rendimiento para múltiples usuarios
- Ideal para producción

#### 2️⃣ **SQLite (Archivo Local)**
- Base de datos en un solo archivo
- No requiere instalación de servidor
- **100% portátil** - copia la carpeta y funciona
- Ideal para demos o uso personal

---

## 🚀 Cómo Funciona

### **Detección Automática:**

El sistema detecta automáticamente cuál usar:

```
¿Existe archivo .env con credenciales MySQL?
├─ SÍ ─► Intenta conectar a MySQL
│         ├─ Éxito ─► Usa MySQL 🟢
│         └─ Falla ─► Usa SQLite 🟡
└─ NO ─► Usa SQLite directamente 🟡
```

---

## 📦 Instalación

### **Opción 1: MySQL (XAMPP/Servidor)**

1. **Crea la base de datos:**
   ```sql
   CREATE DATABASE medi_agenda;
   ```

2. **Importa el schema:**
   ```bash
   mysql -u root medi_agenda < schema.sql
   ```

3. **Configura `.env`:**
   ```env
   DB_HOST=localhost
   DB_NAME=medi_agenda
   DB_USER=root
   DB_PASS=
   GROQ_API_KEY=tu_api_key_aqui
   ```

4. **Accede:**
   ```
   http://localhost/medi_agenda
   ```

---

### **Opción 2: SQLite (Sin Servidor)**

1. **¡No hagas nada!** 🎉

2. **Simplemente accede:**
   ```
   http://localhost/medi_agenda
   ```

3. **O abre directamente:**
   - Copia la carpeta a cualquier servidor PHP
   - Funciona inmediatamente
   - La base de datos se crea automáticamente en `/data/medi_agenda.db`

---

## 📁 Estructura de Archivos

```
medi_agenda/
├── data/
│   └── medi_agenda.db          ← Base de datos SQLite (se crea automáticamente)
├── db.php                      ← Conexión híbrida
├── db_info.php                 ← Ver qué DB se está usando
├── .env                        ← Configuración MySQL (opcional)
└── [resto de archivos]
```

---

## 🔍 Ver Qué Base de Datos Estás Usando

Accede a:
```
http://localhost/medi_agenda/db_info.php
```

Verás:
- ✅ Tipo de base de datos activa
- 📊 Estadísticas (doctores, pacientes, citas)
- 📥 Botón para descargar DB (si es SQLite)

---

## 🔄 Migrar Entre Bases de Datos

### **De MySQL a SQLite:**

1. Exporta datos de MySQL
2. Elimina o renombra `.env`
3. El sistema usará SQLite automáticamente
4. Importa los datos manualmente (o inicia desde cero)

### **De SQLite a MySQL:**

1. Crea la base MySQL
2. Configura `.env` con credenciales
3. El sistema detectará MySQL automáticamente
4. Migra los datos manualmente si es necesario

---

## 💡 Casos de Uso

### **Usa SQLite cuando:**
- ✅ Desarrollando localmente
- ✅ Haciendo demos
- ✅ Necesitas portabilidad máxima
- ✅ Solo 1-2 usuarios concurrentes
- ✅ Quieres cero configuración

### **Usa MySQL cuando:**
- ✅ Producción con múltiples usuarios
- ✅ Necesitas backups automáticos
- ✅ Servidor ya configurado
- ✅ Mejor rendimiento a escala

---

## 🛠️ Resolución de Problemas

### **"Database connection failed"**
- Verifica que PHP tenga extensión `pdo_sqlite` o `pdo_mysql` habilitada
- Revisa permisos de la carpeta `/data`

### **"Failed to initialize database"**
- Verifica permisos de escritura en `/data`
- Intenta crear la carpeta manualmente

### **"MySQL no conecta"**
- Revisa credenciales en `.env`
- Verifica que MySQL esté corriendo (XAMPP)
- El sistema automáticamente usará SQLite como fallback

---

## 📥 Backup de Datos

### **SQLite:**
```bash
# El archivo está en:
data/medi_agenda.db

# O descárgalo desde:
http://localhost/medi_agenda/db_info.php?download=db
```

### **MySQL:**
```bash
mysqldump -u root medi_agenda > backup.sql
```

---

## 🎉 Ventajas del Sistema Híbrido

1. **✅ Portabilidad Total:** Copia y funciona en cualquier lado
2. **✅ Sin Configuración:** SQLite funciona sin setup
3. **✅ Escalabilidad:** Migra a MySQL cuando crezcas
4. **✅ Desarrollo Fácil:** Empieza con SQLite, produce con MySQL
5. **✅ Backup Simple:** SQLite = 1 archivo para hacer backup

---

## 📞 Soporte

Si tienes problemas, verifica:
1. Extensiones PHP habilitadas (PDO, SQLite, MySQL)
2. Permisos de carpeta `/data`
3. Archivo `.env` correcto (si usas MySQL)

---

**© 2025 Medi-Agenda AI - Sistema Inteligente de Gestión Médica**
