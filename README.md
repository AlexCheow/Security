XAMPP Setup
Installed the latest version of XAMPP on https://www.apachefriends.org/download.html
1) Start XAMPP Control Panel
2) Run Apache and MySQL modules
3) Open your web browser and type "http://localhost/" to ensure XAMPP is running.
4) Download the ZIP folder in GitHub and extract it.
5) Rename the folder to "Security" and put it in your htdocs folder (C:\xampp\htdocs\Security)
6) Open Admin in MySQL modules in XAMPP Control Panel
7) Create a database called "admin"
8) Find the file called "admin.sql" in GitHub and import it into the database "admin".
9) After that run "http://localhost/security/" and you will be able to view the website's login page
10) You can create a new account to log in or you can use an existing user to log in.
Admin Account - Username: admin
    Password: admin123
Staff Account - Username: staff
    Password: staff123

Ngrok Setup
Go to https://download.ngrok.com/windows?tab=download to download the appropriate version for your operating system.
1) Unzip and Run Ngrok.exe
2) Go to "https://ngrok.com/" to sign up for an Ngrok account for Auth Token
3) Go to "Your AuthToken" to get your Auth Token
4) Open Ngrok.exe then enter the command "ngrok authtoken <your_auth_token>"
5) After that, enter this command "ngrok http 80"
6) Then, it will generate a forwarding URL
Example: https://a841-2001-f40-907-4f2c-31e1-1737-d55c-90c2.ngrok-free.app
7) Copy the URL and run it in your browser
8) Then click "Visit Site" button to run your localhost in HTTPS with SSL/TLS Certificate.

For more information about Ngrok refer to this :
https://github.com/AlexCheow/Security/blob/main/Ngrok%20Guide
