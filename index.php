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

// Figure out if we have already set up our database tables.  If not, do that now.
$db = DB::getInstance();
if ($db->db_exists()){
    echo('database exists');
} else {
    echo('database is not set up.');
    $db->initialize();
}

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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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
        <div class="row">
            <div class="col">
                <div class="poll-stage">

                    <h3 id="current-poll-question"></h3>

                    <form class="form-horizontal" id="current-poll-form">
                    </form>
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'forms/new_poll_form.php'; ?>
    <?php include 'forms/edit_poll_form.php'; ?>
    <?php include 'forms/poll_options_form.php'; ?>

    <script type="text/javascript">
        (function($) {

            /**
             * Initialize everything we need. 
             */

            google.charts.load('current', {'packages':['corechart']});
            showCurrentPolls();

            /**
             * First we'll include our support functions.
             */

            function showCurrentPolls(){
                var output = "<p>No Polls exist yet.</p>";
                var data = {"object": "poll", "action": "list"};
                $.post("/api.php", data)
                    .done(function(data){
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
                                output += '<button class="btn options_poll_btn" data-target="' + poll.id + '">Configure Answers</button> ';
                                output += '</td></tr>';
                            });
                            $( "#current_polls" ).html( output );
                        }
                    });
            }

            function generatePollForm(poll){
                var output = '<div class="form-group">';
                if (!(poll.poll_options === null) && poll.poll_options.length > 0 ){
                    output += '<input type="hidden" name="poll_id" value="'+poll.id+'">';
                    output += '<input type="hidden" name="object" value="answer">';
                    output += '<input type="hidden" name="action" value="create">';
                    output += '<label for="options" class="control-label col-xs-4">Options</label>'; 
                    output += '<div class="col-xs-8">';
                    poll.poll_options.forEach(option => {
                        output += '<div class="checkbox">';
                        output += '<label class="checkbox">';
                        output += '<input type="radio" name="options" value="'+option.id+'"> ';
                        output += option.option;
                        output += '</label>';
                        output += '</div>';
                    });
                    output += '<span id="optionsHelpBlock" class="help-block">Pick one answer.</span>';
                    output += '</div>';
                    output += '</div>';
                    output += '<div class="form-group row">';
                    output += '<div class="col-xs-offset-4 col-xs-8">';
                    output += '<button name="submit" type="submit" class="btn btn-primary">Submit</button>';
                    output += '</div>';
                } else {
                    output += "<p>No Options yet.</p>";
                }
                output += '</div>';
                return output;
            }

            function drawChart(poll_id){
                var poll_result_data;
                $.post("/api.php", {"object":"poll", "action":"chart_results", "poll_id":poll_id})
                    .done(function(data){
                        poll_result_data = data;
                        var datatable = new google.visualization.DataTable();
                        datatable.addColumn('string', 'Option');
                        datatable.addColumn('number', 'Votes');
                        datatable.addRows(poll_result_data);
                        var chartOptions = {'width':600, 'height':300};
                        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                        chart.draw(datatable, chartOptions);
                    });
            }

            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for(var i=0;i < ca.length;i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }

            function deleteCookie( name ) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }

            function option_edit_form(poll_option){
                var output = '<form class="form-inline option-edit-form" id="poll-option-'+poll_option.id+'">';
                output += '<input type="text" id="option-text" value="' + poll_option.option + '">';
                output += '<button class="btn delete-option-button" data-target="' + poll_option.id + '">Delete</button> ';
                output += '</form>';
                return output;
            }

            /**
             * Event Handlers here.
             */

            $("#new_poll_button").on('click', function(event){
                event.preventDefault();
                $('#addPollModal').modal('show');
            });

            $("#new_poll_form").on('submit', function(event){
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
                // reset anything that might be displayed already.
                $("#current-poll-question").html('');
                $("#chart_div").html('');
                $("#current-poll-form").html('');
                var poll_id = $(this).attr("data-target");
                var data = {"object":"poll", "action":"read", "poll_id": poll_id};
                $.post("/api.php", data)
                    .done(function(data){
                        $("#current-poll-question").html("Poll: " + data.poll_question);
                        if (!data.user_has_voted){
                            $("#current-poll-form").html(generatePollForm(data));
                        } else {
                            drawChart(data.id);
                            $("#current-poll-form").html('<button class="btn" id="change_vote" data-target="'+data.id+'">Change my vote</button>');
                        }
                    });
            });

            $("#current-poll-form").on('click', '#change_vote', function(event){
                var poll_id = $(this).attr("data-target");
                var poll_cookie_name = "Ian_Broadnet_Poll_" + poll_id;
                var userAnswerId = getCookie( poll_cookie_name );
                var data = {"object":"answer", "action":"delete", "answer_id":userAnswerId};
                deleteCookie( poll_cookie_name )
                $.post("/api.php", data)
                    .done(function(data){
                        data_poll = {"object":"poll", "action":"read", "poll_id": poll_id};
                        $.post("/api.php", data_poll)
                            .done(function(updated_poll){
                                $("#current-poll-question").html("Poll: " + updated_poll.poll_question);
                                $("#current-poll-form").html(generatePollForm(updated_poll));
                            });
                    });
            });

            $("#current-poll-form").on('submit', function(event){
                event.preventDefault();
                var data = $(this).serialize();
                // we need the poll ID still, so let's pull that out.
                var poll_id = $('input[name="poll_id"]').val();
                $.post("/api.php", data)
                    .done(function(data){
                        drawChart(poll_id);
                        $("#current-poll-form").html('<button class="btn" id="change_vote" data-target="'+poll_id+'">Change my vote</button>');
                    });
            });

            $("#current_polls").on('click', '.edit_poll_btn', function(event){
                event.preventDefault();
                var poll_id = $(this).attr("data-target");
                var data = {"object": "poll", "action":"read", "poll_id": poll_id};
                $.post("/api.php", data)
                    .done(function(data){
                        $('#editPollModal').modal('show');
                        $("#edit_form_poll_id").val(data.id);
                        $("#edit_form_poll_question").html(data.poll_question);
                    });
            });

            $("#edit_poll_form").on('submit', function(event){
                event.preventDefault();
                var data = $(this).serialize();
                $.post("/api.php", data)
                    .done(function(data){
                        $('#editPollModal').modal('hide');
                        showCurrentPolls();
                    });
                $("#edit_poll_form").trigger("reset");
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

            $("#current_polls").on('click', '.options_poll_btn', function(event){
                event.preventDefault();
                var poll_id = $(this).attr("data-target");
                var data = {"poll_id": poll_id, "object": "poll", "action": "read"};
                $.post("/api.php", data)
                    .done(function(data){
                        var output = '';
                        if (!(data.poll_options === null) && data.poll_options.length > 0 ){
                            data.poll_options.forEach(option => {
                                output += option_edit_form(option);
                            });
                        } else {
                            output += "<p>No Options yet.</p>";
                        }
                        $("#new_option_poll_id").val(data.id);
                        $("#poll-options-stage").html( output );
                    });
                $('#pollOptionsModal').modal('show');
            });

            $("#new_option_form").on('submit', function(event){
                event.preventDefault();
                var data = $(this).serialize();
                $.post("/api.php", data)
                    .done(function(data){
                        var new_option = option_edit_form(data);
                        $("#poll-options-stage").append(new_option);
                        $("#new_option_form").trigger("reset");
                    });
            });

            $("#pollOptionsModal").on('click', '.delete-option-button', function(event){
                event.preventDefault();
                var poll_option_id = $(this).attr("data-target");
                var data = {"object": "poll_option", "action": "delete", "poll_option_id": poll_option_id};
                $.post("/api.php", data)
                    .done(function(data){
                        $("#poll-option-"+poll_option_id).remove();
                    });
            });

        })(jQuery);
    </script>
   
  </body>
</html>