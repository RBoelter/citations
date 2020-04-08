const citationsContainer = document.querySelector('.citations-container');
const citationsLoader = document.querySelector('#citations-loader');
const citationsImagePath = citationsContainer.getAttribute('data-image-path');
const citationUrl = citationsContainer.getAttribute('data-citations-url');
const citationProvider = citationsContainer.getAttribute('data-citations-provider');
const citationShowTotal = citationsContainer.getAttribute('data-citations-total');
const citationShowList = citationsContainer.getAttribute('data-citations-list');
const showGoogle = citationsContainer.getAttribute('data-show-google');
const showPmc = citationsContainer.getAttribute('data-show-pmc');

fetch(citationUrl, {
	method: 'GET',
}).then(function (response) {
	if (response.ok) {
		return response.json();
	} else
		throw new Error('Error getting API Data!');
}).then((data) => {
	displayTotalContent(data.content);
	displayListContent(data.content);
	citationsLoader.style.display = 'none';
}).catch(error => {
	citationsLoader.style.display = 'none';
	console.log(error);
});

function displayTotalContent(data) {
	if (citationProvider && citationShowTotal) {
		let crossrefTotal = document.querySelector('.citations-count-crossref');
		let scopusTotal = document.querySelector('.citations-count-scopus');
		let gridColumns = "1fr";
		switch (citationProvider) {
			case 'crossref':
				crossrefTotal.style.display = 'block';
				crossrefTotal.querySelector('.badge_total').innerText = data["crossref_count"] ? data["crossref_count"] : 0;
				break;
			case 'scopus':
				scopusTotal.style.display = 'block';
				scopusTotal.querySelector('.badge_total').innerText = data["scopus_count"] ? data["scopus_count"] : 0;
				break;
			case 'all':
				crossrefTotal.style.display = 'block';
				scopusTotal.style.display = 'block';
				crossrefTotal.querySelector('.badge_total').innerText = data["crossref_count"] ? data["crossref_count"] : 0;
				scopusTotal.querySelector('.badge_total').innerText = data["scopus_count"] ? data["scopus_count"] : 0;
				gridColumns += ' 1fr';
				break;
		}
		if (showGoogle === "1") {
			document.querySelector('.citations-count-google').style.display = 'block';
			gridColumns += ' 1fr';
		}
		if (showPmc === "1") {
			document.querySelector('.citations-count-pmc').style.display = 'block';
			document.querySelector('.citations-count-pmc').querySelector('.badge_total').innerText = data["pmc_count"] ? data["pmc_count"] : 0;
			gridColumns += ' 1fr';
		}
		document.querySelector('.citations-count').style.gridTemplateColumns = gridColumns;
		if (gridColumns.length === 3) {
			document.querySelector('.citations-count').querySelector('img').style.maxWidth = '50%';
		}
	}
}

function displayListContent(data) {
	if (citationShowList) {
		let list = data[citationProvider + '_list'];
		if (list && list.length > 0)
			for (let item of list) {
				document.querySelector('.citations-list').appendChild(createListElement(item));
			}
	}
}

function createListElement(item) {
	let outerDiv = document.createElement('div');
	let img = document.createElement("img");
	img.src = citationsImagePath + '/' + item['type'] + '.png';
	img.alt = item['type'] + " Logo";
	/*outerDiv.appendChild(img);*/
	let author = document.createElement('div');
	author.innerHTML = item['authors'] + ' (' + item['year'] + ')';
	outerDiv.appendChild(author);
	let title = document.createElement('span');
	title.style.fontWeight = 'bold';
	title.innerHTML = item['article_title'] + '. ';
	outerDiv.append(title);
	if (item['journal_title'] && item['journal_title'] !== '')
		outerDiv.append(item['journal_title'] + ', ');
	if (item['volume'] && item['volume'] !== '') {
		outerDiv.append(" " + item['volume']);
		if (item['issue'] && item['issue'] !== '')
			outerDiv.append("(" + item['issue'] + '), ');
		else
			outerDiv.append(", ");
	}
	if (item['first_page'] && item['first_page'] !== ' :')
		outerDiv.append(item['first_page'] + '.');
	outerDiv.appendChild(document.createElement('br'));
	let doi = document.createElement('a');
	doi.href = "https://doi.org/" + item['doi'];
	doi.target = "_blank";
	doi.rel = "noreferrer";
	doi.innerText = item['doi'];
	outerDiv.append(doi);
	return outerDiv;
}

