@extends('layouts.app')

@section('content')
<div class="container">
    <div class="response"></div>
    <div id='calendar'></div>
    @foreach ($timesheets as $index => $timesheet)
        <div id="timesheetModal{{{$timesheet['date']}}}" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 id="sheetDate{{{$timesheet['date']}}}" class="modal-title">{{ $timesheet['date'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="accordionExample">
                            @foreach ($timesheet['tasks'] as $tindex => $task)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{{$task['id']}}}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{{$task['id']}}}" aria-expanded="true" aria-controls="collapse{{{$task['id']}}}">
                                    {{ $task['title'] }}
                                </button>
                                </h2>
                                <div id="collapse{{{$task['id']}}}" class="accordion-collapse collapse" aria-labelledby="heading{{{$task['id']}}}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {{ $task['content'] }}
                                </div>
                                </div>
                            </div>
                            @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Detail</button>
                </div>
                </div>
            </div>
        </div>
        <button id="modalToggle{{{$timesheet['date']}}}" type="button" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#timesheetModal{{{$timesheet['date']}}}">
        </button>
    @endforeach
    <div id="timesheetModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 id="sheetDate" class="modal-title">{{ $timesheet['date'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h2>Empty</h2>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Add task</button>
                </div>
                </div>
            </div>
        </div>
        <button id="modalToggle" type="button" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#timesheetModal">
        </button>
</div>
@endsection

@section('scripts')
<script type="module">
    $(document).ready(function() {
        let SITEURL = "{{url('/')}}";
        let list = {!! str_replace("'", "\'", json_encode($timesheets)) !!};
        let events = [];
        list.forEach(item => {
            events.push({
                id: `In ${item.id}`,
                title: `Check in: ${item.check_in}`,
                start: item.date,
            })
            events.push({
                id: `Out ${item.id}`,
                title: `Check out: ${item.check_in}`,
                start: item.date,
                backgroundColor: '#198754',
            })
        })
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let calendar = $('#calendar').fullCalendar({
            editable: true,
            displayEventTime: true,
            editable: true,
            events: events,
            eventRender: function (event, element, view) {
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: true,
            selectHelper: true,
            select: function (start, end, allDay) {
                let dateId = $.fullCalendar.formatDate(start, "YYYY-MM-DD");
                let timesheet = null;
                $.ajax({
                    url: `/timesheet-detail`,
                    data: {'date': dateId},
                    type: 'GET',
                    success: function (data) {
                        timesheet = data.data;
                        if(Object.keys(timesheet).length) {
                            $(`#modalToggle${timesheet.date}`).click();
                            $(`#sheetDate${timesheet.date}`).text($.fullCalendar.formatDate(start, "Y MM DD"));
                        } else {
                            $(`#sheetDate`).text($.fullCalendar.formatDate(start, "Y MM DD"));
                            $(`#modalToggle`).click();
                        }
                    }
                })
                
                // let title = prompt('Event Title:');

                // if (title) {
                //     let start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                //     let end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");

                //     $.ajax({
                //         url: SITEURL + "/fullcalendar/create",
                //         data: {'title' : title, 'start': start, 'end': end},
                //         type: "POST",
                //         success: function (data) {
                //             displayMessage("Added Successfully");
                //         }
                //     });
                //     calendar.fullCalendar('renderEvent',
                //         {
                //             title: title,
                //             start: start,
                //             end: end,
                //             allDay: allDay
                //         },
                //         true
                //     );
                // }
                // calendar.fullCalendar('unselect');
            },

            eventDrop: function (event, delta) {
                let start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                let end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                $.ajax({
                    url: SITEURL + '/fullcalendar/update',
                    data: {'title': event.title, 'start': start, 'end': end, 'id': event.id},
                    type: "POST",
                    success: function (response) {
                        displayMessage("Updated Successfully");
                    }
                });
            },
            // eventClick: function (event) {
            //     let deleteMsg = confirm("Do you really want to delete?");
            //     if (deleteMsg) {
            //         $.ajax({
            //             type: "POST",
            //             url: SITEURL + '/fullcalendar/delete',
            //             data: {'id': event.id},
            //             success: function (response) {
            //                 if(parseInt(response) > 0) {
            //                     $('#calendar').fullCalendar('removeEvents', event.id);
            //                     displayMessage("Deleted Successfully");
            //                 }
            //             }
            //         });
            //     }
            // }
        });
    })
</script>
@endsection


