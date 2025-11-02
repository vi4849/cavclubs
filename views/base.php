<head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Shriya, Vivian, Pallavi, & Rakshitha">
    <meta name="description" content="CS 4750 Final Project">
    <meta name="keywords" content="CS 4750, CIO, UVA">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <style>
      .small-form-box {
          width: 350px;                     /* set box width to 350 pixels */
          padding: 30px;                    /* add 30px of space inside the box around the content */
          border: 1px solid #ccc;           /* add a 1px gray border around the box */
          border-radius: 10px;              /* rounds the corners of the box by 10px */
          box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* add a subtle shadow below and around the box */
      }
      .large-form-box {
          width:600px;                     
          padding: 30px;                    /* add 30px of space inside the box around the content */
          border: 1px solid #ccc;           /* add a 1px gray border around the box */
          border-radius: 10px;              /* rounds the corners of the box by 10px */
          box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* add a subtle shadow below and around the box */
      }
      .large-form-box h1,
      .small-form-box h1 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.5rem; /* 1.5x the root font size */
      }
      .form-container {
        display: flex;
        justify-content: center;           /* center the login box horizontally */
        align-items: center;               /* center the login box vertically within the container */
        min-height: calc(100vh - 150px);  /* make container at least the full viewport height minus 150px (for header space) */
        padding-top: 40px;                 /* add extra space at the top (from header or page content) */
      }
    </style>
    <script type="text/javascript">
        // makes alerts disappear after 1.5 seconds
        document.addEventListener("DOMContentLoaded", function() {
            var alertNode = document.querySelector('.alert');
            if (alertNode) {
                var alert = new bootstrap.Alert(alertNode);
                setTimeout(function() {
                    alert.close();
                }, 1500); 
            }
        });
    </script>

    <?php include("header.php") ?> 
</head>