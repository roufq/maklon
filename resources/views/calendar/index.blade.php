@extends('layouts.app')

@section('title', 'Calendar - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Calendar</h1>
            <p class="text-muted mb-0">View and manage project deadlines and schedules</p>
        </div>
        <div class="d-flex gap-2">
            <select id="calendarView" class="form-select" style="width: auto;">
                <option value="month">Month</option>
                <option value="week">Week</option>
                <option value="day">Day</option>
            </select>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="bi bi-plus-circle me-2"></i>Add Event
            </button>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" name="end_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="task">Task Deadline</option>
                            <option value="meeting">Meeting</option>
                            <option value="milestone">Milestone</option>
                            <option value="reminder">Reminder</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select class="form-select" name="project_id">
                            <option value="">No Project</option>
                            @foreach(\App\Models\Project::all() as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetails">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editEventBtn">Edit</button>
                <button type="button" class="btn btn-danger" id="deleteEventBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
#calendar {
    min-height: 600px;
}

.fc-event {
    cursor: pointer;
}

.fc-event.task {
    background-color: #007bff;
    border-color: #007bff;
}

.fc-event.meeting {
    background-color: #28a745;
    border-color: #28a745;
}

.fc-event.milestone {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.fc-event.reminder {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/api/calendar/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                })
            })
            .then(response => response.json())
            .then(data => {
                successCallback(data);
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                failureCallback(error);
            });
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            // Open add event modal with pre-filled date
            document.querySelector('input[name="start_date"]').value = info.dateStr + 'T09:00';
            document.querySelector('input[name="end_date"]').value = info.dateStr + 'T17:00';
            new bootstrap.Modal(document.getElementById('addEventModal')).show();
        },
        eventClassNames: function(arg) {
            return [arg.event.extendedProps.type];
        }
    });

    calendar.render();

    // Handle calendar view change
    document.getElementById('calendarView').addEventListener('change', function() {
        const view = this.value;
        if (view === 'month') {
            calendar.changeView('dayGridMonth');
        } else if (view === 'week') {
            calendar.changeView('timeGridWeek');
        } else if (view === 'day') {
            calendar.changeView('timeGridDay');
        }
    });

    // Handle add event form submission
    document.getElementById('addEventForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/api/calendar/events', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
                this.reset();
            } else {
                alert('Error adding event');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding event');
        });
    });
});

function showEventDetails(event) {
    document.getElementById('eventTitle').textContent = event.title;

    const details = `
        <p><strong>Description:</strong> ${event.extendedProps.description || 'No description'}</p>
        <p><strong>Start:</strong> ${event.start.toLocaleString()}</p>
        <p><strong>End:</strong> ${event.end ? event.end.toLocaleString() : 'N/A'}</p>
        <p><strong>Type:</strong> ${event.extendedProps.type}</p>
        ${event.extendedProps.project ? `<p><strong>Project:</strong> ${event.extendedProps.project.name}</p>` : ''}
    `;

    document.getElementById('eventDetails').innerHTML = details;
    new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();
}
</script>
@endsection
