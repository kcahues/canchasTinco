<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src='/calendar/calendar/calendar/dist/index.global.min.js'></script>
    <script src="js/sweetalert2.all.min.js"></script>

    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          height: 650,
          events: 'fetchEvents.php',

	selectable: true,
	select: async function (start, end, allDay) {
	  const { value: formValues } = await Swal.fire({
		title: 'Add Event',
		html:
		  '<input id="swalEvtTitle" class="swal2-input" placeholder="Enter title">' +
		  '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Enter description"></textarea>' +
		  '<input id="swalEvtURL" class="swal2-input" placeholder="Enter URL">',
		focusConfirm: false,
		preConfirm: () => {
		  return [
			document.getElementById('swalEvtTitle').value,
			document.getElementById('swalEvtDesc').value,
			document.getElementById('swalEvtURL').value
		  ]
		}
	  });

	  if (formValues) {
		// Add event
		fetch("eventHandler.php", {
		  method: "POST",
		  headers: { "Content-Type": "application/json" },
		  body: JSON.stringify({ request_type:'addEvent', start:start.startStr, end:start.endStr, event_data: formValues}),
		})
		.then(response => response.json())
		.then(data => {
		  if (data.status == 1) {
			Swal.fire('Event added successfully!', '', 'success');
		  } else {
			Swal.fire(data.error, '', 'error');
		  }

		  // Refetch events from all sources and rerender
		  calendar.refetchEvents();
		})
		.catch(console.error);
	  }
	},

eventClick: function(info) {
  info.jsEvent.preventDefault();
  
  // change the border color
  info.el.style.borderColor = 'red';
  
  Swal.fire({
    title: info.event.title,
    icon: 'info',
    html:'<p>'+info.event.extendedProps.description+'</p><a href="'+info.event.url+'">Visit event page</a>',
    showCloseButton: true,
    showCancelButton: true,
    showDenyButton: true,
    cancelButtonText: 'Close',
    confirmButtonText: 'Delete',
    denyButtonText: 'Edit',
  }).then((result) => {
    if (result.isConfirmed) {
      // Delete event
      fetch("eventHandler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ request_type:'deleteEvent', event_id: info.event.id}),
      })
      .then(response => response.json())
      .then(data => {
        if (data.status == 1) {
          Swal.fire('Event deleted successfully!', '', 'success');
        } else {
          Swal.fire(data.error, '', 'error');
        }

        // Refetch events from all sources and rerender
        calendar.refetchEvents();
      })
      .catch(console.error);
    } else if (result.isDenied) {
      // Edit and update event
      Swal.fire({
        title: 'Edit Event',
        html:
          '<input id="swalEvtTitle_edit" class="swal2-input" placeholder="Enter title" value="'+info.event.title+'">' +
          '<textarea id="swalEvtDesc_edit" class="swal2-input" placeholder="Enter description">'+info.event.extendedProps.description+'</textarea>' +
          '<input id="swalEvtURL_edit" class="swal2-input" placeholder="Enter URL" value="'+info.event.url+'">',
        focusConfirm: false,
        confirmButtonText: 'Submit',
        preConfirm: () => {
        return [
          document.getElementById('swalEvtTitle_edit').value,
          document.getElementById('swalEvtDesc_edit').value,
          document.getElementById('swalEvtURL_edit').value
        ]
        }
      }).then((result) => {
        if (result.value) {
          // Edit event
          fetch("eventHandler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ request_type:'editEvent', start:info.event.startStr, end:info.event.endStr, event_id: info.event.id, event_data: result.value})
          })
          .then(response => response.json())
          .then(data => {
            if (data.status == 1) {
              Swal.fire('Event updated successfully!', '', 'success');
            } else {
              Swal.fire(data.error, '', 'error');
            }

            // Refetch events from all sources and rerender
            calendar.refetchEvents();
          })
          .catch(console.error);
        }
      });
    } else {
      Swal.close();
    }
  });
}
});

calendar.render();
});

    </script>
</head>
<body>
    <section class="container my-5" >
        <div id="calendar"></div>
      </section>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/script.js"></script>
</body>
</html>