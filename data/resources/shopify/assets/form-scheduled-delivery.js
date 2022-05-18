document.addEventListener('DOMContentLoaded', function() {
    // Don't edit!!! From here to..
    // area_flatpickr_s
    var fp = flatpickr('#scheduled-delivery', {
        enableTime: true,
        dateFormat: "Y-m-d H:i"
    })
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
