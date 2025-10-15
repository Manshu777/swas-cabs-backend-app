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


heelo bro!!!


sabsa phala migrat bro 
Bro Ham Bar bar Kyu Regiester kara vaya or driver or user dono ka reg alag alag nhai ha 
dno ka ik hi h phla nomal user na acc create kia frr os user ko agar diver bna h 
vo apni file update kara ga isa kya hoga otp verification ik hi barr hoga 2nd tine sirf doics verify hoya ga 
 to bro extra code and exta route delete karr da frr  mana curl bnaa h test karr bass simpe 
well stuctred karrr dio yarr ples user s verify ka time jada info nhai laa rha ho ma kyukki bro need nhai h bki ki
jab vo otp verify kara ga tab details fetech karka bhai store karr rha hio ma


curl -X POST http://localhost:8000/api/v1/user/genrate-otp \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-d '{
  "aadhaar_number": "123412341234"
}'



curl -X POST http://127.0.0.1:8000/api/v1/user/verify-otp \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-d '{
  "reference_id": "TXN123456789",
  "otp": "123456",
  "email": "user@gmail.com",
  "password": "User@123"
}'




curl -X POST "http://your-domain.com/api/become-driver" \
-H "Authorization: Bearer YOUR_USER_TOKEN" \
-F "license_number=DL1234567890" \
-F "license_image=@/path/to/license.jpg" \
-F "vehicle_rc_number=RC123456" \
-F "vehicle_rc_image=@/path/to/rc.jpg" \
-F "insurance_number=INS123456" \
-F "insurance_image=@/path/to/insurance.jpg" \
-F "police_verification_image=@/path/to/police.jpg" \
-F "brand=Toyota" \
-F "model=Corolla" \
-F "vehicle_type=Car" \
-F "license_plate=MH12AB1234" \
-F "year=2020" \
-F "color=White"











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