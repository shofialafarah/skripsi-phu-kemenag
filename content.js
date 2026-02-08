document.addEventListener("DOMContentLoaded", function () {
    const indicators = document.getElementsByClassName("indicators");

    if (indicators.length > 0) {
        new Sortable(indicators[0], {
            animation: 150,
            ghostClass: 'sortable-ghost', // Opsional: menambahkan class saat item sedang dipindahkan
            chosenClass: 'sortable-chosen', // Opsional: menambahkan class pada item yang dipilih
            dragClass: 'sortable-drag', // Opsional: menambahkan class pada item yang sedang didrag
        });
    }
});
