import $ from 'jquery';
import 'bootstrap/js/dist/collapse';

$('.delete-form').on('submit', function (event) {
    if (!window.confirm('Удалить альбом из коллекции?')) event.preventDefault();
});

$('.needs-validation').each(function () {
    const form = this;
    form.noValidate = true;

    // HTML5 проверяет ограничения, а Bootstrap показывает ошибки рядом с полями.
    $(form).on('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            form.querySelector(':invalid')?.focus();
        }
        form.classList.add('was-validated');
    });

    $(form).on('input change', '.form-control', function () {
        this.classList.remove('is-invalid');
    });
});

$('.track-row').each(function () {
    const title = this.querySelector('.track-title');
    const duration = this.querySelector('.track-duration-input');
    function updateRequired() {
        const filled = Boolean(title.value.trim() || duration.value.trim());
        title.required = filled;
        duration.required = filled;
    }
    $(this).on('input', updateRequired);
    updateRequired();
});

let previewUrl;
$('#cover').on('change', function () {
    const input = this;
    const file = input.files[0];
    const preview = document.querySelector('#cover-preview');
    input.setCustomValidity('');
    $('#cover-error').text('');
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    if (!file) return;

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
        const message = 'Нужен JPG, PNG или WebP размером до 2 МБ.';
        input.setCustomValidity(message);
        $('#cover-error').text(message);
        return;
    }

    input.setCustomValidity('Подожди, пока загрузится предпросмотр.');
    const image = new Image();
    previewUrl = URL.createObjectURL(file);
    image.onload = () => {
        const message = image.width > 4096 || image.height > 4096 ? 'Размер изображения не должен превышать 4096 × 4096.' : '';
        input.setCustomValidity(message);
        $('#cover-error').text(message);
        if (!message) {
            preview.src = image.src;
            preview.classList.remove('d-none');
        }
    };
    image.onerror = () => {
        input.setCustomValidity('Не удалось открыть изображение.');
        $('#cover-error').text('Не удалось открыть изображение. Выбери другой файл.');
    };
    image.src = previewUrl;
});

// Стрелки переключают подробные страницы, как карточки в прошлой работе.
$(document).on('keydown', function (event) {
    if ($(event.target).is('input, textarea, select, button, [contenteditable]') || event.ctrlKey || event.metaKey || event.altKey) return;
    const selector = event.key === 'ArrowLeft' ? '[data-previous-album]' : event.key === 'ArrowRight' ? '[data-next-album]' : null;
    const link = selector && document.querySelector(selector);
    if (link) {
        event.preventDefault();
        window.location.href = link.href;
    }
});
