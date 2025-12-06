# CavClubs
CavClubs is a centralized website where UVA students can easily stay updated on club and CIO events. Currently, students must visit individual websites, social media pages, or rely on personal connections to learn about meetings, which makes it difficult to stay involved or explore new organizations. By consolidating this information into a single platform, our website provides a reliable space for students to browse events and learn more about various organizations across Grounds.
<br /> <br />
Website: https://cs4750db-476016.uk.r.appspot.com/

# Getting Started
**Clone the repository**

``git clone https://github.com/vi4849/cavclubs.git``

**Add config.php**

In the base directory, add config.php file with the following contents. Replace username and password with the correct credentials.

```
<?php 
return [
    'LOCAL_IP_HOST' => '127.0.0.1', ////127.0.0.1 if on eduroam, or 34.11.54. for host if on a wifi without a firewall that blocks public mysql connection
    'CLOUD_SQL_HOST' => 'cs4750db-476016:us-east4:cs4750',
    'DB_NAME' => 'CavClubs',
    'DB_USER' => '[username]',
    'DB_PASS' => '[password]'
];
```

**Create an authorized connection on the Google Cloud Console**
- Navigate to https://console.cloud.google.com/sql/instances/cs4750/connections/networking?project=cs4750db-476016
- Under Authorized Networks, click Add Network and Use My IP Address
- For a visual walkthrough, see: https://www.cs.virginia.edu/~up3f/cs4750/supplement/connecting-PHP-DB.html#section2

**Install Google Cloud SDK and run ``gcloud init``**
- See: https://www.cs.virginia.edu/~up3f/cs4750/supplement/php-deployment-GCP.html#section2
- Set the default project as cs4750db-476016 and the default region / zone to us-east4-a

# Deploying on GCP

- In the terminal, when in the project folder, run ``gcloud app deploy``
  - See https://www.cs.virginia.edu/~up3f/cs4750/supplement/php-deploymewnt-GCP.html#section3
- Navigate to https://cs4750db-476016.uk.r.appspot.com/ in your browser


# Deploying Locally

- Set up Google Cloud SQL Auth Proxy (necessary if on eduroam, as eduroam has a firewall that blocks connecting to the MySQL database directly)
  - Installation link: https://cloud.google.com/sql/docs/mysql/sql-proxy#windows-64-bit 
- Navigate to Google Cloud CLI directory
  - ``cd C:\Users\vivia\AppData\Local\Google``
- Start the proxy
  - ``.\cloud-sql-proxy.exe cs4750db-476016:us-east4:cs4750 --port 3306``
  - <img width="1122" height="94" alt="googlecloud" src="https://github.com/user-attachments/assets/8ef13449-7bae-48d6-8fec-ed617ddbf41f" />
- Start MySQL and Apache on XAMPP
  - Download XAMPP if not already installed
  - https://www.apachefriends.org/download.html
  - <img width="1038" height="236" alt="xampp" src="https://github.com/user-attachments/assets/428b061c-d5e1-49ed-a634-402700777089" />
- In a browser, navigate to http://localhost/cavclubs/
