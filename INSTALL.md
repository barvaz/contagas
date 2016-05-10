# ContaGas - INFORMATION AND INSTALLATION

## Information

ContaGas is build and tested on a sandard LAMP server (Linux, Apache2, MySQL, PHP5). It will probably break on Windows.

## Installation

1. Manually create your database.
   You will need to know the database name, user, and password for installation
   purposes.
   (phpMyAdmin or similar are good tools for this)

2. Execute the conf/contagas.sql script using the newlly created database

3. Edit conf/conf.php to set all Database and email parameters

4. Create log directory with writing permissions

5. Upload the contagas repository to your webserver; it is happy to work in a
   subdirectory.

6. Open your browser and go to the root of your Wolf CMS site.
   (e.g. http://www.mysite.com/ if contagas is in the root;
      or http://www.mysite.com/contagas if Wolf is in a subdirectory)

7. Login with the admin username/password. (admin/admin)
   You should change your admin passsword to something private and secure!

## Update

1. grab the new code

2. Execute sql updates you find in sql/ directory
