<?php

function SentenceSummary()
{

    ob_start();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Summarizer</title>
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
			rel="stylesheet"
			integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
			crossorigin="anonymous"
		/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" integrity="sha512-fD9DI5bZwQxOi7MhYWnnNPlvXdp/2Pj3XSTRrFs5FQa4mizyGLnJcN6tuvUS6LbmgN1ut+XGSABKvjN0H6Aoow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

		<link rel="stylesheet" href="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/style.css" />
		<script
			src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
			crossorigin="anonymous"
		></script>
		<script>
    const handleClick = (e) => {
        callApi("<?php echo wp_create_nonce( 'kamranfaisal'); ?>");
    };
</script>

	</head>
	<body>
		<div style='box-shadow: 0px 3px 8px 0px rgba(0, 0, 0, 0.08)' class='container-fluid'>
			
				<!--- Heading -->
				<div class='row header-box' style='border-top-right-radius:16px; border-top-left-radius:16px;'>		
				<div style='display:flex;' class='col-4'>			
					<p style='padding-top: 5px; margin: 0; font-size: 16px; font-family: Open Sans,sans-serif; font-weight: 600; line-height: 24px; color: #434343;'>Summarizer <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="The Summarizer condenses articles, papers, and other documents into a bulleted Key Sentences list or into a new paragraph."> <i class="fa fa-info-circle"></i></span></p>
					<button onclick="clearText()" style='margin-left:20px;' type="button" class="btn btn-primary shadow"><i class="fa fa-eraser" aria-hidden="true"></i> Clear</button>
					<button style='margin-left:20px;' class='btn btn-primary shadow' onclick="pasteTextToTextarea()"><i class="fa fa-clipboard" aria-hidden="true"></i> Paste</button>
				</div>
				<div class='col-4'>	
					<div style='display:flex; padding-top:16px;' class="range-div">
					<p style="padding-top:4px; white-space:nowrap; margin: 0px; font-size: 14px; font-family: 'Open Sans',sans-serif; font-weight: 600; line-height: 20px; color: #434343;">Summary Length: <p style='white-space:nowrap; color:red; font-weight:600px;' id="currentValue">  19</p> </p>
					<div style='display:flex; position:absolute; margin-left:150px;'>	
					<p style='padding-left:20px; padding-right:20px;'>Short</p>
						<input
							type="range"
							class="form-range"
							id="customRange1"
							max="30"
							min="1"
							placeholder="Enter the text"
						/>
						<p style='padding-left:20px; padding-right:20px;'>Long</p>
					</div>
					</div>
				</div>

				<div class='col-4'>
					<div style="float:right;margin-left:10px;" class="btn-group shadow">
  <button id='dwn-btn' onclick="downloadFile('txt')" type="button" class="btn btn-primary"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Download</button>
  <button id='dwnt-btn' type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
    <span class="visually-hidden">Toggle Dropdown</span>
  </button>
  <ul class="dropdown-menu">
    <li><a onclick="downloadFile('pdf')" class="dropdown-item" href="#">.pdf</a></li>
    <li><a onclick="downloadFile('doc')" class="dropdown-item" href="#">.doc</a></li>
    <li><a onclick="downloadFile('txt')" class="dropdown-item" href="#">.txt</a></li>
  </ul>
</div>
					<button id="copy-btn" style='white-space:nowrap; float:right;' class='btn btn-primary shadow' onclick="copyTextToClipboard('summary-output')"><i class="fa-solid fa-copy"></i> Copy</button>
				</div>

				</div>

			<div class="row">
				<div style='padding:0;' class='col'>
					<div style='border-bottom-left-radius:16px;' class="input-box">
						<textarea style='font-family:Open Sans,sans-serif; font-size:16px;resize:none;'
							name="mainText"
							class="mainText"
							placeholder='Enter or paste your text and press "Summarize."' 
						></textarea>
						<div class="footer-box">
							<div id="word-count">
								<p style='font-weight:600;' id="word-count-text"></p>
								<input
									type="file"
									name=""
									id="file-upload"
									accept=".pdf, .docx, .txt"
								/>
								<button style='border:none; color: #000;' class="btn btn-primary  rounded-pill bg-transparent" onclick="uploadFileHandler()" id="upload-btn">
									<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload
								</button>
							</div>
							<button style='white-space:nowrap;'
								onclick="handleClick()"
								id="summarize-btn"
								class="btn btn-primary btn-md rounded-pill shadow-lg">
								<i class="fa fa-scissors" aria-hidden="true"></i> Summarize
							</button>
						</div>
					</div>
				</div>
				<div style='padding:0;' class='col'>
					<div style='border-bottom-right-radius:16px;' class="summary-container">
						<div id="summary">
							<p id='summary-output' class="summary-text"></p>
							<img style='margin: 150px;' src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/loading.gif" alt="" class="loader" id="loader" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="snackbar"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  You need minimum 200 Words <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> </div>
		<script src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/pdf.js"></script>
		<script src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/docToText.js"></script>
		<script>
			pdfjsLib.GlobalWorkerOptions.workerSrc = "https://www.grammarlookup.com/wp-content/plugins/sentece-summary/pdf.worker.js";
		</script>
		<script src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/script.js"></script>
	</body>
</html>


<?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

add_shortcode('sentencesummary_shortcode', 'SentenceSummary');

?>
