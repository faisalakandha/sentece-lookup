const ranger = document.querySelector(".form-range");
const textIp = document.querySelector(".mainText");
const summary = document.querySelector(".summary-text");
const currentVal = document.getElementById("currentValue");
const wordCount = document.querySelector("#word-count-text");
const uploadFile = document.querySelector("#file-upload");
const uploadButton = document.querySelector("#upload-btn");
const loader = document.getElementById("loader");
const summarizeButton = document.getElementById("summarize-btn");
const copyButton = document.getElementById("copy-btn");
const siteUrl = '<?php get_site_url(); ?>';

let text = "";
let selectedFile = null;
const setDefault = () => {
	wordCount.classList.add("hide");
	ranger.value = "1";
	textIp.value = "";
	selectedFile = null;
	loader.classList.add("hide");
	copyButton.style.display = "none";
};

setDefault();

ranger.addEventListener("input", (e) => {
	console.log(e.target.value);
	currentVal.innerText = e.target.value;
});

function getPageText(pageNum, PDFDocumentInstance) {
	// Return a Promise that is solved once the text of the page is retrieven
	return new Promise(function (resolve, reject) {
		PDFDocumentInstance.getPage(pageNum).then(function (pdfPage) {
			// The main trick to obtain the text of the PDF page, use the getTextContent method
			pdfPage.getTextContent().then(function (textContent) {
				var textItems = textContent.items;
				var finalString = "";

				// Concatenate the string of the item to the final string
				for (var i = 0; i < textItems.length; i++) {
					var item = textItems[i];

					finalString += item.str + " ";
				}

				// Solve promise with the text retrieven from the page
				resolve(finalString);
			});
		});
	});
}

uploadFile.addEventListener("change", (e) => {
	const file = e.target.files[0];
	console.log(file);
	const fileReader = new FileReader();
	function onLoadPdfFile() {
		fileReader.onload = function () {
			var typedarray = new Uint8Array(this.result);

			const loadingTask = pdfjsLib.getDocument(typedarray);
			loadingTask.promise.then((pdf) => {
				var pdfDocument = pdf;
				var pagesPromises = [];

				for (var i = 0; i < pdf.numPages; i++) {
					(function (pageNumber) {
						pagesPromises.push(
							getPageText(pageNumber, pdfDocument)
						);
					})(i + 1);
				}
				Promise.all(pagesPromises).then(function (pagesText) {
					let s = "";
					pagesText.forEach((item) => (s += item));
					setInputValue(s);
				});
			});
		};
		//Step 3:Read the file as ArrayBuffer
		fileReader.readAsArrayBuffer(e.target.files[0]);
	}

	function onLoadDocFile() {
		const docToText = new DocToText();
		console.log("first");
		docToText
			.extractToText(file, "docx")
			.then((text) => {
				let s = "";
				console.log(text.split(" "));
				// console.log(text);
				s = text.replace(/(\r\n|\n|\r)/gm, "");
				setInputValue(s);
			})
			.catch((err) => console.log(err));
	}

	if (file.type === "application/pdf") {
		onLoadPdfFile();
	} else if (file.type === "text/plain") {
		fileReader.onload = function () {
			setInputValue(this.result);
		};
		fileReader.readAsText(e.target.files[0]);
	} else {
		onLoadDocFile();
	}
});

const uploadFileHandler = () => {
	uploadFile.click();
};

const setInputValue = (newVal) => {
	textIp.value = newVal;
	updateWordCount(newVal);
};

const updateWordCount = (text) => {
	const len = text.split(" ").filter(function (n) {
		return n !== "";
	}).length;
	wordCount.innerText = `${len} Words`;
	if (len > 0) {
		wordCount.classList.remove("hide");
		uploadButton.classList.add("hide");
	} else {
		wordCount.classList.add("hide");
		uploadButton.classList.remove("hide");
	}
	if (len > 200) {
		// summarizeButton.disabled = false;
	} else {
		// summarizeButton.disabled = true;
	}
};

// textIp.addEventListener("")

textIp.addEventListener("input", (e) => {
	// text = newVal;
	console.log("first");
	text = e.target.value;
	updateWordCount(e.target.value);
});

const handleClick = (e) => {
	callApi();
};

function clearText() {
	setInputValue("");
}

function copyResult() {
	navigator.clipboard.writeText(summary.innerText);
	myFunction("Text Copied to clipboard", 3000);
}

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
            "Content-Type": "application/json"
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


function myFunction(text = "Minimum number of words is 200", time = 3000) {
	// Get the snackbar DIV
	var x = document.getElementById("snackbar");
	x.innerText = text;
	// Add the "show" class to DIV
	x.className = "show"; // After 3 seconds, remove the show class from DIV

	setTimeout(function () {
		x.className = x.className.replace("show", "");
	}, time);
}
