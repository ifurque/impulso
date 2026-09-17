# Diagrama de clases UML

```mermaid
classDiagram
    class User {
        +id
        +name
        +email
        +phone
        +role
        +is_entrepreneur
        +password
        +businesses()
        +ownedBusinesses()
        +inquiries()
    }

    class Business {
        +id
        +name
        +slug
        +category
        +description
        +location
        +appointments_enabled
        +delivery_enabled
        +delivery_radius_km
        +delivery_cost
        +payment_methods_customer
        +payment_methods_business
        +database_components
        +canBeManagedBy(user)
    }

    class Product {
        +id
        +catalog_number
        +name
        +brand
        +model
        +description
        +price
        +quantity
        +unit
        +category
        +is_active
    }

    class Appointment {
        +id
        +appointment_date
        +start_time
        +end_time
        +status
        +confirmation_token
        +management_token
        +notes
    }

    class Inquiry {
        +id
        +subject
        +message
        +response
        +status
        +answered_at
    }

    class Order {
        +id
        +customer_name
        +customer_phone
        +delivery_method
        +delivery_address
        +total
        +payment_method
        +status
    }

    class OrderItem {
        +id
        +product_name
        +unit_price
        +quantity
        +subtotal
    }

    class Post {
        +id
        +title
        +body
        +photo
        +price
        +is_published
        +share_on_social
    }

    class Review {
        +id
        +reviewer_name
        +rating
        +body
        +is_visible
    }

    class Expense {
        +id
        +description
        +amount
        +expense_date
    }

    class Income {
        +id
        +description
        +amount
        +income_date
        +source
    }

    class BusinessAvailabilityHours {
        +id
        +day_of_week
        +opening_time
        +closing_time
        +is_closed
    }

    User "1" --> "0..*" Business : posee
    User "0..*" --> "0..*" Business : participa
    Business "1" --> "0..*" Product
    Business "1" --> "0..*" Appointment
    Business "1" --> "0..*" Inquiry
    Business "1" --> "0..*" Order
    Order "1" --> "0..*" OrderItem
    Business "1" --> "0..*" Post
    Business "1" --> "0..*" Review
    Business "1" --> "0..*" Expense
    Business "1" --> "0..*" Income
    Business "1" --> "0..*" BusinessAvailabilityHours
```

## Observaciones del modelo

- User puede ser dueño o miembro de varios negocios.
- Business centraliza la información del emprendimiento y las reglas de gestión.
- Product y Order son claves para el flujo comercial del negocio.
- Appointment representa la disponibilidad del servicio y la reserva del cliente.
- Inquiry encierra la relación de contacto y soporte.
- Expense e Income permiten una operación financiera básica.

## Relación funcional destacada

El modelo refleja una estructura centrada en Business como entidad principal. Todo el ciclo de operación del emprendimiento gira alrededor de su usuario propietario, sus miembros, la oferta comercial y las transacciones con clientes.
