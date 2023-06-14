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
    const len = text.split(" ").filter(function(n) {
        return n !== "";
    }).length;

    if (len < 200) {
        myFunction();
        return;
    }

    const requestOptions = {
        method: "POST",
        body: JSON.stringify({ text: text }),
        headers: {
               'Content-Type': 'application/json',
               'X-WP-Nonce': "<?php echo wp_create_nonce('wp_rest') ?>"
        }
    };

    loader.style.display = "block";

    fetch(siteUrl + '/wp-json/sentencesummary/v1/summary', requestOptions)
        .then((response) => response.json())
        .then((data) => {
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

		</script>
	</head>
	<body>
		<div class="input-box">
			<div class="header-box">
				<!-- heading -->
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
			</div>
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
		<div class="summary-container">
			<div class="header-box">
				<p>Summary:</p>
				<button onclick="copyResult()" id="copy-btn">Copy</button>
			</div>
			<div id="summary">
				<p class="summary-text"></p>
				<img src="http://wp.docker.localhost:8000/wp-content/plugins/sentece-summary/loading.gif" alt="" class="loader" id="loader" />
			</div>
		</div>
		<div id="snackbar"></div>

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
