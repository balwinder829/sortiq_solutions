<?php

return [

    // Common for all users
    'common' => [
        [
            'title' => 'Dashboard',
            'icon'  => 'fas fa-tachometer-alt',
            'route' => 'dashboard',
            'roles' => [1,2,3], // everyone
        ],
    ],

    // Section-based menus
    'menus' => [

        [
            'section' => 'Admin Menu',
            'items' => [
                ['title'=>'Sessions','icon'=>'fas fa-calendar-alt','route'=>'sessions.index','roles'=>[1]],
                ['title'=>'Courses','icon'=>'fas fa-book','route'=>'courses.index','roles'=>[1]],
                ['title'=>'Colleges','icon'=>'fas fa-university','route'=>'colleges.index','roles'=>[1]],
                ['title'=>'Trainers','icon'=>'fas fa-chalkboard-teacher','route'=>'trainers.index','roles'=>[1,2]],
                ['title'=>'Batches','icon'=>'fas fa-layer-group','route'=>'batches.index','roles'=>[1,2]],
                ['title'=>'Departments','icon'=>'fa-solid fa-graduation-cap','route'=>'departments.index','roles'=>[1]],
                ['title'=>'References','icon'=>'fas fa-address-book','route'=>'references.index','roles'=>[1]],
                ['title'=>'Students Confirmation','icon'=>'fas fa-user-check','route'=>'students.index','roles'=>[1]],
                ['title'=>'Tests','icon'=>'fas fa-pen-to-square','route'=>'admin.tests.index','roles'=>[1]],
                ['title'=>'Certificates','icon'=>'fas fa-certificate','route'=>'certificates.index','roles'=>[1]],
                ['title'=>'Student Verification','icon'=>'fas fa-graduation-cap','route'=>'student_certificates.index','roles'=>[1]],
                ['title'=>'Users','icon'=>'fas fa-users','route'=>'users.index','roles'=>[1]],
            ],
        ],

        [
            'section' => 'Trainer Menu',
            'items' => [
                ['title'=>'Trainers','icon'=>'fas fa-chalkboard-teacher','route'=>'trainers.index','roles'=>[2]],
                ['title'=>'Batches','icon'=>'fas fa-layer-group','route'=>'batches.index','roles'=>[2]],
            ],
        ],

        [
            'section' => 'Sales Menu',
            'items' => [
                ['title'=>'Students','icon'=>'fas fa-user-check','route'=>'sales.students.index','roles'=>[3]],
                ['title'=>'Register Students','icon'=>'fas fa-user-plus','route'=>'students.create','roles'=>[3]],
            ],
        ],

    ],
];
