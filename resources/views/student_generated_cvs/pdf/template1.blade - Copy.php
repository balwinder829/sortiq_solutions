<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>

        @page {
            margin-top: 9mm;
            margin-right: 10mm;
            margin-bottom: 9mm;
            margin-left: 10mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #111111;
        }

        /* =====================================
           HEADER
        ====================================== */

        .header {
            margin: 0 0 25px 0;
        }

        .name {
            font-size: 25px;
            font-weight: normal;
            line-height: 1.15;
            margin: 0 0 7px 0;
        }

        .professional-title {
            font-size: 12px;
            line-height: 1.3;
            margin: 0 0 20px 0;
        }

        .contact {
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
        }


        /* =====================================
           SECTION
        ====================================== */

        .section {
            margin: 0 0 27px 0;
        }

        .section-heading {
            font-size: 13px;
            font-weight: bold;
            line-height: 1.2;

            margin: 0;
            padding: 0 0 6px 0;

            border-bottom: 1px solid #222222;

            page-break-after: avoid;
        }

        .section-content {
            margin-top: 11px;
            line-height: 1.45;
        }


        /* =====================================
           SUMMARY
        ====================================== */

        .summary {
            margin: 0;
            padding: 0;
            line-height: 1.45;
        }


        /* =====================================
           SKILLS
        ====================================== */

        .skills-list {
            margin: 0;
            padding-left: 17px;
        }

        .skills-list li {
            margin: 0;
            padding: 0;
            line-height: 1.45;
        }


        /* =====================================
           PROJECTS
        ====================================== */

        .project {
            margin: 0 0 12px 0;

            page-break-inside: avoid;
        }

        .project-title {
            font-weight: bold;
            margin: 0 0 4px 0;
        }

        .project-description {
            margin: 0;
            line-height: 1.45;
        }


        /* =====================================
           EDUCATION
        ====================================== */

        .education-item {
            margin: 0 0 12px 0;

            page-break-inside: avoid;
        }

        .education-title {
            font-weight: bold;
            margin: 0 0 3px 0;
        }

        .education-details {
            margin: 0;
            line-height: 1.45;
        }


        /* =====================================
           EXPERIENCE
        ====================================== */

        .experience-item {
            margin: 0 0 12px 0;

            page-break-inside: avoid;
        }

        .experience-title {
            margin: 0 0 4px 0;
        }

        .experience-details {
            margin: 0;
            line-height: 1.45;
        }


        /* =====================================
           ADDITIONAL INFORMATION
        ====================================== */

        .additional-info {
            margin: 0;
            line-height: 1.45;
        }


        /* =====================================
           LONG TEXT
        ====================================== */

        .text-content {
            margin: 0;
            line-height: 1.45;
        }

    </style>
</head>

<body>

    {{-- =====================================
         HEADER
    ====================================== --}}

    <div class="header">

        <div class="name">
            {{ $cv->name }}
        </div>

        @if(!empty($cv->professional_title))
            <div class="professional-title">
                {{ $cv->professional_title }}
            </div>
        @endif

        @if(!empty($cv->contact))
            <div class="contact">
                {!! nl2br(e($cv->contact)) !!}
            </div>
        @endif

    </div>


    {{-- =====================================
         SUMMARY
    ====================================== --}}

    @if(!empty($cv->summary))

        <div class="section">

            <div class="section-heading">
                Summary
            </div>

            <div class="section-content">

                <p class="summary">
                    {!! nl2br(e($cv->summary)) !!}
                </p>

            </div>

        </div>

    @endif


    {{-- =====================================
         SKILLS
    ====================================== --}}

    @if(!empty($cv->skills))

        <div class="section">

            <div class="section-heading">
                Skills
            </div>

            <div class="section-content">

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


    {{-- =====================================
         PROJECTS
    ====================================== --}}

    @if(!empty($cv->projects))

        <div class="section">

            <div class="section-heading">
                Projects
            </div>

            <div class="section-content">

                <div class="project">

                    <p class="text-content">
                        {!! nl2br(e($cv->projects)) !!}
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================
         EDUCATION
    ====================================== --}}

    @if(!empty($cv->education))

        <div class="section">

            <div class="section-heading">
                Education
            </div>

            <div class="section-content">

                <div class="education-item">

                    <p class="text-content">
                        {!! nl2br(e($cv->education)) !!}
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================
         EXPERIENCE
    ====================================== --}}

    @if(!empty($cv->experience))

        <div class="section">

            <div class="section-heading">
                Experience
            </div>

            <div class="section-content">

                <div class="experience-item">

                    <p class="text-content">
                        {!! nl2br(e($cv->experience)) !!}
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================
         ADDITIONAL INFORMATION
    ====================================== --}}

    @if(!empty($cv->additional_info))

        <div class="section">

            <div class="section-heading">
                Additional Information
            </div>

            <div class="section-content">

                <p class="additional-info">
                    {!! nl2br(e($cv->additional_info)) !!}
                </p>

            </div>

        </div>

    @endif

</body>
</html>