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
    <title>AniManga Forum</title>
    <link rel="stylesheet" href="Forum_style.css">
    <script src="https://kit.fontawesome.com/186eb98a62.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
      <h2><span id="logo">AniManga Forum</span></h2>


      <nav class="navigation">
        <a href="#">Anime</a>
        <a href="#">Manga</a>
      </nav>

    </header>


    <!-- Modal 
    <div id="ReplyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
      
          <!-- Modal content
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Reply Question</h4>
            </div>
            <div class="modal-body">
              <form name="frm1" method="post">
                  <input type="hidden" id="commentid" name="Rcommentid">
                  <div class="form-group">
                    <label for="usr">Write your name:</label>
                    <input type="text" class="form-control" name="Rname" required>
                  </div>
                  <div class="form-group">
                    <label for="comment">Write your reply:</label>
                    <textarea class="form-control" rows="5" name="Rmsg" required></textarea>
                  </div>
                   <input type="button" id="btnreply" name="btnreply" class="btn btn-primary" value="Reply">
            </form>
            </div>
          </div>
      
        </div>
    </div> -->


    <div class="container">

        <div class="panel panel-default" style="margin-top:50px">
          <div class="panel-body">
            <h3>Animanga forum</h3>
            
            <form name="frm" method="post">
                <input type="hidden" id="commentid" name="Pcommentid" value="0">
            <div class="form-group">
                <label for="usr">Welcome <span id="welcomeUsername"></span>, write in the box down below to share your thoughts! <hr></label>
            </div>
            <div class="form-group">
              <label for="comment">Write your question:</label>
              <textarea class="form-control" rows="5" name="msg" required></textarea>
            </div>
             <input type="button" id="butsave" name="save" class="btn btn-primary" value="Send">
          </form>
          </div>
        </div>
          
        
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Recent questions</h4>           
            <table class="table" id="MyTable" style="background-color: #edfafa; border:0px;border-radius:10px">
              <tbody id="record"></tbody>
            </table>
          </div>
        </div>


      <script>
        // Aggiorna il messaggio di benvenuto con l'username dell'utente
          var user = {
          username: <?= json_encode($username) ?> // Utilizza json_encode per gestire correttamente le stringhe JSON
        };
          document.getElementById("welcomeUsername").innerText = user.username;
        </script>




    </div>


  <script>
    // In caso di clic su animanga
    document.getElementById('logo').addEventListener('click', function () {
    window.location.href = '../Homepage.php';});
  </script>
</body>
</html>
