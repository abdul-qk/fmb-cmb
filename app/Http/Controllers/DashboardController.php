<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use App\Models\ServingItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  function getDaysInMonthWithWeekdays()
  {
    $now = Carbon::now();
    $totalDays = $now->daysInMonth;
    $days = [];
   

    // $events = Event::with('servingItems.tiffinSize')->whereYear('date', $now->year)
    $events = Event::with(['menus.recipes.dish', 'servingItems.tiffinSize'])->whereYear('date', $now->year)
      ->whereMonth('date', $now->month)
      ->get();

    for ($i = 1; $i <= $totalDays; $i++) {
      $date = Carbon::create($now->year, $now->month, $i);
      $formattedDate = $date->format('Y-m-d');
      // Filter events that match this date
      $matchingEvents = $events->filter(function ($event) use ($formattedDate) {
        return $event->date === $formattedDate;
      });
      

      // $dishes = $matchingEvents->flatMap(function ($event) {
      //   return $event->menus->flatMap(function ($menu) {
      //     return $menu->dishes->pluck('name');
      //   });
      // })->unique()->values()->toArray();
      // $dishes = $matchingEvents->flatMap(fn($event) => 
      //   $event->menus->flatMap(fn($menu) => 
      //       $menu->dishes->pluck('name')
      //   )
      //   )->unique()->values()->toArray();

      $days[] = [
        'day' => $i,
        'weekday' => $date->format('D'),
        'events' => $matchingEvents->values()->toArray(),
      ];
    }

    return $days;
  }

  public function index()
  {
   
    // $servings = ServingItem::with(['tiffinSize', 'event'])->whereHas('event', function ($query) {
    //   $query->whereDate('date', Carbon::today());
    // })->get();
    // $groupedServings = $servings->groupBy(function ($item) {
    //   return $item->tiffinSize->name . ' Tiffin (for ' . $item->tiffinSize->person_no . ' ' . ($item->tiffinSize->person_no == 1 ? "person" : "persons") . ')';
    // })->map(function ($group) {
    //   return $group->sum('count');
    // });
    // $totalThaal = Event::whereDate('date', Carbon::today())->sum('no_of_thaal');

    // $dishes = Event::with('menus.recipes.dish')->whereDate('date', Carbon::today())->get();
    // $dishNames = [];
    // foreach ($dishes as $event) {
    //   foreach ($event->menus as $menu) {
    //     foreach ($menu->recipes as $recipe) {
    //       $dishNames[] = $recipe->dish->name;
    //     }
    //   }
    // }

    $totalDays = $this->getDaysInMonthWithWeekdays();

    // dd($totalDays);
    // return view($this->view, compact("totalDays", "groupedServings", "totalThaal", 'dishNames'));
    return view($this->view, compact("totalDays"));
  }
}
