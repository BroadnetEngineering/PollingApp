<?php
namespace App;
// Composer
require __DIR__.'/vendor/autoload.php';

// Load the environment variables
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Ok, to get around writing an entire routing system in PHP and using a whole bunch of different templates,
// the strategy here is to just make a simple interface for our objects and do all the UI in Javascript.
// That probably minimizes the lines of code involved here.
// For the sake of convenience, we're just going to use POST requests to this file to interact with our objects,
// rather than bothering to make it RESTful.
// Downsides to this approach: an enormously verbose switch statement that anyone coming after will hate having to maintain.

$request = $_POST;

switch ($request['object']){
    case 'poll':
        switch($request['action']){
            case 'list':
                $poller = new Poll;
                $all_polls = $poller->all();
                header('Content-Type: application/json');
                echo json_encode($all_polls);
                break;
            case 'create':
                $new_poll = new Poll;
                $new_poll->poll_question = $request['poll_question'];
                $new_poll->save();
                $all_polls = $new_poll->all();
                header('Content-Type: application/json');
                echo json_encode($all_polls);
                break;
            case 'read':
                $poll = new Poll($request['poll_id']);
                header('Content-Type: application/json');
                echo json_encode($poll);
                break;
            case 'update':
                $poll = new Poll($request['poll_id']);
                $poll->poll_question = $request['poll_question'];
                $poll->update();
                header('Content-Type: application/json');
                echo json_encode($poll);
                break;
            case 'delete':
                $poll = new Poll($request['poll_id']);
                $poll->delete();
                $all_polls = $poll->all();
                header('Content-Type: application/json');
                echo json_encode($all_polls);
                break;
            case 'chart_results':
                $poll = new Poll($request['poll_id']);
                header('Content-Type: application/json');
                echo $poll->chart_results();
                break;
            default:
                throw new \Exception('no action specified');
                break;
        }
        break;
    case 'poll_option':
        switch($request['action']){
            case 'create':
                $new_option = new PollOption;
                $new_option->poll_id = $request['new_option_poll_id'];
                $new_option->option = $request['new_option'];
                $new_option = $new_option->save();
                header('Content-Type: application/json');
                echo json_encode($new_option);
                break;
            case 'delete':
                $option = new PollOption($request['poll_option_id']);
                $option->delete();
                return true;
                break;
            default:
                throw new \Exception('no action specified');
                break;
        }
        break;
    case 'answer':
        switch($request['action']){
            case 'create':
                $new_answer = new Answer;
                $new_answer->poll_id = $request['poll_id'];
                $new_answer->option_id = $request['options'];
                $new_answer->save();
                return true;
                break;
            case 'delete':
                $answer = new Answer($request['answer_id']);
                $poll_id = $answer->poll_id;
                $answer->delete();
                header('Content-Type: application/json');
                echo json_encode(['poll_id' => $poll_id]);
                break;
            default:
                throw new \Exception('no action specified');
                break;
        }
        break;
    default:
        throw new \Exception('no object specified');
        break;
}

