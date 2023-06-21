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
		<link rel="stylesheet" href="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/style.css" />
		<script
			src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
			crossorigin="anonymous"
		></script>
		<script>
    const callApi = () => {
        const text = textIp.value;
        const len = text.split(" ").filter(function (n) {
            return n !== "";
        }).length;

        if (len < 200) {
            myFunction();
            return;
        }

		const nonce =  "<?php echo wp_create_nonce( 'kamranfaisal'); ?>";

        const requestOptions = {
            method: "POST",
            body: JSON.stringify({ txt: textIp.value, sentences: currentVal.innerText, my_nonce:nonce }),
            headers: {
                'Content-Type': 'application/json'
            }
        };

        loader.style.display = "block";

        fetch("http://wp.docker.localhost:8000/wp-json/sentencesummary/v1/summary", requestOptions)
            .then((response) => response.json())
            .then((data) => {
                console.log(JSON.stringify(data));
                if (data.summary) {
                    summary.innerText = data.summary;
                    copyButton.style.display = "block";
                } else {
                    summary.innerText = "Invalid";
                }
                setTimeout(() => {
                    window.scrollBy(0, 300);
                }, 1000);
            })
            .catch((error) => console.log("error", error))
            .finally(() => {
                loader.style.display = "none";
            });
    };

    const handleClick = (e) => {
        callApi();
    };
</script>

	</head>
	<body>
		<div class='container-fluid'>
			<div class='row'>
				<!--- Heading -->
				<div style='border-top-right-radius:16px; border-top-left-radius:16px;'  class="header-box">
					
					<p>Choose the Size of the Summary:</p>
					<div class="range-div">
						<div class="range-values">
							<p>Short</p>
							<p id="currentValue">19</p>
							<p>Long</p>
						</div>
						<input
							type="range"
							class="form-range"
							id="customRange1"
							max="30"
							min="1"
							placeholder="Enter the text"
						/>
					</div>
					
					<p>Summary:</p> <br />
					<button onclick="copyResult()" id="copy-btn">Copy</button>

				</div>
			</div>

			<div class="row">
				<div style='padding:0;' class='col'>
					<div style='border-bottom-left-radius:16px;' class="input-box">
						<textarea
							name="mainText"
							class="mainText"
							placeholder="Enter the text here"
						></textarea>
						<div class="footer-box">
							<div id="word-count">
								<p id="word-count-text"></p>
								<input
									type="file"
									name=""
									id="file-upload"
									accept=".pdf, .docx, .txt"
								/>
								<button onclick="uploadFileHandler()" id="upload-btn">
									Upload
								</button>
							</div>
							<button
								onclick="handleClick()"
								id="summarize-btn"
								class="summarize-btn">
								Summarize
							</button>
							<div class="empty-div">
								<button onclick="clearText()">Clear Text</button>
							</div>
						</div>
					</div>
				</div>
				<div style='padding:0;' class='col'>
					<div style='border-bottom-right-radius:16px;' class="summary-container">
						<div id="summary">
							<p class="summary-text"></p>
							<img src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/loading.gif" alt="" class="loader" id="loader" />
						</div>
					</div>
				</div>
				<div id="snackbar"></div>
			</div>
		</div>
		<script src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/pdf.js"></script>
		<script src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/docToText.js"></script>
		<script>
			pdfjsLib.GlobalWorkerOptions.workerSrc = "http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/pdf.worker.js";
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
