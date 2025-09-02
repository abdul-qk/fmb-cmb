<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Event;
use App\Models\ItemCategory;
use App\Models\PurchaseOrderDetail;
use App\Models\Dish;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function procurementRequisition()
    {
        $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        if (request()->has('date_from') && request('date_from') != null) {
          $dateFrom = request('date_from');
        } elseif (request()->has('date_from') && request('date_from') == null) {
          $dateFrom = null;
        }
        if (request()->has('date_to') && request('date_to') != null) {
          $dateTo = request('date_to');
        } elseif (request()->has('date_to') && request('date_to') == null) {
          $dateTo = null;
        }
        $itemCategories = ItemCategory::all();
        $events = Event::all();
        $items = Item::all();
        $results = Item::query()
        ->when(
            (isset($dateFrom) && isset($dateTo)) || request('event_id'),
            function ($query) use ($dateFrom, $dateTo) {
                $query->where(function ($query) use ($dateFrom, $dateTo) {
                    $query->whereHas('purchaseOrderDetails', function ($query) use ($dateFrom, $dateTo) {
                        $query->when(isset($dateFrom) && isset($dateTo), function ($query) use ($dateFrom, $dateTo) {
                            $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                                if ($dateFrom) {
                                    $query->whereDate('date', '>=', $dateFrom);
                                }
                                if ($dateTo) {
                                    $query->whereDate('date', '<=', $dateTo);
                                }
                            });
                        });
                        if (request('event_id')) {
                            $query->where('event_id', request('event_id'));
                        }
                    });
                    $query->orWhereHas('inventoryDetails', function ($query) use ($dateFrom, $dateTo) {
                        $query->when(isset($dateFrom) && isset($dateTo), function ($query) use ($dateFrom, $dateTo) {
                            $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                                if ($dateFrom) {
                                    $query->whereDate('date', '>=', $dateFrom);
                                }
                                if ($dateTo) {
                                    $query->whereDate('date', '<=', $dateTo);
                                }
                            });
                        });
                        if (request('event_id')) {
                            $query->where('event_id', request('event_id'));
                        }
                    });
                });
            }
        )
        ->when(request('item_id'), function ($query) {
            $query->where('id', request('item_id'));
        })
        ->when(request('item_category_id'), function ($query) {
            $query->whereHas('itemCategory', function ($query) {
                $query->where('id', request('item_category_id'));
            });
        })
        ->with([
            'itemCategory',
            'itemBase.baseUom',
            'purchaseOrderDetails' => function ($query) use ($dateFrom, $dateTo) {
                $query->with('approvedDetail.inventories')
                    ->with('event', function ($query) use ($dateFrom, $dateTo) {
                        $query->when(
                            isset($dateFrom) ||
                            isset($dateTo) ||
                            request('event_id'),
                            function ($query) use ($dateFrom, $dateTo) {
                            if ($dateFrom) {
                                $query->whereDate('date', '>=', $dateFrom);
                            }
                            if ($dateTo) {
                                $query->whereDate('date', '<=', $dateTo);
                            }
                            if (request('event_id')) {
                                $query->where('id', request('event_id'));
                            }
                        });
                    });
            },
            'inventoryDetails' => function ($query) use ($dateFrom, $dateTo) {
                $query->with('returns')
                    ->when(isset($dateFrom) && isset($dateTo), function ($query) use ($dateFrom, $dateTo) {
                        $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                            if ($dateFrom) {
                                $query->whereDate('date', '>=', $dateFrom);
                            }
                            if ($dateTo) {
                                $query->whereDate('date', '<=', $dateTo);
                            }
                        });
                    })
                    ->when(request('event_id'), function ($query) {
                        $query->where('event_id', request('event_id'));
                    });
            },
            'supplierReturns'
        ])
        ->get()
        ->map(function ($result) {
            $requestedEvents = $result->purchaseOrderDetails->map(function ($purchaseOrderDetail) {
                return $purchaseOrderDetail->event?->name;
            });
            $requestedQuantities = $result->purchaseOrderDetails->map(function ($purchaseOrderDetail) {
                return $purchaseOrderDetail->approvedDetail->quantity ?? 0;
            });
            $requestedEventServings = $result->purchaseOrderDetails->map(function ($purchaseOrderDetail) {
                return $purchaseOrderDetail->event?->serving;
            });
            $supplierReturnedQuantity = $result->supplierReturns->sum(function ($supplierReturn) {
                return $supplierReturn?->quantity ?? 0;
            });
            return [
                'category_name' => $result->itemCategory->name,
                'item_name' => $result->name,
                'base_uom' => $result->itemBase->baseUom->short_form,
                'requested_quantity' => $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
                    return $purchaseOrderDetail->approvedDetail->quantity ?? 0;
                }),
                'inventory_quantity' => $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
                    return collect($purchaseOrderDetail->approvedDetail->inventories ?? [])->sum('quantity');
                }),
                'issued_quantity' => $result->inventoryDetails->sum(function ($inventoryDetail) {
                    return $inventoryDetail->quantity ?? 0;
                }),
                'returned_quantity' => $result->inventoryDetails->sum(function ($inventoryDetail) {
                    return collect($inventoryDetail->returns ?? [])->sum('quantity');
                }),
                'supplier_returned_quantity' => $supplierReturnedQuantity,
                'last_purchased_price' => $result->purchaseOrderDetails->where('unit_price', '>', 0)->first()?->unit_price ?? '-',
                'requested_events' => $requestedEvents,
                'requested_quantities' => $requestedQuantities,
                'requested_event_servings' => $requestedEventServings
            ];
        });
        // return $results;
        return view('reports.' . $this->view, compact('dateFrom', 'dateTo', 'itemCategories', 'events', 'items', 'results'));
    }

    public function eventConsumption()
    {
        $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        if (request()->has('date_from') && request('date_from') != null) {
            $dateFrom = request('date_from');
        } elseif (request()->has('date_from') && request('date_from') == null) {
            $dateFrom = null;
        }
        if (request()->has('date_to') && request('date_to') != null) {
            $dateTo = request('date_to');
        } elseif (request()->has('date_to') && request('date_to') == null) {
            $dateTo = null;
        }
        $events = Event::all();
        $mealTimes = $events->pluck('meal')->unique();
        $mealTypes = $events->pluck('serving')->unique();
        $eventStatuses = $events->pluck('status')->unique();
        $dishes = Dish::all();
        $results = Item::query()
        ->whereHas('purchaseOrderDetails', function ($query) use ($dateFrom, $dateTo) {
            $query->when(
                isset($dateFrom) ||
                isset($dateTo) ||
                request('event_id') ||
                request('host_name') ||
                request('host_its_no') ||
                request('host_sabeel_no') ||
                request('meal_time') ||
                request('meal_type') ||
                request('status') ||
                request('dish_id'),
                function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                    if ($dateFrom) {
                        $query->whereDate('date', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $query->whereDate('date', '<=', $dateTo);
                    }
                    if (request('event_id')) {
                        $query->where('id', request('event_id'));
                    }
                    if (request('host_name')) {
                        $query->where('host_name', request('host_name'));
                    }
                    if (request('host_its_no')) {
                        $query->where('host_its_no', request('host_its_no'));
                    }
                    if (request('host_sabeel_no')) {
                        $query->where('host_sabeel_no', request('host_sabeel_no'));
                    }
                    if (request('meal_time')) {
                        $query->where('meal', request('meal_time'));
                    }
                    if (request('meal_type')) {
                        $query->where('serving', request('meal_type'));
                    }
                    if (request('status')) {
                        $query->where('status', request('status'));
                    }
                    if (request('dish_id')) {
                        $query->whereHas('menus', function ($query) {
                            $query->whereHas('recipes', function ($query) {
                                $query->where('dish_id', request('dish_id'));
                            });
                        });
                    }
                });
            });
        })
        ->orWhereHas('inventoryDetails', function ($query) use ($dateFrom, $dateTo) {
            $query->when(
                isset($dateFrom) ||
                isset($dateTo) ||
                request('event_id') ||
                request('host_name') ||
                request('host_its_no') ||
                request('host_sabeel_no') ||
                request('meal_time') ||
                request('meal_type') ||
                request('status') ||
                request('dish_id'),
                function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                    if ($dateFrom) {
                        $query->whereDate('date', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $query->whereDate('date', '<=', $dateTo);
                    }
                    if (request('event_id')) {
                        $query->where('id', request('event_id'));
                    }
                    if (request('host_name')) {
                        $query->where('host_name', request('host_name'));
                    }
                    if (request('host_its_no')) {
                        $query->where('host_its_no', request('host_its_no'));
                    }
                    if (request('host_sabeel_no')) {
                        $query->where('host_sabeel_no', request('host_sabeel_no'));
                    }
                    if (request('meal_time')) {
                        $query->where('meal', request('meal_time'));
                    }
                    if (request('meal_type')) {
                        $query->where('serving', request('meal_type'));
                    }
                    if (request('status')) {
                        $query->where('status', request('status'));
                    }
                    if (request('dish_id')) {
                        $query->whereHas('menus', function ($query) {
                            $query->whereHas('recipes', function ($query) {
                                $query->where('dish_id', request('dish_id'));
                            });
                        });
                    }
                });
            });
        })
        ->with([
            'itemCategory',
            'itemBase.baseUom',
            'purchaseOrderDetails' => function ($query) use ($dateFrom, $dateTo) {
                $query->with('approvedDetail.inventories')
                ->when(
                    isset($dateFrom) ||
                    isset($dateTo) ||
                    request('event_id') ||
                    request('host_name') ||
                    request('host_its_no') ||
                    request('host_sabeel_no') ||
                    request('meal_time') ||
                    request('meal_type') ||
                    request('status') ||
                    request('dish_id'),
                    function ($query) use ($dateFrom, $dateTo) {
                    $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                        if ($dateFrom) {
                            $query->whereDate('date', '>=', $dateFrom);
                        }
                        if ($dateTo) {
                            $query->whereDate('date', '<=', $dateTo);
                        }
                        if (request('event_id')) {
                            $query->where('id', request('event_id'));
                        }
                        if (request('host_name')) {
                            $query->where('host_name', request('host_name'));
                        }
                        if (request('host_its_no')) {
                            $query->where('host_its_no', request('host_its_no'));
                        }
                        if (request('host_sabeel_no')) {
                            $query->where('host_sabeel_no', request('host_sabeel_no'));
                        }
                        if (request('meal_time')) {
                            $query->where('meal', request('meal_time'));
                        }
                        if (request('meal_type')) {
                            $query->where('serving', request('meal_type'));
                        }
                        if (request('status')) {
                            $query->where('status', request('status'));
                        }
                        if (request('dish_id')) {
                            $query->whereHas('menus', function ($query) {
                                $query->whereHas('recipes', function ($query) {
                                    $query->where('dish_id', request('dish_id'));
                                });
                            });
                        }
                    });
                });
            },
            'inventoryDetails' => function ($query) use ($dateFrom, $dateTo) {
                $query->with('returns')
                ->when(
                    isset($dateFrom) ||
                    isset($dateTo) ||
                    request('event_id') ||
                    request('host_name') ||
                    request('host_its_no') ||
                    request('host_sabeel_no') ||
                    request('meal_time') ||
                    request('meal_type') ||
                    request('status') ||
                    request('dish_id'),
                    function ($query) use ($dateFrom, $dateTo) {
                    $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                        if ($dateFrom) {
                            $query->whereDate('date', '>=', $dateFrom);
                        }
                        if ($dateTo) {
                            $query->whereDate('date', '<=', $dateTo);
                        }
                        if (request('event_id')) {
                            $query->where('id', request('event_id'));
                        }
                        if (request('host_name')) {
                            $query->where('host_name', request('host_name'));
                        }
                        if (request('host_its_no')) {
                            $query->where('host_its_no', request('host_its_no'));
                        }
                        if (request('host_sabeel_no')) {
                            $query->where('host_sabeel_no', request('host_sabeel_no'));
                        }
                        if (request('meal_time')) {
                            $query->where('meal', request('meal_time'));
                        }
                        if (request('meal_type')) {
                            $query->where('serving', request('meal_type'));
                        }
                        if (request('status')) {
                            $query->where('status', request('status'));
                        }
                        if (request('dish_id')) {
                            $query->whereHas('menus', function ($query) {
                                $query->whereHas('recipes', function ($query) {
                                    $query->where('dish_id', request('dish_id'));
                                });
                            });
                        }
                    });
                });
            }
        ])
        ->get()
        ->map(function ($result) {
            $issuedQuantities = $result->inventoryDetails->map(function ($inventoryDetail) {
                return $inventoryDetail->quantity ?? 0;
            });
            $returnedQuantities = $result->inventoryDetails->flatMap(function ($inventoryDetail) {
                return collect($inventoryDetail->returns ?? [])->pluck('quantity');
            });
            return [
                'category_name' => $result->itemCategory->name,
                'item_name' => $result->name,
                'base_uom' => $result->itemBase->baseUom->short_form,
                'requested_quantity' => $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
                    return $purchaseOrderDetail->approvedDetail->quantity ?? 0;
                }),
                'inventory_quantity' => $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
                    return collect($purchaseOrderDetail->approvedDetail->inventories ?? [])->sum('quantity');
                }),
                'issued_quantity' => $issuedQuantities->sum(),
                'returned_quantity' => $returnedQuantities->sum(),
                'last_purchased_price' => $result->purchaseOrderDetails->where('unit_price', '>', 0)->first()?->unit_price ?? '-',
                'issued_quantities' => $issuedQuantities,
                'returned_quantities' => $returnedQuantities
            ];
        });
        return view('reports.' . $this->view, compact(
            'dateFrom', 'dateTo', 'mealTimes', 'events', 'mealTypes', 'results', 'eventStatuses',
            'dishes'
        ));
    }

    public function inventorySummary()
    {
        $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        if (request()->has('date_from') && request('date_from') != null) {
          $dateFrom = request('date_from');
        } elseif (request()->has('date_from') && request('date_from') == null) {
          $dateFrom = null;
        }
        if (request()->has('date_to') && request('date_to') != null) {
          $dateTo = request('date_to');
        } elseif (request()->has('date_to') && request('date_to') == null) {
          $dateTo = null;
        }
        $itemCategories = ItemCategory::all();
        $events = Event::all();
        $items = Item::all();
        $results = Item::query()
        ->when(
            isset($dateFrom) || isset($dateTo) || request('event_id'),
            function ($query) use ($dateFrom, $dateTo) {
                $query->where(function ($query) use ($dateFrom, $dateTo) {
                    $query->whereHas('purchaseOrderDetails', function ($query) use ($dateFrom, $dateTo) {
                        $query->when(isset($dateFrom) && isset($dateTo), function ($query) use ($dateFrom, $dateTo) {
                            $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                                if ($dateFrom) {
                                    $query->whereDate('date', '>=', $dateFrom);
                                }
                                if ($dateTo) {
                                    $query->whereDate('date', '<=', $dateTo);
                                }
                            });
                        });
                        if (request('event_id')) {
                            $query->where('event_id', request('event_id'));
                        }
                    });
                    $query->orWhereHas('inventoryDetails', function ($query) use ($dateFrom, $dateTo) {
                        $query->when(isset($dateFrom) || isset($dateTo), function ($query) use ($dateFrom, $dateTo) {
                            $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                                if ($dateFrom) {
                                    $query->whereDate('date', '>=', $dateFrom);
                                }
                                if ($dateTo) {
                                    $query->whereDate('date', '<=', $dateTo);
                                }
                            });
                        });
                        if (request('event_id')) {
                            $query->where('event_id', request('event_id'));
                        }
                    });
                });
            }
        )
        ->when(request('item_id'), function ($query) {
            $query->where('id', request('item_id'));
        })
        ->when(request('item_category_id'), function ($query) {
            $query->whereHas('itemCategory', function ($query) {
                $query->where('id', request('item_category_id'));
            });
        })
        ->with([
            'itemCategory',
            'itemBase.baseUom',
            'purchaseOrderDetails' => function ($query) use ($dateFrom, $dateTo) {
                $query->with('approvedDetail.inventories')
                    ->with('event', function ($query) use ($dateFrom, $dateTo) {
                        $query->when(
                            isset($dateFrom) ||
                            isset($dateTo) ||
                            request('event_id'),
                            function ($query) use ($dateFrom, $dateTo) {
                            if ($dateFrom) {
                                $query->whereDate('date', '>=', $dateFrom);
                            }
                            if ($dateTo) {
                                $query->whereDate('date', '<=', $dateTo);
                            }
                            if (request('event_id')) {
                                $query->where('id', request('event_id'));
                            }
                        });
                    });
            },
            'inventoryDetails' => function ($query) use ($dateFrom, $dateTo) {
                $query->with('returns')
                    ->when(isset($dateFrom) && isset($dateTo), function ($query) use ($dateFrom, $dateTo) {
                        $query->whereHas('event', function ($query) use ($dateFrom, $dateTo) {
                            if ($dateFrom) {
                                $query->whereDate('date', '>=', $dateFrom);
                            }
                            if ($dateTo) {
                                $query->whereDate('date', '<=', $dateTo);
                            }
                        });
                    })
                    ->when(request('event_id'), function ($query) {
                        $query->where('event_id', request('event_id'));
                    });
            },
            'supplierReturns'
        ])
        ->get()
        ->map(function ($result) {
            $supplierReturnedQuantity = $result->supplierReturns->sum(function ($supplierReturn) {
                return $supplierReturn?->quantity ?? 0;
            });
            $allInventories = $result->purchaseOrderDetails
            ->flatMap(function ($purchaseOrderDetail) {
                return collect($purchaseOrderDetail->approvedDetail->inventories ?? []);
            });
            $initialQuantity = $allInventories
            ->sortBy('id')
            ->first()?->quantity ?? 0;
            return [
                'category_name' => $result->itemCategory->name,
                'item_name' => $result->name,
                'base_uom' => $result->itemBase->baseUom->short_form,
                'requested_quantity' => $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
                    return $purchaseOrderDetail->approvedDetail->quantity ?? 0;
                }),
                'inventory_quantity' => $result->purchaseOrderDetails->sum(function ($purchaseOrderDetail) {
                    return collect($purchaseOrderDetail->approvedDetail->inventories ?? [])->sum('quantity');
                }),
                'issued_quantity' => $result->inventoryDetails->sum(function ($inventoryDetail) {
                    return $inventoryDetail->quantity ?? 0;
                }),
                'returned_quantity' => $result->inventoryDetails->sum(function ($inventoryDetail) {
                    return collect($inventoryDetail->returns ?? [])->sum('quantity');
                }),
                'supplier_returned_quantity' => $supplierReturnedQuantity,
                'initial_quantity' => $initialQuantity
            ];
        });
        // return $results;
        return view('reports.' . $this->view, compact('dateFrom', 'dateTo', 'itemCategories', 'events', 'items', 'results'));
    }
}
