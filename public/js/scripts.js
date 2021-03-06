$( document ).ready(function() {

    // console.log('last script');

    $('header .mobile_nav_button').on('click', function(){
        $('header .mobile_nav').slideToggle();
    });

    $('header .profile_name').on('click', function(){
        $(this).siblings('.header_dropdown').slideToggle();
    });

    $('header .current_language').on('click', function(){
        $(this).siblings('.header_dropdown').slideToggle();
    });

    /************* Pie Charts ***********/
    $('#chart_active').pieChart({
        barColor: '#2cc763',
        trackColor: '#ecf0f1',
        lineCap: 'round',
        lineWidth: 6,
        size: 115,
        onStep: function (from, to, percent) {
            $(this.element).find('.pie-value').text(Math.round(percent) + '%');
        }
    });

    $('#chart_pending').pieChart({
        barColor: '#ffca14',
        trackColor: '#ecf0f1',
        lineCap: 'round',
        lineWidth: 6,
        size: 115,
        onStep: function (from, to, percent) {
            $(this.element).find('.pie-value').text(Math.round(percent) + '%');
        }
    });

    $('#chart_not_used').pieChart({
        barColor: '#8d97a6',
        trackColor: '#ecf0f1',
        lineCap: 'round',
        lineWidth: 6,
        size: 115,
        onStep: function (from, to, percent) {
            $(this.element).find('.pie-value').text(Math.round(percent) + '%');
        }
    });

    $('#chart_average').pieChart({
        barColor: '#2cc763',
        trackColor: '#8d97a6',
        lineCap: 'round',
        lineWidth: 6,
        size: 115,
        onStep: function (from, to, percent) {
            $(this.element).find('.pie-value').text(Math.round(percent) + '%');
            $(this.element).closest('.average_numbers').find('.average_value > .value').text(Math.round(percent) + '%');
            var remainder = 100 - percent;
            $(this.element).closest('.average_numbers').find('.remainder_value > .value').text(Math.round(remainder) + '%');

        }
    });

    /***************** end of Pie Charts *****************/

     /**************** Style select tag ******************/

    // Iterate over each select element
    $('.styled_select').each(function () {

        // Cache the number of options
        var $this = $(this),
            numberOfOptions = $(this).children('option').length;

        // Hides the select element
        $this.addClass('s-hidden');

        // Wrap the select element in a div
        $this.wrap('<div class="select"></div>');

        $this.closest('.select').addClass('block_btn login_input');
        // Insert a styled div to sit over the top of the hidden select element
        $this.after('<div class="styled_select"></div>');

        // Cache the styled div
        var $styledSelect = $this.next('div.styled_select');

        $('.select').append('<i class="icon-dropdown"></i>');

        // Show the first select option in the styled div
        $styledSelect.text($this.children('option').eq(0).text());

        // Insert an unordered list after the styled div and also cache the list
        var $list = $('<div class="options"></div>').insertAfter($styledSelect);

        // Insert a list item into the unordered list for each select option
        for (var i = 0; i < numberOfOptions; i++) {
            $('<div />', {
                class: 'imitation_item',
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list);
        }

        // Cache the list items
        var $listItems = $list.children('.imitation_item');

        // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
        $styledSelect.click(function (e) {
            e.stopPropagation();
            $(this).toggleClass('active').next('.options').toggle();
            $(".options").perfectScrollbar('update');
        });

        // Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
        // Updates the select element to have the value of the equivalent option
        $listItems.click(function (e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active');
            $this.val($(this).attr('rel'));
            $list.hide();
            /* alert($this.val()); Uncomment this for demonstration! */
        });

        // Hides the unordered list when clicking outside of it
        $(document).click(function () {
            $styledSelect.removeClass('active');
            $list.hide();
        });

    });

    $('.options').perfectScrollbar();
    /************ end of styling select tag *************/

    // rotate arrow for nested rows
    $(document).on('click', '.open_nested', function (e) {

        e.preventDefault();
        $(this).find('.icon-dropdown').toggleClass('expanded');
    });


    /********** Uploaded Image Name ***********/
    $('.modal .file_container').on('click', function(e){

        $(this).find('.modal_image_name').click(function(e){
            e.stopImmediatePropagation();
        });
    });

    $('.modal_image_name').change(function (e) {

        if (this.files && this.files[0]) {

            var file_ext = this.files[0].type.split('/')[1].toLowerCase();

            if($.inArray(file_ext, ['xls','xlsx', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet']) == -1) {

                $(this).parent('.file_container').siblings('.uploaded_file_links').find('.download_file').addClass('disabled');
                alert('invalid extension!!!!');

            }else{
                var file_name = this.files[0].name;
                var tmp_path = URL.createObjectURL(e.target.files[0]);

                $(this).parent().siblings('.keep_file_name').html(file_name);
            }
        }

    });
    /********** end of Uploaded Image Name ***********/

    /* Owl Slider in Orders List Modal*/


    $('#modal_new_order').on('show.bs.modal', function () {
        // do something…
        setTimeout(function(){

            $('#time_element').timepicki({
                show_meridian:false,
                min_hour_value:0,
                max_hour_value:23,
                step_size_minutes:15,
                overflow_minutes:true,
                increase_direction:'up',
                disable_keyboard_mobile: true,
                start_time: ["00", "00"]
            });

            $('#time_element2').timepicki({
                show_meridian:false,
                min_hour_value:0,
                max_hour_value:23,
                step_size_minutes:15,
                overflow_minutes:true,
                increase_direction:'up',
                disable_keyboard_mobile: true,
                start_time: ["00", "00"]
            });

            $('.wrap_package_list').show(); // show package list after modal was open
            $('.wrap_package_list').owlCarousel({
                nav : true,
                navText : ['<i class="vd_prev icon-dropdown"></i>', '<i class="vd_next icon-dropdown"></i>'],
                margin : 22,
                responsive:{
                    0:{
                        items:1
                    },
                    480:{
                        items:2,
                        margin : 48
                    },
                    640:{
                        items:3,
                        margin : 28
                    },
                    1000:{
                        items:2
                    },
                    1200:{
                        items:3
                    }
                }
            });

            $( "<span class='colon'>:</span>" ).insertAfter($(".wrap_time").find(".time"));

        }, 600);


    });


    $('#modal_new_order').on("hidden.bs.modal", function () {

        $(this).find('.time_pick').remove();
        $(this).find('.timepicker_wrap').remove();


        $('#time_element').remove();
        $('#time_element2').remove();

        $('#lnd_time').append('<input type="text" name="timepicker" id="time_element" class="inline_block_btn time_element vd_time_required"/>');
        $('#dpr_time').append('<input type="text" name="timepicker2" id="time_element2" class="inline_block_btn time_element vd_time_required"/>');
        $(this).find('form')[0].reset();
        /**** PUT Reload Logic Here****/
        if (reload){
            location.reload();
        }

    });

    $('#modal_order_email').on("hidden.bs.modal", function () {
        if ($("#refresh").val() == "refresh"){
        location.reload();
        }
    });

    $('#cancel_order').on("click", function () {

       $(this).closest('#modal_new_order').modal('hide');
        $('#time_element').val('');
        $('#time_element2').val('');

        return false;
    });




    $('#modal_edit_order').on('show.bs.modal', function () {

        $('#time_element3').timepicki({
            show_meridian:false,
            min_hour_value:0,
            max_hour_value:23,
            step_size_minutes:15,
            overflow_minutes:true,
            increase_direction:'up',
            disable_keyboard_mobile: true,
            start_time: ["00", "00"]
        });

        $('#time_element4').timepicki({
            show_meridian:false,
            min_hour_value:0,
            max_hour_value:23,
            step_size_minutes:15,
            overflow_minutes:true,
            increase_direction:'up',
            disable_keyboard_mobile: true,
            start_time: ["00", "00"]
        });

        // do something…
        setTimeout(function(){

            $('.wrap_package_list_edit').show(); // show package list after modal was open
            $('.wrap_package_list_edit').owlCarousel({
                nav : true,
                navText : ['<i class="vd_prev icon-dropdown"></i>', '<i class="vd_next icon-dropdown"></i>'],
                margin : 22,
                responsive:{
                    0:{
                        items:1
                    },
                    480:{
                        items:2,
                        margin : 48
                    },
                    640:{
                        items:3,
                        margin : 28
                    },
                    1000:{
                        items:2
                    },
                    1200:{
                        items:3
                    }
                }
            });
        }, 600);

    });


    /* Bootstrap Datepicker */
    var date = new Date();
    date.setDate(date.getDate());

    // console.log('DATE ', date);

    $('.date').datetimepicker();

    $('.lnd').datetimepicker({
        showClear: true,
        minDate: date
    });
    $('.dpr').datetimepicker({
        showClear: true,
        minDate: date
    });

    var ln_date_min, dp_date_max ;

    $('#lnd').on('dp.change', function(e){

        $("#dpr").prop('disabled', true);

        console.log('lnd ', $(this));
        ln_date_min =  $('#landing_date').closest('.lnd').data('date');

        var res = ln_date_min.split('/');
        var final_res = res[1] + '/' + res[0] + '/' + res[2];

        $('#departure_date').val("");
        $('#departure_date').closest('.dpr').data("DateTimePicker").minDate(ln_date_min);
        $('#departure_date').closest('.dpr').find('td.active').removeClass('active');
        $('#departure_date').closest('.dpr').find('td[data-day="' + final_res + '"]').addClass('active');
        // $('#time_element').cle
    });

    $('#dpr').on('dp.change', function(e){
        console.log('dpr ', $(this));
        var check_min_date =  $(this).closest('#modal_new_order').find('#landing_date').parent('.lnd').data('date');
        console.log('departure ', check_min_date);

        if(typeof check_min_date != 'undefined'){
            dp_date_max =  $('#departure_date').closest('.dpr').data('date');
            $('#landing_date').closest('.lnd').data("DateTimePicker").maxDate(dp_date_max);
        } else if(typeof check_min_date == 'undefined') {
            //e.preventDefault();
            console.log('Please choose landing date first ');
        }


    });

    $('#modal_new_order').on("hidden.bs.modal", function () {

        // reset datepicker for new order modal
        var id = $(this).attr('id');

        if(id == 'modal_new_order'){
            // console.log('IF ', id);
            $('#' + id).find('#landing_date').val("");
            $('#' + id).find('#departure_date').val("");

            $('#' + id).find('.flight_dates').datetimepicker({
                showClear: true,
                minDate: date
            });
            $('#' + id).find('.flight_dates').data("DateTimePicker").maxDate(false);
            $(this).find('form')[0].reset();

        }

    });


    /* Put Editable values inside modal window */
    $(document).on('click', '.table .table_action_cell .edit', function () {

        var target_form_id;
        target_form_id = $(this).attr('data-form');

        $(this).closest('tr').find('.editable_cell').each(function () {
            
            var attribute_title = $(this).attr('data-th');
            var cell_value = $(this)[0].innerHTML.trim();
            var cell_status;

            if (attribute_title == "Status"){
                cell_status = $(this).find('.table_status_text').text();
            }

            // Capture Modal Open Event
            $(target_form_id).one('shown.bs.modal', function () {

                if(attribute_title == "Id"){ // set form action id

                    var form_action = $(this).find('form').attr('action');
                    $(this).find('form').attr('action', form_action + '/' + cell_value);
                }
                var prop_name = $(this).find('[data-th="' + attribute_title + '"]').prop("tagName");

                if(prop_name){

                    if(prop_name.toUpperCase()  == "INPUT"){

                        $(this).find('input[data-th="' + attribute_title + '"]').each(function(){

                            $(this).val(cell_value);
                            if(attribute_title == "Status"){

                                if(cell_status == "parking"){

                                    $(this).prop('checked', true);
                                }
                            }
                        });

                    } else if(prop_name.toUpperCase()  == "SELECT"){

                        $(this).find('select[data-th="' + attribute_title + '"] option').each(function () {
                            if ($(this).text().toLowerCase() == cell_value.toLowerCase()) {
                                $(this).prop('selected','selected');
                                return;
                            }
                        });

                    } else if(prop_name.toUpperCase()  == "TEXTAREA"){

                        $(this).find('textarea[data-th="' + attribute_title + '"]').each(function(){

                            $(this).val(cell_value);
                        });
                    }
                } else {
                    // console.log('Property data-th="' + attribute_title + '" not found');
                }
            });

        });



        // Capture Modal Close Event
        $(target_form_id).one("hidden.bs.modal", function () {

            // put your default event here
            var form_action = $(this).find('form').attr('action');
            var reset_form_action = form_action.split('/')[0];

            $(this).find('form').attr('action', reset_form_action + '/');
            $(this).find('form')[0].reset();
        });

    });

    /* Bootstrap Modal Close Event */
    $('.modal').one("hidden.bs.modal", function () {

        if(typeof $(this).find('form')[0] != "undefined"){
            $(this).find('form')[0].reset();
        }
        $(".error_response").empty();
        $(".success_response").empty();


    });



    /* highlight selected package */
    $(document).on('click', '.package_item a', function () {

        $(this).addClass('selected_package');
        $(this).parents('.owl-item').siblings('.owl-item').find('a').removeClass('selected_package');
        return false;
    });

    /* Print Message in browser */
    $(document).on('click', '.email_send_print', function () {

        onPrintFinished(window.print());
    });


    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
        $('.table').on('all.bs.table', function (e, name, args) {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        });
    });

    /* Open order modal after double click */

    $('.link').on('click', function (event) {
        event.preventDefault();
    });

    $('.table').on('dbl-click-row.bs.table', function ($element, row, field) {
        $('#modal_view_order').modal('show');
        /******* Open View modal  *******/
        $('.vdf_modal_sub_header > h3').text('View Order #' + row.id);
        $('#wrap_package_list_view').empty();
        $('#wrap_package_list_view').append("<label class='table_label'>Selected Package </label>" +
            "<a class='selected_package' title='"+ row.package.name +"'>" +
            "<h4>"+ row.package.name +"</h4>" +
            "<span>"+ row.package.description +"</span>" +
            "</a>");
        $('.sim-edit').val(row.sim.number);
        $('.remark-view').val(row.remark);
        $('.reference_number-view').val(row.reference_number);
        if (row.status != "waiting") {
            $('#phone_number-view2').val(row.phone.phone);
        }
        $('#order_status-view').val(row.status);
        $('.creator').text(row.creator.name + " " + row.created_at);
        if (row.editor != null)
            $('.editor').text(row.editor.name);
        $('.edited_at').text(" " + row.updated_at);
        $('#landing_date_view').val(row.landing.split(' ')[0]);
        $('#departure_date_view').val(row.departure.split(' ')[0]);
        $('.landing_time_val').val(row.landing.split(' ')[1]);
        $('.departure_time_val').val(row.departure.split(' ')[1]);

    });


    /* Responsive scroll bars */
    $('#wrap_tree_table').perfectScrollbar();

    /*** Bootstrap modal open event ****/
    $("#modal_add_user").on('shown.bs.modal', function(event){
        $('#add-user')[0].reset();
    });


});


$(window).load(function() {
    $(".loader_inner").fadeOut();
    $(".loader").delay(400).fadeOut("slow");
});

var onPrintFinished=function(printed){
    $('#modal_print_order').modal('toggle');
};



