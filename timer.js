document.addEventListener("DOMContentLoaded", () => {
    const elapsedEl = document.getElementById("elapsed-time");
    if (!elapsedEl) return;

    const startTime = new Date(elapsedEl.dataset.start);

    function updateTimer() {
        const now = new Date();
        const diff = Math.floor((now - startTime) / 1000);

        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        elapsedEl.textContent = `${hours}h${minutes.toString().padStart(2,"0")}min${seconds.toString().padStart(2,"0")}s`;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
});
