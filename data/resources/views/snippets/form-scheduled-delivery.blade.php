<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#scheduled-delivery', {
            enableTime: true,
            dateFormat: "Y-m-d H:i"
        })

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

</script>
<style>
    .form-scheduled-delivery {
        padding: 40px 0;
    }
</style>
<p class="cart-attribute__field form-scheduled-delivery">
    <label for="scheduled-delivery">配送希望日</label>
    <input type="text" id="scheduled-delivery" name="attributes[配送希望日]" />
    <input type="hidden" id="scheduled-delivery-hidden" name="attributes[form-scheduled-delivery-key-name]" />
</p>
