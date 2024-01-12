<?php
session_start();

$connection = require __DIR__ . "/../database_connection.php";

if (isset($_SESSION['user_id'])) { //verifico se l'utente è settato e in tal caso accedo ai parametri sottostanti 
    
    $query = "SELECT * FROM utenti WHERE id = :id_utente";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id_utente', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $result['username'];


    $currentTime = date("Y-m-d H:i:s");
    $id_utente = $result['id'];


    $query = "SELECT * FROM post ORDER BY data_creazione DESC";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: array();
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

        <div class="box">
            <input type="text" id="searchInput" placeholder="Type to search..">
            <a href="#" onclick="search()">
                <div class="icon">
                    <i id="searchIcon" class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
                </div>
            </a>
        </div>
    </header>

    <div class="container">
        <div class="panel panel-default" style="margin-top:50px">
            <div class="panel-body">
                <h3>Animanga forum</h3>
                <form name="frm" onsubmit="return CreatePost();">
                    <input type="hidden" id="commentid" name="Pcommentid" value="0">
                    <div class="form-group">
                        <label for="usr">Welcome <span id="welcomeUsername"><?= htmlspecialchars($username) ?></span>, write in the box down below to share your thoughts! <hr></label>
                    </div>
                    <div class="form-group">
                        <label for="comment">Write your question:</label>
                        <textarea class="form-control" rows="5" name="msg" id="msg" required onkeypress="handleKeyPress(event)"></textarea>
                    </div>
                    <button type="submit" name = "save" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Recent questions</h4>
                <table class="table" id="MyTable">
                    <tbody id="record">
                        <?php foreach ($posts as $post) : ?>
                            <div class="post-box">
                                <p><strong>Username:</strong> <?= htmlspecialchars($post['username']) ?></p>
                                <strong>Post:</strong> <p class="post-text" style="display: inline"><?= htmlspecialchars($post['posts'])?></p>
                                <p><strong>Data Creazione:</strong> <?= htmlspecialchars($post['data_creazione']) ?></p>

                                <button class="joinconv-button" onclick="joinConversation('<?= htmlspecialchars($post['username']) ?>')">Join the Conversation</button>

                                <?php
                                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['utente_id']) :
                                    ?>
                                    <form method="post" style="display: inline;">
                                        <button type="button" name="remove" class="remove-button" onclick="DeletePost(<?= htmlspecialchars($post['id']) ?>)">Remove</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <p id="ajaxres"></p>


    <script>
        // Funzione per gestire l'invio del modulo quando si preme il tasto "Invio"
        function handleKeyPress(event) {
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault();
                document.getElementById('butsave').click();
            }
        }
    </script>


    <script>
        // Funzione per gestire l'invio del modulo quando si preme il tasto "Invio"
        window.onload = bindEvents;

        function bindEvents() {
            document.getElementById("POST").addEventListener("click", CreatePost);
            document.getElementById("msg").addEventListener("keydown", handleKeyPress);
        }

        function handleKeyPress(event) {
        if (event.keyCode === 13 && !event.shiftKey) {
            event.preventDefault();
            CreatePost();
        }}


        function incrementPostCount(incrementValue) {
        var oReq = new XMLHttpRequest();
        oReq.onload = function () {
            if (oReq.status === 200 && oReq.readyState === 4) {
                try {
                    var response = JSON.parse(oReq.responseText);
                    
                    console.log(response); //log di console per fare si che almeno capisco se qualcosa va storto o meno
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                    
                }
            } else {
                console.error("Error updating post_count:", oReq.status, oReq.responseText);
                o
            }
        };

        oReq.open("PUT", "api.php/utenti/" + <?php echo $id_utente?>, true);
        oReq.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

        // Invia un oggetto JSON con il campo post_count per il metodo PUT
        oReq.send(JSON.stringify({ post_count: incrementValue }));
    }

    function CreatePost() {
        var data = {};
        data.posts = document.getElementById("msg").value;
        data.username = "<?php echo $username?>";
        data.utente_id = "<?php echo $id_utente?>";
        data.data_creazione = "<?php echo $currentTime?>";

        var jsondata = JSON.stringify(data);

        var oReq = new XMLHttpRequest();
        oReq.onload = function () {
            if (oReq.status === 200 && oReq.readyState === 4) {
                try {
                    var response = JSON.parse(oReq.responseText);

                    // Incremento il contatore post_count
                    incrementPostCount(1);

                    location.reload(); // Ricarica la pagina dopo l'invio del post
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                    
                }
            }
        };

        oReq.open("POST", "api.php/post", true);
        oReq.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        oReq.send(jsondata);

        return false; // Impedisce il comportamento predefinito del modulo (ricaricamento della pagina)
    }

    function DeletePost(post_id) {
        var oReq = new XMLHttpRequest();
        console.log(oReq.responseText);
        
        oReq.onload = function () {
            if (oReq.status === 200 && oReq.readyState === 4) {
                // Decrementa il contatore post_count
                incrementPostCount(-1);

                location.reload(); // Ricarica la pagina dopo la rimozione del post
            } else {
                console.log(oReq.responseText);
                document.getElementById("ajaxres").innerHTML = "Errore durante l'eliminazione dell'articolo.";
            }
        };

        oReq.open("DELETE", "api.php/post/" + post_id, true);
        oReq.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        oReq.send();
    }

    </script>

    <script>
        function search() {
            var searchTerm = document.getElementById("searchInput").value;
            highlightSearchTerm(searchTerm);
        }

        function highlightSearchTerm(searchTerm) {
            var postElements = document.querySelectorAll('.post-box');

            postElements.forEach(function(postElement) {
                var postTextElement = postElement.querySelector('.post-text');
                var postText = postTextElement.innerText;
                var highlightedText = postText.replace(new RegExp(searchTerm, "gi"), match => `<span style="background-color: #38b0ff;">${match}</span>`);
                postTextElement.innerHTML = highlightedText;
            });
        }

        function clearHighlight() {
            var postElements = document.querySelectorAll('.post-box');

            postElements.forEach(function(postElement) {
                var postTextElement = postElement.querySelector('.post-text');
                postTextElement.innerHTML = postTextElement.innerText;
            });

            document.getElementById('searchInput').value = '';
        }

        function handleSearch(event) {
            if (event.key === "Enter") {
                search();
            }
        }

        function handleIconClick() {
            search();
        }

        function handleOutsideClick(event) {
            if (!event.target.closest('.box')) {
                clearHighlight();
            }
        }

        document.getElementById('searchInput').addEventListener('keyup', handleSearch);
        document.getElementById('searchIcon').addEventListener('click', handleIconClick);
        document.addEventListener('click', handleOutsideClick);
    </script>

    <script>
        function joinConversation(username) {
            const url = 'Conversation.php?username=' + encodeURIComponent(username);
            window.location.href = url;
        }
    </script>

    <script>
        var user = {
            username: <?= json_encode($_SESSION['username']) ?>
        };
        document.getElementById("welcomeUsername").innerText = user.username;
    </script>

    <script>
        document.getElementById('logo').addEventListener('click', function () {
            <?php if(isset($_SESSION['user_id'])) : ?>
                window.location.href = '../After_register.php';
            <?php else : ?>
                window.location.href = '../Homepage.php';
            <?php endif; ?>
        });
    </script>
</body>
</html>
