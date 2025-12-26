<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <title>Multiple Choice Test</title>



    <style>

        @page {

            size: A4;

            margin: 18mm;

        }



        body {

            font-family: DejaVu Sans, sans-serif;

            font-size: 12px;

            color: #333;

        }



        /* ---------- Layout ---------- */

        .container {

            width: 100%;

        }



        h1 {

            text-align: center;

            margin-bottom: 20px;

        }



        /* ---------- Rules Box ---------- */

        .rules {

            background: #fefedc;

            border: 1px solid #e6e6a8;

            padding: 15px;

            margin-bottom: 20px;

        }



        .rules h3 {

            margin-top: 0;

            font-size: 14px;

        }



        .rules ul {

            padding-left: 18px;

            margin: 0;

        }



        .rules li {

            margin-bottom: 6px;

        }



        /* ---------- Student Info ---------- */

        .student-info {

            margin-bottom: 25px;

        }



        .field {

            margin-bottom: 10px;

        }



        .line {

            display: inline-block;

            border-bottom: 1px solid #000;

            width: 250px;

            height: 14px;

        }



        /* ---------- Questions ---------- */

        .question {

            margin-bottom: 25px;

            page-break-inside: avoid;

        }



        .question-title {

            font-weight: bold;

            margin-bottom: 10px;

        }



        /* ---------- Options (2 Column) ---------- */

        .options {

            width: 100%;

        }



        .option {

            width: 50%;

            display: inline-block;

            margin-bottom: 8px;

            vertical-align: top;

        }



        .radio {

            display: inline-block;

            width: 12px;

            height: 12px;

            border: 1px solid #555;

            border-radius: 50%;

            margin-right: 6px;

            vertical-align: middle;

        }



        .option-text {

            vertical-align: middle;

        }



        hr {

            border: none;

            border-top: 1px solid #ddd;

            margin: 20px 0;

        }

    </style>

</head>

<body>



<div class="container">



    {{-- ================= HEADER ================= --}}

    <h1>Multiple Choice Test</h1>



    {{-- ================= RULES ================= --}}

    <div class="rules">

        <h3>Rules and Regulations</h3>

        <ul>

            <li>Write your name and student ID correctly.</li>

            <li>The total duration of the test is {{ $duration ?? '45 minutes' }}.</li>

            <li>You can submit answers only once.</li>

            <li>Each question carries its own marks.</li>

            <li>In case of technical issues, inform the examiner.</li>

        </ul>

    </div>



    {{-- ================= STUDENT INFO ================= --}}

    {{-- ================= STUDENT INFORMATION ================= --}}

<div style="margin-bottom: 25px;">



    <div style="margin-bottom: 12px;">

        <strong>Student Name:</strong>

        <span style="display:inline-block; width: 380px; border-bottom:1px solid #000; margin-left:10px;"></span>

    </div>



    <div style="margin-bottom: 12px;">

        <strong>Mobile No:</strong>

        <span style="display:inline-block; width: 380px; border-bottom:1px solid #000; margin-left:23px;"></span>

    </div>



    <div style="margin-bottom: 12px;">

        <strong>Email:</strong>

        <span style="display:inline-block; width: 380px; border-bottom:1px solid #000; margin-left:63px;"></span>

    </div>



    <div style="margin-bottom: 12px;">

        <strong>College Name:</strong>

        <span style="display:inline-block; width: 380px; border-bottom:1px solid #000; margin-left:14px;"></span>

    </div>



    <div style="margin-bottom: 12px;">

        <strong>Total Marks:</strong>

        <span style="display:inline-block; width: 380px; border-bottom:1px solid #000; margin-left:22px;"></span>

    </div>



</div>



<hr>





    {{-- ================= QUESTIONS ================= --}}

    @foreach($questions as $qIndex => $question)

        <div class="question">



            <div class="question-title">

                {{ $qIndex + 1 }}. {{ $question->question }}

                @if(isset($question->marks))

                    ({{ $question->marks }} Points)

                @endif

            </div>



            <div class="options">

                @foreach($question->options as $oIndex => $option)

                    <div class="option">

                        <span class="radio"></span>

                        <span class="option-text">

                            {{ $option->option_text }}

                        </span>

                    </div>

                @endforeach

            </div>



        </div>

    @endforeach

{{-- ================= ANSWER KEY PAGE ================= --}}

<div style="page-break-before: always;"></div>



<h2 style="text-align:center; margin-top:20px; margin-bottom:20px;">

    Answer Key

</h2>



<table width="100%" cellspacing="0" cellpadding="8"

       style="border-collapse: collapse; font-size: 12px;">

    <thead>

        <tr>

            <th style="border:1px solid #000; text-align:center;">

                Question

            </th>

            <th style="border:1px solid #000; text-align:center;">

                Correct Option

            </th>

        </tr>

    </thead>

    <tbody>

        @foreach($questions as $index => $question)

            <tr>

                <td style="border:1px solid #000; text-align:center;">

                    Q{{ $index + 1 }}

                </td>

                <td style="border:1px solid #000; text-align:center;">

                    {{ $question->correct_index !== null

                        ? chr(97 + $question->correct_index)

                        : '-' }}

                </td>

            </tr>

        @endforeach

    </tbody>

</table>







</div>



</body>

</html>

