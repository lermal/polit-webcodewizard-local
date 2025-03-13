$(document).ready(function () {
    var table = $('#faq-table').DataTable({
        ajax: {
            url: '/api/faqs',
            dataSrc: ''
        },
        columns: [
            { data: 'question' },
            {
                data: 'id',
                render: function (data, type, row) {
                    return `
<div class="action-buttons">
<a href="javascript: void(0);" class="action-icon btn-edit" data-id="${data}" data-question="${row.question}" data-answer="${row.answer}"> <i class="mdi mdi-pencil"></i></a>
<a href="javascript: void(0);" class="action-icon btn-delete" data-id="${data}"> <i class="mdi mdi-delete"></i></a>
</div>
`;
                },
                orderable: false
            }
        ]
    });

    $('#faq-table').on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        var question = $(this).data('question');
        var answer = $(this).data('answer');

        $('#edit-faq-id').val(id);
        $('#edit-question').val(question);
        $('#edit-answer').val(answer);

        var editFaqModal = new bootstrap.Modal(document.getElementById('editFaqModal'));
        editFaqModal.show();
    });

    // Submit edit form
    $('#edit-faq-form').on('submit', function (e) {
        e.preventDefault();
        var id = $('#edit-faq-id').val();
        var formData = $(this).serialize();

        $.ajax({
            url: '/api/faqs/' + id,
            method: 'PUT',
            data: formData,
            success: function (response) {
                $('#editFaqModal').modal('hide');
                table.ajax.reload();
            },
            error: function (xhr, status, error) {
                console.error('Error updating FAQ: ' + error);
            }
        });
    });

    // Open delete modal
    $('#faq-table').on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        $('#confirm-delete').data('id', id)

        var deleteFaqModal = new bootstrap.Modal(document.getElementById('deleteFaqModal'));
        deleteFaqModal.show();
    });

    // Confirm delete
    $('#confirm-delete').on('click', function () {
        var id = $('#confirm-delete').data('id');

        $.ajax({
            url: '/api/faqs/' + id,
            method: 'DELETE',
            success: function (response) {
                $('#deleteFaqModal').modal('hide');
                table.ajax.reload();
            },
            error: function (xhr, status, error) {
                console.error('Error deleting FAQ: ' + error);
            }
        });
    });


    $('#addFaqForm').on('submit', function (event) {
        event.preventDefault();
        let formData = {
            question: $('#question').val(),
            answer: $('#answer').val(),
        };

        $.ajax({
            url: '/api/faqs',
            method: 'POST',
            data: formData,
            success: function (response) {
                // Обработка успешного ответа
                $('#addFaqModal').modal('hide');
                $('#addFaqForm')[0].reset();
                table.ajax.reload();
            },
            error: function (response) {
                // Обработка ошибок
                let errors = response.responseJSON.errors;
                let errorMessages = '';
                for (let field in errors) {
                    errorMessages += errors[field].join(' ') + '\n';
                }
                alert('Ошибка при добавлении FAQ: \n' + errorMessages);
            }
        });
    });
})
