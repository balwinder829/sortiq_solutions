<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>

        @page {
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;

            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.45;

            color: #111111;
            background: #ffffff;
        }


        /* ==========================================================
           MAIN CV TABLE
        ========================================================== */

        .cv-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }


        /* ==========================================================
           LEFT SIDEBAR
        ========================================================== */

        .left-column {
            width: 29.5%;

            background-color: #344b60;
            color: #ffffff;

            vertical-align: top;

            padding: 14mm 10mm 14mm 12mm;

            height: 297mm;

            box-sizing: border-box;
        }


        /* ==========================================================
           RIGHT CONTENT
        ========================================================== */

        .right-column {
            width: 70.5%;

            background-color: #ffffff;
            color: #111111;

            vertical-align: top;

            padding: 21mm 13mm 15mm 14mm;

            box-sizing: border-box;
        }


        /* ==========================================================
           SIDEBAR NAME
        ========================================================== */

        .sidebar-name {
            font-size: 23px;
            font-weight: bold;

            line-height: 1.15;

            margin: 0;
            padding: 0 0 7px 0;

            color: #ffffff;
        }


        /* ==========================================================
           SIDEBAR TITLE
        ========================================================== */

        .sidebar-title {
            font-size: 12px;

            line-height: 1.3;

            margin: 0;
            padding: 0 0 30px 0;

            color: #ffffff;
        }


        /* ==========================================================
           SIDEBAR CONTACT
        ========================================================== */

        .sidebar-contact {
            font-size: 11px;

            line-height: 1.75;

            margin: 0;
            padding: 0 0 28px 0;

            color: #ffffff;
        }


        /* ==========================================================
           SIDEBAR SECTIONS
        ========================================================== */

        .sidebar-section {
            margin: 0;
            padding: 0 0 25px 0;

            page-break-inside: avoid;
        }


        .sidebar-heading {
            font-size: 13px;
            font-weight: bold;

            line-height: 1.2;

            margin: 0;
            padding: 0 0 7px 0;

            color: #ffffff;

            border-bottom: 1px solid #ffffff;
        }


        .sidebar-content {
            margin: 0;
            padding: 10px 0 0 0;

            color: #ffffff;

            line-height: 1.5;
        }


        /* ==========================================================
           SKILLS
        ========================================================== */

        .skills-list {
            margin: 0;
            padding: 0 0 0 17px;
        }


        .skills-list li {
            margin: 0;
            padding: 0 0 4px 0;

            line-height: 1.4;
        }


        /* ==========================================================
           RIGHT SIDE SECTIONS
        ========================================================== */

        .main-section {
            margin: 0;
            padding: 0 0 38px 0;

            page-break-inside: auto;
        }


        .main-section:last-child {
            padding-bottom: 0;
        }


        /* ==========================================================
           RIGHT SECTION HEADING
        ========================================================== */

        .main-heading {
            font-size: 13px;
            font-weight: bold;

            line-height: 1.2;

            margin: 0;
            padding: 0 0 7px 0;

            border-bottom: 1px solid #d2d2d2;

            page-break-after: avoid;
        }


        /* ==========================================================
           SPACE AFTER SEPARATOR
        ========================================================== */

        .main-content {
            margin: 0;
            padding: 15px 0 0 0;

            line-height: 1.5;
        }


        .main-text {
            margin: 0;
            padding: 0;

            line-height: 1.5;
        }


        /* ==========================================================
           PROJECT
        ========================================================== */

        .project-item {
            margin: 0;
            padding: 0;

            page-break-inside: avoid;
        }


        .project-description {
            margin: 0;
            padding: 0;

            line-height: 1.5;
        }


        /* ==========================================================
           EDUCATION
        ========================================================== */

        .education-item {
            margin: 0;
            padding: 0;

            page-break-inside: avoid;
        }


        .education-description {
            margin: 0;
            padding: 0;

            line-height: 1.5;
        }


        /* ==========================================================
           EXPERIENCE
        ========================================================== */

        .experience-item {
            margin: 0;
            padding: 0;

            page-break-inside: avoid;
        }


        .experience-description {
            margin: 0;
            padding: 0;

            line-height: 1.5;
        }


        /* ==========================================================
           ADDITIONAL INFORMATION
        ========================================================== */

        .additional-info {
            margin: 0;
            padding: 0;

            line-height: 1.5;
        }


        /* ==========================================================
           GENERAL
        ========================================================== */

        p {
            margin: 0;
            padding: 0;
        }

    </style>
</head>


<body>

<table class="cv-table">

    <tr>

        {{-- ======================================================
             LEFT SIDEBAR
        ======================================================= --}}

        <td class="left-column">

            {{-- NAME --}}
            <div class="sidebar-name">
                {{ $cv->name }}
            </div>


            {{-- PROFESSIONAL TITLE --}}
            @if(!empty($cv->professional_title))

                <div class="sidebar-title">
                    {{ $cv->professional_title }}
                </div>

            @endif


            {{-- CONTACT --}}
            @if(!empty($cv->contact))

                <div class="sidebar-contact">
                    {!! nl2br(e($cv->contact)) !!}
                </div>

            @endif


            {{-- SKILLS --}}
            @if(!empty($cv->skills))

                <div class="sidebar-section">

                    <div class="sidebar-heading">
                        Skills
                    </div>

                    <div class="sidebar-content">

                        @php
                            $skills = preg_split(
                                '/[\r\n,]+/',
                                $cv->skills,
                                -1,
                                PREG_SPLIT_NO_EMPTY
                            );
                        @endphp

                        <ul class="skills-list">

                            @foreach($skills as $skill)

                                <li>
                                    {{ trim($skill) }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            @endif


            {{-- ADDITIONAL INFORMATION --}}
            @if(!empty($cv->additional_info))

                <div class="sidebar-section">

                    <div class="sidebar-heading">
                        Additional Information
                    </div>

                    <div class="sidebar-content">

                        {!! nl2br(e($cv->additional_info)) !!}

                    </div>

                </div>

            @endif

        </td>


        {{-- ======================================================
             RIGHT CONTENT
        ======================================================= --}}

        <td class="right-column">


            {{-- ==================================================
                 PROFILE
            =================================================== --}}

            @if(!empty($cv->summary))

                <div class="main-section">

                    <div class="main-heading">
                        Profile
                    </div>

                    <div class="main-content">

                        <p class="main-text">
                            {!! nl2br(e($cv->summary)) !!}
                        </p>

                    </div>

                </div>

            @endif


            {{-- ==================================================
                 PROJECTS
            =================================================== --}}

            @if(!empty($cv->projects))

                <div class="main-section">

                    <div class="main-heading">
                        Projects
                    </div>

                    <div class="main-content">

                        <div class="project-item">

                            <p class="project-description">
                                {!! nl2br(e($cv->projects)) !!}
                            </p>

                        </div>

                    </div>

                </div>

            @endif


            {{-- ==================================================
                 EDUCATION
            =================================================== --}}

            @if(!empty($cv->education))

                <div class="main-section">

                    <div class="main-heading">
                        Education
                    </div>

                    <div class="main-content">

                        <div class="education-item">

                            <p class="education-description">
                                {!! nl2br(e($cv->education)) !!}
                            </p>

                        </div>

                    </div>

                </div>

            @endif


            {{-- ==================================================
                 EXPERIENCE
            =================================================== --}}

            @if(!empty($cv->experience))

                <div class="main-section">

                    <div class="main-heading">
                        Experience
                    </div>

                    <div class="main-content">

                        <div class="experience-item">

                            <p class="experience-description">
                                {!! nl2br(e($cv->experience)) !!}
                            </p>

                        </div>

                    </div>

                </div>

            @endif


            {{-- ==================================================
                 ADDITIONAL INFORMATION
            =================================================== --}}

            @if(!empty($cv->additional_info))

                <div class="main-section">

                    <div class="main-heading">
                        Additional Information
                    </div>

                    <div class="main-content">

                        <p class="additional-info">
                            {!! nl2br(e($cv->additional_info)) !!}
                        </p>

                    </div>

                </div>

            @endif


        </td>

    </tr>

</table>

</body>
</html>