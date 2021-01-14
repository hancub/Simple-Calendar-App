<template>	
	<div id="calendar" class="calendar-week" style="background-color:#f0f0ea;">		
		<!-- Connect to Google Calendar (normal) -->
		<div id="information">
			<p class="calender-message" style="text-align:center;margin-bottom:20px;">This application communicate with Google Calender API. Make sure that you have a Google acount with Google Calendar enabled before you press the button.</p>
			<form>
				<div style="text-align:center;"><button id="connect-button" type="button" class="btn btn-primary" style="font-size:16px;" v-on:click="getEvents()">Connect to Google Calendar!</button></div>
			</form>
		</div>
		<!-- Connect to Google Calendar (authorization code) -->
		<div id="information-authorization-code">
			<p class="calender-message">You need a authorization code in order to connect to Google Calender. Follow the steps below:</p>
			<p class="calender-message" style="margin-left:15px;margin-top:5px;">1. Open this link in your browser: <span id="url" style="color:#ff0000;"></span></p>
			<p class="calender-message" style="margin-left:15px;margin-top:5px;">2. Login to your Google acount</p>
			<p class="calender-message" style="margin-left:15px;margin-top:5px;">3. Give 'Simple Calender App' permition to read/edit</p>
			<p class="calender-message" style="margin-left:15px;margin-top:5px;">4. Enter the authorization code below</p>
			<p class="calender-message" style="margin-left:15px;margin-top:5px;">5. Press the button</p>
			<form>
				<label for="authorization-code" class="calender-message" style="font-weight:400;">Authorization code:</label>
				<input type="text" id="authorization-code" size="50">
				<div style="margin-top:15px;margin-left:10px;">
					<button id="connect-authorization-button" type="button" class="btn btn-primary" style="font-size:16px;" v-on:click="getEventsAuth()">Connect with authorization code</button>
				</div>
			</form>
		</div>
		<!-- Calendar content -->
		<div id="calendar-content">
			<section class="calendar-week-header">
				<!-- Week -->
				<div id="selected-week" class="calendar-week-header-selected-week">
					{{ calenderMonth + " " + calenderYear }}
				</div>			
				<!-- Pagination> -->
				<div class="calendar-week-header-selectors">
				  <span id="previous-week-selector" v-on:click="previousWeek">&#10094;</span>
				  <span id="present-week-selector">Week {{ calenderWeek }}</span>
				  <span id="next-week-selector" v-on:click="nextWeek">&#10095;</span>
				</div>
			</section>
			<ol id="days-of-week" class="day-of-week">
				<li>Sun</li>
				<li>Mon</li>
				<li>Tue</li>
				<li>Wed</li>
				<li>Thu</li>
				<li>Fri</li>
				<li>Sat</li>
			</ol>
			<ol id="calendar-days" class="days-grid">
				<week-component v-for="day in calenderDays" v-bind:key="day.id" v-bind:dayofmonth="day.dayofmonth" v-bind:dayofweek="day.dayofweek" v-bind:hourofday="calenderhour" v-bind:events="day.events" v-on:new-event="newEvent($event, day.id, day.dayofweek)"></week-component>
			</ol>
		</div>
		<!-- Modal -->
		<div id="create-event-modal" class="modal" role="dialog">
		  <div class="modal-dialog modal-dialog-centered">
			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 id="modal-title" class="modal-title">New event</h4>
			  </div>
			  <div class="modal-body">
				<form>
					<div class="form-group">
					  <label for="event-title" style="font-size:16px;font-weight:500;">Title:</label>
					  <input type="text" class="form-control" id="event-title" style="font-size:16px;font-weight:400;">
					</div>
					<p style="font-size:16px;font-weight:400;margin-top:20px;"><span id="event-info-text" style="margin-right:10px;"></span><input id="event-start-time"><span style="margin:0px 5px 0px 5px;">&ndash;</span><input id="event-stop-time"></span></p>				
				</form>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button id="create-event-button" type="button" class="btn btn-primary" disabled = "disabled" v-on:click="createNewEvent()">Create</button>
				<button id="update-event-button" type="button" class="btn btn-primary" v-on:click="updateEvent()">Update</button>
				<button id="remove-event-button" type="button" class="btn btn-danger" v-on:click="deleteEvent()">Delete</button>
			  </div>
			</div>
		  </div>
		</div>
	</div>
</template>
<script>
  import WeekComponent from './WeekComponent.vue';
  
  // Upcoming events 				  				  
  var events = [];

  export default {
    data: function() {
		return {
			calenderDays: [
				{ id: 1, dayofmonth: 0, dayofweek: "sun" },
				{ id: 2, dayofmonth: 1, dayofweek: "mon" },
				{ id: 3, dayofmonth: 2, dayofweek: "tue" },
				{ id: 4, dayofmonth: 3, dayofweek: "wed" },
				{ id: 5, dayofmonth: 4, dayofweek: "thu" },
				{ id: 6, dayofmonth: 5, dayofweek: "fri" },
				{ id: 7, dayofmonth: 6, dayofweek: "sat" }
			  ],
			calenderhour: ['-0AM', '-1AM' , '-2AM', '-3AM', '-4AM', '-5AM', '-6AM', '-7AM', '-8AM', '-9AM', '-10AM', '-11AM', '-12PM', 
						   '-1PM', '-2PM', '-3PM', '-4PM', '-5PM' , '-6PM', '-7PM', '-8PM', '-9PM', '-10PM', '-11PM'],		
			calenderMonth: 0,
			calenderWeek: 0,
			calenderYear: dayjs().format("YYYY")
		}
	},
	computed: {
		/** 
		*  Get and set the calender 
		*		
		*/
		weekInfo: {
			get: function () {
			  return this.calenderDays;
			},
			set: function (newWeek) {
				 this.calenderWeek = newWeek;
				 this.calenderMonth = calcMonth(newWeek);
				 setCalenderWeek(this.calenderWeek, this.calenderDays);
			}
		}
	},
    methods: {
		/** 
		 *  Interactivity - Next week button
		 *
		 */	
		nextWeek: function () {
			if(this.calenderWeek < 52){	
				this.calenderWeek++;
				this.calenderMonth = calcMonth(this.calenderWeek);
				setCalenderWeek(this.calenderWeek, this.calenderDays);
				showEvents(this.calenderWeek);	
			}	
		},
		
		/** 
		 *  Interactivity - Previous week button
		 *
		 */	
		previousWeek: function () {
			if(this.calenderWeek > 1){	
				this.calenderWeek--;
				this.calenderMonth = calcMonth(this.calenderWeek);
				setCalenderWeek(this.calenderWeek, this.calenderDays);
				showEvents(this.calenderWeek);	
			}	
		},
		
		/** 
		 *  Interactivity - User clicks somewhere on the calender 
		 *
		 */	
		newEvent: function (event, dayid, weekday) {
		    var year = this.calenderYear; 
		    var month = this.calenderMonth;
			var day = this.calenderDays[dayid - 1].dayofmonth;
			var dayofweek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
			var week = this.calenderWeek;
			var hour = event.target.id.split('-')[1];
			var hourindex = hour.indexOf("AM");
			
			if(hourindex != -1) 	
				hour = hour.substr(0, hourindex);
			else {
			    var temphour = parseInt(hour.substr(0, hour.indexOf("PM")));
			    hour = (temphour != 12) ? 12 + temphour : temphour;
			}
		
			if(month.indexOf('/') != -1){
				if(day > 15)
					month = month.substr(0, month.indexOf('/') - 1);
				else
					month = month.substr(month.indexOf('/') + 2, month.length - 1);
			}
			
			var selectedDate = new Date(month + ' ' + day + ', ' + year + ' ' + hour + ':00:00');
			
			// Initiate 'create-event-modal' dialogbox 	
			$("#create-event-modal").modal({backdrop: false});
			$('#modal-title').text("New event");
			$('#update-event-button').hide();
			$('#remove-event-button').hide();
			$('#create-event-button').show();
			$('#create-event-modal').data('id', event.target.id);
			$('#create-event-modal').data('week', week);
			$('#create-event-modal').data('day', weekday);
			$('#create-event-modal').data('date', selectedDate);
			$("#event-title").val("");	
			$("#create-event-button").prop('disabled', true);
			$('#event-info-text').text(dayofweek[dayid - 1] + ', ' + day + ' ' + month.toLowerCase() + ' ' + year + ':');	
			// Initiate timepickers					
			setupTimepickerRange(selectedDate);
			$('#event-start-time').timepicker('setTime', selectedDate);
			selectedDate.setHours(selectedDate.getHours() + 1);
			$('#event-stop-time').timepicker('setTime', selectedDate);
		},
		
		/** 
		 *  Interactivity - Create event button   
		 *
		 */
		createNewEvent: function (){
			var id = $('#create-event-modal').data('id');
			var week = $('#create-event-modal').data('week');
			var day = $('#create-event-modal').data('day');
			var date = $('#create-event-modal').data('date');
			var startTime = $('#event-start-time').timepicker('getTime');
			var endTime = $('#event-stop-time').timepicker('getTime');
			var eventTitle = $("#event-title").val();
			
			if(startTime >= endTime)
				alert("You have specified an incorrect time!");
			else {
				startTime.setFullYear(date.getFullYear());
				startTime.setMonth(date.getMonth());
				startTime.setDate(date.getDate());
				
				endTime.setFullYear(date.getFullYear());
				endTime.setMonth(date.getMonth());
				endTime.setDate(date.getDate());
				
				$("#create-event-modal").modal("hide");
				
				axios.post('createEvent/', {
					params: {
						title: eventTitle,
						start: startTime,
						end: endTime
					}
				})
				.then(res => {
                    if(res.data.success == '1') {
						createEvent(week, day, startTime, endTime, eventTitle, id, res.data.eventId);
						alert('Operation successful');
					}
					else {
						alert('Operation failed: Google Calender API failure');
					}
                }).catch((error) => {
					alert('Operation failed: ' + error.response.data.message);
				});
			}
		},
		
		/** 
		 *  Interactivity - Update event button   
		 *
		 */
		updateEvent: function (){
			var id = $('#create-event-modal').data('id');
			var startTime = $('#event-start-time').timepicker('getTime'); 
			var endTime = $('#event-stop-time').timepicker('getTime'); 
			var eventTitle = $("#event-title").val();
			var week = $('#create-event-modal').data('week');
			var eventId = getEventId(week, id);
			
			if(startTime >= endTime)
				alert("You have specified an incorrect time!");
			else {
				if(eventId != null) { 
					var oldStartTime = getStartTime(eventId);
					var oldEndTime = getEndTime(eventId);
					
					startTime.setFullYear(oldStartTime.getFullYear());
					startTime.setMonth(oldStartTime.getMonth());
					startTime.setDate(oldStartTime.getDate());
					
					endTime.setFullYear(oldEndTime.getFullYear());
					endTime.setMonth(oldEndTime.getMonth());
					endTime.setDate(oldEndTime.getDate());
					
					$("#create-event-modal").modal("hide");
					
					axios.post('updateEvent/', {
						params: {
							id: eventId,
							title: eventTitle,
							start: startTime,
							end: endTime
						}
					})
					.then(res => {
						if(res.data.success == '1') {
							updateEvent(startTime, endTime, eventTitle, id, eventId);
							alert('Operation successful');
						}
						else {
							alert('Operation failed: Google Calender API failure');
						}
					}).catch((error) => {
						alert('Operation failed: ' + error.response.data.message);
					});
				}
			}
		},
		
		/** 
		 *  Interactivity - Delete event button   
		 *
		 */
		deleteEvent: function (){
			var id = $('#create-event-modal').data('id');
			var week = $('#create-event-modal').data('week');
			var eventId = getEventId(week, id);
			
			if(eventId != null) {
				$("#create-event-modal").modal("hide");
				
				axios.post('deleteEvent/', {
					params: {
						eventId: eventId
					}
				}).then(res => {
                    if(res.data.success == '1') {
						removeEvent(eventId);
						alert('Operation successful');
					}
					else {
						alert('Operation failed: Google Calender API failure');
					}
                }).catch((error) => {
					alert('Operation failed:' + error.response.data.message);
				});
			}
		},
		
		/** 
		 *  Retrieve events from the primary calender         
		 *
		 */
		getEvents: function () {
			axios.get('eventList/')
			.then(res => {
                    if(res.data.success == '1') {
						$("#information").hide();
						$("#calendar-content").show();
						$("#calendar").css("background-color", "#cfd7e3");
						this.initEvents(res.data.message);
					}
					else {
						var url = res.data.url;
						if(url.length > 0) {
							$("#authorization-code").val('');
							$("#url").text(url);
							$("#information").hide();
							$("#information-authorization-code").show();
						}
						else
							alert('Operation failed: Google Calender API failure');
					}
                }).catch((error) => {
					alert('Operation failed:' + error.response.data.message);
			});
		},
		
		/** 
		 *  Retrieve events from the primary calender (authorization)        
		 *
		 */
		getEventsAuth: function () {
			var authCode = $("#authorization-code").val();
			
			axios.post('eventListAuth/', {
				params: {
					authCode: authCode
				}
			})
			.then(res => {
                    if(res.data.success == '1') {
						$("#information-authorization-code").hide();
						$("#calendar-content").show();
						$("#calendar").css("background-color", "#cfd7e3");
						this.initEvents(res.data.message);
					}
					else {
						alert('Operation failed: Google Calender API failure');
					}
                }).catch((error) => {
					alert('Operation failed: ' + error.response.data.message);
			});
		},
		
		/** 
		 *  Initiate events and update the view         
		 *
		 */
		initEvents: function (ievents){
			var i;
			for (i = 0; i < ievents.length; i++) {
				var event = ievents[i];
				var dow = dayjs(event.startTime).format("ddd").toString().toLowerCase();
				var hour = dayjs(event.startTime).format("H");
				var id = dow + this.calenderhour[hour];
				
				createEvent(dayjs(event.startTime).week(), dow, new Date(event.startTime), new Date(event.endTime), event.eventTitle, id, event.eventId);
			}
			showEvents(dayjs().week());
		},
    },
	
	/** 
	 *  Initiate the calender          
	 *
	 */
	created() {
		this.weekInfo = dayjs().week();
	  
		$(document).ready(function(){	
			/** 
			*  Initiate the timepickers
			*/
			$(function() {
				$('#event-start-time').timepicker({ useSelect: true, 'step': 15});
			});
			$(function() {
				$('#event-stop-time').timepicker({ useSelect: true , 'step': 15});
			});
			
			/** 
			*  #event-title changed
			*/
			$("#event-title").on("input", function() {
			  var eventTitle = $("#event-title").val();
			  if(eventTitle.length > 2)
				$("#create-event-button").prop('disabled', false);
			  else
				$("#create-event-button").prop('disabled', true);
			});
		});
	},
    components: {
      WeekComponent
    }
  }  

 /** 
 *  Places a specified event on the calender 
 *  	
 *	@param ielement - Event id in the calendar
 *	@param istart - Date and time when the event begins 
 *	@param iend - Date and time when the event ends 
 *	@param iid - Event id defined in the calendar 
 *	@param idayofweek - Day of week (sun, mon, tue, wed, thu, fri, sat)
 */
 function eventOnCalender(ielement, istart, iend, iid, idayofweek) {
	var calenderGridElementid = '#calender-day-content-grid-' + idayofweek;
	var hourscontentGridElementId = '#hours-content-grid-' + idayofweek;
	var calenderGridPosition =  $(calenderGridElementid).position();
	var hourscontentGridPosition =  $(hourscontentGridElementId).position();
	var hourscontentPosition = $('#' + iid).position();
	var marginTop = parseInt($(calenderGridElementid).css('margin-top')) + parseInt($(hourscontentGridElementId).css('margin-top')) + parseInt($('#' + iid).css('margin-top')); 
	var borderTop = parseInt($(calenderGridElementid).css('borderTopWidth')) + parseInt($(hourscontentGridElementId).css('borderTopWidth')) +  parseInt($('#' + iid).css('borderTopWidth'));
	var topPos = calenderGridPosition.top + hourscontentGridPosition.top + hourscontentPosition.top + marginTop + borderTop;
	var leftPos = calenderGridPosition.left + hourscontentGridPosition.left + hourscontentPosition.left;
	var hourdiff = (iend.getHours() > 0) ? iend.getHours() - istart.getHours() : 24 - istart.getHours();
	var mindiff = iend.getMinutes() - istart.getMinutes();
	var totdiff = hourdiff + mindiff / 60;
	
	topPos = topPos + $('#' + iid).innerHeight() * istart.getMinutes() / 60;	
	$('#' + ielement).css({left: leftPos +  "px", top: topPos + "px", position: "absolute"}); 
	$('#' + ielement).innerHeight($('#' + iid).innerHeight() * totdiff + hourdiff);
 } 
  
 /** 
 *  Create a new CalendarEvent object (constructor)  
 *  	
 *	@param iweek - Week of year
 *	@param idayofweek - Day of week (sun, mom, tue, wed, thu, fri, sat)
 *	@param istarttime - Date and time when the event begins  
 *	@param iendtime - Date and time when the event ends 
 *	@param ititle - Title of the event
 *	@param iid - Event id defined in the calendar
 *	@param ieventid - Event id defined by Google Calendar API
 */
 function CalenderEvent(iweek, idayofweek, istarttime, iendtime, ititle, iid, ieventid){
	this.title = ititle;
	this.startTime = istarttime;
	this.endTime = iendtime;
	this.id = iid + "-event";
	this.week = iweek;
	this.dayofweek = idayofweek;
	this.eventId = ieventid; 	
 } 
 
 /** 
 *  Create a new event 
 *  	
 *	@param iweek - Week of year
 *	@param idayofweek - Day of week (sun, mon, tue, wed, thu, fri, sat)
 *	@param istart - Date and time when the event begins  
 *	@param iend - Date and time when the event ends 
 *	@param ititle - Title of the event
 *	@param iid - Event id defined in the calendar
 *	@param ieventid - Event id defined by Google Calendar API
 */
 function createEvent(iweek, idayofweek, istart, iend, ititle, iid, ieventid){
	var ev = new CalenderEvent(iweek, idayofweek, istart, iend, ititle, iid, ieventid);
	var calenderGridElementid = '#calender-day-content-grid-' + idayofweek;
	var eventtext = ititle + '<br /> (' + dayjs(istart).format("HH")  + ":" + dayjs(istart).format("mm") + " &ndash; " + dayjs(iend).format("HH") + ":" + dayjs(iend).format("mm") + ')'; 
	
	events.push(ev);
	$('<div id=\"' + ev.id + '\" class="event-content">' + eventtext + '</div>').insertAfter(calenderGridElementid);
	eventOnCalender(ev.id, istart, iend, iid, idayofweek);
	
	// Respond to a click on the event (update or remove)
	$('#' + ev.id).on("click", function(){
	  $("#create-event-modal").modal({backdrop: false});
	  $('#update-event-button').show();
	  $('#remove-event-button').show();
	  $('#create-event-button').hide();	
	  $('#modal-title').text("Update event");
	  $('#create-event-modal').data('id', ev.id);
	  $('#create-event-modal').data('week', ev.week);
	  $('#create-event-modal').data('day', ev.dayofweek);
	  $("#event-title").val(ev.title);		  
	  $("#create-event-button").prop('disabled', true);
	  $('#event-start-time').timepicker('setTime', ev.startTime);
	  $('#event-stop-time').timepicker('setTime', ev.endTime);
	  setupTimepickerRange(ev.startTime);
	});
 }
 
 /** 
 *  Update a specified event 
 *  	
 *	@param istart - Date and time when the event begins  
 *	@param iend - Date and time when the event ends 
 *	@param ititle - Title of the event
 *	@param iid - Event id defined in the calendar
 *	@param ieventid - Event id defined by Google Calendar API
 */
 function updateEvent(istart, iend, ititle, iid, ieventid) {
	var eventUpdated = false;
	var eventtext = "";
	var i = 0, ev;
	var id = "";
	while(!eventUpdated && i < events.length){
		ev = events[i];
		if(ev.eventId == ieventid) {
			ev.startTime = istart;
			ev.endTime = iend;
			ev.title = ititle;
			eventtext = ititle + '<br /> (' + dayjs(istart).format("HH")  + ":" + dayjs(istart).format("mm") + " &ndash; " + dayjs(iend).format("HH") + ":" + dayjs(iend).format("mm") + ')';
			$('#' + ev.id).html(eventtext);
			id = iid.split('-event')[0];
			eventOnCalender(ev.id, istart, iend, id, ev.dayofweek);
			eventUpdated = true;
		}
		i++;
	}
 }
 
 /** 
 *  Remove a specified event 
 *
 *	@param ieventid - Event id defined by Google Calendar API
 */
 function removeEvent(ieventId) {
	var eventRemoved = false;
	var i = 0, ev;
	while(!eventRemoved && i < events.length){
		ev = events[i];
		if(ev.eventId == ieventId) {
			$('#' + ev.id).remove();
			events.splice(i,1);
			eventRemoved = true;
		}
		i++;
	}
 }
 
 /** 
 *  Initiate the specified week
 *  	
 *	@param iweek - Week of year
 *  @param icalenderdays - Array of calenderdays (id, dayofmonth, dayofweek)
 */
 function setCalenderWeek(iweek, icalenderdays) {
	var mon = dayjs().day(0).week(iweek).date();  
	var dmonth = dayjs().day(0).week(iweek).daysInMonth();
	var i,j;
	for (i = 0, j = 0; i < icalenderdays.length; i++, j++) {
		if(mon + j > dmonth){
			mon = 1;
			j = 0;
		}
		icalenderdays[i].dayofmonth = mon + j;
	}		
 } 
  
 /** 
 *  Show upcoming events on the specified week
 *  	
 *	@param iweek - Week of year
 */
 function showEvents(iweek) {
	var i, ev;
	for(i = 0; i < events.length; i++) {
		ev = events[i];
		if(ev.week != iweek) 
			$('#' + ev.id).hide();
		else 	
			$('#' + ev.id).show();
	}		
 }
 
 /** 
 *   Calculate and return the month of year 
 *	 based on the specified week of year
 *  	
 *	 @param iweek - Week of year
 *   @return - Month of year 
 */
 function calcMonth(iweek){
	var month1 = dayjs().day(0).week(iweek).format("MMMM");
	var month2 = dayjs().day(6).week(iweek).format("MMMM");
	
	if(month1 != month2)
		return month1 + " / " + month2;
	else
		return month1;
 }
 
 /** 
 *  Return the unique event id defined by Google Calendar API  
 *  	
 *	@param iweek - Week of year
 *	@param iid - Event id defined in the calendar
 *	@return eventId - The unique event id defined by Google Calendar API 
 *	@return null - If the event wasn't found
 */
 function getEventId(iweek, iid){
	var i = 0, ev;
	var found = false;
	
	while(!found && i < events.length){
		ev = events[i];
		if((ev.id == iid) && (ev.week == iweek)) {			
			found = true;
		}
		i++;
	}
	if(found)
		return ev.eventId;
	else
		return null;
 }
 
 /** 
 *  Return the time when the specified event begins 
 *  	
 *	@param ieventId - The unique event id defined by Google Calendar API
 *	@return startTime - Time when the specified event begins  
 *	@return null - If the event wasn't found
 */
 function getStartTime(ieventId){
	var i = 0, ev;
	var found = false;
	
	while(!found && i < events.length){
		ev = events[i];
		if(ev.eventId == ieventId) {			
			found = true;
		}
		i++;
	}
	if(found)
		return ev.startTime;
	else
		return null;
 }

 /** 
 *  Return the time when the specified event ends 
 *  	
 *	@param ieventId - The unique event id defined by Google Calendar API
 *	@return startTime - Time when the specified event ends  
 *	@return null - If the event wasn't found
 */
 function getEndTime(ieventId){
	var i = 0, ev;
	var found = false;
	
	while(!found && i < events.length){
		ev = events[i];
		if(ev.eventId == ieventId) {			
			found = true;
		}
		i++;
	}
	if(found)
		return ev.endTime;
	else
		return null;
 }
 
 /** 
 *  Initiate the timepickers range
 *  	
 *	@param idate - Selected date and time    
 */
 function setupTimepickerRange(idate){
	var tempDate = new Date(idate);
	var timepickerminHour = tempDate.getHours();
	var timepickermaxMinute = 45;
	var timepickerminMinute = 0;
	var timepickermaxTime = "";
	var timepickerminTime = "";
	var timepickerminRangeminTime = "";
	
	timepickerminTime = (timepickerminHour >= 12) ? timepickerminHour - 12 + ':' + timepickerminMinute + 'pm' : timepickerminHour + ':' + timepickerminMinute + 'am';
	timepickermaxTime = (timepickerminHour >= 12) ? timepickerminHour - 12 + ':' + timepickermaxMinute + 'pm' : timepickerminHour + ':' + timepickermaxMinute + 'am';
	
	timepickerminMinute = tempDate.getMinutes() + 15;
	if(timepickerminMinute == 60) {
		timepickerminHour = (timepickerminHour == 23) ? 0 : timepickerminHour + 1;
		timepickerminMinute = 0;
	}
	
	timepickerminRangeminTime = (timepickerminHour >= 12) ? timepickerminHour - 12 + ':' + timepickerminMinute + 'pm' : timepickerminHour + ':' + timepickerminMinute + 'am';	
	
	$('#event-start-time').timepicker('option', 'minTime', timepickerminTime);
	$('#event-start-time').timepicker('option', 'maxTime', timepickermaxTime);
	$('#event-stop-time').timepicker('option', 'minTime', timepickerminRangeminTime);
	$('#event-stop-time').timepicker('option', 'maxTime', '12am');
 }
</script>
<style>
	ol, li {
	  padding: 0;
	  margin: 0;
	  list-style-type: none;
	}

	.calendar-week {
	  position: relative;
	  background-color: #cfd7e3;
	  border: solid 3px #cfd7e3;
	  margin: 25px;
	}
	
	.calendar-week-header {
	  display: flex;
	  justify-content: space-between;
	  background-color: #fff;
	  padding: 10px;
	}
	
	.calendar-week-header-selected-week {
	  font-size: 24px;
	  font-weight: 600;
	}

	.calendar-week-header-selectors {
	  display: flex;
	  align-items: center;
	  justify-content: space-between;
	  width: 120px;
	  -webkit-user-select: none; /* Safari */
	  -ms-user-select: none; /* IE 10 and IE 11 */
	  user-select: none; /* Standard syntax */
	}
	
	.day-of-week {
	  color: #3e4e63;
	  font-size: 18px;
	  background-color: #fff;
	  padding-bottom: 5px;
	  padding-top: 10px;
	  padding-right: 15px;
	}
	
	.day-of-week, .days-grid {
	  display: grid;
	  grid-template-columns: repeat(7, 1fr);
	}

	.day-of-week > * {
	  text-align: right;
	  padding-right: 10px;
	}

	.days-grid {
	  height: 100%;
	  position: relative;
	  grid-column-gap: 1px;
	  grid-row-gap: 1px;
	  border-top: solid 1px #cfd7e3;
	  max-height: 600px;
	  overflow: auto;
	}
	
	.calender-message {
	  font-size: 16px;
	  margin: 10px;
	  padding: 0;
	  color: #000;
	}
	
	.event-content {
	  font-size: 12px;
	  background-color: #9099a5;
	  border-radius: 4px;
	  color: #fff; 
	  font-weight: 600;
	  z-index: 1;
	  padding-top: 3px;
	  padding-left: 5px;
	  width: calc(100% - 45px);
	}
	
	#previous-week-selector, #next-week-selector  {
	  cursor: pointer;
	}
	
	#information, #information-authorization-code {
	  background-color:#f0f0ea;
	  margin:20px;
	}
	
	#information-authorization-code, #calendar-content {
	  display: none;
	}
</style>