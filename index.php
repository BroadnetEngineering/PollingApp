<?php
namespace App;
// Composer
require __DIR__.'/vendor/autoload.php';

// Load the environment variables
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

// this is for debugging purposes.
function dd($var){
    echo(var_export($var));
    die();
}

$db = DB::getInstance();
//$con = $db->getConnection();
//dd($con);

$all_polls = $db->query('SELECT * from polls');
//$result = $all_polls->execute();
// dd($all_polls->fetchAll());
$poller = new Poll;
$all_polls = $poller->all();
//$poll->load(1);
//dd($all_polls);

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- javascript libs -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <title>Broadnet Demo</title>
  </head>
  <body>
    <h1>Broadnet Demo</h1>

    <div class="container">
        <div class="row">
            <div class="col">
                <h3>All Polls:</h3>
                <button id="new_poll_button" class="btn btn-info">Create a new Poll</button>
                <table class="table" id="current_polls">
                    
                </table>

            </div>
        </div>
    </div>

    <?php include 'forms/new_poll_form.php'; ?>

    <script type="text/javascript">
        (function($) {
            
            showCurrentPolls();

            function showCurrentPolls(){
                var output = "<p>No Polls exist yet.</p>";
                var data = {"object": "poll", "action": "list"};
                $.post("/api.php", data)
                    .done(function(data){
                        //console.log(data);
                        if (data.length > 0){
                            output = '<thead><tr><td>Poll ID</td><td>Poll Question</td><td>Created</td><td>Last Updated</td><td>Actions</td><tr></thead>';
                            data.forEach(poll => {
                                output += '<tr>';
                                output += '<td>' + poll.id + '</td>' ;
                                output += '<td>' + poll.poll_question + '</td>' ;
                                output += '<td>' + poll.created + '</td>' ;
                                output += '<td>' + poll.updated + '</td>' ;
                                output += '<td>';
                                output += '<button class="btn view_poll_btn" data-target="' + poll.id + '">View</button> ';
                                output += '<button class="btn edit_poll_btn" data-target="' + poll.id + '">Edit</button> ';
                                output += '<button class="btn delete_poll_btn" data-target="' + poll.id + '">Delete</button> ';
                                output += '</td></tr>';
                            });
                            $( "#current_polls" ).html( output );
                        }

                    });
            }

            $("#new_poll_button").on('click', function(event){
                event.preventDefault();
                $('#addPollModal').modal('show');
            });

            $("#new_poll_form").on('submit', function(){
                event.preventDefault();
                var data = $(this).serialize();
                $.post("/api.php", data)
                    .done(function(data){
                        $('#addPollModal').modal('hide');
                        showCurrentPolls();
                    });
            });

            $("#current_polls").on('click', '.view_poll_btn', function(event){
                event.preventDefault();
                var poll_id = $(this).attr("data-target");
                alert("view "+poll_id);
            });

            $("#current_polls").on('click', '.edit_poll_btn', function(event){
                event.preventDefault();
                var poll_id = $(this).attr("data-target");
                alert("edit "+poll_id);
            });

            $("#current_polls").on('click', '.delete_poll_btn', function(event){
                event.preventDefault();
                var poll_id = $(this).attr("data-target");
                var data = {"object": "poll", "action": "delete", "poll_id": poll_id};
                $.post("/api.php", data)
                    .done(function(data){
                        showCurrentPolls();
                    });
            });


        })(jQuery);
    </script>
   
  </body>
</html>