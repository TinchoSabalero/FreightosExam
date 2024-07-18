Requisitos:
- Tener instalado Docker y Docker Compose.

Pasos a seguir:
- Clonar el repositorio con el comando: git clone https://github.com/TinchoSabalero/FreightosExam.git
- Ejecutar el comando: cd FreightosExam/laravel/
- Levantar el servidor con el comando: docker compose up -d --build
- Dar permisos con el comando: docker compose exec phpmyadmin chmod 777 /sessions
- Ejecutar comando: docker compose exec php bash
- Ejecutar comando: chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
- Ejecutar comando: chmod -R 775 /var/www/storage /var/www/bootstrap/cache
- Ejecutar comando: composer setup
- Importar la db en el phpmyadmin: http://localhost:8080/
- Usar user: root y password: root, para usar el phpmyadmin

Endpoints:
    Ejecutar en postman preferentemente.

-Token CSRF:
Method: GET
URL: http://localhost/csrf-token

- Lista de clases: 
Method: GET
URL: http://localhost/classrooms

- Insertar Reserva: 
Method: POST
URL: http://localhost/newBooking
Headers: Key X-CSRF-TOKEN, Value <consultar previamente>
Body:
{
    "classroom_name": "<Nombre de la clase, ejemplo: Clase A>",
    "booking_date": "<Fecha de la resserva, ejemplo: 2024-07-18 11:00:00>"
}

- Eliminar Reserva: 
Method: DELETE
URL: http://localhost/removeBooking/<ID RESERVA>
Headers: Key X-CSRF-TOKEN, Value <consultar previamente>
