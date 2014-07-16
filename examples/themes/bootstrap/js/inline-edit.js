$(document).on('click','table.inline-edit a.row-remove', function(event){
    $(event.target).closest('tr').remove();
    return false;
});

$(document).on('click','table.inline-edit a.row-add', function(event){

    var table = $(event.target).closest('table');
    var copy = table.find('tbody tr:first-child').clone();

    // Find a free number
    var highest = 0;
    var num = 0;

    $(table).find('input,select,textarea').each(function(){
            var element = $(this);
            var name = element.attr('name');
            var number = name.replace( /[^\d.]/g, '');
            if(number){
                var num = parseInt(number);
                if(num && num > highest){
                    highest = num;
                }
            }
    });

    if(copy){
        $(copy).find('input,select,textarea').each(function(){
            var element = $(this);
            var name = element.attr('name');
            var id = element.attr('id');
            var number = name.replace( /[^\d.]/g, '');
            var next = highest + 1;
            element.attr('name',name.replace('['+number+']','['+next+']'));
            element.attr('id',id.replace(number,next));
            if(element.attr('name') == 'id'){
                element.attr('value','');
            }
        });
        copy.appendTo(table.find('tbody'));
    }

    return false;
});