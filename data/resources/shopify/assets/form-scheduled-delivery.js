document.addEventListener('DOMContentLoaded', function() {
    // Don't edit!!! From here to..
    // area_flatpickr_s
    var fp = flatpickr('#scheduled-delivery', {
        enableTime: true,
        minDate: "today",
        dateFormat: "Y-m-d H:i",
        inline: true
    })
    document.getElementById('scheduled-delivery').style = "position: absolute; visibility: hidden";
    // area_flatpickr_e
    // here.

    var fsd = document.querySelector('#scheduled-delivery')
    var fsdh = document.querySelector('#scheduled-delivery-hidden')
    if (fsd && fsdh) {
        var inputName = fsd.attributes.name.textContent
        var matches = inputName.match(/^attributes\[(.*)\]/)
        if (matches[1]) {
            fsdh.setAttribute('value', matches[1])
        }
    }
});
