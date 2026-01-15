# Guía de Pruebas - Sistema Multi-Tab de Sesiones

Este documento describe cómo probar la nueva funcionalidad de gestión de sesiones multi-tab de Clinivet.

## ¿Qué cambió?

El sistema ahora permite una mejor experiencia al usar múltiples pestañas:

1. **Evita auto-declaración**: Las pestañas NO se auto-declaran activas al cargar
2. **Comunicación inteligente**: Una nueva pestaña envía `whois-active` para preguntar quién está activo
3. **Respuesta de pestañas activas**: Las pestañas logueadas y visibles responden con `active`
4. **Modal interactivo**: Se muestra un modal que permite elegir qué pestaña usar
5. **Transferencia de sesión**: Cuando decides usar la sesión en una pestaña, las otras se cierran silenciosamente

## Instrucciones de Prueba

### Escenario 1: Tab A Activa → Tab B Nueva

1. **Abre Tab A** (Ctrl+T o Cmd+T)
   - Navega a `http://localhost/clinivet/frontend/public/` (o tu URL local)
   - Haz clic en "Iniciar sesión"
   - Completa el formulario y inicia sesión

2. **Deja Tab A en el home/activa**
   - Tab A debe mostrar tu nombre de usuario en la esquina superior
   - Deja esta pestaña visible y activa

3. **Abre Tab B** (Ctrl+T o Cmd+T)
   - En la misma ventana, abre otra pestaña
   - Navega a la misma URL: `http://localhost/clinivet/frontend/public/`
   - **ESPERA ~300ms** (el sistema espera este tiempo antes de preguntar)

### Resultado Esperado:

**En Tab A:**
- Debe permanecer como estaba (logueada y activa)
- Verá el evento en la consola del navegador: `[Clinivet] Respondiendo como tab activa`

**En Tab B:**
- Después de ~300ms, debería mostrar un **modal** que dice:
  - "Sesión activa detectada"
  - "Ya tienes una sesión activa en otra pestaña. ¿Quieres usar la sesión en esta pestaña?"
- Dos botones:
  - "Mantener otra pestaña" (cancela el modal)
  - "Usar sesión aquí" (transfiere la sesión)

---

### Escenario 2: Transferencia de Sesión

Continuando desde el Escenario 1:

1. **En Tab B**, haz clic en **"Usar sesión aquí"**
   - El sistema enviará un mensaje `take-session` a las otras pestañas

### Resultado Esperado:

**En Tab A:**
- Aproximadamente 1-2 segundos después de tu clic:
  - La pestaña se recargará automáticamente
  - Verá: "Iniciar sesión" (botón)
  - Tu sesión habrá sido cerrada silenciosamente
  - NO verás ningún modal ni mensaje de error (logout silencioso)

**En Tab B:**
- Se recargará automáticamente
- Después de recargar, verá tu nombre de usuario (sesión transferida correctamente)
- Permanecerá logueada

---

### Escenario 3: Cancelar Modal (sin transferencia)

1. **Abre Tab A** y **Tab B** como en el Escenario 1

2. **En Tab B**, cuando aparezca el modal, haz clic en **"Mantener otra pestaña"**

### Resultado Esperado:

- El modal desaparece
- Tab A: Permanece como estaba (logueada)
- Tab B: Permanece cargada pero sin sesión (no logueada)
- Verás botón "Iniciar sesión" en Tab B

---

### Escenario 4: Tab A sin Sesión (o cerrada)

1. **Abre Tab A** SIN iniciar sesión
2. **Abre Tab B** sin iniciar sesión
3. Ambas cargan normalmente sin mostrar modal

### Resultado Esperado:

- Ambas pestañas muestran el botón "Iniciar sesión"
- No hay comunicación entre pestañas (no hay sesión activa)

---

### Escenario 5: Cambio de Visibilidad

1. **Abre Tab A** y inicia sesión (deja activa/visible)
2. **Abre Tab B** (sin iniciar sesión)
3. **Minimiza o oculta Tab A**
4. **Haz clic en Tab B** para hacerla visible
5. **En Tab B**, recarga la página (F5)

### Resultado Esperado:

- Tab B debería preguntar de nuevo por una sesión activa (ya que Tab A estaba oculta)
- Si Tab A se vuelve visible después, Tab B podría mostrar el modal

---

## Cómo Verificar en la Consola del Navegador

1. En cualquier pestaña, presiona **F12** para abrir Herramientas de Desarrollador
2. Ve a la pestaña **Console**
3. Deberías ver mensajes como:
   - `[Clinivet] Tab ID: tab_xxxxx_xxxxx`
   - `[Clinivet] Respondiendo como tab activa`
   - `[Clinivet] Otro tab requiere sesión - mostrando modal`

---

## Solución de Problemas

### El modal no aparece en Tab B

**Posibles causas:**
- Tab A está oculta o sin sesión → verifica que Tab A esté activa y logueada
- El tiempo de espera fue muy corto → asegúrate de esperar ~500ms después de cargar Tab B
- localStorage está deshabilitado en el navegador

**Solución:**
- Recarga Tab B (F5)
- Asegúrate de que Tab A tenga tu nombre en la esquina superior
- Comprueba que localStorage esté habilitado en Configuración → Privacidad

### Tab A no se recarga después de "Usar sesión aquí"

**Posibles causas:**
- Timeout en la comunicación entre pestañas
- CORS o problemas de permisos

**Solución:**
- Recarga manualmente Tab A (F5)
- Verifica que no haya errores en la consola del navegador (F12)

### Ambas pestañas quedan logueadas

**Esto NO debe pasar.** Si sucede:
- Abre la Consola (F12)
- Busca errores o mensajes de advertencia
- Verifica que el archivo `main.php` fue guardado correctamente

---

## Cambios Técnicos Realizados

### 1. **main.php** (Lógica JavaScript)
- Sistema de eventos con localStorage
- Mensajes: `whois-active`, `active`, `take-session`, `session-taken`
- Modal con Bootstrap para seleccionar pestaña
- Logout silencioso vía fetch

### 2. **AuthController.php** 
- Método `logout()` mejorado
- Soporta parámetro `silent` para logout JSON
- Retorna `{"ok": true}` para requests AJAX

---

## Notas Importantes

- La comunicación entre pestañas usa **localStorage**, que está disponible en todos los navegadores modernos
- El sistema respeta la **visibilidad** de las pestañas (tabs ocultas no responden a `whois-active`)
- Si inicias sesión en múltiples navegadores/máquinas, no hay conflicto (localStorage es por dominio)
- El **logout silencioso** no muestra ningún mensaje (por diseño - es transparente para el usuario)

---

## ¿Necesitas ayuda?

Si algo no funciona como se espera:
1. Abre la Consola (F12)
2. Busca mensajes de error
3. Verifica que localStorage esté habilitado
4. Intenta en otro navegador o en modo incógnito
5. Borra el sessionStorage (ejecuta en la consola: `sessionStorage.clear()`)
