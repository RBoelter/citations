const loader = document.querySelector('#citations-loader');
const pluginContainer = document.querySelector('#citation-plugin');
const providerList = ['crossref', 'scopus', 'europepmc', 'google'];

if (pluginContainer) {
    fetchCitationsData(pluginContainer.getAttribute('data-citations-url'));
}

function fetchCitationsData(url) {
    if (url) {
        fetch(url, {
            method: 'GET',
        }).then(function (response) {
            if (response.ok) {
                return response.json();
            } else
                throw new Error('Error getting API Data!');
        }).then((data) => {
            displayTotalContent(data.content);
            displayListContent(data.content);
            if (undefined !== loader) {
                loader.style.display = 'none';
            }
        }).catch(error => {
            if (undefined !== loader) {
                loader.style.display = 'none';
            }
            console.log(error);
        });
    }
}

function displayTotalContent(data) {
    let gridColumns = '';
    providerList.forEach(function (provider) {
        if (data.hasOwnProperty(provider)) {
            if ('google' === provider) {
                pluginContainer.querySelector('.citations-count-google').style.display = 'block';
                gridColumns += '1fr ';
            } else if (data[provider].hasOwnProperty('count')) {
                pluginContainer.querySelector('.citations-count-' + provider + ' .badge_total').innerHTML = data[provider].count;
                pluginContainer.querySelector('.citations-count-' + provider).style.display = 'block';
                gridColumns += '1fr ';
            }
        }
    });
    gridColumns = gridColumns.trim();
    pluginContainer.querySelector('.citations-count').style.gridTemplateColumns = gridColumns;
    if (gridColumns.length === 3) {
        pluginContainer.querySelector('.citations-count').querySelector('img').style.maxWidth = '50%';
    }
}

function displayListContent(data) {
    let list = pluginContainer.querySelector('.citations-list');
    providerList.forEach(function (provider) {
        if (data.hasOwnProperty(provider)) {
            if (data[provider].hasOwnProperty('citations') && data[provider]['citations'].length > 0) {
                data[provider]['citations'].forEach(function (item) {
                    list.append(createListElement(item));
                });
            }
        }
    });
}


function createListElement(item) {
    let prototype = pluginContainer.querySelector('.cite-prototype').cloneNode(true);
    prototype.classList.remove('cite-prototype');
    prototype.style.display = 'block';
    let source = item.hasOwnProperty('source') ? item.source.toLowerCase() : '';
    prototype.querySelector('.cite-img').src = pluginContainer.getAttribute('data-img-url') + source + '.png';
    prototype.querySelector('.cite-img').alt = capitalizeFirstLetter(source) + " Logo";
    if (item.hasOwnProperty('authors')) {
        prototype.querySelector('.cite-author').innerHTML = item.authors;
    }
    if (item.hasOwnProperty('year')) {
        prototype.querySelector('.cite-date').innerHTML = '(' + item.year + ')';
    }
    if (item.hasOwnProperty('title')) {
        prototype.querySelector('.cite-title').innerHTML = '<strong>' + item.title + '.</strong>';
    }
    if (item.hasOwnProperty('journal') && item.journal !== '') {
        prototype.querySelector('.cite-info').innerHTML += item.journal;
    }
    if (item.hasOwnProperty('volume') && item.volume !== '') {
        prototype.querySelector('.cite-info').innerHTML += ', ' + item.volume;
    }
    if (item.hasOwnProperty('issue') && item.issue !== '') {
        prototype.querySelector('.cite-info').innerHTML += '(' + item.issue + ')';
    }
    if (item.hasOwnProperty('pages') && item.pages !== '') {
        prototype.querySelector('.cite-info').innerHTML += ', ' + item.pages;
    }
    prototype.querySelector('.cite-info').innerHTML += '.';
    if (item.hasOwnProperty('doi') && item.doi !== '') {
        prototype.querySelector('.cite-doi').innerHTML = '<a href="https://doi.org/' + item.doi + '" target="_blank" rel="noreferrer">' + item.doi + '</a>';
    }

    return prototype;
}


function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
