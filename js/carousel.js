const carousel1 = new bootstrap.Carousel(document.getAnimationsById('Carousel1'), {
    interval: false
});

const carousel2 = new bootstrap.Carousel(document.getAnimationsById('Carousel2'), {
    interval: false
});

const carousel3 = new bootstrap.Carousel(document.getElementById('Carousel3'), {
    interval: false
});

document.getElementById('prevBut').addEventListener('click'), function() {
    carousel1.prev();
    carousel2.prev();
    carousel3.prev();
}

document.getElementById('nextBut').addEventListener('click'), function() {
    carousel1.next();
    carousel2.next();
    carousel3.next();
}