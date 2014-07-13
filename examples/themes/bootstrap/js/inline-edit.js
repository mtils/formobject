$(document).on('click','table.inline-edit a.row-remove', function(event){
    console.log($(event.target).parent('tr'));
    console.log($(event.target).parent());
    $(event.target).closest('tr').remove();
//     $(event.target).parent('tr').hide();
    return false;
});