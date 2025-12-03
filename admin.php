<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//                                     
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">  
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Admin Panel - KTU Magic</title>     
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">     
    <style>         
        body { background-color: #f8f9fa; }         
        .navbar { margin-bottom: 20px; }         
        .btn-custom { margin: 5px; }         
        .container { margin-top: 20px; }         
        .btn-home { background-color: #007bff; color: white; }         
        .btn-home:hover { background-color: #0056b3; }         
        .btn-admin { background-color: #28a745; color: white; }         
        .btn-admin:hover { background-color: #218838; }         
        .btn-view { background-color: #ffc107; color: black; }         
        .btn-view:hover { background-color: #e0a800; }         
        .btn-delete { background-color: #dc3545; color: white; }         
        .btn-delete:hover { background-color: #c82333; }         
        .btn-add { background-color: #17a2b8; color: white; }         
        .btn-add:hover { background-color: #138496; }     
    </style> 
</head>  


<body>      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">KTU Magic Admin</a>
        <a href="logout.php" class="btn btn-danger ml-auto">Logout</a>
    </nav>
 
       
    
    <div class="container">         
        <h2 class="mb-4">Admin Dashboard</h2>         
        <p>Welcome to the KTU Magic admin panel.</p>         
        <hr><br>         
        <div class="row">             
            <div class="col-md-12">       

                <h3>notes</h3>                 
                <a href="admin_notes.php" class="btn btn-custom btn-view">Notes</a>
                <hr><br>                 
            </div>         
        </div>     
    </div>          
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>     
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>     
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
</body>  
</html>