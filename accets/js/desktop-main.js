///Scroll
const scrollWrapper = document.getElementById("board");
const zoneLeft = document.getElementById("zoneLeft");
const zoneRight = document.getElementById("zoneRight");
const indicatorLeft = document.getElementById("indicatorLeft");
const indicatorRight = document.getElementById("indicatorRight");

let scrollInterval = null;
const scrollSpeed = 20; // px за шаг (~60fps)

function startAutoScroll(direction) {
    stopAutoScroll();

    if (direction === "left") {
        indicatorLeft.classList.add("active");
    } else {
        indicatorRight.classList.add("active");
    }

    scrollInterval = setInterval(() => {
        if (direction === "left") {
            scrollWrapper.scrollLeft -= scrollSpeed;
            if (scrollWrapper.scrollLeft <= 0) stopAutoScroll();
        } else {
            scrollWrapper.scrollLeft += scrollSpeed;
            const maxScroll = scrollWrapper.scrollWidth - scrollWrapper.clientWidth;
            if (scrollWrapper.scrollLeft >= maxScroll) stopAutoScroll();
        }
    }, 16); // ~60fps
}

function stopAutoScroll() {
    if (scrollInterval) {
        clearInterval(scrollInterval);
        scrollInterval = null;
    }
    indicatorLeft.classList.remove("active");
    indicatorRight.classList.remove("active");
}

zoneLeft.addEventListener("mouseenter", () => startAutoScroll("left"));
zoneLeft.addEventListener("mouseleave", stopAutoScroll);

zoneRight.addEventListener("mouseenter", () => startAutoScroll("right"));
zoneRight.addEventListener("mouseleave", stopAutoScroll);