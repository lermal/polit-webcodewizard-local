const formData = new FormData(form);
formData.append('admin_name', $('#admin_name').val());
formData.append('admin_email', $('#admin_email').val());

if ($('#admin_password').val()) {
    if ($('#admin_password').val() !== $('#admin_password_confirmation').val()) {
        showError('Пароли не совпадают');
        return;
    }
    formData.append('admin_password', $('#admin_password').val());
}

// Отправка на сервер
$.ajax({
    url: '/admin/settings/update',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
        if (response.success) {
            $('#success-alert-modal').modal('show');
            // Обновляем отображаемое имя в шапке
            $('.account-user-name').text($('#admin_name').val());
        } else {
            showError(response.message);
        }
    },
    error: function(xhr) {
        showError('Произошла ошибка при сохранении настроек');
    }
});
