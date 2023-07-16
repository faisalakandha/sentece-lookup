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
const downloadButton = document.getElementById("dwn-btn");
const downloadtButton = document.getElementById("dwnt-btn");

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

let text = "";
let selectedFile = null;
const setDefault = () => {
	wordCount.classList.add("hide");
	ranger.value = "5";
	textIp.value = "";
	selectedFile = null;
	loader.classList.add("hide");
	copyButton.classList.add("disabled");
	downloadButton.classList.add("disabled");
	downloadtButton.classList.add("disabled");		
};

setDefault();

ranger.addEventListener("input", (e) => {
	console.log(parseInt(e.target.value, 10) * 3);
	currentVal.innerText = e.target.value; // Change here. It is the frontend
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

function clearText() {
	setInputValue("");
}

function copyResult() {
  // Get the textarea element
  var textarea = summary.innerText;

  // Select the text in the textarea
  textarea.select();

  try {
    // Execute the copy command using execCommand
    var successful = document.execCommand('copy');
    var message = successful ? 'Text copied to clipboard successfully.' : 'Unable to copy text to clipboard.';
    console.log(message);

    // Deselect the textarea
    textarea.setSelectionRange(0, 0);
  } catch (err) {
    console.error('Failed to copy text to clipboard: ', err);
  }
}



function myFunction(text = " ", time = 3000) {
	// Get the snackbar DIV
	var x = document.getElementById("snackbar");
	//x.innerText = text;
	// Add the "show" class to DIV
	x.className = "show"; // After 3 seconds, remove the show class from DIV

	setTimeout(function () {
		x.className = x.className.replace("show", "");
	}, time);
}

function pasteTextToTextarea() {
  if (!navigator.clipboard) {
    console.error('Clipboard API is not supported in this browser.');
    return;
  }

  navigator.clipboard.readText()
    .then(function(text) {
      let textarea = textIp;
      textarea.value += text;
    })
    .catch(function(err) {
      console.error('Failed to read clipboard contents: ', err);
    });
}

// Copy Text to Clipboard

function copyTextToClipboard(elementId) {
  // Create a temporary textarea element
  const textarea = document.createElement('textarea');

  // Set the value of the textarea to the inner text of the specified element
  textarea.value = document.getElementById(elementId).innerText;

  // Append the textarea to the document body
  document.body.appendChild(textarea);
  console.log(textarea.value);
  // Select the content of the textarea
  textarea.select();

  // Copy the selected text to the clipboard
  document.execCommand('copy');

  // Remove the temporary textarea
  document.body.removeChild(textarea);

  // Optionally, provide some visual feedback to the user
  alert('Text copied to clipboard!');
}

function downloadFile(type) {
    fetch('https://www.grammarlookup.com/wp-content/plugins/sentece-summary/converter.php?type=' + type + '&text=' + encodeURIComponent(summary.innerText))
        .then(function(response) {
            return response.blob();
        })
        .then(function(blob) {
            // Create a temporary download link
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'file.' + type;
            link.click();
        })
        .catch(function(error) {
            console.log(error);
        });
}

    const callApi = (nonce) => {
        const text = textIp.value;
        const len = text.split(" ").filter(function (n) {
            return n !== "";
        }).length;

        if (len < 200) {
            myFunction();
            return;
        }

        const requestOptions = {
            method: "POST",
            body: JSON.stringify({ txt: textIp.value, sentences: `${parseInt(currentVal.innerText, 10) * 3}`, my_nonce:nonce }),
            headers: {
                'Content-Type': 'application/json'
            }
        };

        loader.style.display = "block";

        fetch("https://www.grammarlookup.com/wp-json/sentencesummary/v1/summary", requestOptions)
            .then((response) => response.json())
            .then((data) => {
                console.log(currentVal.innerText);
                if (data.summary) {
                    summary.innerText = removeEllipses(data.summary);
                    copyButton.classList.remove("disabled");
					downloadButton.classList.remove("disabled");
					downloadtButton.classList.remove("disabled");
                } else {
                    summary.innerText = "Invalid";
                }
            })
            .catch((error) => console.log("error", error))
            .finally(() => {
                loader.style.display = "none";
            });
    };

function removeEllipses(text) {
  return text.replace(/\[+\.\.\.\]/g, "");
}
