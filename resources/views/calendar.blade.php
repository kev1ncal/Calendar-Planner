@extends('layout.index')
@section('content')
    <div class="container">

        <!-- toast notification here -->
        <div class="toast d-flex align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="1090" style="position: absolute; top: 30px; right: 30px;">
            <div class="toast-body">
                Event created successfully.
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>

        <div class="shadow-lg mb-5 mt-5 p-5 bg-white rounded">
            <div class="row" id="main_row">
                <div class="col-md">
                    <h1>Calendar</h1>
                    <hr>
                    <form class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="eventName" class="form-label">Event</label>
                            <input type="text" class="form-control" id="eventName" required>
                            <div class="invalid-feedback">
                                Please don't leave this blank.
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col">
                                <label for="fromDate" class="form-label">From</label>
                                <input type="date" class="form-control" value="{{ $fromDate }}" id="fromDate" required>
                                <div class="invalid-feedback">
                                    Please select a date.
                                </div>
                            </div>
                            <div class="col">
                                <label for="toDate" class="form-label">To</label>
                                <input type="date" class="form-control" value="{{ $toDate }}" id="toDate"  required>
                                <div class="invalid-feedback">
                                    Please select a date.
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Monday" id="monday">
                                    <label class="form-check-label" for="monday">
                                        Mon
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Tuesday" id="tuesday">
                                    <label class="form-check-label" for="tuesday">
                                        Tues
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Wednesday" id="wednesday">
                                    <label class="form-check-label" for="wednesday">
                                        Wed
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Thursday" id="thursday">
                                    <label class="form-check-label" for="thursday">
                                        Thu
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Friday" id="friday">
                                    <label class="form-check-label" for="friday">
                                        Fri
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Saturday" id="saturday">
                                    <label class="form-check-label" for="saturday">
                                        Sat
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Sunday" id="sunday">
                                    <label class="form-check-label" for="sunday">
                                        Sun
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                
                <div class="col-md" id="plannerTab">
                    <h1 id="monthInput">{{ $monthNow }}</h1>
                    <hr>
                    @foreach($days as $day)
                        <p>{{ $day }}</p>
                        <hr>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script type="text/javascript">
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        //these prevents the page from refreshing
                        event.preventDefault()
                        event.stopPropagation()
                    }else{
                        //now do your ajax call here
                        var eventName = $("#eventName").val();
                        var fromDate = $("#fromDate").val();
                        var toDate = $("#toDate").val();
                        var days = [];

                        $(":checkbox:checked").each(function(){
                            days.push($(this).val());
                        });



                        $.ajax({
                            url: '/create-event',
                            type: 'GET',
                            dataType: "json",
                            data: {
                                eventName: eventName,
                                fromDate: fromDate,
                                toDate: toDate,
                                days: days,
                            },
                            success: function(response){
                                // change the month and days display here
                                // alert(response.monthNow);
                                $("#plannerTab").remove();
                                $("<div class='col-md' id='plannerTab'>").appendTo("#main_row");
                                $("<h1 id='monthInput'>"+ response.monthNow +"</h1><hr>").appendTo("#plannerTab");


                                //this will get all the data from the response (which is an array)
                                //key is the index
                                //value is the value inside the index
                                
                                $.each(response.days, function (key, value) {
                                    var hit = 0; // this is to see if the checked checkbox is amongst the given dates

                                    //now calling it as the value will be okay, since the key is present
                                    //without the key, this will ouput the index itself not the value
                                    // alert(value);
                                    $(":checkbox:checked").each(function(){
                                        if(value.includes($(this).val())){
                                            hit++; //if it has included in the checked ones, increment the hit
                                            $("<div class='alert alert-success'>" + value + "&nbsp&nbsp&nbsp---&nbsp&nbsp&nbsp" + response.eventName + "</alert>").appendTo("#plannerTab");
                                        }
                                    });
                                    if(hit == 0){ // dont make any paragraph if the hit is still on.
                                        $("<p>" + value + "</p><hr>").appendTo("#plannerTab");
                                    }
                                });
                                $('.toast').toast('show');
                            }
                        });
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })();

</script>
@endsection