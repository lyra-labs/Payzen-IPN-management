# PayZen IPN - Instant Payment Notification management in PHP

## Introduction
The code presented here is a PHP & sqlite demonstration implementation of the *PayZen* Instant Payment Notification.



## Contents
The code :
* **ipn.php**, add in the sqlite database each IPN call from Payzen platform.
* **list.php**, displays the IPN from the database using TinyButStrong template engine and Bootstrap.
* **InitScript.php**, used to create the sqlite database.


/classes/  the classes used for IPN manipulation and TinyButStrong ones.  
/database/ the sqlite database.  
/config/   the **key.ini** configuration file.  
/tpl/      TinyButStrong templates.


## What to do first 
1. Install the package, ipn.php must be reacheable from internet. 
2. Call **InitScript.php** to create the ipn database.
3. On your Payzen back-office configure IPN to call ipn.php script.
4. configure the **key.ini** file with your site_id (shop id) TEST and/or PRODUCTION keys,  
   this step isn't mandatory, but the signature calculation will fail without the correct keys.


## The next steps
Perform some payments and check Payzen calls the **ipn.php** scripts. 
You should see the details of each IPN call using **list.php**   
Clic on the "Details" button to see all parameters.  
When "Mutli" button is displayed you can see the details of each payment. 

## Notes
Parameters in **classes/classes.inc.php**
* you can remove obfuscation to email & customers details setting **OBFUSCATION** to **NO**  
* You can change the sqlite database name and location in **DATABASE_FILE** 

Payzen parameter **vads_ext_info_IpnError** 
* if value is **TimeOut** IPN demonstrator will simulate one
* if value is a valid HTTP status code demonstrator will return this status  

