Backend Plan for inDrive-like Cab Booking App (Laravel)

Overview

The backend for the inDrive-like app will be built using Laravel (PHP) for its robust ecosystem, scalability, and built-in tools like Eloquent ORM, middleware, and Blade templating. The app will support core features like ride booking, fare negotiation, driver management, and an admin panel for oversight. The folder structure will follow Laravel conventions with custom organization for modularity.

Key Features





User Management:





Passenger and driver registration/login (email, phone, OAuth).



Role-based access (passenger, driver, admin).



Profile management (name, photo, preferences).



Ride Booking:





Create ride requests with pickup/drop-off locations.



Propose fare and negotiate with drivers.



Real-time ride tracking (using WebSocket or polling).



Driver Features:





View nearby ride requests and propose counter-offers.



Accept/reject rides and track earnings.



On-the-way mode for en-route pickups.



Admin Panel:





Manage users (passengers/drivers), rides, and payments.



Monitor disputes and generate reports (e.g., earnings, ride stats).



Configure app settings (commission rates, geofencing).



Safety and Communication:





In-app chat/call (via WebSocket or third-party APIs).



SOS button and emergency contact integration.



Driver verification (documents, background checks).



Payment and Earnings:





Cash and digital payments (Stripe/PayPal integration).



Driver earning dashboard with trip history.



Other Services (Scalable):





Courier delivery, intercity travel, and grocery delivery endpoints.



Micro-loan system (future integration).

Technology Stack





Framework: Laravel 11.x (PHP 8.2+).



Database: MySQL (for relational data like users, rides).



Real-Time: Laravel WebSockets or Pusher for ride tracking and chat.



APIs: RESTful APIs for mobile app; Sanctum for authentication.



Frontend (Admin): Laravel Blade with Tailwind CSS for responsive UI.



Queueing: Laravel Queues (Redis) for async tasks (e.g., notifications).



Geolocation: Google Maps API for location services and distance calculation.



Storage: Laravel Storage (local or S3) for driver documents and images.



Testing: PHPUnit and Laravel Dusk for unit and browser testing.

Folder Structure

The structure follows Laravel conventions with custom modules for clarity and scalability.


project_root/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── PermissionController.php
│   │   │   │   └── SettingsController.php
│   │   ├── Middleware/
│   │   │   ├── AdminAuthMiddleware.php
│   │   │   └── RolePermissionMiddleware.php
│   │   └── Requests/
│   │       ├── User/
│   │       │   ├── CreateUserRequest.php
│   │       │   └── UpdateUserRequest.php
│   │       ├── Role/
│   │       │   ├── CreateRoleRequest.php
│   │       │   └── UpdateRoleRequest.php
│   │       └── Settings/
│   │           └── UpdateSettingsRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Permission.php
│   └── Providers/
│       └── AdminServiceProvider.php
│
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── admin/
│   │       ├── dashboard.js
│   │       ├── users.js
│   │       └── settings.js
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   ├── partials/
│       │   │   ├── header.blade.php
│       │   │   ├── sidebar.blade.php
│       │   │   ├── footer.blade.php
│       │   │   └── navbar.blade.php
│       ├── admin/
│       │   ├── dashboard/
│       │   │   └── index.blade.php
│       │   ├── users/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   └── show.blade.php
│       │   ├── roles/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   ├── permissions/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   └── settings/
│       │       └── index.blade.php
│
├── routes/
│   ├── web.php
│   └── admin.php
│
├── public/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── assets/
│       ├── images/
│       ├── fonts/
│       └── icons/
│
├── config/
│   ├── app.php
│   └── admin.php
│
├── database/
│   ├── migrations/
│   │   ├── xxxx_xx_xx_create_users_table.php
│   │   ├── xxxx_xx_xx_create_roles_table.php
│   │   └── xxxx_xx_xx_create_permissions_table.php
│   ├── seeders/
│   │   ├── UserSeeder.php
│   │   ├── RoleSeeder.php
│   │   └── PermissionSeeder.php
│
├── tailwind.config.js
├── package.json
├── vite.config.js
└── README.md