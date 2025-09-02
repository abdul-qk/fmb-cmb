<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
use App\Models\Event; // Ensure to import your Event model
use Illuminate\Support\Facades\Log; // Import the Log facade

class MonthlyDateTime implements Rule
{
  protected $start;
  protected $end;
  protected $eventDate;
  protected $isUpdate;
  protected $eventId; 

  public function __construct($start, $end, $eventDate = null, $isUpdate = false, $eventId = null)
  {
    $this->start = $start; 
    $this->end = $end;    
    $this->eventDate = $eventDate; 
    $this->isUpdate = $isUpdate; 
    $this->eventId = $eventId; 
  }

  public function passes($attribute, $value)
  {
    if (empty($this->eventDate)) {
      return true; 
    }
    try {
      $startDateTime = $this->start;
      $endDateTime = $this->end;

    } catch (\Exception $e) {
      Log::error("Time parsing error: " . $e->getMessage());
      return false; // Validation fails if there's an error
    }
    // Check for existing events that overlap
    $existingEventsQuery = Event::whereDate('date', $this->eventDate)
      ->where(function ($query) use ($startDateTime, $endDateTime) {
        $query->where(function ($query) use ($startDateTime, $endDateTime) {
          $query->where('start', '<', $endDateTime)
            ->where('end', '>', $startDateTime);
        })
          ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
            $query->where('start', $startDateTime)
              ->where('end', $endDateTime);
          });
      });

    if ($this->isUpdate && $this->eventId) {
      $existingEventsQuery->where('id', '!=', $this->eventId); // Exclude the current event
    }

    return !$existingEventsQuery->exists();
  }


  public function message()
  {
    return 'Event already created';
  }
}
