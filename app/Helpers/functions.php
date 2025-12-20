<?php

use App\Models\Session;
use App\Models\Course;
use App\Models\Trainer;

if (!function_exists('getSessions')) {
    function getSessions($arrayOnly = false)
    {
        $sessions = Session::all(); // Fetch all sessions from DB
        if ($arrayOnly) {
            return $sessions->pluck('name')->toArray(); // Return array of session names
        }
        return $sessions;
    }
}

if (!function_exists('getCourses')) {
    function getCourses($arrayOnly = false)
    {
        $courses = Course::all();
        if ($arrayOnly) {
            return $courses->pluck('name')->toArray();
        }
        return $courses;
    }
}

if (!function_exists('getTrainers')) {
    function getTrainers($arrayOnly = false)
    {
        $trainers = Trainer::all();
        if ($arrayOnly) {
            return $trainers->pluck('name')->toArray();
        }
        return $trainers;
    }
}
