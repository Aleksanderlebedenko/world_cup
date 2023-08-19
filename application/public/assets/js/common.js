const getEventsCall = () => {
    const xhr = new XMLHttpRequest();
    const url = '/api/events';

    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            console.log(this.responseText);
        }
    }
    xhr.send();
}

const getEvents = () => {
    getEventsCall();
    setTimeout(getEvents, 3000);
}

setTimeout(getEvents, 3000);