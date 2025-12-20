<!DOCTYPE html>
<html>
<head>
<title>Receipt</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">

<style>
	body {
		margin:0px;
		padding:0px;
		font-family: "Inter", sans-serif;
		font-weight: 400;
		color: #2c2e35;
	}
	p {
	    margin: 0;
	    font-size: 16px;
	    color: #2c2e35;
	}
	.rc-main-title {
	    font-family: "Katibeh", serif;
	    text-align: center;
	    font-size: 80px;
	    font-weight: 400;
	    color: #2c2e35;
	    margin: 0 0 10px;
	}
	.wrapper {
	    width: 100%;
	    overflow: hidden;
	    background-color: #fff;
	}
	.inner-container {
	    padding: 0 20px;
	    max-width: 800px;
	    margin: 0 auto;
	    width: 100%;
	    display: block;
	}

	.rc-head-content {
		text-align: center;
	}
	.rc-bdy-content p {
	    font-size: 22px;
	    line-height: 36px;
	    margin: 0 0 25px;
	}
	.rc-head-main {
	    padding-top: 50px;
	}
	.rc-certi-body {
	    padding-top: 60px;
	    padding-bottom: 60px;
	}
	.ct-bdy-sr {
	    display: flex;
	    flex-wrap: wrap;
	    justify-content: space-between;
	    margin: 0 0 40px;
	}
	.rc-bdy-sr p {
	    margin: 0;
	    font-size: 16px;
	}
	.rc-hghlt {
	    margin: 110px 0 0;
	    display: flex;
	    flex-wrap: wrap;
	    align-items: center;
	    justify-content: space-between;
	    padding-right: 5%;
	}
	.rc-hghlt-left h4 {
	    margin: 0 0 15px;
	    font-size: 20px;
	    line-height: 28px;
	}
</style>
</head>
<body>

<div class="wrapper">
	<div class="rc-head-main">
		<div class="inner-container">
			<div class="rc-head-content">
				<h2 class="rc-main-title">SORTIZ SOLUTIONS PVT.LTD.</h2>
				<p>E-51, Ground Floor<br>
				Industrial Area, Phase 8<br>
				SAS Nagar Mohali, Punjab - 160072</p>
			</div>
		</div>
	</div>

	<div class="rc-certi-body">
		<div class="inner-container">
			
			<div class="rc-bdy-content">

				<p><strong>Receipt No.</strong></p>

				<p><strong>Date:</strong> {{ now()->format('d M Y') }}</p>

				<p>
					Received with thanks from  
					<strong>Mr/Ms/Messers {{ $student->student_name }}</strong>
				</p>

				<p>
					Rs. <strong>{{ $amount }}</strong>  
					(Rupees <strong>{{ $amountInWords }}</strong>)
				</p>

				<p>
					vide Cash/Cheque/Do No.: <strong>{{ $transaction_no ?? '________' }}</strong>  
					Date: <strong>{{ now()->format('d M Y') }}</strong>
				</p>

				<p>
					Course <strong>{{ $student->courseData->course_name ?? '________' }}</strong>
				</p>

				<p>
					Duration <strong>{{ $student->durationData->name ?? '________' }}</strong>
				</p>

				<div class="rc-hghlt">
					<div class="rc-hghlt-left">
						<h4>(Authorised Signatory)</h4>
						<h4>Note: Fees is Non-Refundable</h4>
					</div>
				</div>

			</div>

		</div>
	</div>
</div>

</body>
</html>
