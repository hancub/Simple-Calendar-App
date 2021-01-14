<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    /**
     * Test of GoogleCalendar.
     *
     * @return void
     */
    public function testEventList()
    {			
	   
		$logfile = fopen(__DIR__ . "/testlog.txt", "a") or die("Unable to open file!");
		
		$logtxt = "Response from get(/eventList):\n\n";
		
		fwrite($logfile, $logtxt);

		$response = $this->get('/eventList');
		$dumparray = $response->dump();
				
		foreach ($dumparray as $logtxt) {
		  fwrite($logfile, $logtxt);
		}
		
		fwrite($logfile, "\n\n");
		fclose($logfile);
		$response->assertStatus(200);
    }
}
