$(function (e) {
    'use strict';
  
        $('#load-more').on('click', function() {
            var offset = parseInt($(this).attr('data-offset'));
            var limit = parseInt($(this).attr('data-limit'));
            $(this).addClass('btn-loader');
            $(this).find('.loadMoreText').text('Loading...');
            var column = $("#datatable-basic").attr('data-sorting');
            var direction = $("#datatable-basic").attr('data-order');
            
            loadData(offset,limit,"default",direction,column);
        });
        $('.sortable').on('click', function() {
            var column = $(this).data('column');
            var direction = 'asc'; // Default direction
            var className = 'ri-sort-asc'; // Default direction
            if ($(this).find('.sort-icon').hasClass('ri-sort-asc')) {
                direction = 'desc';
                className = 'ri-sort-desc';
            }
        
            $(this).find('.sort-icon').removeClass('ri-sort-asc ri-sort-desc');
            $(this).find('.sort-icon').addClass(className);
            var offset = 0;
            var limit = parseInt($('#load-more').attr('data-offset'));
            $("#datatable-basic").attr('data-order',direction)
            $("#datatable-basic").attr('data-sorting',column)
            loadData(offset,limit,'sort',direction,column);
        });

        $('#kt_search_btn').click(function(event) {
            $('.sort-icon').removeClass('ri-sort-asc ri-sort-desc');
            $('.sort-icon').addClass('ri-sort-asc');
            var offset = parseInt($('#load-more').attr('data-default-offset'));
            var limit = parseInt($('#load-more').attr('data-default-limit'));
            loadData(offset, limit,'search', "","");
        });
        function loadData(offset = "", limit = "",type="default", order = "", sortBy = "") {
            $('#loader-row').show();
            var formData = new FormData($('#listSearchForm')[0]);
            formData.append('offset', offset);
            formData.append('limit', limit);
            formData.append('order', order);
            formData.append('sortBy', sortBy);

            $.ajax({
                url: routeName,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false, 
                contentType: false,  
                success: function(response) {
                    if (response) {
                        if(type == 'sort' || type == 'search'){
                            $('#loader-row').nextAll().remove();
                            $('#loader-row').after(response);
  
                            if($('.noresults-row') && $('.noresults-row').length > 0){
                                $('#load-more').hide();
                            }else{
                                if($('.list-data-row') && parseInt($('.list-data-row').attr('data-total-count')) > parseInt(limit)){
                                    $('#load-more').show();  
                                }else{
                                    $('#load-more').hide(); 
                                }
                            }
                        }else{
                            $('#datatable-basic tbody').append(response);
                            if($('.noresults-row') && $('.noresults-row').length > 0){
                                $('.noresults-row').remove();
                                $('#load-more').hide();
                            }
                        }
                        $('#load-more').attr('data-offset',parseInt(offset + limit));
                        
                    } else {
                        $('#load-more').hide();
                    }
                    // Hide the loader row after appending data
                    $('#loader-row').hide();
                    $('#load-more').removeClass('btn-loader');
                    $('.loadMoreText').text('Load More');
                    if($('.list-data-row') && $('.list-data-row').length > 0){
                        $('.totalDataCount').text($('.list-data-row').attr('data-total-count'));
                    }else{
                        $('.totalDataCount').text(0);
                    }
                },
                error: function() {
                    // In case of an error, ensure the loader row is hidden
                    $('#loader-row').hide();
                    $('#load-more').removeClass('btn-loader');
                    $('.loadMoreText').text('Load More');
                }
            });
        };

    
  
    // basic datatable
    // $('#datatable-basic').DataTable({
    //     "processing": true,
    //     "serverSide": true,
    //     "ajax": {
    //         "url": "{{ $listRouteName }}",
    //         "data": function (d) {
    //             d.length = length;
    //             d.start = start;
    //         },
    //         "dataSrc": function (response) {
               
    //             return response.results;
    //         },
    //         "columns" : [
    //             { "data": "id", "title": "ID" },
    //             { "data": "name", "title": "Name" },
    //             // Other columns defined similarly
    //         ]
    //     }
    // });

    $('#loadMoreBtn').on('click', function() {
        start += length;
        table.ajax.reload();
    });
    // basic datatable

    // responsive datatable
    $('#responsiveDataTable').DataTable({
        responsive: true,
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        },
        "pageLength": 10,
    });
    // responsive datatable

    // responsive modal datatable
    $('#responsivemodal-DataTable').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data[0] + ' ' + data[1];
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        }
    });
    // responsive modal datatable

    // file export datatable
    $('#file-export').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollX: true
    });
    // file export datatable

    // delete row datatable
    var table = $('#delete-datatable').DataTable({
        language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
        }
    });
    $('#delete-datatable tbody').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });
    $('#button').on("click", function () {
        table.row('.selected').remove().draw(false);
    });
    // delete row datatable

    // scroll vertical 
    $('#scroll-vertical').DataTable({
        scrollY: '265px',
        scrollCollapse: true,
        paging: false,
        scrollX: true,
    });
    // scroll vertical 

    // hidden columns
    $('#hidden-columns').DataTable({
        columnDefs: [
            {
                target: 2,
                visible: false,
                searchable: false,
            },
            {
                target: 3,
                visible: false,
            },
        ],
        "pageLength": 10,
        scrollX: true
    });
    // hidden columns
    
    // add row datatable
    var t = $('#add-row').DataTable();
    var counter = 1;
    $('#addRow').on('click', function () {
        t.row.add([counter + '.1', counter + '.2', counter + '.3', counter + '.4', counter + '.5']).draw(false);
        counter++;
    });
    // add row datatable

});

