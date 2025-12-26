<!DOCTYPE html>
<html>
<head>
<title>General Aptitude Test</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', sans-serif;
}

* {
    box-sizing: border-box;
}
.inner-container {
    padding-left: 5%;
    padding-right: 5%;
}
.h-detials {
    margin-left: auto;
    max-width: 220px;
    width: 100%;
}
.rw-flex {
    display: flex;
    flex-wrap: wrap;
    margin-left: -15px;
    margin-right: -15px;
}
.apdt-6 {
    width: 50%;
    padding: 10px 15px;
    border: 1px solid #dadada;
}
.apdt-8 {
    width: 66.6%;
    padding: 10px 15px;
    border: 1px solid #dadada;
}
.apdt-4 {
    width: 33.3%;
    padding: 10px 15px;
    border: 1px solid #dadada;
}
.apd-6 {
    width: 50%;
    padding: 10px 15px;
}
.apd-8 {
    width: 66.6%;
    padding: 10px 15px;
}
.apd-4 {
    width: 33.3%;
    padding: 10px 15px;
}


.apd-tt {
    font-size: 20px;
    display: flex;
    gap: 10px;
    
}
.apd-tt input {
    font-size: 18px;
    border: none;
    padding: 4px 5px;
    width: 100%;
}

.apd-tt span {
    width: 82%;
}

.apd-tt input:focus {
    outline: none;
    box-shadow: none !important;
}
.apt-body-title h2 {
    font-family: 'Katibeh', serif;
    text-align: center;
    font-size: 80px;
    font-weight: 700;
    color: #2c2e35;
    margin: 0 0 30px;
    text-transform: uppercase;
}
.apt-note h4 {
    font-family: 'Inter', sans-serif;
    text-align: center;
    font-size: 18px;
    font-weight: 700;
    color: red;
    margin: 0 0 10px;
}
.apt-note {
    margin-top: 80px;
}
.apt-question {
    margin-bottom: 10px;
    background-color: #f5f5f5;
    padding: 20px 20px;
    border-radius: 10px;
}
.apt-question h3 {
    font-size: 18px;
    line-height: normal;
    margin: 0 0 15px;
    font-family: 'Inter', sans-serif;
}

.opt-list {
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    margin: 0;
    padding: 0;
}

.opt-list li {
    width: 18%;
}

.opt-list label {
    font-size: 18px;
}
.radio input[type="radio"], .checkbox input[type="checkbox"] {
    float: left;
    margin-left: -20px;
}
input[type="radio"] {
    display: none !important;
}
input[type="radio"] + label {
    display: inline-block;
    cursor: pointer;
    position: relative;
    padding-left: 35px;
    margin-right: 15px;
    font-size: 16px;
}
input[type="radio"] + label:before {
    content: "";
    display: block;
    width: 20px;
    height: 20px;
    margin-right: 14px;
    position: absolute;
    top: -1px;
    left: 0;
    border: 1px solid #aaa;
    background-color: #fff;
    border-radius: 0px;
}
input[type="radio"]:checked + label:after {
    content: "✔";
    display: block;
    position: absolute;
    top: -1px;
    left: 5px;
    border-radius: 0px;
    color: #000;
}
.apt-auth h4 {
    margin: 0;
    font-size: 22px;
    font-family: 'Inter', sans-serif;
}
.apt-auth h5 {
    margin: 0;
    font-size: 22px;
    font-family: 'Inter', sans-serif;
    text-align:right;
}
.apt-auth {
    margin-top: 80px;
    display: inline-block;
    width: 100%;
}
.apt-submit button {
    background-color: #ed762e;
    color: #fff;
    border: none;
    padding: 15px 50px;
    font-size: 18px;
    border-radius: 5px;
    line-height: normal;
    cursor: pointer;
}

.apt-submit {
    text-align: center;
    margin-top: 40px;
}
.footer-shape {
    margin-top: 80px;
}
@media only screen and (max-width:1199px){
.apd-tt {
    font-size: 18px;
}
.apt-body-title h2 {
    font-size: 60px;
}
.apt-note h4 {
    font-size: 14px;
}
.apt-question h3 {
    font-size: 16px;
}

input[type="radio"] + label {
    font-size: 14px;
}

.apt-auth h4 {
    font-size: 18px;
}
.apd-tt input {
    font-size: 16px;
}

.apd-tt span {
    width: 78%;
}
.apt-auth h5 {
    font-size: 18px;
}
}
@media only screen and (max-width:767px){
.inner-container {
    padding: 0 15px;
}

.apt-body-title h2 {
    font-size: 36px;
    line-height: 36px;
}

.apdt-4 {
    width: 100%;
}

.apdt-8 {
    width: 100%;
}

.apt-body-head {
    padding: 0 15px;
}
.apd-tt {
    font-size: 16px;
    flex-wrap: wrap;
    gap: 5px;
}
.apd-tt span {
    width: 100%;
}
.apd-tt input {
    font-size: 14px;
    padding: 4px 0;
}
.opt-list li {
    width: 50%;
    margin: 8px 0;
}

.apd-6 {
    width: 100%;
}

.apt-auth h5 {
    text-align: left;
    margin-top: 50px;
}

.h-logo {
    text-align: center;
}

.h-detials {
    max-width: 100%;
}

.h-detials p {
    text-align: center !important;
}

.h-detials p span {
    width: 100% !important;
    display: inline-block;
    text-align: left;
    max-width: 190px !important;
}

.head-main {
    padding-top: 30px !important;
}

.certi-body {
    padding-top: 30px !important;
}
}
</style>
</head>
<body class="bg-light">

@yield('content')


</body>
</html>