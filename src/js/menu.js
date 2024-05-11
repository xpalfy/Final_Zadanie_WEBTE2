document.addEventListener('DOMContentLoaded', function() {
    function updateTime() {
        let now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        let timeString = hours + ':' + minutes + ':' + seconds;
        document.getElementById('time').innerHTML = timeString;
    }

    updateTime();
    setInterval(updateTime, 1000);
});
