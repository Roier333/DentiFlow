# Configuracion de DentiFlow en Railway

URL publica:

`https://dentiflow-production-ab21.up.railway.app`

## Variables del servicio web

Copiar en **Railway > servicio DentiFlow > Variables** los valores de
`.env.railway.example`. `APP_KEY` debe contener una clave generada una sola vez
con `php artisan key:generate --show` y no debe cambiarse en cada despliegue.

No crear la variable `SESSION_DOMAIN`. La cookie debe quedar limitada
automaticamente al dominio publico de Railway. Usar el texto `"null"` como
dominio puede impedir que algunos navegadores conserven la sesion.

Para que las sesiones sobrevivan reinicios y funcionen de forma consistente en
mas de una instancia, usar `SESSION_DRIVER=database`. La migracion incluida en
el proyecto ya crea la tabla `sessions`.

Las variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y
`DB_PASSWORD` deben referenciar el servicio MySQL de Railway, no `127.0.0.1`.

Despues de cambiar las variables, desplegar nuevamente y ejecutar en el proceso
de despliegue:

```shell
php artisan migrate --force
php artisan optimize:clear
```

## Sesiones simultaneas

Cada navegador o dispositivo recibe una cookie de sesion independiente. Un
doctor, un asistente y un administrador pueden permanecer conectados al mismo
tiempo desde dispositivos distintos. Para probar dos cuentas en un solo equipo,
usar navegadores distintos o una ventana privada; dos pestanas normales del
mismo navegador comparten la misma sesion por diseno.

## Datos que no deben publicarse

`APP_KEY`, `DB_URL`, `DB_PASSWORD` y las claves de proveedores de correo son
secretos. Deben existir solo en Railway. Si alguno se comparte accidentalmente,
hay que rotarlo desde el proveedor correspondiente.
