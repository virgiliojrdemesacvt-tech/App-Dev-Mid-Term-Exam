CAR RENTAL - One-to-Many (ONE CUSTOMER has MANY RENTALS) with PDO
+ USER MODULE: Register / Login / Logout + "who added / who last updated" tracking

SETUP
1. Open XAMPP Control Panel and START Apache and MySQL.
2. Put this folder named "carrental2" inside C:\xampp\htdocs\
   Final path: C:\xampp\htdocs\carrental2\index.php
3. Open: http://localhost/carrental2/index.php
   (You will be sent to the login page. Click "Register here" to create an account.)

The database (carrental_db) and tables (users, customers, rentals) are created automatically.
If you already had customers / rentals data, the new columns (added_by, last_updated_by,
last_updated) are added automatically and your old data is kept.
Do NOT import any .php file into phpMyAdmin.

FILES ADDED
  register.php, login.php, logout.php, core/auth.php
FILES CHANGED
  core/dbConfig.php, core/models.php, core/handleForms.php, index.php, viewrentals.php,
  editcustomer.php, deletecustomer.php, editrental.php, deleterental.php, styles.css
