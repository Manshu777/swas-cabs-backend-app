Recommended Libraries

Laravel Sanctum:

Used for API authentication, already included in the User model.


Spatie Laravel Permission:

Enhance role management beyond the role field (e.g., add admin role).
Install: composer require spatie/laravel-permission


Pusher or Laravel WebSockets:

Enable real-time notifications for ride updates and SOS alerts.
Install: composer require pusher/pusher-php-server or composer require beyondcode/laravel-websockets


Google Maps API:

Calculate distances, fares, and validate locations.
Use for real-time driver tracking.




////----- Vivek Bhai!!! -------////////
1 Bro Phala Kaam h tara ki Mana FolderStucrue or user ka table New Kia ha To apna db delete karr dio frr migrate karrio , 
2 mana koch models bhi m bhi changs kia h phala check karr ka lana migrate sa phala 
3 Api ka Kaam Api ka controller m hi hoga or route bhi vhai bana ga 
4 moja Curl Comand ko yha md file ma dalo bna ka or sara api test bhi karo userregiester or driver abhi ka lia otp verification nhai chahiya ha 
5 sirf abhi ka lia main goal ha user or driver regiester bass 
















Admin Features Overview
The admin panel should provide the following features to manage the cab booking app:

User Management:

View, create, update, and delete users (passengers and drivers).
Toggle user roles (passenger to driver after document approval).
Activate/deactivate user accounts.
View user profiles, including emergency contacts and ratings.


Ride Management:

View all rides (pending, accepted, in_progress, completed, cancelled).
Filter rides by status, user, or driver.
Cancel or reassign rides.
View ride details (pickup, dropoff, fare, etc.).


Driver Document Verification:

View and approve/reject driver documents (RiderDocument).
Provide rejection reasons.
Update user role to driver upon document approval.


Vehicle Management:

View, edit, or delete driver vehicles (VehicleDetail).
Approve/reject vehicles based on details.


SOS Alert Monitoring:

Real-time dashboard for SOS alerts with location mapping.
Notify emergency contacts or authorities.
Update alert status (e.g., resolved, pending).


Rating Management:

View ratings and reviews for rides.
Moderate reviews (e.g., remove inappropriate content).
Analyze driver performance based on ratings.


Analytics Dashboard:

Total rides, revenue, and user statistics.
Driver performance metrics (e.g., average rating, rides completed).
Daily/weekly/monthly ride trends.


Payment Management:

View transaction history for rides.
Handle refunds or disputes.
Integrate with payment gateways (e.g., Stripe, Razorpay).


Notifications:

Send manual notifications to users or drivers.
Configure automated notifications for ride updates or SOS alerts.


Settings:

Configure fare calculation rules (e.g., per km rate).
Manage app settings (e.g., supported languages, emergency contact protocols).