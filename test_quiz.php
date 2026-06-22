<?php
use Illuminate\Http\Request;

Auth::loginUsingId(2); // assuming 2 is the instructor
$quiz = App\Models\Quiz::find(1);
$req = Request::create('/instructor/quizzes/1/update-timer', 'POST', ['time_limit_minutes' => 45]);
// Mock the session for back() to work
$req->setLaravelSession(app('session')->driver('array'));

$controller = new App\Http\Controllers\Instructor\QuizController();
$res = $controller->updateTimer($req, $quiz);

dump('Session Success Message: ' . $res->getSession()->get('success'));
dump('DB Timer: ' . App\Models\Quiz::find(1)->time_limit_minutes);
