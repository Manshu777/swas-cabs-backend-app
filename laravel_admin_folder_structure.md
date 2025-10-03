
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