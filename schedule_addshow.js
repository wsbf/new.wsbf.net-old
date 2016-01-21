/** David Cohen - Fall 2011 / Spring 2012
 *	called by schedule_addshow.php
 *	enables:
 *		- the fancy autocomplete (with support for multiple names)
 *		- the time picker
 *		- adding shows via AJAX
 */

/** on load **/

$(function() {
    $(".success").hide();
    $("#addShowForm").validate({
        rules: {
            names: "required",
            dayID: "required",
            start_time: "required",
            end_time: "required"

        },
        messages: {
            names: "Who's going to do this show?",
            dayID: "Please pick a day",
            start_time: "What time is this show going to start?",
            end_time: "What time is this show going to end?"
        },

        submitHandler: function(form) {
            //			form.submit();
            event.preventDefault();
            $('.error').hide();

            $.ajax({
                type: 'POST',
                url: 'schedule_add.php',
                data: $("#addShowForm").serialize(),
                success: function(html) {
                    $("#successMessage").show('fast',
                    function() {
                        $(this).replaceWith('<div id="successMessage" class="success"><b>Success: ' + html + '</div>');
                    });
                    $("#addShowForm").clearForm();
                    //	$("#usernames").value = '';   // line 161 || type = 'hidden'
                    usernames.clear();
                    return false;
                }
            });
        }

    });


    $("#dayRadio").buttonset();

    /*** CLEAR FORM FUNCTION **/
    $.fn.clearForm = function() {
        return this.each(function() {
            var type = this.type,
            tag = this.tagName.toLowerCase();
            if (tag == 'form')
            return $(':input', this).clearForm();
            if (type == 'text' || type == 'password' || tag == 'textarea' || type == 'hidden')
            this.value = '';
            //	    else if (type == 'checkbox' || type == 'radio')
            //	      this.checked = false;
            else if (tag == 'select')
            this.selectedIndex = 0;
            // -1
        });
    };

    function split(val) {
        return val.split(/,\s*/);
    }
    function extractLast(term) {
        return split(term).pop();
    }
    // don't navigate away from the field on tab when selecting an item
    $("#names").bind("keydown", function(event) {
       	if (event.keyCode === $.ui.keyCode.TAB &&
       		$(this).data("autocomplete").menu.active) {
           		event.preventDefault();
        }
    })
    .autocomplete({
        source: function(request, response) {
            $.getJSON("schedule_addshow_json.php", {
                term: extractLast(request.term)
            },
            response);
        },
        search: function() {
            // custom minLength
            var term = extractLast(this.value);
            if (term.length < 2) {
                return false;
            }
        },
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        select: function(event, ui) {
            var terms = split(this.value);
            var usernames = document.getElementById("usernames").value.split(/,\s*/);
            // remove the current input
            terms.pop();
            usernames.pop();
            // add the selected item
            terms.push(ui.item.label);
            usernames.push(ui.item.value);
            // add placeholder to get the comma-and-space at the end
            terms.push("");
            usernames.push("");
            document.getElementById("usernames").value = usernames.join(", ");
            this.value = terms.join(", ");
            return false;
        }
    });

    /*** Time Entry Stuff ***/
    $('#timeFrom').timeEntry({
        timeSteps: [1, 30, 0],
        defaultTime: "11:00 AM"
    });

    $('#timeTo').timeEntry({
        timeSteps: [1, 30, 0],
        beforeShow: guessEndTime
    });

    // automatically
    function guessEndTime() {
        var timeFrom = $('#timeFrom').timeEntry('getTime');
        if ((timeFrom.getHours() >= 11 && timeFrom.getHours() < 16)
        || (timeFrom.getHours() >= 1 && timeFrom.getHours() < 5)) {
            timeFrom.setMinutes(timeFrom.getMinutes() + 90);
        }
        else {
            timeFrom.setHours(timeFrom.getHours() + 2);
        }

        return {
            defaultTime: timeFrom
        };
    }

    //	$(selector).timeEntry();
    /*

*/


});
// end onload
