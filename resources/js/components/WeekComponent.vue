<template>
	<li class="calendar-day">
		<span>{{ dayofmonth }}</span>
		<div class="calender-day-content-grid" v-bind:id="'calender-day-content-grid-' + dayofweek"> 
			<ol id="hours-label-grid" class="hours-label-grid">
				<li class="hours-label"></li>
				<li class="hours-label">1 AM</li>
				<li class="hours-label">2 AM</li>
				<li class="hours-label">3 AM</li>
				<li class="hours-label">4 AM</li>
				<li class="hours-label">5 AM</li>
				<li class="hours-label">6 AM</li>
				<li class="hours-label">7 AM</li>
				<li class="hours-label">8 AM</li>
				<li class="hours-label">9 AM</li>
				<li class="hours-label">10 AM</li>
				<li class="hours-label">11 AM</li>
				<li class="hours-label">12 PM</li>  
				<li class="hours-label">1 PM</li>
				<li class="hours-label">2 PM</li>
				<li class="hours-label">3 PM</li>
				<li class="hours-label">4 PM</li>
				<li class="hours-label">5 PM</li>
				<li class="hours-label">6 PM</li>
				<li class="hours-label">7 PM</li>
				<li class="hours-label">8 PM</li>
				<li class="hours-label">9 PM</li>
				<li class="hours-label">10 PM</li>
				<li class="hours-label">11 PM</li>
			</ol>
			<ol class="hours-content-grid" v-bind:id="'hours-content-grid-' + dayofweek">
				<li class="hours-content" v-bind:id="dayofweek+hourofday[0]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[1]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[2]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[3]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[4]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[5]" v-on:click="$emit('new-event', $event)"></li>  
				<li class="hours-content" v-bind:id="dayofweek+hourofday[6]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[7]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[8]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[9]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[10]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[11]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[12]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[13]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[14]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[15]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[16]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[17]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[18]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[19]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[20]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[21]" v-on:click="$emit('new-event', $event)"></li> 
				<li class="hours-content" v-bind:id="dayofweek+hourofday[22]" v-on:click="$emit('new-event', $event)"></li>
				<li class="hours-content" v-bind:id="dayofweek+hourofday[23]" v-on:click="$emit('new-event', $event)"></li>
			</ol>
		</div>
	</li> 
</template>
<script>
  export default {
    props: {
		dayofmonth: 
		{
			type: Number
		},
		dayofweek: 
		{
			type: String
		},
		hourofday: 
		{
			type: Array
		},
		events: 
		{
			type: String
		}
	},
  }
</script>
<style>
	.calendar-day {
		position: relative;
		min-height: 400px;
		font-size: 16px;
		background-color: #fff;
		color: #3e4e63;
		padding: 5px;
		border: solid 2px #fff;
		z-index: 0;
	}
	
	.calendar-day:hover {
	  border-color: #cfd7e3; 
	}

	.calendar-day > span {
		display: flex;
		justify-content: center;
		align-items: center;
		position: absolute;
		right: 2px;
		width: 20px;
		height: 20px;
	}
	
	.calender-day-content-grid {
		position: relative;	
		display: grid;
		grid-template-columns: 35px auto;
		z-index: 0;
	}
	
	.hours-label-grid, .hours-content-grid {
		position: relative;	
		display: grid;
		margin-bottom: 15px;
		grid-row-gap: 1px;
		grid-template-columns: auto;
	}

	.hours-label-grid {
		background-color: #fff;
		margin-top: 40px;
		padding-bottom: 7px;
	}

	.hours-content-grid {  
		background-color: #ddd;
		margin-top: 47px;
		border-top: solid 1px #dddddd;
		border-bottom: solid 1px #dddddd;
	}

	.hours-label, .hours-content {
		position: relative;
		font-size: 10px;
		text-align: left;
		height: 50px;
	}

	.hours-label, .hours-content {	
		color: #3e4e63;
		background-color: #fff;
	}

	.hours-label {
		padding: 0px 0px 5px 0px;
	}

	.hours-content {
		padding: 0px 0px 5px 5px;
	}
</style>