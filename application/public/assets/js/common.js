const moveGame = (destination, pair) => {
    document.querySelector('[data-pair-id="' + pair.id + '"]').remove();

    const newPair = document.createElement("div");
    newPair.className = "pair";
    newPair.setAttribute("data-pair-id", pair.id);
    newPair.innerHTML = '<div class="name">' + pair.homeTeam + '</div>' +
        '<div class="result result-home">' + pair.result.home + '</div>' +
        '<div class="delimiter">-</div>' +
        '<div class="result result-away">' + pair.result.away + '</div>' +
        '<div class="name">' + pair.awayTeam + '</div>';

    destination.insertBefore(newPair, destination.firstChild);
}
const moveGameToCurrent = (pair) => {
    const currentDiv = document.querySelector('.group-of-matches.current');
    moveGame(currentDiv, pair);
}
const moveGameFromCurrent = (pair) => {
    const finishedDiv = document.querySelector('.group-of-matches.finished');
    moveGame(finishedDiv, pair);
}
const changeGameScore = (pair) => {
    const pairWrapper = document.querySelector('[data-pair-id="' + pair.id + '"]');

    pairWrapper.querySelector('.result-home').innerText = pair.result.home;
    pairWrapper.querySelector('.result-away').innerText = pair.result.away;
}

const handleEvent = (event) => {
    const {type, pair, time} = event;
    switch (type) {
        case 'start':
            moveGameToCurrent(pair);
            break;
        case 'goal':
            changeGameScore(pair);
            break;
        case 'end':
            moveGameFromCurrent(pair);
            break;
        default:
            console.log('Unknown event type');
    }
}

const handleEvents = (events) => {
    const eventsArray = JSON.parse(events);
    eventsArray.events.forEach(event => handleEvent(event));
}
const getEventsCall = () => {
    const xhr = new XMLHttpRequest();
    const url = '/api/events';

    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            handleEvents(this.responseText);
        }
    }
    xhr.send();
}

const getEvents = () => {
    getEventsCall();
    setTimeout(getEvents, 3000);
}

setTimeout(getEvents, 3000);