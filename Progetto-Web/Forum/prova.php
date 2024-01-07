<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $id_utente = $_SESSION['user_id'];
    $connection = require __DIR__ . "/../database_connection.php";
    $query = "SELECT * FROM utenti WHERE id = :id_utente";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id_utente', $id_utente, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $result['username'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Forum_style.css">
    <title>Document</title>
</head>
<body>


    <!-- Modal
    <div id="ReplyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
      
          <!-- Modal content
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Reply Box</h4>
            </div>
            <div class="modal-body">
              <form name="frm1" method="post">
                  <input type="hidden" id="commentid" name="Rcommentid">
                  <div class="form-group">
                    <label for="usr">Write your reply down there , <span id="welcomeUsername"></span>!<hr></label>
                  </div>
                  <div class="form-group">
                    
                    <label for="comment">Write your reply:</label>
                    
                    <div><textarea class="form-reply" rows="5" name="Rmsg" required></textarea></div>
                    
                  </div>
                   <input type="button" id="btnreply" name="btnreply" class="btn btn-primary" value="Reply">
            </form>
            </div>
          </div>
      
        </div> -->


        <script>
        // Aggiorna il messaggio di benvenuto con l'username dell'utente
          var user = {
          username: <?= json_encode($username) ?> // Utilizza json_encode per gestire correttamente le stringhe JSON
        };
          document.getElementById("welcomeUsername").innerText = user.username;
        </script>




    </div> 

    
</body>
</html>